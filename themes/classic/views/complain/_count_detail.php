<?php
/**
 * Created by JetBrains PhpStorm.
 * User: daiyihui
 * Date: 13-10-29
 * Time: 下午3:40
 * To change this template use File | Settings | File Templates.
 */
$complainData = CustomerComplain::model()->getDetailAndType($data->complain_id, 1);
?>


<div class="accordion-group">
    <div class="accordion-heading">
        <a href="#<?php echo $data->complain_id;?>" data-parent="#accordion2" data-toggle="collapse" class="accordion-toggle collapsed" title="点击查看详情">
            <strong>投诉时间：<?php echo date('Y-m-d',strtotime($complainData['create_time']));?> &nbsp;&nbsp;&nbsp;投诉类型：<?php echo CustomerComplain::model()->getDetailAndType($data->complain_id);?></strong>
        </a>

    </div>
    <div class="accordion-body collapse" id="<?php echo $data->complain_id;?>" style="height: 0px;">
        <div class="accordion-inner">
            <p class="text-left" style="margin-left: 8px;"><strong>投诉详情：</strong><?php echo $complainData['detail'];?></p><hr/>
            <table class="table table-condensed">
                <thead>
                    <tr>
                        <th style="width: 500px;">处理意见</th>
                        <th>处理结果</th>
                        <th>处理人</th>
                        <th>处理日期</th>
                    </tr>
                </thead>
                <?php
                $logList = CustomerComplainLog::model()->getComplainLogList($data->complain_id);
                if(!empty($logList)){
                    foreach($logList as $log){
                        echo '<tr>';
                        echo '<td>'.$log['mark'].'</td>';
                        echo '<td>'.CustomerComplainLog::$process_result[$log['result']].'</td>';
                        echo '<td>'.$log['operator'].'</td>';
                        echo '<td>'.date('Y-m-d', strtotime($log['create_time'])).'</td>';
                        echo '</tr>';
                    }
                }else{
                    echo '<tr><td colspan="4">没有找到数据</td></tr>';
                }
                ?>
            </table>
        </div>
    </div>
</div>
