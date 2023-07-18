<h1>操作记录</h1>
<table border=1 style="line-height:25px;width:100%;">
    <tr>
        <th width="20%">操作记录</th>
        <th width="20%">操作人</th>
        <th width="20%">操作时间</th>
        <th width="20%">操作备注</th>
        <th width="20%">所发短信</th>
    </tr>
    <tr>
        <td>投诉</td>
        <td><?php echo $complainDiver->driver_user?></td>
        <td><?php echo date('Y-m-d H:i:s',$complainDiver->create_time);?></td>
        <td><?php echo $complainDiver->complaint_content?></td>
        <td></td>
    </tr>
    <?php if(!empty($driver_complaint_log)){?>
    <?php foreach($driver_complaint_log as $k=>$v) {?>
    <tr>
        <td><?php echo DriverComplaint::$customer_pulish_type[$v['status']]?></td>
        <td><?php echo $v['operator'];?></td>
        <td><?php echo date('Y-m-d H:i:s',$v['create_time'])?></td>
        <td><?php echo $v['mark'];?></td>
        <td><?php echo $v['content'];?></td>
    </tr>
    <?php }}?>
</table>