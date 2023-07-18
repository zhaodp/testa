<h1>与上下订单关系</h1>

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
	font-size: 12px;
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
	<table width="600px" border="0" cellspacing="0" cellpadding="0"
	align="left" class="items">
	<?php 
	foreach($datas as $data) :
	?>
	<tr>
		<th><?php echo $data['type']==0? '与上一订单':'与下一订单';?></th>
	</tr>
	<tr>
		<td>间隔时间</td>
		<td><?php echo $data['time_interval'];?>秒</td>
		<td>移动距离</td>
		<td><?php echo $data['move_distance'];?></td>
		<td>总上报位置次数</td>
		<td><?php echo $data['upload_count'];?>次</td>
		<td>空闲上报次数</td>
		<td><?php echo $data['count_state_0'];?>次</td>
		<td>服务上报次数</td>
		<td><?php echo $data['count_state_1'];?>次</td>
		<td>下班上报次数</td>
		<td><?php echo $data['count_state_2'];?>次</td>
	</tr>
	<?php
	endforeach; 
	?>
</table>
</div></div>