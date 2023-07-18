<?php
/* @var $this ActivityReportController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Activity Reports',
);

$this->menu=array(
	array('label'=>'Create ActivityReport', 'url'=>array('create')),
	array('label'=>'Manage ActivityReport', 'url'=>array('admin')),
);
?>

<h1>Activity Reports</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
