<?php
/* @var $this DriverRecommandController */
/* @var $model DriverRecommand */

$this->breadcrumbs=array(
	'Driver Recommands'=>array('index'),
	'Create',
);

?>

<h2>司机添加奖励</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>