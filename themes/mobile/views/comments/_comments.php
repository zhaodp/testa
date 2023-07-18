<div class="span12 alert <?php echo $this->commentsLevel($data->level); ?>">
<p>
	<?php echo $data->name;?> 评价 
	<?php echo isset($data->driver->name) ? '<a href="callto:'.$data->driver->phone.'">'. $data->driver->name . '</a>' : '（未知）';?>:
</p>
<p><?php echo $data->comments;?></p>
<i class="span12 pull-right"><?php echo $this->commentsShowDate($data->insert_time)?></i>
</div>

