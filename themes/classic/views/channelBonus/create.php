<?php
$this->pageTitle = '新建渠道优惠券';
?>

<h1><?php echo $this->pageTitle; ?></h1>

<div class=" well">
<?php
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'driver-form', 
	'enableAjaxValidation'=>false,
));
?>
<?php 
echo CHtml::textField('Employee[user]','',array('class'=>'span3','placeholder'=>'司机工号'));
echo $form->error($model,'sn_start');
echo CHtml::submitButton('查询',array('class'=>'span2'));
$this->endWidget();
?>
</div>

<?php 
if ($employee){
	echo $this->renderPartial('_form', array('model'=>$model, 'employee'=>$employee)); 
}
?>