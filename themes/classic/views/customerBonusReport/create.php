<?php
/* @var $this CustomerBonusReportController */
/* @var $model CustomerBonusReport */

$this->breadcrumbs=array(
	'Customer Bonus Reports'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List CustomerBonusReport', 'url'=>array('index')),
	array('label'=>'Manage CustomerBonusReport', 'url'=>array('admin')),
);
?>

<h1>Create CustomerBonusReport</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>