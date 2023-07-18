<?php
if (!empty($driver)) {
    renderDriverInfo($driver);
} elseif (!empty($vip)) {
    renderVipInfo($vip, $vipPhone);
} else{
    renderCustomerInfo($customerInfo, $appOrderNum, $a400OrderNum, $otherOrderNum,$appBonusCount,$commonBonusCount,$firstOrderTime);
}
?>


<?php function renderDriverInfo($driver)
{
    echo "<h3 style='padding-left:19px'>司机 $driver->name $driver->user </h3>";
}

?>

<?php function renderVipInfo($vip, $vipPhone)
{
    $vipType = '';
    switch ($vip->type) {
        case Vip::TYPE_CREDIT:
            $vipType = '（VIP客户）';
            break;
        case Vip::TYPE_FIXED:
            $vipType = '（定额客户-不可透支）';
            break;
        case Vip::TYPE_COMPENSATE:
            $vipType = '（补偿客户-不可透支）';
            break;
    }

    $name = empty($vipPhone['name']) ? $vip->name : $vipPhone['name'];
    $balance = $vip->balance + $vip->credit;
    ?>
    <h3 style="padding-left: 19px">
        <?php echo $name . $vipType ?>
        余额:<?php echo $balance ?>元
    </h3>
<?php } ?>

<?php
function renderCustomerInfo($customerInfo, $appOrderNum, $a400OrderNum, $otherOrderNum, $appBonusCount, $commonBonusCount, $firstOrderTime)
{
    echo '<h4 style="padding-left: 19px">';
    if (isset($customerInfo) && !empty($customerInfo)) {
        echo $customerInfo['name'] . " &nbsp;&nbsp;";
        if (isset($customerInfo['balance'])) {
            echo ' 余额:' . $customerInfo['balance'] . ',&nbsp;&nbsp;';
        }
    }
    echo "首次代驾：";
    if ($firstOrderTime == 0) echo "无 "."优惠券:" . 'app：' .$appBonusCount . '张 通用：' . $commonBonusCount . '张，共' . ($commonBonusCount + $appBonusCount) . '张' . "</h4>";
    else echo date('Y-m-d', $firstOrderTime) . "代驾次数：400:" . $a400OrderNum . "次、app：" . $appOrderNum . "
        次、其它" . $otherOrderNum . "次,共" . ($a400OrderNum + $appOrderNum + $otherOrderNum) . '次' . "
        优惠券:" . 'app：' . $appBonusCount . '张 通用：' .$commonBonusCount  . '张，共' . ($commonBonusCount + $appBonusCount) . '张' . "</h4>";
}

?>

