<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-8
 * Time: 下午3:56
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

    <li class="active"><a href="javascript:void(0);"><?php echo date('m', strtotime($date_time)); ?>月汇总</a></li>
</ul>
<div class="row-fluid">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>日期</th>
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
                'ReportFsVipRp[city_id]' => 0,
                'ReportFsVipRp[account_date]' => $v->daily_date
            );
            $url = Yii::app()->createUrl('/finance/daily_vip', $arr);

            $back_status = BDailyAccountReport::model()->getDailyAccountNotStatus($v->daily_date);
            if ($back_status) {
                $like = "否";
            } else {
                $like = "是";
            }
            $start_balance = doubleval($v->start_balance);
            $add_balance = doubleval($v->add_balance);
            $minus_balance = doubleval($v->minus_balance);
            $end_balance = doubleval($v->end_balance);

            echo "<tr>";
            echo "<td>$v->daily_date</td>";
            echo "<td>" . number_format($start_balance, 2) . "</td>";
            echo "<td>" . number_format($add_balance, 2) . "</td>";
            echo "<td>" . number_format($minus_balance, 2) . "</td>";
            echo "<td>" . number_format($end_balance, 2) . "</td>";
            echo "<td>" . $like . "</td>";
            echo "<td><a href='" . $url . "'>查看详情</a></td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>