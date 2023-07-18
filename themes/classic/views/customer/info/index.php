<?php
/* @var $this CustomerMainController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Customer Mains',
);

$this->menu=array(
	array('label'=>'Create CustomerMain', 'url'=>array('create')),
	array('label'=>'Manage CustomerMain', 'url'=>array('admin')),
);
?>

<h1>Customer Mains</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
