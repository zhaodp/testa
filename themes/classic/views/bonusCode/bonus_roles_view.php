<table class="table table-bordered">
    <tr>
        <th>优惠号码：</th>
        <td><?php echo $model->bonus_sn; ?></td>
        <th>手机号数量：</th>
        <td><?php echo $model->phone_num; ?>个</td>
    </tr>
    <tr>
        <th>金额：</th>
        <td><?php echo $model->balance; ?>元</td>
        <th>一个手机号绑定次数：</th>
        <td><?php echo $model->number; ?>次</td>
    </tr>
    <tr>
        <th>商家：</th>
        <td><?php echo $model->merchants; ?></td>
        <th>创建人：</th>
        <td><?php echo $model->create_by; ?><br/><?php echo $model->created; ?></td>
    </tr>
    <tr>
        <th>短信内容：</th>
        <td colspan="3"><?php echo $model->sms; ?></td>
    </tr>
    <tr>
        <th>操作日志：</th>
        <td colspan="3">
            创建人：<?php echo $model->create_by; ?>&nbsp;&nbsp;&nbsp;
            创建时间：<?php echo $model->created; ?>；<br/>

            <?php
            if (!empty($model->audit)) {
                ?>
                审核人：<?php echo $model->audit; ?>&nbsp;&nbsp;&nbsp;
                审核时间：<?php echo $model->audit_time; ?>；<br/>
            <?php
            }
            ?>

            <?php
            if (!empty($model->update_by)) {
                ?>
                生成人：<?php echo $model->update_by; ?>&nbsp;&nbsp;&nbsp;
                生成时间：<?php echo $model->update; ?>；<br/>
            <?php
            }
            ?>

        </td>
    </tr>
</table>