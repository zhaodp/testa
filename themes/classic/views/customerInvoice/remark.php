<?php
$this->breadcrumbs=array(
        'Vips'=>array('index'),
        $model->id=>array('view','id'=>$model->id),
        'Update',
);
?>

<h1>备注信息</h1>


<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'remark-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->hiddenField($model, 'id'); ?>   
            <div>
                <?php echo $form->labelEx($model, 'remark'); ?>
                <?php echo $form->textArea($model, 'remark', array('rows'=> 5, 'cols'=> 30)); ?>
                <?php echo $form->error($model, 'remark'); ?>
            </div>
           <div class="span2">
              <?php echo CHtml::submitButton('保存', array('class' => 'btn btn-success btn-block')); ?>
           </div>
    </div>

    <?php $this->endWidget(); ?>
</div>
                     
