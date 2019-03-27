<?
function NormStr($S)
{
 return join("\n", array_map(rtrim, preg_split('/(\\r\\n?)|\\n/', rtrim($S))));
}

function prevVals()
{
 global $CFG;
 $n=(int)$CFG->entry->n;
 $q=sqlite3_query($CFG->db, 
    "Select S, (Select Value From History Where oId=$n Order By TimeStamp Desc, N Desc Limit 1) As PrevS From Origins Where N=$n");
 $r=sqlite3_fetch_array($q);
 sqlite3_query_close($q);
 return $r;
}

function toHistory($val, $Operation='=', $Note=null)
{
 global $CFG;
 if(!isset($Note))
   $Note=$CFG->entry->Note;
 if(!strlen($Note))
  switch($Operation)
  {
    case '+': $Note='Создание'; break;
    case '-': $Note='Удаление'; break;
    case '~': $Note='Модификация'; break;
    default:  $Note='Событие';
  }
 $IP=$_SERVER["HTTP_X_FORWARDED_FOR"];
 if($IP) $IP=" ".$IP;
 return sqlite3_exec($CFG->db, "Insert Into History(oId, TimeStamp, Operation ,Note, user, IP, Value) Values(".
    $CFG->entry->n.", strftime('%s', 'now'), ".sqlite3_escape($Operation).", ".
    sqlite3_escape($Note).", ".
    sqlite3_escape($CFG->u).", ".sqlite3_escape($_SERVER['REMOTE_ADDR'].$IP).", ".
    sqlite3_escape($val).")");
}

$S=NormStr($_REQUEST['o']);
$CFG->entry->n=(int)trim($_REQUEST['n']);
$CFG->entry->Note=NormStr($_REQUEST['Note']);

sqlite3_exec($CFG->db, 'begin');
$Ok=false;

switch($_REQUEST['x'])
{
 case 'new':
  unset($CFG->entry->n);
  if(!sqlite3_exec($CFG->db, "Insert Into Origins(S) Values(".sqlite3_escape($S).")")) break;
  $CFG->entry->n=sqlite3_last_insert_rowid($CFG->db);
  toHistory($S, '+');
  $Ok=true;
  break;
 case 'delete':
  $Prev=prevVals();
  if(!$Prev):
   unset($CFG->entry->n);
   $Ok=true;
   break;
  endif;
  toHistory($Prev['S']==$Prev['PrevS']? null : $Prev['S'], '-')
    and sqlite3_exec($CFG->db, "Delete From Origins Where N=".$CFG->entry->n)
    and ($Ok=true);
  if($Ok) unset($CFG->entry->n);
  break;
 default:
  $Prev=prevVals();
  if(!$Prev):
   unset($CFG->entry->n);
   $Ok=true;
   break;
  endif;
  if($Prev['S']!=$Prev['PrevS'])
    if(!toHistory($Prev['S'], '=', 'Сохранено для истории')) break;
  if($Prev['S']==$S and !$CFG->entry->Note):
    $Ok=1;
    break;
  endif;
  sqlite3_exec($CFG->db, "Update Origins Set S=".sqlite3_escape($S)." Where N=".(int)$CFG->entry->n)
    and toHistory($S, '~')
    and ($Ok=true);
}

if($Ok):
 sqlite3_exec($CFG->db, 'commit');
 header('Location: ./'.hRef('n', $CFG->entry->n));
else:
 $CFG->Error='SQL error: '.sqlite3_error($CFG->db);
 sqlite3_exec($CFG->db, 'rollback');
endif;

?>
