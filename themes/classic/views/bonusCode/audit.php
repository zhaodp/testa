<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'bonus-code-form',
        'enableAjaxValidation' => false,
    ));
    ?>
    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->radioButtonList($model, 'status', array('1' => '审核通过', '2' => '审核不通过'), array('separator' => '&nbsp;&nbsp;&nbsp;','labelOptions'=>array('class'=>'radio inline','style'=>'padding-left:2px;'))); ?>
        <?php echo $form->error($model, 'status'); ?>
    </div>
    <br>
    <div class="row">
        <?php echo CHtml::label('审核意见：', 'BonusCode_remark'); ?>
        <br>
        <?php echo $form->textArea($model, 'remark', array('rows' => 5, 'style' => 'width:80%')); ?>
        <?php echo $form->error($model, 'remark'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('保存',array('class'=>'btn')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->