<?
if('new'==$_REQUEST['x']):
 $CFG->Mode='edit';
 $CFG->title='����� �������';
elseif($CFG->params->n=(int)$_REQUEST['n']):
 $CFG->Mode='edit';
 $CFG->title='�������';
else:
 $CFG->Mode='list';
 $CFG->title='��������';
endif;
?>
