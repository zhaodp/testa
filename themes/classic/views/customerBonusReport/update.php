<?php
/* @var $this CustomerBonusReportController */
/* @var $model CustomerBonusReport */

$this->breadcrumbs=array(
	'Customer Bonus Reports'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List CustomerBonusReport', 'url'=>array('index')),
	array('label'=>'Create CustomerBonusReport', 'url'=>array('create')),
	array('label'=>'View CustomerBonusReport', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage CustomerBonusReport', 'url'=>array('admin')),
);
?>

<h1>Update CustomerBonusReport <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>