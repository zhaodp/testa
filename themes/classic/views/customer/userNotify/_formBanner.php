<?php
/* @var $this UserNotifyBannerController */
/* @var $model UserNotifyBanner */
/* @var $form CActiveForm */
?>

<div class="form span11">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-notify-banner-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
    'enableClientValidation' => true,
    'action'=>array('customer/userNotify'),
    'clientOptions' => array(
        'validateOnSubmit' => true  //在这个位置做验证
    ),
)); ?>

    <div class="row">
	<?php echo $form->errorSummary($model); ?>
   </div>
    <input type="hidden" name='action' value='saveBanner'>
    <?php echo $form->hiddenField($model, 't_user_notify_id', array('value' => $model->t_user_notify_id)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'word'); ?>
		<?php echo $form->textField($model,'word',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'word'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'word_order_status'); ?>
        <br>
        <?php
        $order_status_list = Dict::items('order_status');
        foreach ($order_status_list as $key=>$item){
            $checked = false;
            echo CHtml::checkBox("word_order_status[]",$checked,array("value"=>$key,'class'=>'city_id')).$item.'&nbsp;&nbsp;';
        }

        ?>
        <?php echo $form->error($model,'word_order_status'); ?>
        <br>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'banner_picture_url'); ?>
		<?php echo $form->textField($model,'banner_picture_url',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'banner_picture_url'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'banner_jump_url'); ?>
		<?php echo $form->textField($model,'banner_jump_url',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'banner_jump_url'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'banner_order_status'); ?>

        <br>
        <?php
        //$order_status_list = Dict::items('order_status');
        foreach ($order_status_list as $key=>$item){
            $checked = false;
            echo CHtml::checkBox("banner_order_status[]",$checked,array("value"=>$key,'class'=>'city_id')).$item.'&nbsp;&nbsp;';
        }

        ?>
        <?php echo $form->error($model,'banner_order_status'); ?>
        <br>
        <?php echo $form->error($model,'total'); ?>
        <br>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : 'SaveBanner',array('name' => 'save','class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->