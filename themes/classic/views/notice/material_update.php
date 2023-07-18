<?php
/* @var $this DriverTrainDataController */
/* @var $model DriverTrainData */

$this->breadcrumbs=array(
	'Driver Train Datas'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);
?>

<h1>修改资料</h1>

<?php echo $this->renderPartial('_material_form', array('model'=>$model)); ?>