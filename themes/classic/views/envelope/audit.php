<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'form-submit',
        'enableAjaxValidation' => false,
    )); ?>

    <input type="hidden" id="envelope_id" name="envelope_id" value="<?php echo $model->id; ?>"/>
    <input type="hidden" id="envelope_status" name="envelope_status" value="<?php echo $model->status; ?>"/>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <?php echo '确认' . ($model->status == 1 ? '禁用' : '启用') . '该红包？'; ?>
    </div>
    <br/>
    <div class="row-fluid">

        <div style="width:27%; float: left;">
            <?php echo CHtml::submitButton('确认', array('class' => 'btn', 'style' => 'width:120px')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
