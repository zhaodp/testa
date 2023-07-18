<?php 
$this->pageTitle = '客户信息';
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
	        if (!empty($vip_arr)) {
	    ?>
		<table class="items">
			<tr>
				<td>VIP号码:</td>
				<td><?php echo $vip_arr['vipcard'];?></td>
				<td>主卡人姓名:</td>
				<td><?php echo $vip_arr['card_customer_name'];?></td>
			</tr>
			<tr>
				<td>姓名:</td>
				<td><?php echo $vip_arr['customer_name'];?></td>
				<td>状态:</td>
				<td><?php echo $vip_arr['status'];?></td>
			</tr>
			<tr>
				<td>余额:</td>
				<td><?php echo $vip_arr['balance'];?></td>
				<td>信誉度:</td>
				<td><?php echo $vip_arr['credit'];?></td>
			</tr>
		</table>
		<?php } ?>
	</div>
</div>