<div class="span12 alert <?php echo $this->orderStatusEx($data); ?>">
<p>单号：<?php echo CHtml::link($data->order_number); ?></p>
<p>姓名：<?php echo $data->name; ?> 电话：<?php echo $data->phone; ?></p>
<p>预约时间：<?php echo date("Y-m-d H:i",$data->booking_time); ?></p>
<p>状态：<?php echo $this->orderOptration($data); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->orderCacnel($data);?></p>
</div>