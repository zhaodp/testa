<?php
/* @var $this ActivityController */
/* @var $model BActivity */
/* @var $form CActiveForm */
?>

<div class="row-fluid">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'bactivity-form',
        'enableAjaxValidation' => false,
        'focus' => array($model, 'title'),
        'htmlOptions' => array('class' => 'row-fluid')
    ));
    ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo $form->errorSummary($model); ?>

<div class="row-fluid">
    <div class="row span3">
        <?php echo $form->labelEx($model, 'title'); ?>
        <?php echo $form->textField($model, 'title', array('size' => 60, 'maxlength' => 60)); ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model, 'begin_time'); ?>
        <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array(
            'attribute' => 'begin_time',
            'model' => $model, //Model object
            'htmlOptions' => array(
                'value' => $model->begin_time ? date('Y-m-d H:i', $model->begin_time) : '',
            ),
            'mode' => 'datetime', //use "time","date" or "datetime" (default)
            'options' => array(
                'dateFormat' => 'yy-mm-dd'
            ), // jquery plugin options
            'language' => 'zh'
        ));
        ?>
    </div>

    <div class="row span3">
        <?php echo $form->labelEx($model, 'end_time'); ?>
        <?php
        $this->widget('CJuiDateTimePicker', array(
            'attribute' => 'end_time',
            'model' => $model, //Model object
            'htmlOptions' => array(
                'value' => date('Y-m-d H:i', $model->end_time ? $model->end_time : time() + 86400 * 30),
            ),
            'mode' => 'datetime', //use "time","date" or "datetime" (default)
            'options' => array(
                'dateFormat' => 'yy-mm-dd'
            ), // jquery plugin options
            'language' => 'zh'
        ));
        ?>
    </div>
</div>

<?php // if ($model->isNewRecord) { ?>
<div class="row-fluid">
    <div class="row span3">
        <?php echo $form->labelEx($model, 'bonusSn', array('label' => '优惠券号')); ?>
        <?php echo $form->textField($model, 'bonusSn', array('size' => 64, 'maxlength' => 64)); ?>
    </div>
    
    <div class="row span4">
        <?php echo $form->labelEx($model, 'bonusWorkTime', array('label' => '自领取之日起有效时间（天）,0表示用优惠券配置')); ?>
        <?php echo $form->textField($model, 'bonusWorkTime', array('size' => 64, 'maxlength' => 64)); ?>
    </div>
</div>
<?php // } ?>
    
<div class="row-fluid">
    <div class="row span3">
        <?php echo $form->labelEx($model, 'status'); ?>
        <?php echo $form->dropDownList($model, 'status', array('0' => '未激活', '1' => '已激活')); ?>
    </div>
</div>

<div class="row-fluid">
    <div class="row span8">
        <?php echo $form->labelEx($model, 'remark'); ?>
        <?php echo $form->textArea($model, 'remark', array('size' => 260, 'maxlength' => 1000, 'class' => 'row8')); ?>
    </div>
</div>
<div class="row-fluid">
    <div class="row span12 buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '修改', array('class' => 'btn btn-success')); ?>
    </div>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->