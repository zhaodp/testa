<?php
/* @var $this AdminActionController */
/* @var $model AdminActions */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'material-config-form',
        'enableAjaxValidation'=>false,
    ));
    echo $form->errorSummary($model);

    echo $form->labelEx($model,'name');
    echo $form->textField($model,'name');
    echo $form->error($model,'name');

    echo $form->labelEx($model,'type_id');
    echo $form->dropDownList($model,'type_id',Material::getTypeInfoName());
    echo $form->error($model,'type_id');

    echo $form->labelEx($model,'price');
    echo $form->textField($model,'price');
    echo $form->error($model,'price');

    echo $form->labelEx($model,'depreciation');
    echo $form->textField($model,'depreciation');
    echo $form->error($model,'depreciation');

    echo $form->labelEx($model,'loss_cost');
    echo $form->textField($model,'loss_cost');
    echo $form->error($model,'loss_cost');


    echo $form->labelEx($model,'third_id');
    echo $form->textField($model,'third_id');
    echo $form->error($model,'third_id');

    echo $form->labelEx($model,'status');
    echo $form->dropDownList($model,'status',Material::getStatus());
    echo $form->error($model,'status'); ?>


    <div class="buttons">
        <?php echo CHtml::submitButton('保存',array('class'=>'btn btn-large')); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>