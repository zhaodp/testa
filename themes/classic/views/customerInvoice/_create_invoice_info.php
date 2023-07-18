<?php
?>
<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array('action' => Yii::app()->createUrl($this->route),'method' => 'get',)); ?>
        <div class="span12">
		
                <div class="span3">
                    <label>客户电话</label>
                    <?php echo CHtml::textField('customer_phone',isset($model['customer_phone'])?$model['customer_phone']:''); ?>
                </div>
     		<div class="span3">
                  	<label>&nbsp;</label>
                    	<?php echo CHtml::link("查询","javascript:;",array("onclick"=>"return getTransList()","class"=>"btn btn-success")); ?>
               	</div>
	</div>
	<div class="span12">
		<div class="span3">
		    <label>抬头</label>
		    <?php echo CHtml::textArea('title',isset($model['title'])?$model['title']:''); ?>
		</div>

		<div class="span3">
		    <label>收件人</label> 
		    <?php echo CHtml::textField('contact',isset($model['contact'])?$model['contact']:''); ?>
		</div>
		      
		<div class="span3">
		    <label>收件人电话</label>
		    <?php echo CHtml::textField('telephone',isset($model['telephone'])?$model['telephone']:''); ?>
		</div>
		<div class="span3">
                    <label>类型</label>
                    <?php echo CHtml::dropDownList('type',CustomerInvoice::TYPE_DAIJIA,CustomerInvoice::$type); ?>
                </div>

	</div>
	 <div class="span12">
		<div class="span3">
                    <label>地址</label>
                    <?php echo CHtml::textArea('address',isset($model['address'])?$model['address']:''); ?>
                </div>
		<div class="span3">
                    <label>发票备注</label>
                    <?php echo  CHtml::textArea('remark',isset($model['remark'])?$model['remark']:''); ?>
                </div>
		<div class="span3">
                    <label>当前拥有E币数：<?php if(isset($wealth)){ echo $wealth;}else{ echo 0;} ?></label>
                    <?php echo CHtml::dropDownList('pay_type',CustomerInvoice::PAY_TYPE_NOCHARGE,CustomerInvoice::$pay_type); ?>
                </div>
		<div class="span3">
		     <label>客户申请发票总金额</label>
                    <?php echo CHtml::textField('client_amount',isset($model['client_amount'])?$model['client_amount']:''); ?>
		</div>
	 </div>
	<div class="span12">
		<div class="span3">
                    <label>&nbsp;</label>
                    <?php echo CHtml::link("提交发票信息","javascript:;",array("onclick"=>"return saveInvoice()","class"=>"btn btn-success")); ?>
                </div>
	</div>
	<?php $this->endWidget(); ?>
</div><!-- search-form -->
