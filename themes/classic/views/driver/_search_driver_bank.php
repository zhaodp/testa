<div class="well span12" style="border:0px">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
        'htmlOptions' => array('class' => 'form-inline'),
    )); ?>
    <div class="controls controls-row">
        <div class="span3">
            <?php echo $form->label($model, 'driver_id'); ?>
            <?php echo $form->textField($model, 'driver_id', array('size' => 10, 'maxlength' => 255, 'class' => "span12")); ?>
        </div>
        <div class="span3">
            <?php echo $form->label($model, 'pay_name'); ?>
            <?php echo $form->textField($model, 'pay_name', array('size' => 10, 'maxlength' => 255, 'class' => "span12")); ?>
        </div>

        <div class="span3">
            <label for="city">&nbsp; </label>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn span8')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->