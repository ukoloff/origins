<Script><!--
function Xpand(X)
{
 Y=findId('Search');
 Y.className='On'==Y.className?'Off':'On';
 X.blur();
}
//--></Script>
<?
LoadLib('/forms');
foreach($searchParams=Array('q', 'x', 'case') as $k)
 $CFG->params->$k=$CFG->entry->$k=trim($_REQUEST[$k]);
$CFG->entry->qx=$CFG->entry->q;
if(!$CFG->entry->case)$CFG->entry->qx=strtolower($CFG->entry->qx);
if('re'==$CFG->entry->x) $CFG->entry->qx="`".strtr($CFG->entry->qx, Array('`'=>"\\`"))."`";
$Modes=Array(
''=>'Содержит',
'begin'=>'Начинается',
'end'=>'Заканчивается',
're'=>'Рег. выражение',
);
$CFG->defaults->Input->BR='';

Load($CFG->entry->q?Filter:null);
$N=count($CFG->Origins);
list($pageNo, $pages)=explode('/', $_REQUEST['p']);
$pageNo=(int)$pageNo;
$pages=(int)$pages;
if($pages<1):
 $pages=ceil($N/$CFG->params->lines);
 if($pages>$CFG->params->pages)$pages=$CFG->params->pages;
endif;
$CFG->pages=$pages;
if($pageNo>$pages) $pageNo=$pages;
if($pageNo<=0)$pageNo=1;
?>
<Table Class='Hdr' CellSpacing='0' CellPadding='0'>
<TR><TD><Table Class='Upper' CellSpacing='0' CellPadding='0'><TR>
<TD Class='Search'><A hRef='#' onClick='Xpand(this); return false;' Title='Открыть/спрятать панель поиска'>Поиск</A></TD>
<TD Class='Letters'>
<?
function hRefPage($n)
{
 global $CFG;
 return hRef('p', "$n/{$CFG->pages}");
}

function line3($line)
{
 global $CFG;
 return ucfirst(strtolower(substr(preg_replace('/[^[:alpha:]]/', '', $CFG->Origins[$line]->S), 0, 3)));
}

function charOf($line)
{
 return substr(line3($line), 0, 1);
}

if($pages>1):
 if($CFG->Editor)
  echo "<A Class='Add' hRef='./", hRef('x', 'new'), "' Title='Добавить ориджин'>+</A>";
 echo "<A\nhRef='./", hRefPage($pageNo<=1?$pages:$pageNo-1), "' Title='На страницу назад'>&laquo;</A>";
 $prevC='';
 for($i=1; $i<=$pages; $i++):
  $beg3=line3($p=floor($N*($i-1)/$pages));
  $end3=line3($q=floor($N*$i/$pages)-1);
  $C=$beg3{0};
  if($C==$prevC and $prevC!=$end3{0}):
   while($q>$p+1)
    if($prevC==charOf($pp=floor(($p+$q)/2)))$p=$pp; else $q=$pp;
   $C=charOf($q);
  endif;
  echo "<A\n Class='", $i==$pageNo? 'This' :($i<$pageNo?'Head':'Tail'), $i%2, 
    "' hRef='./", hRefPage($i), "' Title='", htmlspecialchars($beg3), " - ", htmlspecialchars($end3), "'>",
    strtolower($C), "</A>";
  $prevC=$C;
 endfor;
 echo "<A\nhRef='./", hRefPage($pageNo>=$pages?1:$pageNo+1), "' Title='На страницу вперёд'>&raquo;</A>";
endif;
if($CFG->Editor)
 echo "<A Class='Add' hRef='./", hRef('x', 'new'), "' Title='Добавить ориджин'>+</A>";
foreach($searchParams as $k)
  unset($CFG->params->$k);
?>
<BR /></TD>
</TR></Table>	
</TD></TR>
<TR><TD Class='Lower'><Div id='NoSearch'><BR /></Div><Div id='Search' Class='<?= $CFG->entry->q?'On':'Off'?>'>
<Form>
<?
Input('q', 'Текст:');
Select('x', $Modes, 'Как:');
CheckBox('case', 'Регистр');
?>
<Input Type='Submit' Value=' Искать! ' />
<? if($CFG->entry->q) echo "&raquo<A hRef='./'>Полный список</A>";
?>
</Form>
</Div></TD></TR></Table>
<?
$start=floor($N*($pageNo-1)/$pages);
$stop=floor($N*$pageNo/$pages);
for($i=$start; $i<$stop; $i++):
 $r=&$CFG->Origins[$i];
 echo "<Div Class='Origin'>", nl2br(htmlspecialchars($r->S)), 
    "\n<A hRef='./", hRef('n', $r->N) ,"' Class='Edit'>&raquo;</A></Div>\n";
endfor;

function Filter($S)
{
 global $CFG;
 if(!$CFG->entry->case) $S=strtolower($S);
 switch($CFG->entry->x)
 {
  case 'begin': return substr($S, 0, strlen($CFG->entry->qx))==$CFG->entry->qx;
  case 'end': return substr($S, -strlen($CFG->entry->qx))==$CFG->entry->qx;
  case 're': return preg_match($CFG->entry->qx, $S);
  default: return false!==strpos($S, $CFG->entry->qx);
 }
}

$xx=sqlite3_query($CFG->db, "Select count(*) N From Origins");
$x=sqlite3_fetch($xx);
sqlite3_query_close($xx);
?>
<Small>&raquo;Найдено <?=count($CFG->Origins)?>/<?= $x[0] ?>
(<A hRef='origins.txt/' Title='Сохранить в виде файла для the Bat!'>Загрузить все</A>)<BR />
</Small>
