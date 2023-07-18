<?php
/* @var $this BonusCodeController */
/* @var $model BonusCode */

$this->breadcrumbs=array(
	'Bonus Codes'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List BonusCode', 'url'=>array('index')),
	array('label'=>'Create BonusCode', 'url'=>array('create')),
	array('label'=>'View BonusCode', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage BonusCode', 'url'=>array('admin')),
);
?>

<h1>修改优惠券 <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form_new', array('model'=>$model)); ?>