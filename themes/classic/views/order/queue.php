<?php 
$this->pageTitle = '客户信息';
?>

<style type="text/css">
.table-view {
	width:100%;
	text-align: center;
	padding:15px 0 5px 0px;
}
.title {
	width:100%;
	text-align:center;
	font-weight:bold;
	font-size:16px;
}
.table-view table.items {
    width:100%;
	text-align:center;
}
.table-view table.items td {
	border: 1px solid #ccc;
	font-size: 15px;
	padding: 2px;
}
.BR{
    width:100%;
    height:20px;
}
</style>

<div class="view">
    <?php
        if ($Queue) {
    ?>
    <div class="title">预约信息</div>
	<div class="table-view">
		<table class="items">
		    <tr>
			<th>预约时间</th>
			<th>城市</th>
			<th>客户名称</th>
			<th>地址</th>
			<th>预约人数</th>
			<th>派单人数</th>
			<th>备注</th>
			<th>接单调度</th>
			<th>派单调度</th>
			<th>派单时间</th>
			</tr>
			<tr>
			    <td><?php echo $Queue->booking_time;?></td>
			    <td><?php echo $Queue->city_id;?></td>
			    <td><?php echo $Queue->name;?></td>
			    <td><?php echo $Queue->address;?></td>
			    <td><?php echo $Queue->number;?></td>
			    <td><?php echo $Queue->dispatch_number;?></td>
			    <td><?php echo $Queue->comments;?></td>
			    <td><?php echo $Queue->agent_id;?></td>
			    <td><?php echo $Queue->dispatch_agent;?></td>
			    <td><?php echo $Queue->dispatch_time;?></td>
			</tr>
		</table>
	</div>
	<?php
        } else {
        	echo '<div class="title">无预约信息</div>';
        }
	?>
	<div class='BR'></div>
	<?php
	    if ($Queue && $QueueMap) {
	?>
	<div class="title">预约派出司机</div>
	<div class="table-view">
		<table class="items">
		    <tr>
			<th>司机工号</th>
			<th>派单时间</th>
			</tr>
			<?php
			    $i = 0;
			    foreach ($QueueMap as $val) {
			?>
			<tr>
			    <td>
			        <?php 
			            if ($Queue->number > 1) {
			            	if ($i == 0) {
			            		echo $val->driver_id."(组长)";
			            	} else {
			            		echo $val->driver_id."(组员)";
			            	}
			            } else {
			            	echo $val->driver_id;
			            }
			            
			        ?>
			    </td>
			    <td><?php echo $val->confirm_time;?></td>
			</tr>
			<?php $i++;} ?>
		</table>
	</div>
	<?php }?>
</div>