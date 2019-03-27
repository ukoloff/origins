<?
if('new'==$_REQUEST['x']):
 $CFG->Mode='edit';
 $CFG->title='Новый ориджин';
elseif($CFG->params->n=(int)$_REQUEST['n']):
 $CFG->Mode='edit';
 $CFG->title='Ориджин';
else:
 $CFG->Mode='list';
 $CFG->title='Ориджины';
endif;
?>
