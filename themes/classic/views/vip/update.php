<?php
/* @var $this VipController */
/* @var $model Vip */

$this->breadcrumbs=array(
	'Vips'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

?>

<h1>修改  <?php echo $model->id; ?> 主卡信息</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>