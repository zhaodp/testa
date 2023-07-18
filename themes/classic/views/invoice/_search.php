<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<div>
	<?php echo $form->label($model,'order_id',array("class"=>"span1")); ?>		
	<input maxlength="40" name="OrderInvoice[order_id]" id="OrderInvoice_order_id" type="text" class="span2"/>
</div>
<div>	
	<label class="span1">司机工号</label>
	<input maxlength="40" name="OrderInvoice[contact]" id="OrderInvoice_contact" type="text"  class="span2"/>
</div>
<div>	
	<?php echo $form->label($model,'title',array("class"=>"span1")); ?>
	<input maxlength="100" name="OrderInvoice[title]" id="OrderInvoice_title" type="text"  class="span2"/>
</div>
<div>	
	<?php echo $form->label($model,'status',array("class"=>"span1")); ?>		
	<?php echo $form->dropDownList($model,'status',array('9'=>'全部','0'=>'未开发票','1'=>'已开发票'),array("class"=>"span2")); ?>
</div>
<div>	
	<?php echo CHtml::submitButton('Search'); ?>
</div>
<?php $this->endWidget(); ?>

</div><!-- search-form -->