<?
setlocale(LC_ALL, "ru_RU.cp1251");

LoadLib('/sqlite');

$Path=dirname(dirname(__FILE__))."/data/origins.sq3";
$CFG->db=sqlite3_open($Path);

if($_SERVER['HTTPS'] and $CFG->Auth>0 and 'stas'==$CFG->u) $CFG->Editor=1;

foreach(Array('lines'=>25, 'pages'=>40) as $p=>$def):
 $CFG->defaults->$p=$def;
 $n=(int)$_REQUEST[$p];
 if($n<=1) $n=$def;
 $CFG->params->$p=$n;
endforeach;

function Sorter(&$a, &$b)
{
 return strcoll($a->S, $b->S);
}

//Загрузить все ориджины или только по фильтру
function Load($Filter=null)
{
 global $CFG;
 unset($CFG->Origins);
 $x=sqlite3_query($CFG->db, "Select * From Origins");
 while($rr=sqlite3_fetch_array($x)):
  unset($r);
  foreach($rr as $k=>$v) $r->$k=$v;
  if($Filter and !$Filter($r->S)) continue;
  $CFG->Origins[]=$r;
 endwhile;
 sqlite3_query_close($x);
 usort($CFG->Origins, Sorter);
}

?>
