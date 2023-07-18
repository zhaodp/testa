<?php
/* @var $this VipController */
/* @var $model Vip */
/* @var $form CActiveForm */


?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'vip-form',
        'enableAjaxValidation' => false,
        'errorMessageCssClass' => 'alert alert-error'
    )); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div class="span4">
            <?php
            if ($model->getIsNewRecord()) {
                ?>
                <div>
                    <?php echo $form->labelEx($model, 'id'); ?>
                    <?php echo $form->textField($model, 'id'); ?>
                    <?php echo $form->error($model, 'id'); ?>
                </div>
            <?php
            } else {
                ?>
                <div>
                    卡号：
                    <?php echo $model->id; ?>
                    <?php echo $form->hiddenField($model, 'id'); ?>
                </div>
            <?php
            }
            ?>
            <div>
                开卡时间：
                <?php
                if ($model->isNewRecord) {
                    echo date("Y-m-d", time());
                } else {
                    echo date("Y-m-d", $model->created);
                }

                ?>
                <?php //echo $form->error($model,'ctime'); ?>
            </div>

            <div>
                <?php echo $form->labelEx($model, 'name'); ?>
                <?php echo $form->textField($model, 'name', array('size' => 50, 'maxlength' => 50)); ?>
                <?php echo $form->error($model, 'name'); ?>
            </div>

            <div>
                <?php echo $form->labelEx($model, 'company'); ?>
                <?php echo $form->textField($model, 'company', array('size' => 50, 'maxlength' => 50)); ?>
                <?php echo $form->error($model, 'company'); ?>
            </div>


            <div>
                <?php echo $form->labelEx($model, 'phone'); ?>
                <?php echo $form->textField($model, 'phone', array('size' => 15, 'maxlength' => 15)); ?>
                <?php echo $form->error($model, 'phone'); ?>
            </div>

            <div>
                <?php echo $form->labelEx($model, 'send_phone'); ?>
                <?php echo $form->textField($model, 'send_phone', array('size' => 15, 'maxlength' => 15)); ?>
                <?php echo $form->error($model, 'send_phone'); ?>
            </div>
	    <div>
                <?php echo $form->labelEx($model, 'email'); ?>
                <?php echo $form->textField($model, 'email', array('size' => 50, 'maxlength' => 50)); ?>
                <?php echo $form->error($model, 'email'); ?>
            </div>
	    <div>
		<?php echo $form->checkBox($model,'invoiced'); ?> <?php echo '发票申请' ?>
	    </div>

        </div>
        <div class="span4">
            <div>
                <?php echo $form->labelEx($model, 'type'); ?>
                <?php
                $types = Dict::items('vip_type');
                ksort($types);
                echo $form->dropDownList($model,
                    'type',
                    $types,
                    array()
                );
                ?>
                <?php echo $form->error($model, 'type'); ?>

            </div>
            <div>
                <?php echo $form->labelEx($model, 'city_id'); ?>
                <?php
                $citys = Dict::items('city');
                $citys[0] = '--请选择城市--';
                ksort($citys);
                echo $form->dropDownList($model,
                    'city_id',
                    $citys,
                    array()
                );
                ?>
                <?php echo $form->error($model, 'city_id'); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model, 'status'); ?>
                <?php
                $status = Dict::items('vip_status');
                $status["0"] = '--请选择状态--';
                ksort($status);
                echo $form->dropDownList($model,
                    'status',
                    $status,
                    array()
                );
                ?>
                <?php echo $form->error($model, 'status'); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model, '发送账单类型'); ?>
                <?php echo $form->dropDownList($model, 'send_type', array('0' => 'wap页面', '1' => '短信')) ?>
            </div>
	    <?php
                if ($model->isNewRecord) {
                    echo $form->labelEx($model, '金额');
                }else{
                    echo $form->labelEx($model, 'totelamount');
                }
            ?>
		<div> 
                <?php echo $model->isNewRecord ? $form->textField($model, 'totelamount') : $form->textField($model, 'totelamount', array('readonly' => 'reado
nly')); ?>  
                <?php echo $form->error($model, 'totelamount'); ?>
            </div>
	    <div> 
                <?php echo $form->labelEx($model, 'credit'); ?>
                <?php echo $form->textField($model, 'credit', array('size' => 50, 'maxlength' => 50)); ?>
                <?php echo $form->error($model, 'credit'); ?>
            </div>
        </div>
        <div class="span4">
            <?php if (!$model->isNewRecord) { ?>

                <div>
                    <?php echo $form->labelEx($model, 'balance'); ?>
                    <?php echo $form->textField($model, 'balance', array('readonly' => 'readonly')); ?>
                    <?php echo $form->error($model, 'balance'); ?>
                </div>

            <?php } ?>

            <div>
                <?php echo $form->labelEx($model, 'commercial_invoice'); ?>
                <?php echo $form->textArea($model, 'commercial_invoice'); ?>
                <?php echo $form->error($model, 'commercial_invoice'); ?>
            </div>

	     <div>
                <?php echo $form->labelEx($model, 'contact'); ?>
                <?php echo $form->textArea($model, 'contact') ?>
                <?php echo $form->error($model, 'contact'); ?>
            </div>
		
 	    <div>
                <?php echo $form->labelEx($model, 'telephone'); ?>
                <?php echo $form->textField($model, 'telephone') ?>
                <?php echo $form->error($model, 'telephone'); ?>
            </div>

  	    <div>
                <?php echo $form->labelEx($model, 'address'); ?>
                <?php echo $form->textArea($model, 'address') ?>
                <?php echo $form->error($model, 'address'); ?>
            </div>
	    <div>
                <?php echo $form->labelEx($model, 'invoice_type'); ?>
                <?php echo $form->dropDownList($model, 'invoice_type',CustomerInvoice::$type, array()); ?>
                <?php echo $form->error($model, 'invoice_type'); ?>
            </div>
     	   <div>
                <?php echo $form->labelEx($model, 'remarks'); ?>
                <?php echo $form->textArea($model, 'remarks') ?>
                <?php echo $form->error($model, 'remarks'); ?>
           </div>
        </div>
    </div>


    <div class="row-fluid">
        <div class="span2">
            <?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '保存', array('class' => 'btn btn-success btn-block')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
 $('#vip-form').submit(function() {
	if($('#Vip_invoiced').attr("checked") == 'checked'){
		if($('#Vip_commercial_invoice').val()==''){
			alert('发票抬头不能为空');return false;
		}
		if($('#Vip_contact').val()==''){
                        alert('收件人不能为空');return false;
                }
		if($('#Vip_telephone').val()==''){
                        alert('收件人电话不能为空');return false;
                }
		if($('#Vip_address').val()==''){
                        alert('收件人地址不能为空');return false;
                }
	}
	return true;
  }
);

</script> 

