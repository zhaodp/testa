<?php
/* @var $this CustomerQuestionController */
/* @var $model CustomerQuestion */

$this->breadcrumbs=array(
	'Customer Questions'=>array('index'),
	'Create',
);
?>

<h1>Create CustomerQuestion</h1>

<?php echo $this->renderPartial('_formcustomer', array('model'=>$model)); ?>