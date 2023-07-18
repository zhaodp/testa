<?php
/**
 * 手动绑定优惠券
 * @date 2012-06-06
 */
$this->pageTitle = '绑定优惠券';
?>

<h1>绑定优惠券</h1>
<div class="row span12">
	<?php
	$form = $this->beginWidget('CActiveForm', array (
		'id'=>'order-form', 
		'enableAjaxValidation'=>false,
	));
	?>
	<table>
		<tr>
			<td><label class="span12">优惠码：</label></td>
			<td><?php echo $form->textField($model,'bonus_sn',array('size'=>60,'maxlength'=>60)); ?></td>
		</tr>
		<tr>
			<td><label class="span12">客户电话：</label></td>
			<td><?php echo $form->textField($model,'customer_phone',array('size'=>60,'maxlength'=>60)); ?></td>
		</tr>		
	</table>
	<?php		 
		echo CHtml::submitButton('保存',array('class'=>'span3 btn btn-success'));
		$this->endWidget();
	?>
</div>