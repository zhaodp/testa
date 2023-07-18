<?php $this->pageTitle = '已派单，短信发送完毕';?>
<h1>调度短信发送</h1>
<hr class="divider"/>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<label>司机短信：</label>
                        <div class="alert alert-success"><?php echo CHtml::encode($dirver_message);?></div>
			<label>客户短信：</label>
			<div class="alert alert-success"><?php echo CHtml::encode($client_message);?></div>
			<label>App推荐短信：</label>
			<div class="alert alert-success"><?php echo CHtml::encode($app_message);?></div>
		</div>		
	</div>
</div>