<div class="container">
    <h3 class="page-header">来电弹窗可用优惠码列表&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="<?php echo $this->createUrl("bonusCode/bonus_rules_create_call")?>">添加新的优惠券</a></h3>

    <div>
        <table class="table table-hover">
            <thead>
            <tr class="success">
                <td>#</td>
                <td>优惠码ID</td>
                <td>优惠码名称</td>
                <td>remark</td>
                <td>是否启用</td>
                <td>是否可以多次绑定</td>
                <td>创建时间</td>
                <td>编辑</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($list as $bonus400rules) {
                echo "<tr>";
                echo "<td>" . $bonus400rules['id'] . "</td>" . "<td>" . $bonus400rules['bonus_code_id'] ."<td>" . $bonus400rules['bonus_name'] . "</td>". "</td>" . "<td>" . $bonus400rules['remark'] . "</td><td>" . ($bonus400rules['status'] == 1 ? '是' : '否') . "</td><td>" . ($bonus400rules['multi'] == 1 ? '是' : '否') . "</td><td>" . $bonus400rules['created'] . "</td>";
                echo "<td><a href='".$this->createUrl("bonusCode/bonus_rules_create_call_edit",array("id"=>$bonus400rules['id']))."'>编辑</a></td>";
                echo "</tr>";
            }
            ?>
            </tr>
            </tbody>
        </table>
    </div>
</div>
