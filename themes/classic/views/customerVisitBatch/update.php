<?php
/* @var $this CustomerVisitBatchController */
/* @var $model CustomerVisitBatch */

$this->breadcrumbs=array(
	'Customer Visit Batches'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);
?>

<h1>更新批次信息</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>