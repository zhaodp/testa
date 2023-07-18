<?php
/* @var $this DriverRecommandController */
/* @var $model DriverRecommand */

$this->breadcrumbs=array(
	'Driver Recommands'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);
?>

<h2>皇冠司机修改</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>