<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-27
 * Time: 下午1:49
 * auther mengtianxue
 */
$year =  isset($_GET['year']) ? $_GET['year'] : date('Y');
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

<h1>VIP销费汇总表</h1>
<ul class="nav nav-tabs">
    <li <?php echo $a_2012;?>>
        <?php
        $year_2012 = Yii::app()->createUrl('/finance/vip_trade_info',
            array('year' => '2012'));
        ?>
        <a href="<?php echo $year_2012; ?>">2012年Vip汇总</a>
    </li>


    <li <?php echo $a_2013;?>>
        <?php
        $year_2013 = Yii::app()->createUrl('/finance/vip_trade_info',
            array('year' => '2013'));
        ?>
        <a href="<?php echo $year_2013; ?>">2013年Vip汇总</a>
    </li>

    <li <?php echo $a_2014;?>>
        <?php
        $year_2014 = Yii::app()->createUrl('/finance/vip_trade_info',
            array('year' => '2014'));
        ?>
        <a href="<?php echo $year_2014; ?>">2014年Vip汇总</a>
    </li>

</ul>

<div class="row-fluid">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>月份</th>
            <th>VIP返现</th>
            <th>信息费</th>
            <th>保险费</th>
            <th>发票扣税</th>
            <th>劳务费</th>
            <th>操作</th>
        </tr>
        </thead>

        <tbody>
        <?php
        foreach ($data as $k => $v) {
            $month = date('Y-m', strtotime($k));
            echo "<tr>";
            echo "<td>" . $month . "</td>";
            echo "<td>" . number_format(doubleval($v['amount']), 2) . "</td>";
            echo "<td>" . number_format(doubleval($v['cast']), 2) . "</td>";
            echo "<td>" . number_format(doubleval($v['insurance']), 2) . "</td>";
            echo "<td>" . number_format(doubleval($v['Invoice_money']), 2) . "</td>";
            echo "<td>" . number_format(doubleval($v['balance']), 2) . "</td>";
            echo "<td> <a href='" . Yii::app()->createUrl('/finance/vip_group_driver',
                    array('month' => $month)) . "'>当月司机收入详情</a></td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>
