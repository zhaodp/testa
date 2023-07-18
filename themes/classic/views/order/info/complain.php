
<tr>
    <td style="border:0">
        <h5 class="text-center">客人投诉司机</h5>
        <?php if(!empty($order->customer_complain)){ ?>
        <strong>投诉时间</strong>：<?php echo $order->customer_complain->create_time; ?><br>
        <strong>投诉ID</strong>：<?php echo $order->customer_complain->id;?><br>
        <strong>投诉来源</strong>：<?php echo CustomerComplain::$source[$order->customer_complain->source];?><br>
        <?php $custome_type = CustomerComplainType::model()->getComplainType($order->customer_complain->complain_type); ?>
        <strong>投诉类型</strong>：<?php echo isset($custome_type[0]->name) ? $custome_type[0]->name : ''; ?><br>
        <strong>投诉详情</strong>：<?php echo $order->customer_complain->detail;?>
        <?php }else{ ?>
        <p>暂无数据</p>
        <?php } ?>
    </td>
</tr>
<tr>
    <td style="border:0">
        <h5 class="text-center">司机投诉客人</h5>
        <?php if(!empty($order->driver_complain)){ ?>
        <strong>投诉时间</strong>：<?php echo date('Y-m-d H:i:s',$order->driver_complain->create_time); ?><br>
        <strong>投诉ID</strong>：<?php echo $order->driver_complain->id;?><br>
        <strong>投诉来源</strong>：<?php echo ($order->driver_complain->order_type==1) ? "报单" :"销单";?><br>
        <strong>投诉类型</strong>：<?php echo Dict::item('confirm_c_type',$order->driver_complain->complaint_type);?><br>
        <strong>投诉详情</strong>：<?php echo $order->driver_complain->complaint_content;?>
        <?php }else{ ?>
        <p>暂无数据</p>
        <?php } ?>
    </td>
</tr>