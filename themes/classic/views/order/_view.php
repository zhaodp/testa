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

.table-view table.items td input {
	width: 106px;
	border: 1px solid #000000;
	height: 24px;
	font-size: 14px;
}

.table-view table.items_must {
	width: 680px;
}

.table-view table.items_must td {
	border: 1px solid green;
	font-size: 15px;
	padding: 2px;
	width: 108px;
}

.table-view table.items_must td input {
	width: 106px;
	border: 1px solid red;
	height: 24px;
	font-size: 14px;
}

.table-view table.items th {
	font-size: 15px;
	font-wight: blod;
}
</style>
<div class="view">
	<div class="table-view">
	<?php
	echo '订单来源：'.$data->description;
	?>
	<table width="600px" border="0" cellspacing="0" cellpadding="0"
	align="left" class="items">
	<tr>
		<td>客户电话:</td>
		<td><?php
		echo $data->phone;
		?></td>
		<td>呼叫时间:</td>
		<td><?php
		echo date('m-d H:i', $data->call_time);
		?></td>
		<td>预约时间:</td>
		<td><?php
		echo date('m-d H:i', $data->booking_time);
		?></td>
	</tr>
	<tr>
		<td>单号</td>
		<td><?php
		echo $data->order_number;?></td>
		<td>客户名称</td>
		<td><?php
		echo $data->name;?></td>
		<td>VIP卡号</td>
		<td><?php
		echo $data->vipcard;?></td>
	</tr>
	<tr>
		<td>开始时间</td>
		<td>
			<?php echo date('Y-m-d H:i',$data->start_time);?>		
		</td>
		<td>结束时间</td>
		<td>
			<?php echo date('Y-m-d H:i',$data->end_time);?>
		</td>
		<td colspan="2" rowspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>开始地点</td>
		<td><?php echo $data->location_start;?></td>
		<td>到达地点</td>
		<td><?php echo $data->location_end; ?></td>
	</tr>
	<tr>
		<td>里程</td>
		<td><?php echo $data->distance;?></td>
		<td>费用</td>
		<td><?php echo $data->income; ?></td>
	</tr>
	<tr>
		<td>车号</td>
		<td><?php if ($data->car) echo $data->car->number; ?></td>
		<td>车型</td>
		<td><?php if ($data->car) echo $data->car->brand; ?></td>
		<td>车况</td>
		<td><?php if ($data->car) echo $data->car->status; ?></td>
	</tr>
	<tr>
		<td>发票项目</td>
		<td><?php if($data->invoice) echo $data->invoice->content;?></td>
		<td>抬头</td>
		<td colspan="3"><?php if($data->invoice) echo $data->invoice->title; ?></td>
	</tr>
	<tr>
		<td>收件人</td>
		<td><?php if($data->invoice) echo $data->invoice->contact; ?></td>
		<td>电话</td>
		<td><?php if($data->invoice) echo $data->invoice->telephone; ?></td>
		<td>邮编</td>
		<td><?php if($data->invoice) echo $data->invoice->zipcode; ?></td>
	</tr>
	<tr>
		<td>收件地址</td>
		<td colspan="5"><?php if($data->invoice) echo $data->invoice->address; ?></td>
	</tr>
	<tr>
		<td>备注</td>
		<td colspan="5"><?php echo $data->log;?></td>
	</tr>
</table>

<div style="clear: both"></div>
</div>

</div>
