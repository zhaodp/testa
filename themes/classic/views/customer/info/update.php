<?php
/* @var $this CustomerMainController */
/* @var $model CustomerMain */

$this->breadcrumbs=array(
	'Customer Mains'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

?>

<h1>客户信息</h1>
<?php echo $this->renderPartial('info/_form', array('model'=>$model)); ?>