<?php
/* @var $this ProblemsCollectController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Problems Collects',
);

$this->menu=array(
	array('label'=>'Create ProblemsCollect', 'url'=>array('create')),
	array('label'=>'Manage ProblemsCollect', 'url'=>array('admin')),
);
?>

<h1>Problems Collects</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
