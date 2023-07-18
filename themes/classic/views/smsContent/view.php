<div class="span12">

	<div class="control-group">
		<label class="control-label">评价者电话：</label>
		<div class="controls">
			<?php echo $model['phone'];?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">评价详情：</label>
		<div class="controls">
			<?php echo $comment['comments'];?>
		</div>
	</div>
	
	<hr>
	<div class="control-group">
		<label class="control-label">回访日期：</label>
		<div class="controls">
			<?php echo $model['create_time'];?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">回访短信详情：</label>
		<div class="controls">
			<?php echo $model['content'];?>
		</div>
	</div>
	
</div>