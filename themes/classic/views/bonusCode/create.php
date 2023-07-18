<?php
/* @var $this BonusCodeController */
/* @var $model BonusCode */

$this->breadcrumbs=array(
	'Bonus Codes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List BonusCode', 'url'=>array('index')),
	array('label'=>'Manage BonusCode', 'url'=>array('admin')),
);
?>

<h1>新建优惠码</h1>

<?php echo $this->renderPartial('_form_new', array('model'=>$model,'model_city' => $model_city, 'model_library' => $model_library)); ?>