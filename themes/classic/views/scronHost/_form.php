<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'crontab-form',
        'enableAjaxValidation'=>false,
        'errorMessageCssClass'=>'alert alert-error'
    )); ?>
    <p class="note">带 <span class="text-error">*</span> 是必填的.</p>
    <?php echo $form->errorSummary($model); ?>

    <div class="row-fluid">
        <div class="span8">
            <?php echo $form->labelEx($model,'host_name'); ?>
            <?php echo $form->textField($model,'host_name',array('size'=>60,'maxlength'=>255)); ?>
            <?php echo $form->error($model,'host_name'); ?>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span8">
            <?php echo $form->labelEx($model,'host'); ?>
            <?php echo $form->textField($model,'host',array('size'=>60,'maxlength'=>255)); ?>
            <?php echo $form->error($model,'host'); ?>
        </div>
    </div>


    <div class="row-fluid">
        <div class="span8">
            <?php echo $form->labelEx($model,'is_enable'); ?>
            <?php echo $form->dropDownList($model,'is_enable',array('0'=>'禁用','1'=>'启用'))?>
            <?php echo $form->error($model,'is_enable'); ?>
        </div>
    </div>
    <div class="margin:0 auto;width:400px">
        <?php echo CHtml::submitButton('提交',array('class'=>'formButton')); ?>
        <input type="button" class="formButton" onclick="window.history.back(); return false;" value="返  回" hidefocus />
    </div>
    <?php $this->endWidget(); ?>

    <?php
    $model->restDbConnection();
    ?>
</div>