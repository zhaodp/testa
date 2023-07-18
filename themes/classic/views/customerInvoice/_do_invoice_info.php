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
		    <label>抬头</label>
		    <?php echo  CHtml::textField('title',isset($model['title'])?$model['title']:''); ?>
		</div>

		<div class="span3">
		    <label>收件人</label> 
		    <?php echo CHtml::textField('contact',isset($model['contact'])?$model['contact']:''); ?>
		</div>
		      
		<div class="span3">
		    <label>收件人电话</label>
		    <?php echo  CHtml::textField('telephone',isset($model['telephone'])?$model['telephone']:''); ?>
		</div>

	</div>
        <div class="span12">

		<div class="span3">
                    <label>地址</label>
                    <?php echo CHtml::textField('address', isset($model['address'])?$model['address']:''); ?>
                </div>
		<div class="span3">
                    <label>类型</label>
		    <?php echo CHtml::textField('type', $model['type'] == 1? '代驾费' :($model['type'] == 2? '服务费': '待定')); ?>
                </div>
		<div class="span3">
                    <label>邮费</label>
		     <?php echo CHtml::textField('pay_type', $model['pay_type'] == 1? '已支付' :($model['type'] == 2? '未支付': '待定')); ?>
                </div>
		<div class="span3">
		    <label>&nbsp;</label>
		    <?php echo CHtml::link("开票","javascript:;",array("onclick"=>"return invoice()","class"=>"btn btn-success")); ?>
		</div>
	 </div>
	 <div class="span12">
		<div class="span3">    
		    <label>&nbsp;</label>
		    <?php echo CHtml::link('返回', Yii::app()->createUrl('/customerInvoice/admin'), array('class' => 'btn')); ?>
		</div>
	 </div>
	<?php $this->endWidget(); ?>
</div><!-- search-form -->
