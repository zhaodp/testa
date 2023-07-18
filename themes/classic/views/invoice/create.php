<h1>订单补录发票</h1>

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
	<table class="table control-group span6" style="width:80%">
		<tr>
			<td colspan="6"><h4>订单信息：</h4></td>
		</tr>
		<tr>
			<td>客户电话:</td>
			<td><?php echo $model->mask_phone; ?></td>
			<td>呼叫时间:</td>
			<td><?php echo date('Y-m-d h:i', $model->call_time); ?></td>
			<td>预约时间:</td>
			<td><?php echo date('Y-m-d h:i', $model->booking_time); ?></td>
		</tr>
		<tr>
			<td>单号</td>
			<td><?php echo $model->order_number;?></td>
			<td>客户名称</td>
			<td><?php echo $model->name;?></td>
			<td>VIP卡号</td>
			<td><?php echo $model->vipcard;?></td>
		</tr>
		<tr>
			<td>开始时间</td>
			<td>
				<?php echo date('Y-m-d h:i',$model->start_time);?>		
			</td>
			<td>结束时间</td>
			<td>
				<?php echo date('Y-m-d h:i',$model->end_time);?>
			</td>
			<td colspan="2" rowspan="3">&nbsp;</td>
		</tr>
		<tr>
			<td>开始地点</td>
			<td><?php echo $model->location_start;?></td>
			<td>到达地点</td>
			<td><?php echo $model->location_end; ?></td>
		</tr>
		<tr>
			<td>里程</td>
			<td><?php echo $model->distance;?></td>
			<td>费用</td>
			<td><?php echo $model->income; ?></td>
		</tr>
	</table>	
	
	<div class="control-group span6">
		<label class="control-label">抬头:</label>
		<div class="controls">
			<?php echo $form->textField($model->invoice, 'title', array ('maxlength'=>200,'class'=>'span5'));?>
			<?php echo $form->error($model->invoice,'title'); ?>
		</div>
		<label class="control-label">发票内容:</label>
		<div class="controls">
			<?php echo $form->textField($model->invoice, 'content');?>
			<?php echo $form->error($model->invoice,'content'); ?>
		</div>
		<label class="control-label">收件人:</label>
		<div class="controls">
			<?php echo $form->textField($model->invoice, 'contact');?>
			<?php echo $form->error($model->invoice,'contact'); ?>
		</div>
		<label class="control-label">电话:</label>
		<div class="controls">
			<?php echo $form->textField($model->invoice, 'telephone');?>
			<?php echo $form->error($model->invoice,'telephone'); ?>
		</div>
		<label class="control-label">收件地址:</label>
		<div class="controls">
			<?php echo $form->textField($model->invoice, 'address',array ('style'=>'width:320px', 'maxlength'=>200));?>
			<?php echo $form->error($model->invoice,'address'); ?>
		</div>
		<label class="control-label">邮编:</label>
		<div class="controls">
			<?php echo $form->textField($model->invoice, 'zipcode', array ('size'=>6,'maxlength'=>6));?>
			<?php echo $form->error($model->invoice,'zipcode'); ?>
		</div>
		<?php 
		echo CHtml::submitButton($model->invoice->isNewRecord ? '新增发票' : '更新发票信息', array (
			'class'=>'btn-large span3 btn-success'
		));	?>
		
	</div>
</fieldset>	

<?php
$this->endWidget();
?>