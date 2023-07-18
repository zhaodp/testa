<?php $this->pageTitle = '已派单，短信发送完毕';?>

<h1>调度短信发送</h1>
<hr class="divider"/>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<label>司机短信：</label>
			<?php foreach ($dirver_message as $message) {?>
			<div class="alert alert-success"><?php echo $message['driver_id'];?>(<?php echo $message['receiver'];?>):<?php echo $message['message'];?></div>
			<?php } ?>
			<label>客户短信：</label>
			<div class="alert alert-success"><?php echo $client_message;?></div>
			<label>App推荐短信：</label>
			<div class="alert alert-success"><?php echo $app_message;?></div>
		</div>		
	</div>
</div>