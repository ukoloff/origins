<?
header("Content-Type: text/plain");
header("Content-disposition: attachment; filename=origins.txt");
Load();
foreach($CFG->Origins as $s):
 $s=strtr($s->S, Array("\n"=>"\\n"));
 echo "$s\r\n";
endforeach;
exit;
?>
