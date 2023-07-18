<div class="content"><b><?php echo $data->name;?></b>
<b><?php switch ($data->level) {
case 1:
	echo '<font color="red">差评</font>';
	break;
case 2:
	echo '<font color="block">中评</font>';
	break;
case 3:
	echo '<font color="green">好评</font>';
	break;
}
?></b>
<?php echo isset($data->driver->name)?$data->driver->name:'(司机不存在)';?>
<span style="float:right"> <?php echo date('m-d H:i:s',strtotime($data->insert_time));?></span>
<div><?php echo CHtml::encode($data->comments); ?></div>
</div>