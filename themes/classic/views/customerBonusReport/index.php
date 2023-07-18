<?php
/* @var $this CustomerBonusReportController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Customer Bonus Reports',
);

$this->menu=array(
	array('label'=>'Create CustomerBonusReport', 'url'=>array('create')),
	array('label'=>'Manage CustomerBonusReport', 'url'=>array('admin')),
);
?>

<h1>Customer Bonus Reports</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
