<?php
/* @var $this SmsContentController */
/* @var $model SmsContent */

$this->breadcrumbs=array(
	'Sms Contents'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List SmsContent', 'url'=>array('index')),
	array('label'=>'Create SmsContent', 'url'=>array('create')),
	array('label'=>'View SmsContent', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage SmsContent', 'url'=>array('admin')),
);
?>

<h1>Update SmsContent <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>