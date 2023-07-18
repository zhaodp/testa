<h2>手动结账</h2>

<div class="row span12">
	<?php
	$form = $this->beginWidget('CActiveForm', array (
		'id'=>'order-form', 
		'enableAjaxValidation'=>false,
	));
	?>
	<table>
		<tr>
			<td><label class="span12">订单序列号：</label></td>
			<td><?php echo $form->textField($model,'order_id',array('size'=>60,'maxlength'=>60)); ?></td>
		</tr>
	</table>
	<?php		 
		echo CHtml::submitButton('保存',array('class'=>'span3 btn btn-success'));
		$this->endWidget();
	?>
</div>