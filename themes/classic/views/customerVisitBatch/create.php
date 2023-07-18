<?php
/* @var $this CustomerVisitBatchController */
/* @var $model CustomerVisitBatch */

$this->breadcrumbs=array(
	'Customer Visit Batches'=>array('index'),
	'Create',
);
?>

<h1>新建批次</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>