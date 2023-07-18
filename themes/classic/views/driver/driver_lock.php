<?php
$this->pageTitle = '锁司机管理';
?>

<style type="text/css">
    .table-view {
        width:100%;
        text-align: center;
        padding:15px 0 5px 0px;
    }
    .title {
        width:100%;
        text-align:center;
        font-weight:bold;
        font-size:16px;
    }
    .table-view table.items {
        width:100%;
        text-align:center;
    }
    .table-view table.items td {
        border: 1px solid #ccc;
        font-size: 15px;
        padding: 2px;
    }
    .BR{
        width:100%;
        height:20px;
    }
</style>

<div class="view">

    <div class='BR'></div>

    <div class="title">己锁司机 (<?php echo count($driver_lock);?>)</div>
    <div class="table-view">
        <table class="items">
            <tr>
                <th>司机ID</th>
                <th>锁定时间</th>
                <th>操作</th>
            </tr>
            <?php
            if(!empty($driver_lock)){
                foreach($driver_lock as $queue){
                    ?>
                    <tr>
                        <td><?php echo $queue[0];?></td>
                        <td><?php echo date(Yii::app()->params['formatDateTime'], $queue[1]);?></td>
                        <td><?php echo CHtml::link('移除锁定',array('driver/lock','del'=>$queue));  ?></td>
                    </tr>
                <?php
                }
            }
            ?>
        </table>
    </div>
</div>