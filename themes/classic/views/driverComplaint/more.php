<h1>累计投诉记录</h1>
<table border=1 style="line-height:25px;width:100%;">
    <tr>
        <th>投诉来源</th>
        <th width="10%">投诉司机</th>
        <th width="35%">投诉内容</th>
        <th>投诉类型</th>
        <th>投诉时间</th>
        <th>投诉状态</th>
    </tr>
    <?php if (!empty($customerMore)) { ?>
        <?php foreach ($customerMore as $k => $v) { ?>
            <tr>
                <td><?php echo $v['order_type'] ?></td>
                <td><?php echo $v['driver_user'] ?></td>
                <td><?php echo $v['complaint_content'] ?></td>
                <td><?php echo $v['complaint_type'] ? $v['complaint_type'] : '其他' ?></td>
                <td><?php echo date('Y-m-d H:i:s', $v['create_time']) ?></td>
                <td><?php echo $v['complaint_status'] == 0 ? '未处理' : DriverComplaint::$customer_pulish_type[$v['complaint_status']] ?></td>
            </tr>
        <?php }
    } ?>
</table>