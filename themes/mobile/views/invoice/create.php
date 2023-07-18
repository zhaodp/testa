<h3>订单补录发票</h3>

<?php
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'invice-form', 
	'focus'=>array ($model->invoice, 'title'),
	'errorMessageCssClass'=>'alert alert-error',
	'enableClientValidation'=>true,
    'clientOptions'=>array( 
        'validateOnSubmit'=>false,  // 这个是设置是否把提交按钮也做成客户端验证。 
    ),
	'enableAjaxValidation'=>false,
));

echo $form->hiddenField($model, 'order_id');

?>
<?php echo $form->errorSummary($model); ?>
<fieldset>
	<table class="table span10">
		<tr>
			<td><label class="label">单号:</label></td>
			<td><?php echo $model->order_number;?></td>
		</tr>
		<tr>
			<td><label class="label">客户名称:</label></td>
			<td><?php echo $model->name;?></td>
		</tr>
		<tr>
			<td><label class="label">电话:</label></td>
			<td><?php echo $model->phone; ?></td>
		</tr>
		<tr>
			<td><label class="label">预约时间:</label></td>
			<td><?php echo date('Y-m-d h:i', $model->booking_time); ?></td>
		</tr>
		<tr>
			<td><label class="label">开始地点:</label></td>
			<td><?php echo $model->location_start;?></td>
		</tr>
		<tr>
			<td><label class="label">到达地点:</label></td>
			<td><?php echo $model->location_end; ?></td>
		</tr>
		<tr>
			<td><label class="label">里程:</label></td>
			<td><?php echo $model->distance;?></td>
		</tr>
		<tr>
			<td><label class="label">费用:</label></td>
			<td><?php echo $model->income; ?></td>
		</tr>
	</table>
	<table class="table table-bordered" align="center">
		<tbody>
		<tr>
			<td>
				<label class="control-label">抬头:</label>
				<?php echo $form->textField($model->invoice, 'title', array ('maxlength'=>200,'class'=>'span12'));?>
				<?php echo $form->error($model->invoice,'title'); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label class="control-label">发票内容:</label>
				<?php echo $form->textField($model->invoice, 'content',array ('class'=>'span12'));?>
				<?php echo $form->error($model->invoice,'content'); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label class="control-label">收件人:</label>
				<?php echo $form->textField($model->invoice, 'contact',array ('class'=>'span12'));?>
				<?php echo $form->error($model->invoice,'contact'); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label class="control-label">电话:</label>
				<?php echo $form->textField($model->invoice, 'telephone',array ('class'=>'span12'));?>
				<?php echo $form->error($model->invoice,'telephone'); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label class="control-label">收件地址:</label>
				<?php echo $form->textField($model->invoice, 'address',array ('maxlength'=>200,'class'=>'span12'));?>
				<?php echo $form->error($model->invoice,'address'); ?>
			</td>
		</tr>
		<tr>
			<td>
				<label class="control-label">邮编:</label>
				<?php echo $form->textField($model->invoice, 'zipcode', array ('maxlength'=>6,'class'=>'span6'));?>
				<?php echo $form->error($model->invoice,'zipcode'); ?>
			</td>
		</tr>
		</tbody>
	</table>
	<?php 
	echo CHtml::submitButton($model->invoice->isNewRecord ? '新增发票' : '更新发票信息', array (
		'class'=>'btn span1 btn-success'
	));	?>
</fieldset>	

<?php
$this->endWidget();
?>