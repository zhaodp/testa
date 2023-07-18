<?php
/**
 * 司机优惠券补账充值
 * @author 柳长华
 * @date 2012-06-06
 */
$this->pageTitle = '司机优惠券补账充值';
?>

<h1>司机优惠券补账充值</h1>
<?php
if (!empty($info))
{
?>
<div class="well alert-error">
<?php
echo $info; 
?>
</div>
<?php
}
?>
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
			<td><label class="span12">工号：</label></td>
			<td><?php echo CHtml::textField('Account[user]',$employee->user,array('class'=>'span5','placeholder'=>'司机工号','readonly'=>'readonly'));?></td>
		</tr>
		<tr>
			<td><label class="span12">订单号：</label></td>
			<td><label class="span12"><?php echo CHtml::textField('Account[cast]',$employee->order_id,array('class'=>'span require', 'readonly'=>'readonly')); ?></label></td>
		</tr>		
		<tr>
			<td><label class="span12">充值金额：</label></td>
			<td><?php echo CHtml::textField('Account[cast]',$employee->cast,array('class'=>'span3 require')); ?></td>
		</tr>
		<tr>
			<td><label class="span12">备注：</label></td>
			<td><?php echo CHtml::textField('Account[comment]',$employee->comment,array('class'=>'require')); ?></td>
		</tr>
	</table>
	<?php
		echo CHtml::hiddenField('Account[user]',$employee->user);
		echo CHtml::hiddenField('Account[order_id]',$employee->order_id); 		 
		echo CHtml::submitButton('充值',array('class'=>'span3 btn btn-success','confirm'=>'确定充值吗？'));
		$this->endWidget();
	?>
</div>
<?php }?>
