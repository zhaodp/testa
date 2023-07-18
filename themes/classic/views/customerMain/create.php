<?php
/* @var $this CustomerMainController */
/* @var $model CustomerMain */

$this->breadcrumbs=array(
	'Customer Mains'=>array('index'),
	'Create',
);
/*
$this->menu=array(
	array('label'=>'List CustomerMain', 'url'=>array('index')),
	array('label'=>'Manage CustomerMain', 'url'=>array('admin')),
);
*/
?>

<h1>添加新客户</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>