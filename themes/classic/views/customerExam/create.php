<?php
/* @var $this CustomerExamController */
/* @var $model CustomerExam */

$this->breadcrumbs=array(
	'Customer Exams'=>array('index'),
	'Create',
);
?>

<h1>创建新的答卷</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'question'=>$qustion,'paperlist'=>$paperlist)); ?>