<?php
/* @var $this ZhaopinController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Driver Zhaopins',
);

$this->menu=array(
	array('label'=>'Create DriverZhaopin', 'url'=>array('create')),
	array('label'=>'Manage DriverZhaopin', 'url'=>array('admin')),
);
?>

<h1>Driver Zhaopins</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
