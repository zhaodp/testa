<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'bonus-code-form',
        'enableAjaxValidation' => false,
    ));
    ?>
    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->radioButtonList($model, 'payStatus', array('1' => '付款'), array('separator' => '&nbsp;&nbsp;&nbsp;','labelOptions'=>array('class'=>'radio inline','style'=>'padding-left:2px;'))); ?>
        <?php echo $form->error($model, 'payStatus'); ?>
    </div>
    <br>

    <div class="row buttons">
        <?php echo CHtml::submitButton('保存',array('class'=>'btn')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->