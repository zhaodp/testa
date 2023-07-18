<?php
/* @var $this HolidayController */
/* @var $model Holiday */

$this->breadcrumbs=array(
	'Holidays'=>array('index'),
	'Create',
);

?>

<h1>增加节假日</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>