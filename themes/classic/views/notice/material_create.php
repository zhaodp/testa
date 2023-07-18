<?php
/* @var $this DriverTrainDataController */
/* @var $model DriverTrainData */

$this->breadcrumbs=array(
	'Driver Train Datas'=>array('index'),
	'Create',
);

?>

<h1>司机学习资料</h1>

<?php echo $this->renderPartial('_material_form', array('model'=>$model)); ?>