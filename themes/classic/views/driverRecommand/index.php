<?php
/* @var $this DriverRecommandController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Driver Recommands',
);

$this->menu=array(
	array('label'=>'Create DriverRecommand', 'url'=>array('create')),
	array('label'=>'Manage DriverRecommand', 'url'=>array('admin')),
);
?>

<h1>Driver Recommands</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
