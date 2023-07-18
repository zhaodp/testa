<?php
$this->breadcrumbs=array(
        'Vips'=>array('index'),
        $model->id=>array('view','id'=>$model->id),
        'Update',
);
?>
<h1>修改发票申请信息</h1>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'customer_invoice-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->hiddenField($model, 'id'); ?>   
            <div>
                <?php echo $form->labelEx($model, 'title'); ?>
                <?php echo $form->textField($model, 'title', array('size' => 50, 'maxlength' => 50)); ?>
                <?php echo $form->error($model, 'title'); ?>
            </div>
	     <div>
                <?php echo $form->labelEx($model, 'contact'); ?>
                <?php echo $form->textField($model, 'contact', array('size' => 50, 'maxlength' => 50)); ?>
                <?php echo $form->error($model, 'contact'); ?>
            </div>
	     <div>
                <?php echo $form->labelEx($model, 'telephone'); ?>
                <?php echo $form->textField($model, 'telephone', array('size' => 50, 'maxlength' => 50)); ?>
                <?php echo $form->error($model, 'telephone'); ?>
            </div>
	     <div>
                <?php echo $form->labelEx($model, 'address'); ?>
                <?php echo $form->textField($model, 'address', array('size' => 50, 'maxlength' => 50)); ?>
                <?php echo $form->error($model, 'address'); ?>
            </div>
	     <div>
                <?php echo $form->labelEx($model, 'client_amount'); ?>
                <?php echo $form->textField($model, 'client_amount', array('size' => 50, 'maxlength' => 50)); ?>
                <?php echo $form->error($model, 'client_amount'); ?>
            </div>
	    <div>
                <?php echo $form->labelEx($model, 'type'); ?>
                <?php echo $form->dropDownList($model, 'type', CustomerInvoice::$type);  ?>
                <?php echo $form->error($model, 'type'); ?>
            </div>
	    <div>
                <?php echo $form->labelEx($model, 'export'); ?>
                <?php echo $form->dropDownList($model, 'export', array(0=>'未导出',1=>'已导出'));  ?>
                <?php echo $form->error($model, 'export'); ?>
            </div>
           <div class="span2">
              <?php echo CHtml::submitButton('保存', array('class' => 'btn btn-success btn-block')); ?>
           </div>
    </div>

    <?php $this->endWidget(); ?>
</div>
                     
