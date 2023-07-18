<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-6
 * Time: 下午2:53
 * auther mengtianxue
 */
?>
<h1>VIP汇总表</h1>
<ul class="nav nav-tabs">
    <li>
        <?php
        $year_url = Yii::app()->createUrl('/finance/vip_month_list',
            array('date_time' => 2013
            ));
        ?>
        <a href="<?php echo $year_url; ?>">2013年汇总</a></li>
    </li>

    <li>
        <?php
        $year_url = Yii::app()->createUrl('/finance/vip_month_list',
            array('date_time' => 2014
            ));
        ?>
        <a href="<?php echo $year_url; ?>">2014年汇总</a></li>
    </li>
    <li>
        <?php
        $account_date = date('Y-m', strtotime($params['account_date']));
        $month_url = Yii::app()->createUrl('/finance/vip_daily_list',
            array('date_time' => $account_date,
            ));
        ?>
        <a href="<?php echo $month_url; ?>"><?php echo date('m', strtotime($params['account_date']));;?>月汇总</a></li>
    <li class="active">
        <a href="javascript:void(0);">Vip日汇总</a>
    </li>
</ul>

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
                <?php echo $form->label($model, 'account_date'); ?>
                <?php
                Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                $this->widget('CJuiDateTimePicker', array(
                    'name' => 'ReportFsVipRp[account_date]',
                    'model' => $model, //Model object
                    'value' => $params['account_date'],
                    'mode' => 'date', //use "time","date" or "datetime" (default)
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd'
                    ), // jquery plugin options
                    'language' => 'zh',
                ));
                ?>
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
        $channel_arr = Dict::items('vip_channel');
        $count = count($channel_arr);
        foreach( $channel_arr as $index => $channel){
            $str = "<tr>";
            if ($index == -1) {
                $str .= "<th rowspan='" . ($count + 1) . "' style ='text-align:center; vertical-align:middle;'>VIP</th>";
            }
            $str .= "<td>" . $params['account_date'] . "</td>
                <td>$channel</td>";
            $channel_data = empty($data[$index]) ? '' : $data[$index];
            if (empty($channel_data)) {
                $bill_type = 0;
                $money = '';
            } else {
                $bill_type = isset($channel_data->bill_type) ? $channel_data->bill_type : 0;
                $money = isset($channel_data->money) ? $channel_data->money : '';
            }

            $str .= ($bill_type == 0 && $money != '') ? "<td>" . number_format($money, 2) . "</td>" : "<td>&nbsp;</td>";
            $str .= ($bill_type == 1 && $money != '') ? "<td>" . number_format($money, 2) . "</td>" : "<td>&nbsp;</td>";
            $str .= ($bill_type == 2 && $money != '') ? "<td>" . number_format($money, 2) . "</td>" : "<td>&nbsp;</td>";
            $str .= ($bill_type == 3 && $money != '') ? "<td>" . number_format($money, 2) . "</td>" : "<td>&nbsp;</td>";

            $url_arr = array('ReportFsVipTradeInfo[channel]' => $index,
                'ReportFsVipTradeInfo[month]' => date('Ym', strtotime($params['account_date'])),
                'ReportFsVipTradeInfo[city_id]' => $params['city_id'],
                'ReportFsVipTradeInfo[daily_date]' => $params['account_date']
            );

            $select_url = Yii::app()->createUrl('/finance/vip_info', $url_arr);
            $back_status = BDailyAccountReport::model()->getDailyAccountNotStatus($params['account_date'], $index, $params['city_id']);

            if ($back_status) {
                $success_url = Yii::app()->createUrl('/finance/account_success', array(
                    'account_date' => $params['account_date'],
                    'channel' => $index,
                    'city_id' => $params['city_id'],
                    'status' => 1
                ));
                $success_like = "<a href='javascript:void(0);' onclick='account_success(\"$success_url\")'>对账成功</a>";
            } else {
                $success_like = "";
            }

            if ($index == -1 || $index == 99) {
                $str .= "<td>
                    &nbsp;
                </td>";
            } else {
                $str .= "<td>
                    <a href='" . $select_url . "'>查看详情</a>
                    $success_like
                </td>";
            }
            $str .= "</tr>";
            echo $str;

        }
        ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    function account_success(url) {
        if (confirm("确定对账成功！")) {
            $.ajax({
                'url': url,
                'type': 'get',
                'success': function (data) {
                    if (data == 1) {
                        alert("成功对账");
                        location.reload();
                    } else {
                        alert("失败了，联系一下技术");
                    }
                },
                'cache': false
            });
        }
        return false;
    }
</script>











