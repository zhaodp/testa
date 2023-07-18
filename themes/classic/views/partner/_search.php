<?php
/* @var $this PartnerController */
/* @var $model Partner */
/* @var $form CActiveForm */
?>

<div class="well span12" style="margin-top: 5px;">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row span3">
		<?php echo $form->label($model,'city'); ?>
        <?php $cityList = Dict::items('city');echo $form->dropDownList($model,'city', $cityList); ?>
	</div>

    <div class="row span3">
        <?php echo $form->label($model,'name'); ?>
        <?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>100)); ?>
    </div>

    <div class="row span3">
		<?php echo $form->label($model,'contact'); ?>
		<?php echo $form->textField($model,'contact',array('size'=>50,'maxlength'=>50)); ?>
	</div>

    <div class="row span3">
        <?php echo $form->label($model,'phone'); ?>
        <?php echo $form->textField($model,'phone',array('size'=>15,'maxlength'=>15)); ?>
    </div>

	<div class="row span3">
		<?php echo $form->label($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status',array('' => '全部', '0'=> '正常',1 =>'屏蔽')); ?>
	</div>

	<div class="row span3">
        <?php echo $form->label($model, '&nbsp'); ?>
        <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->