<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-8
 * Time: 下午3:56
 * auther mengtianxue
 */
$year =  isset($_GET['date_time']) ? $_GET['date_time'] : date('Y');
$a_2012 = $a_2013 = $a_2014 = '';
switch ($year) {
    case 2012:
        $a_2012 = "class='active'";
        break;
    case 2013:
        $a_2013 = "class='active'";
        break;
    case 2014:
        $a_2014 = "class='active'";
        break;
}
?>
<h1>VIP汇总表</h1>
<ul class="nav nav-tabs">
    <li <?php echo $a_2013;?>>
        <?php
        $year_url = Yii::app()->createUrl('/finance/vip_month_list',
            array('date_time' => 2013
            ));
        ?>
        <a href="<?php echo $year_url; ?>">2013年汇总</a></li>
    </li>

    <li <?php echo $a_2014;?>>
        <?php
        $year_url = Yii::app()->createUrl('/finance/vip_month_list',
            array('date_time' => 2014
            ));
        ?>
        <a href="<?php echo $year_url; ?>">2014年汇总</a></li>
    </li>
</ul>
<div class="row-fluid">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>月份</th>
            <th>期初余额</th>
            <th>本期增加</th>
            <th>本期减少</th>
            <th>期末余额</th>
            <th>是否对账成功</th>
            <th>操作</th>
        </tr>
        </thead>

        <tbody>
        <?php
        foreach ($data as $v) {
            $arr = array(
                'date_time' => $v->month_date,
            );
            $url = Yii::app()->createUrl('/finance/vip_daily_list', $arr);
            $start_time = $v->month_date . '-01';
            $start_balance = BReportDailyVipReport::model()->getDailyMoney($start_time, 0);

            if ($v->month_date != date('Y-m')) {
                $days = date('t', strtotime($v->month_date));
            } else {
                $days = date('d', strtotime("-1 day"));
            }
            $end_time = $v->month_date . '-' . $days;
            $end_balance = BReportDailyVipReport::model()->getDailyMoney($end_time, 1);

            $back_status = BReportDailyVipReport::model()->getDailyAccountNotStatus($v->month_date);
            if ($back_status) {
                $like = "是";
            } else {
                $like = "否";
            }

            echo "<tr>";
            echo "<td>$v->month_date</td>";
            echo "<td>" . number_format(doubleval($start_balance), 2) . "</td>";
            echo "<td>" . number_format(doubleval($v->add_balance), 2) . "</td>";
            echo "<td>" . number_format(doubleval($v->minus_balance), 2) . "</td>";
            echo "<td>" . number_format(doubleval($end_balance), 2) . "</td>";
            echo "<td>" . $like . "</td>";
            echo "<td><a href='" . $url . "'>查看详情</a></td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>