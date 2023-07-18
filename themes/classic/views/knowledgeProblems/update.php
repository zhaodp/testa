<?php
/* @var $this ProblemsCollectController */
/* @var $model ProblemsCollect */

$this->breadcrumbs=array(
	'Problems Collects'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ProblemsCollect', 'url'=>array('index')),
	array('label'=>'Create ProblemsCollect', 'url'=>array('create')),
	array('label'=>'View ProblemsCollect', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ProblemsCollect', 'url'=>array('admin')),
);
?>

<h1>Update ProblemsCollect <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>