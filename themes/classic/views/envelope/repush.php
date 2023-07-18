<div class="form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'form-submit',
        'enableAjaxValidation' => false,
    )); ?>

    <input type="hidden" id="envelope_id" name="envelope_id" value="<?php echo $model->id; ?>"/>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <?php echo '确认重新发放该红包？'; ?>
    </div>
    <br/>
    <div class="row-fluid">

        <div style="width:27%; float: left;">
            <?php echo CHtml::submitButton('确认', array('class' => 'btn', 'style' => 'width:120px')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->
