<?php
/* @var $this VipController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Vips',
);

$this->menu=array(
	array('label'=>'Create Vip', 'url'=>array('create')),
	array('label'=>'Manage Vip', 'url'=>array('admin')),
);
?>

<h1>Vips</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
