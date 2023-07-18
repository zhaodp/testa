
<tr>
    <td style="border:0">
        <?php if(!empty($order->order_bonus)){ ?>
        <strong>绑定时间</strong>：<?php echo isset($order->order_bonus->created) ? date('Y-m-d H:i:s',$order->order_bonus->created) : ''; ?><br>
        <strong>优惠券名称</strong>：<?php echo isset($order->order_bonus->bonus_type_id) ? CustomerBonus::model()->getBonusCodeList($order->order_bonus->bonus_type_id) : ''; ?><br>
        <strong>优惠码</strong>：<?php echo isset($order->order_bonus->bonus_sn) ? $order->order_bonus->bonus_sn : ''; ?><br>
        <strong>金额</strong>：<?php echo isset($order->order_bonus->balance) ? $order->order_bonus->balance : ''; ?>
        <?php }else{ ?>
        <p>暂无数据</p>
        <?php } ?>
    </td>
</tr>