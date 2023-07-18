<?php
$this->breadcrumbs=array(
        'CustomerInvoice'=>array('index'),
        'Create',
);
?>
<h1>创建发票申请信息</h1>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'customer_invoice-create-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div class="span4">
	    <div> 
                <?php echo $form->labelEx($model, 'customer_phone'); ?>
                <?php echo $form->textField($model, 'customer_phone', array('size' => 15, 'maxlength' => 15)); ?>
                <?php echo $form->error($model, 'customer_phone'); ?>
            </div>
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
                <?php echo $form->labelEx($model, 'type'); ?>
                <?php echo $form->dropDownList($model, 'type',CustomerInvoice::$type, array()); ?>
                <?php echo $form->error($model, 'type'); ?>
            </div>
           <div class="span2">
		 <?php echo CHtml::submitButton('保存', array('class' => 'btn btn-success btn-block')); ?>
           </div>
    </div>

    <?php $this->endWidget(); ?>
</div>
<script type="text/javascript">
   $('#customer_invoice-create-form').submit(function() {
      	if ($('#CustomerInvoice_customer_phone').val()==''){
		alert('客户电话不能为空');
		return false;
	}
	if ($('#CustomerInvoice_title').val()==''){
                alert('抬头不能为空');
                return false;
        }
	if ($('#CustomerInvoice_contact').val()==''){
                alert('收件人不能为空');
                return false;
        }
	if ($('#CustomerInvoice_telephone').val()==''){
                alert('收件人电话不能为空');
                return false;
        }
	if ($('#CustomerInvoice_address').val()==''){
                alert('收件人地址不能为空');
                return false;
        }
	return true;
    });

</script>

                     
