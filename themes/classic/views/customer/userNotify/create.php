<?php
/* @var $this UserNotifyController */
/* @var $model UserNotify */

$this->breadcrumbs=array(
	'User Notifies'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List UserNotify', 'url'=>array('index')),
	array('label'=>'Manage UserNotify', 'url'=>array('admin')),
);
?>

<h1>添加新的通知（1/2）</h1>

<?php $this->renderPartial('userNotify/_form', array('model'=>$model)); ?>