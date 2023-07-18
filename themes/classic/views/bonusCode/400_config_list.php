<div class="container">
    <h4 class="page-header">400未接通电话设置列表&nbsp;&nbsp;&nbsp;<a class="btn btn-primary" href="<?php echo $this->createUrl("bonusCode/forwardView",array('page'=>'400_config'))?>">添加新的配置</a></h4>

    <div>
        <table class="table table-hover">
            <thead>
            <tr class="success">
                <td>#</td>
                <td>配置名称</td>
                <td>优惠码ID</td>
                <td>是否启用</td>
                <td>创建时间</td>
                <td>编辑</td>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($list as $config) {
                echo "<tr>";
                echo "<td>" . $config['id'] . "</td>" . "<td>" . $config['name'] ."<td>" . $config['bonus_code_id'] . "</td>". "</td><td>" . ($config['status'] == 1 ? '是' : '否') . "</td><td>" . $config['created'] . "</td>";
                echo "<td><a href='".$this->createUrl("bonusCode/config_update",array("id"=>$config['id']))."'>编辑</a></td>";
                echo "</tr>";
            }
            ?>
            </tr>
            </tbody>
        </table>
    </div>
</div>
