<?php
/**
 * 司机信息费充值
 * @author 李玉卿
 * @date 2012-06-06
 */
$this->pageTitle = '司机信息费充值';
?>

<h1>司机信息费充值</h1>
<div class=" well">
<?php
$form = $this->beginWidget('CActiveForm', array (
	'id'=>'order-form', 
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-inline')
));
?>
<?php 
echo CHtml::textField('Employee[user]','',array('class'=>'input-small','placeholder'=>'司机工号'));
echo CHtml::submitButton('查询',array('class'=>'btn'));
$this->endWidget();
?>
</div>

<?php if ($employee) {?>
<div class="row span12">
	<?php
	$form = $this->beginWidget('CActiveForm', array (
		'id'=>'order-form', 
		'enableAjaxValidation'=>false,
	));
	?>
	<table>
		<tr>
			<td><label class="span12">姓名：</label></td>
			<td><label class="span12"><?php echo $employee->name; ?></label></td>
		</tr>
		<tr>
			<td><label class="span12">工号：</label></td>
			<td><label class="span12"><?php echo $employee->user; ?></label></td>
		</tr>
		<tr>
			<td><label class="span12">充值金额：</label></td>
			<td><?php echo CHtml::textField('Account[cast]','',array('class'=>'span require')); ?></td>
		</tr>
		<tr>
			<td><label class="span12">备注：</label></td>
			<td><?php echo CHtml::textField('Account[comment]','',array('class'=>'span12 require')); ?></td>
		</tr>
	</table>
	<?php
		echo CHtml::hiddenField('Account[user]',$employee->user); 
		echo CHtml::submitButton('充值',array('class'=>'btn btn-success','confirm'=>'确定充值吗？'));
		$this->endWidget();
	?>
</div>
<?php }?>
