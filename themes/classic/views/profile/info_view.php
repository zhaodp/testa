<?php 
$this->pageTitle = Yii::app()->name . ' - 个人信息';
$ext = DriverExt::model()->find('driver_id=:driver_id', array(':driver_id'=>$employee->user));
?>

<style type="text/css">
.table-view {
	padding: 12px;
	width: 680px;
	text-align: left;
}

.table-view table.items {
	width: 680px;
}

.table-view table.items td {
	border: 1px solid green;
	font-size: 15px;
	padding: 2px;
	width: 106px;
}

.table-view table.items th {
	font-size: 15px;
	font-wight: blod;
}
</style>

<div class="view">
	<div class="table-view">
	<?php
	echo '个人信息';
	?>
		<table class="items">
			<tr>
				<td><?php echo $employee->getAttributeLabel('user');?></td>
				<td><?php echo $employee->user;?></td>
				<td rowspan="12"><img width='250px' src='<?php echo $employee->picture;?>'></td>
			</tr>
			<tr>
				<td><?php echo $employee->getAttributeLabel('name');?></td>
				<td><?php echo $employee->name;?></td>
			</tr>
			<tr>
				<td><?php echo $employee->getAttributeLabel('imei');?></td>
				<td><?php echo $employee->imei;?></td>
			</tr>
			<tr>
				<td><?php echo $employee->getAttributeLabel('phone');?></td>
				<td><?php echo $employee->phone;?></td>
			</tr>
			<tr>
				<td><?php echo $employee->getAttributeLabel('id_card');?></td>
				<td><?php echo $employee->id_card;?></td>
			</tr>
			<tr>
				<td><?php echo $employee->getAttributeLabel('domicile');?></td>
				<td><?php echo $employee->domicile;?></td>
			</tr>
			<tr>
				<td><?php echo $employee->getAttributeLabel('car_card');?></td>
				<td><?php echo $employee->car_card;?></td>
			</tr>
			<tr>
				<td><?php echo $employee->getAttributeLabel('year');?></td>
				<td><?php echo $employee->year;?></td>
			</tr>
			<tr>
				<td><?php echo $employee->getAttributeLabel('level');?></td>
				<td><?php echo $employee->level;?></td>
			</tr>
			<tr>
				<td>状态</td>				
				<td>
				<?php echo $statusLable;?>			
				</td>
			</tr>
			<tr>
				<td><?php echo $employee->getAttributeLabel('city_id');?></td>
				<td>
				<?php echo Dict::item('city', $employee->city_id); ?>
				</td>
			</tr>
			<tr>
				<td><?php echo $employee->getAttributeLabel('ext_phone');?></td>
				<td><?php echo $employee->ext_phone;?></td>
			</tr>
			<tr>
				<td>代驾次数</td>
				<td colspan="2"><?php if (isset($ext)) echo $ext->service_times;?></td>
			</tr>
			<tr>
				<td>未报单</td>
				<td colspan="2"><?php echo Driver::getDriverReadyOrder($employee->user);?></td>
			</tr>
			<tr>
				<td>好评次数</td>
				<td colspan="2"><?php if (isset($ext)) echo $ext->high_opinion_times; ?></td>
			</tr>
			<tr>
				<td>差评次数</td>
				<td colspan="2"><?php if (isset($ext)) echo $ext->low_opinion_times; ?></td>
			</tr>			
		</table>
	</div>
</div>