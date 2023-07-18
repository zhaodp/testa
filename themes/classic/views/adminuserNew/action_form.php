<?php
/* @var $this AdminActionController */
/* @var $model AdminActions */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'admin-action-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->labelEx($model,'app_id'); ?>
    <?php echo $form->dropDownList($model,'app_id',AdminApp::model()->getAllForUpdate()); ?>

    <?php echo $form->labelEx($model,'name'); ?>
    <?php echo $form->textField($model,'name'); ?>

    <?php echo $form->labelEx($model,'controller'); ?>
    <?php echo $form->textField($model,'controller'); ?>

    <?php echo $form->labelEx($model,'action'); ?>
    <?php echo $form->textField($model,'action'); ?>

    <?php echo $form->labelEx($model,'action_url'); ?>
    <?php echo $form->textField($model,'action_url'); ?>

    <?php echo $form->labelEx($model,'desc'); ?>
    <?php echo $form->textarea($model,'desc'); ?>

    <?php echo $form->labelEx($model,'can_allocate'); ?>
    <?php echo $form->dropDownList($model,'can_allocate',AdminActions::getCanAllocate()); ?>
    <?php echo $form->error($model,'can_allocate'); ?>

    <?php echo $form->labelEx($model,'access_auth'); ?>
    <?php echo $form->dropDownList($model,'access_auth',AdminActions::getActionAccessAuth()); ?>
    <?php echo $form->error($model,'access_auth'); ?>

    <?php echo $form->labelEx($model,'driver_access_auth'); ?>
    <?php echo $form->dropDownList($model,'driver_access_auth',AdminActions::getDriverAccessAuth()); ?>
    <?php echo $form->error($model,'driver_access_auth'); ?>

    <?php echo $form->labelEx($model,'status'); ?>
    <?php echo $form->dropDownList($model,'status',AdminActions::getActionStatus()); ?>
    <?php echo $form->error($model,'status'); ?>

    <h4>分配给各个部门：</h4>
    <legend><div style="margin-bottom:0px;width:120px;padding:4px 0px 0px 8px" class="alert alert-success">部门</div></legend>
    <div style="border-radius:4px;border:1px solid #eeeeee;padding-bottom:6px;margin:-9px 0px 5px 0px">
    <?php

    foreach($department as $k => $item) { //alreday_had_dep
        $checked = isset($alreday_had_dep) && in_array($k,$alreday_had_dep);
        echo '<label class="checkbox inline">';
        echo CHtml::checkBox('AdminActions[dep_id][]', $checked , array (
            'id'=>'AdminUser_action_' . $k,
            'value'=>$k,
            'name'=>$item,
            'separator'=>''));
        echo $item.'</label>';
    }
    ?>
    </div>
    <div class="buttons">
        <?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>
