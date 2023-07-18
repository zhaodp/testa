
<tr>
    <td style="border:0">
        <?php if(!empty($order->comment_sms)){ ?>
        <strong>回评时间</strong>：<?php echo isset($order->comment_sms->created) ? $order->comment_sms->created : ''; ?><br>
        <strong>评价等级</strong>：<?php echo isset($order->comment_sms->level) ? $order->comment_sms->level : ''; ?><br>
        <strong>评价类型</strong>：<?php echo isset($order->comment_sms->sms_type) ? (($order->comment_sms->sms_type==1)?"价格核实":"服务评价") : '';?><br>
        <strong>评价内容</strong>：<?php echo isset($order->comment_sms->content) ? $order->comment_sms->content : ''; ?>
        <?php }else{ ?>
        <p>暂无数据</p>
        <?php } ?>
    </td>
</tr>