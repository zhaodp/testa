<div class="well row-fluid">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>
    <div class="span12">
        <div class="span3">
            <?php echo $form->label($model, 'channel'); ?>
            <?php echo $form->textField($model, 'channel', array('size' => 60, 'maxlength' => 60)); ?>
        </div>

        <div class="span3">
            <?php echo $form->label($model, 'name'); ?>
            <?php echo $form->textField($model, 'name'); ?>
        </div>

    </div>

    <div class="span12">

        <div class="row span3">
            <?php echo $form->label($model, '&nbsp'); ?>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- search-form -->