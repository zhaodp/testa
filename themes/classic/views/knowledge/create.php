<?php
/* @var $this KnowledgeController */
/* @var $model Knowledge */

$this->breadcrumbs=array(
	'Knowledges'=>array('index'),
	'Create',
);
?>
<h3>添加知识库</h3>
<?php echo $this->renderPartial('_form', array('model'=>$model, 'model_data' => $model_data, 'model_case' => $model_case)); ?>