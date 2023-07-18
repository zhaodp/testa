<?php 
$this->pageTitle = 'VIP账单信息 - e代驾';
?>
<h5 style="padding-top:10px; padding-bottom:10px;"><?php echo "您昨天使用了".$vipTrade['order_count']."个代驾，共消费：".-$vipTrade['consumpte']."元，现余额为：".$vipTrade['balance']."元";?></h5>
<div id="well" class="list-view">
	<?php
		foreach ($dataProvider as $list){
	?>
	<div class="alert alert-success">
	<?php 
		$name = preg_replace( '/\{(.*)\}/', '',$list['name']);
		$name = str_replace('()', '', $name);
	?>
		<p>
			<?php echo '电话'.substr_replace($list['phone'], '****', 3, 4).'的用户于'.date('m月d日 H:i',$list['booking_time']).'预约代驾，'.
						$list['driver_id'].'司机为您提供代驾，从'.$list['location_start'].'到'.$list['location_end'].
						'共'.$list['distance'].'公里，等候'.$list['waiting_time'].'分钟，等候金额'.$list['waiting_amount'].'元，总计金额'.-$list['amount'].'元。'; ?>	
		</p>
	</div>
	<?php }?>
</div>


