<?php
$this->pageTitle = '销单';
?>

<h3>销单</h3>
<div class="container">
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'order-form',
		'focus'=>array($model,'order_number'),
		'enableAjaxValidation'=>false,
	)); ?>
	<?php echo $form->hiddenField($model,'order_id'); ?>
	<table class="table table-bordered" align="center">
		<tbody>
		<tr>
			<td nowrap="nowrap"><label>客户电话:</label><?php echo $model->phone; ?></td>
	    </tr>
	    <tr>
			<td><label>呼叫时间:</label><?php echo date('Y-m-d H:i',$model->call_time); ?></td>
		</tr>
		<tr>
			<td><label>销单类型:</label>
			<?php 
				$cancel_type = Dict::items('cancel_type');
				echo $form->dropDownList($model,
					'cancel_type',
					$cancel_type)
			?></td>
		</tr>
	    <tr>
			<td><label>销单原因:</label><textarea class="span12" rows="5" cols="70" id="Order_log" name="Order[log]"></textarea></td>
		</tr>
	    </tbody>
	</table>
	<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : '保存',
		array('class'=>'btn span1 btn-success')); ?>
	<?php $this->endWidget(); ?>
</div>