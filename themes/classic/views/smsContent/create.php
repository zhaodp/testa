<?php
/* @var $this SmsContentController */
/* @var $model SmsContent */

$this->breadcrumbs=array(
	'Sms Contents'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List SmsContent', 'url'=>array('index')),
	array('label'=>'Manage SmsContent', 'url'=>array('admin')),
);
?>


<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>