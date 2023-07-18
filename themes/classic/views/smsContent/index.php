<?php
/* @var $this SmsContentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Sms Contents',
);

$this->menu=array(
	array('label'=>'Create SmsContent', 'url'=>array('create')),
	array('label'=>'Manage SmsContent', 'url'=>array('admin')),
);
?>

<h1>Sms Contents</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
