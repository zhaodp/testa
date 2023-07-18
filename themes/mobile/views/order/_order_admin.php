<div class="span12 alert <?php echo $this->orderStatusEx($data); ?>">
<p>单号<?php echo CHtml::link($data->order_number); ?>，司机 <?php echo $data->driver; ?> <?php echo $data->phone; ?></p>
<p><?php echo $data->name; ?> <?php echo $data->phone; ?>，预约 <?php echo date("m-d H:i",$data->booking_time); ?></p>
<?php if($data->status ==2 && $data->status ==3){echo $data->cancel_desc;}?>
<?php if($data->status ==1){?>
<p>从<?php echo $data->location_start; ?>到<?php echo $data->location_end; ?>，费用<?php echo $data->income; ?></p>
<?php }?>
<p>状态：<?php echo $this->orderOptration($data); ?></p>
</div>