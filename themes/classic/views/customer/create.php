<?php
/* @var $this QuestionnaireController */
/* @var $model Questionnaire */

$this->breadcrumbs=array(
	'Questionnaires'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Questionnaire', 'url'=>array('index')),
	array('label'=>'Manage Questionnaire', 'url'=>array('admin')),
);
?>

<h1>Create Questionnaire</h1>
<p>
	
</p>
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>