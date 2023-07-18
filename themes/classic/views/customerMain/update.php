<?php
/* @var $this CustomerMainController */
/* @var $model CustomerMain */

$this->breadcrumbs=array(
	'Customer Mains'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);
/*
$this->menu=array(
	array('label'=>'List CustomerMain', 'url'=>array('index')),
	array('label'=>'Create CustomerMain', 'url'=>array('create')),
	array('label'=>'View CustomerMain', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage CustomerMain', 'url'=>array('admin')),
);
*/
?>

<h1>修改客户基本信息</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>