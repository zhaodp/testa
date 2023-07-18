<h1>查看投诉信息</h1>

<table border=1 style="line-height:25px;width:100%;">
    <tr>
        <td>司机工号:</td>
        <td><?php echo $model->driver_user; ?></td>
        <td>订单ID:</td>
        <td><?php echo $model->order_id; ?></td>
        <td>客户姓名:</td>
        <td><?php echo $model->customer_name; ?> </td>
    </tr>
    <tr>
        <td>处所城市：</td>
        <td><?php
            if ($model->city == 0 || $model->city == '') {
                echo '未知';
            } else {
                $city = Dict::items('city');
                echo $city[$model->city];
            }
            ?> </td>
        <td>客户电话：</td>
        <td><?php echo $model->customer_phone; ?> </td>
        <td>单订类型：</td>
        <td><?php if ($model->order_type == 1) {
                echo '报单';
            } else {
                echo '销单';
            } ?> </td>
    </tr>
    <tr>
        <td>投诉类型：</td>
        <td><?php
            if ($model->order_type == 1) {
                $data = Dict::item('confirm_c_type', $model->complaint_type);
            } else if ($model->order_type == 2) {
                $data = Dict::item('cancel_c_type', $model->complaint_type);
            }
            echo $data ? $data : '其他';
            ?>
        </td>
        <td>投诉时间：</td>
        <td><?php echo date('Y-m-d H:I', $model->create_time); ?></td>
        <td>代驾时间：</td>
        <td><?php if ($model->driver_time != 0) {
                echo date('Y-m-d H:I', $model->driver_time);
            } else {
                echo '无';
            } ?> </td>
    </tr>
    <tr>
        <td>处理状态:</td>
        <td><?php if ($model->complaint_status == 0) {
                echo '未处理';
            } ?> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>

        <td>诉投内容:</td>
        <td colspan="5"><?php echo $model->complaint_content; ?> </td>
    </tr>
</table>