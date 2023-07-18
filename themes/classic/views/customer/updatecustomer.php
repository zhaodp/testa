<?php
/* @var $this CustomerQuestionController */
/* @var $model CustomerQuestion */

$this->breadcrumbs=array(
	'Customer Questions'=>array('index'),
	'Update',
);
?>

<h1>修改问卷问题</h1>

<?php echo $this->renderPartial('_formcustomer', array('model'=>$model)); ?>