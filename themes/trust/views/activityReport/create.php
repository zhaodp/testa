<?php
/* @var $this ActivityReportController */
/* @var $model ActivityReport */

$this->breadcrumbs=array(
	'Activity Reports'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ActivityReport', 'url'=>array('index')),
	array('label'=>'Manage ActivityReport', 'url'=>array('admin')),
);
?>

<h1>Create ActivityReport</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>