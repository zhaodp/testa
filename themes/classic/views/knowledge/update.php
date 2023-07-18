<?php
/* @var $this KnowledgeController */
/* @var $model Knowledge */

$this->breadcrumbs=array(
	'Knowledges'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);
?>

<h1>修改知识</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'model_data' => $model_data, 'model_case' => $model_case, 'case' => $case)); ?>