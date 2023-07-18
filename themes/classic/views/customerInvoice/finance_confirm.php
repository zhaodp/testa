<?php
$this->breadcrumbs=array(
        'Vips'=>array('index'),
        $model->id=>array('view','id'=>$model->id),
        'Update',
);
?>

<h1>请填写发票号和快递单号</h1>


<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'finance-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div class="span4">
            <?php echo $form->hiddenField($model, 'id'); ?>   
            <div>
                <?php echo $form->labelEx($model, 'invoice_number'); ?>
		<?php echo $form->textField($model,'invoice_number'); ?>
		<?php echo $form->labelEx($model, 'delivery_number'); ?>
		<?php echo $form->textField($model,'delivery_number'); ?>
		<?php echo $form->labelEx($model, 'deliveryer'); ?>
		<?php echo $form->dropDownList($model,'deliveryer',CustomerInvoice::$delivery);?>
            </div>
           <div class="span2">
              <?php echo CHtml::submitButton('保存', array('class' => 'btn btn-success btn-block')); ?>
           </div>
    </div>

    <?php $this->endWidget(); ?>
</div>
<script>    

jQuery(document).ready(function() {
        $("#finance-form").submit(function(){
		var invoice_number = $("#CustomerInvoice_invoice_number").val();
		if(invoice_number == ''){
		    alert('请输入发票号');return false;
		}
		var delivery_number = $("#CustomerInvoice_delivery_number").val();
		if(delivery_number == ''){
			alert('请输入快递单号');return false;
		}
		return true;

	});








})


</script>
