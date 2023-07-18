<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-6
 * Time: 下午2:53
 * auther mengtianxue
 */
?>
<h1>信息费天汇总报表</h1>

<!-- 搜索条件 -->
<div class="row-fluid">
    <div class="well span12">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'action' => Yii::app()->createUrl($this->route),
            'method' => 'get',
        )); ?>
        <div class="row span12">
            <div class="span3">
                <?php echo $form->label($model, 'city_id'); ?>
                <?php echo $form->dropDownList($model, 'city_id', Dict::items('city'), array('value' => $params['city_id'])); ?>
            </div>

            <div class="span3">
                <?php echo $form->label($model, '年份'); ?>
                <select id="ReportFsAccountRp_Year" name="ReportFsAccountRp[year]">
                    <?php
                    $year = date("Y");
                    for ($i = 2011; $i <= $year; $i++) {
                        if($params['year'] == $i){
                            echo "<option selected='selected' value='$i'>$i</option>";

                        }else{
                            echo "<option value='$i'>$i</option>";
                        }
                    }
                    ?>

                </select>
            </div>

            <div class="span3">
                <?php echo $form->label($model, '月份'); ?>
                <select id="ReportFsAccountRp_Month" name="ReportFsAccountRp[month]">
                    <?php
                    for ($i = 1; $i <= 12; $i++) {
                        if ($i < 10) {
                            $i = '0' . $i;
                        }

                        if($params['month'] == $i){
                            echo "<option selected='selected' value='$i'>$i</option>";

                        }else{
                            echo "<option value='$i'>$i</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="row span2">
                <?php echo $form->label($model, '&nbsp'); ?>
                <?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</div>


<div class="row-fluid">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>项目名称</th>
            <th>日期</th>
            <th>摘要</th>
            <th>期初余额</th>
            <th>本期增加</th>
            <th>本期减少</th>
            <th>期末余额</th>
            <th>操作</th>
        </tr>
        </thead>

        <tbody>
        <?php
        $channel = Dict::items('cast_channel');
        unset($channel['0']);
        $count = count($channel);
        for ($i = 1; $i <= $count + 1; $i++) {
            if ($i != 5 && $i != 10) {
                $channel = Dict::item('cast_channel', $i);
                $str = "<tr>";
                if ($i == 1) {
                    $str .= "<th rowspan='" . ($count + 2) . "' style ='text-align:center; vertical-align:middle;'>信息费</th>";
                }
                $str .= "<td>" . $params['account_date'] . "</td>
                <td>$channel</td>";
                $channel_data = empty($data[$i]) ? '' : $data[$i];
                if (empty($channel_data)) {
                    $bill_type = 0;
                    $money = '';
                } else {
                    $bill_type = isset($channel_data->bill_type) ? $channel_data->bill_type : 0;
                    $money = isset($channel_data->money) ? $channel_data->money : '';
                }

                $str .= ($bill_type == 0) ? "<td>$money</td>" : "<td>--</td>";
                $str .= ($bill_type == 1) ? "<td>$money</td>" : "<td>--</td>";
                $str .= ($bill_type == 2) ? "<td>$money</td>" : "<td>--</td>";
                $str .= ($bill_type == 3) ? "<td>$money</td>" : "<td>--</td>";

                $url_arr = array('CarEmployeeAccount[channel]' => $i,
                    'CarEmployeeAccount[month]' => '201312',
                    'CarEmployeeAccount[city_id]' => $params['city_id'],
                    'CarEmployeeAccount[created]' => $params['account_date']
                );
                $select_url = Yii::app()->createUrl('/finance/account_info', $url_arr);
                $back_status = BDailyAccountReport::model()->getDailyAccountNotStatus($params['account_date'], $i, $params['city_id']);

                $str .= "<td>
                    <a href='" . $select_url . "' target='_blank'>查看详情</a>
                </td>
            </tr>";
                echo $str;
            }
        }
        ?>
        </tbody>
    </table>
</div>










