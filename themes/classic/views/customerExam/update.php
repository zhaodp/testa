<?php
/* @var $this CustomerExamController */
/* @var $model CustomerExam */

$this->breadcrumbs=array(
	'Customer Exams'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

?>

<h1>更新答卷信息</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'question'=>$qustion,'paperlist'=>$paperlist)); ?>