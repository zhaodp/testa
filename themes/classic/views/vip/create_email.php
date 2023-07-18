<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-10-31
 * Time: 下午4:47
 */
$this->pageTitle = '新建Vip卡';
?>

<h1>补发邮件</h1>
<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'vip-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>

    <p class="note">带 <span class="text-error">*</span> 是必填的.</p>
    <?php echo $form->errorSummary($model); ?>

    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->labelEx($model, 'email'); ?>
            <?php echo $form->textField($model, 'email', array('size' => 50, 'maxlength' => 50)); ?>
            <?php echo $form->error($model, 'email'); ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->labelEx($model, 'vipcard'); ?>
            <?php echo $form->textField($model, 'vipcard', array('size' => 50, 'maxlength' => 50)); ?>
            <?php echo $form->error($model, 'vipcard'); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->labelEx($model, 'type'); ?>
            <?php echo $form->dropDownList($model, 'type', array('' => '全部', '0' => '日账单', '1' => '月账单')); ?>
            <?php echo $form->error($model, 'type'); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->labelEx($model, 'vip_bill_time'); ?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'VipEmailLog[vip_bill_time]',
                'model' => $model, //Model object
                'value' => $model->vip_bill_time,
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
            ));
            ?>
            <?php echo $form->error($model, 'vip_bill_time'); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->labelEx($model, 'send_time'); ?>
            <?php
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'VipEmailLog[send_time]',
                'model' => $model, //Model object
                'value' => $model->send_time,
                'mode' => 'datetime', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
            ));
            ?>
            <?php echo $form->error($model, 'send_time'); ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->labelEx($model, 'remarks'); ?>
            <?php echo $form->textField($model, 'remarks', array('size' => 50, 'maxlength' => 50)); ?>
            <?php echo $form->error($model, 'remarks'); ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span4">
            <?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存', array('class' => 'span7 btn btn-success')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
