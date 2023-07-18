<?php
$this->breadcrumbs=array(
	'Notices'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Create Notice', 'url'=>array('create')),
	array('label'=>'View Notice', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Notice', 'url'=>array('admin')),
);
?>

<h1>更新公告 <?php echo $model->title; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>