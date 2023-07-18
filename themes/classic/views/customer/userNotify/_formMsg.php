<?php
/* @var $this UserNotifyMsgController */
/* @var $model UserNotifyMsg */
/* @var $form CActiveForm */
?>

<div class="form span11">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-notify-msg-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
    'action'=>array('customer/userNotify'),
    'enableClientValidation'=>true,
    'clientOptions' => array(
        'validateOnSubmit' => true  //在这个位置做验证
    ),
)); ?>



	<?php echo $form->errorSummary($model); ?>
    <?php echo $form->hiddenField($model, 't_user_notify_id', array('value' => $model->t_user_notify_id)); ?>
    <input type="hidden" name='action' value='saveMsg'>
	<div class="row">
		<?php echo $form->labelEx($model,'word'); ?>
		<?php echo $form->textField($model,'word',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'word'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>
    <div class="row">
        <br><br>
        <?php echo $form->labelEx($model,'trigger_condition'); ?>

        <br>
        <?php
        $conditions = Dict::items('trigger_condition');
        foreach ($conditions as $key=>$item){
            $checked = false;
            echo CHtml::checkBox("condition[]",$checked,array("value"=>$key,'class'=>'city_id')).$item.'&nbsp;&nbsp;';
        }

        ?>
        <?php echo $form->error($model,'trigger_condition'); ?>
        <br><br>
    </div>
    <div class="row">
        <?php echo $form->labelEx($model,'client_page'); ?>
        <?php echo $form->dropDownList($model, 'client_page', Dict::items('client_page')); ?>
        <?php echo $form->error($model,'client_page'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
        <?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50)); ?>

		<?php echo $form->error($model,'content'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'button_text'); ?>
		<?php echo $form->textField($model,'button_text',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'button_text'); ?>
        <?php echo $form->labelEx($model,'button_url'); ?>
        <?php echo $form->textField($model,'button_url',array('size'=>60,'maxlength'=>255)); ?>
        <?php echo $form->error($model,'button_url'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : 'SaveMsg'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->