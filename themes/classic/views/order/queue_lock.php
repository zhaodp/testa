<?php
$this->pageTitle = '锁订单管理';
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

        <div class="title">己锁订单 (<?php echo count($queue_lock);?>)</div>
        <div class="table-view">
            <table class="items">
                <tr>
                    <th>订单ID</th>
                    <th>操作</th>
                </tr>
                <?php
                    if(!empty($queue_lock)){
                     foreach($queue_lock as $queue){
                ?>
                        <tr>
                            <td><?php echo $queue;?></td><td><?php echo CHtml::link('移除锁定',array('order/queueLock','del'=>$queue));  ?></td>
                        </tr>
                <?php
                     }
                }
                ?>
            </table>
        </div>
</div>