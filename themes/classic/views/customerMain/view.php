<?php 
$input_style = array(
	'readOnly' => true,
);
$select_stype = array(
	"disabled"=>"disabled",
);
?>
</style>
	<?php 
	$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	)); 
	?>
	<table >
		<tr>
		<td>
		<?php echo CHtml::label('姓名','name'); ?>
		<?php echo $form->textField($model,'name',$input_style);?>
		</td>
		<td>
		<?php echo CHtml::label('性别','gender',$input_style);?>
		<?php 
			$gender_list = CustomerMain::$gender_dict;
			$gender_list[0] = '全部';
			ksort($gender_list);
		?>
		<?php echo $form->dropDownList($model, 'gender', $gender_list,$select_stype); ?>
		</td>
		<td>
		<?php echo CHtml::label('生日','birthday'); ?>
		<?php echo $form->textField($model,'birthday',$input_style);?>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo CHtml::label('地区','city_id');?>
		<?php echo $form->dropDownList($model, 'city_id', Dict::items('city'),$select_stype); ?>
		</td>
		<td>
		<?php echo CHtml::label('手机号码','phone'); ?>
		<?php echo $form->textField($model,'phone',$input_style);?>
		</td>
		<td>
		<?php echo CHtml::label('备用手机','backup_phone'); ?>
		<?php echo $form->textField($model,'backup_phone',$input_style);?>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo CHtml::label('类型','type');?>
		<?php 
			$type_list = CustomerMain::$type_dict;
			$type_list[0] = '全部';
			ksort($type_list);
		?>
		<?php echo $form->dropDownList($model, 'type', $type_list,$select_stype); ?>
		</td>
		<td>
		<?php echo CHtml::label('原vip卡号','vip_card');?>
		<?php echo $form->textField($model,'vip_card',$input_style); ?>
		</td>
		<td>
		<?php echo CHtml::label('来源','channel');?>
		<?php 
			$channel_list = CustomerMain::$channel_dict;
			$channel_list[0] = '全部';
			ksort($channel_list);
		?>
		<?php echo $form->dropDownList($model, 'channel', $channel_list,$select_stype); ?>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo CHtml::label('状态','status',$input_style);?>
		<?php 
			$status_list = CustomerMain::$status_dict;
			$status_list[0] = '全部';
			ksort($status_list);
		?>
		<?php echo $form->dropDownList($model, 'status', $status_list,$select_stype); ?>
		</td>	
		<td>
		<?php echo CHtml::label('信用等级','credit');?>
		<?php echo $form->textField($model, 'credit',$input_style); ?>
		</td>
		<td>
		<?php echo CHtml::label('帐户余额','amount');?>
		<?php echo $form->textField($model, 'amount',$input_style); ?>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo CHtml::label('活跃度','activity');?>
		<?php echo $form->textField($model, 'activity',$input_style); ?>
		</td>
		<td>
		<?php echo CHtml::label('企业名称','company');?>
		<?php echo $form->textField($model, 'company',$input_style); ?>
		</td>
		<td>
		<?php echo CHtml::label('用户来源','channel');?>
		<?php echo $form->dropDownList($model, 'channel', CustomerMain::$channel_dict,$select_stype); ?>
		</td>
		</tr>
		<tr>
		<td>
		<?php echo CHtml::label('发票抬头','invoice_title');?>
		<?php echo $form->textField($model, 'invoice_title',$input_style); ?>
		</td>
		<td>
		<?php echo CHtml::label('发票备注','invoice_remark');?>
		<?php echo $form->textField($model, 'invoice_remark',$input_style); ?>
		</td>
		<tr>
	</table>
	<?php $this->endWidget(); ?>