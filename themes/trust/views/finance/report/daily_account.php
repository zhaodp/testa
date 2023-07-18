<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-6
 * Time: 下午2:53
 * auther mengtianxue
 */
?>
<h1>信息费汇总表</h1>
<ul class="nav nav-tabs">
    <li>
        <?php
        $year_url = Yii::app()->createUrl('/finance/month_list',
            array('date_time' => 2013
            ));
        ?>
        <a href="<?php echo $year_url; ?>">2013年汇总</a></li>
    </li>
    <li>
        <?php
        $year_url = Yii::app()->createUrl('/finance/month_list',
            array('date_time' => 2014
            ));
        ?>
        <a href="<?php echo $year_url; ?>">2014年汇总</a></li>
    </li>
    <li>
        <?php
        $month_url = Yii::app()->createUrl('/finance/daily_list',
            array('date_time' => date('Y-m', strtotime($params['account_date'])),
            ));
        ?>
        <a href="<?php echo $month_url; ?>"><?php echo date('m', strtotime($params['account_date'])); ?>月汇总</a></li>
    <li class="active">
        <a href="javascript:void(0);">日汇总</a>
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
                    'name' => 'ReportFsAccountRp[account_date]',
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
        $channel = Dict::items('cast_channel');
        unset($channel['0']);
        $count = count($channel);
        for ($i = 0; $i <= $count; $i++) {
            switch ($i) {
                case 0:
                    $index = -1;
                    break;
                case 5:
                    break;
                case $count:
                    $index = 99;
                    break;
                default:
                    $index = $i;
            }

            if ($i == 5) {
                continue;
            }
            $channel = Dict::item('cast_channel', $index);
            $str = "<tr>";
            if ($index == -1) {
                $str .= "<th rowspan='" . ($count + 1) . "' style ='text-align:center; vertical-align:middle;'>信息费</th>";
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

            $url_arr = array('CarEmployeeAccount[channel]' => $index,
                'CarEmployeeAccount[month]' => date('Ym', strtotime($params['account_date'])),
                'CarEmployeeAccount[city_id]' => $params['city_id'],
                'CarEmployeeAccount[created]' => $params['account_date']
            );
            $select_url = Yii::app()->createUrl('/finance/account_info', $url_arr);
            $export_url = Yii::app()->createUrl('/finance/account_export', $url_arr);
            $back_status = BDailyAccountReport::model()->getDailyAccountNotStatus($params['account_date'], $index, $params['city_id']);

            if ($back_status) {
                $success_url = Yii::app()->createUrl('/finance/account_success', array(
                    'account_date' => $params['account_date'],
                    'channel' => $index,
                    'city_id' => $params['city_id'],
                    'status' => 1
                ));
                $success_like = "&nbsp;<a href='javascript:void(0);' onclick='account_success(\"$success_url\")'>对账成功</a>";
            } else {
                $success_like = "";
            }

            if ($index == -1 || $index == 99) {
                $str .= "<td>
                    &nbsp;
                </td>";
            } else {
                $str .= "<td>
                    <a href='" . $select_url . "'>查看详情</a>&nbsp;";
                if ($index == 1 || $index == 6) {
                    $str .= "";
                }else{
                    $str .= "<a href='" . $export_url . "'>导出详情</a>";
                }
                $str .= "$success_like
                </td>";
            }
            $str .= "</tr>";
            echo $str;

        }
        ?>
        </tbody>
    </table>
</div>
<pre>
与公司台账相对应公式说明：
	公司总收入 = 信息费 + 发票扣税 + 保险费 + 罚金;
	信息费来源 = 其它项之和;
</pre>
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











