<Script><!--
function DoDel()
{
 if(!confirm('������������� ������� ���� �������?')) return;
 var f=document.forms[0];
 f.x.value='delete';
 f.o.value='';
 f.submit();
}
//--></Script>
<?
global $CFG;

if($CFG->Error)
  echo "<Div Class='Error'>", htmlspecialchars($CFG->Error), "</Div>";

if('new'==trim($_REQUEST['x'])):
 $CFG->params->x='new';
else:
 $CFG->params->x='edit';
 $x=sqlite3_query($CFG->db, "Select S From Origins Where N=".$CFG->params->n);
 if($r=sqlite3_fetch($x))
  $CFG->entry->o=$r[0];
 else
  Header("Location: ./", hRef());
 sqlite3_query_close($x);
endif;

LoadLib('/forms');
$CFG->defaults->Input->maxWidth=1;
?>
<Form Action='./' Method='POST'>
<?
TextArea('o', '�����');
if($CFG->Editor):
 $CFG->defaults->Input->H=2;
 TextArea('Note', '�����������');
 BR();
 echo "<Center>";
 Submit();
 if('new'!=$CFG->params->x) echo "\n<Input Type='Button' Value=' ������� ' onClick='DoDel()' />\n";
 echo "</Center>";
 HiddenInputs();
endif;

if($CFG->params->n):
$x=sqlite3_query($CFG->db, "Select * From History Where oId=".$CFG->params->n." Order By TimeStamp Desc, N Desc");
$numC=0;
#if(odbc_num_rows($x)):
while($r=@sqlite3_fetch_array($x)):
 if(!$numC) echo "<H2>�����������/�������</H2>\n";
 $numC++;
 echo "<Div Class='Origin' id='", $r['N'], "' x-Operation=\"", htmlspecialchars($r['Operation']),
    "\"\nTitle=\"", htmlspecialchars($r['Value']),
    "\"><Span Class='TimeStamp'>", strftime("%x %X", $r['TimeStamp']), "</Span>\n", 
    nl2br(htmlspecialchars($r['Note'])), "</Div>\n";
endwhile;
#endif;
@sqlite3_query_close($x);
endif;

# ��������������� ������ �� �������� ������� ������ �� ������� ��������
function NhRef($N)
{
 global $CFG;
 Load();
 for($i=($M=count($CFG->Origins)); $i>0; $i--)
  if($N==$CFG->Origins[$i-1]->N):
   $pages=ceil($M/$CFG->params->lines);
   if($pages>$CFG->params->pages)$pages=$CFG->params->pages;
   return  hRef('n', null, 'p', (1+floor(($i*$pages+1)/$M))."/$pages");
  endif;
}

?>
<Small>&raquo;������ <A hRef='./<?=NhRef($CFG->params->n)?>'>������</A> ���������
<?
if($CFG->Editor) echo "<BR />&raquo;<A hRef='./?x=new'>��������</A> �������";
?>
</Small>
</Form>
<Script><!--
document.forms[0].o.<?= $CFG->Editor? "focus()":"readOnly=1"?>;
//--></Script>
