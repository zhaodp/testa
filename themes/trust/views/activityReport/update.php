<?php
/* @var $this ActivityReportController */
/* @var $model ActivityReport */

$this->breadcrumbs=array(
	'Activity Reports'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ActivityReport', 'url'=>array('index')),
	array('label'=>'Create ActivityReport', 'url'=>array('create')),
	array('label'=>'View ActivityReport', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ActivityReport', 'url'=>array('admin')),
);
?>

<h1>Update ActivityReport <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>