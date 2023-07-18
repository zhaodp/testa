<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-16
 * Time: 下午12:46
 * To change this template use File | Settings | File Templates.
 */
$this->pageTitle = '账单明细';

$now_month = idate('m') - 1;
$listMonth = array();
for($i = 1; $i <= $now_month; $i++){
    $listMonth[$i] = $i."月";
}

?>
<div id="bill_stats">
<h1><?php echo $this->pageTitle;?></h1>

<form class="form-horizontal">
    <div class="control-group"">
        <?php echo CHtml::label('选择月份', 'Bill[listMonth]', array('class' => 'control-label', 'style' => 'width:70px;margin-right: 10px;'));?>
        <div class="controls" style="margin-left: 10px;">
            <?php echo CHtml::dropDownList('Bill[listMonth]', intval($month), $listMonth);?>
        </div>
    </div>
</form>

<div class="span12" style="margin-left: 0px;">
    <h4>基本信息</h4>
    <div class="row-fluid">
        <div class="span3">商家名称：<?php echo $partnerInfo['name']?></div>
        <div class="span3">联系人姓名：<?php echo $partnerInfo['contact']?></div>
        <div class="span3">联系电话：<?php echo $partnerInfo['phone']?></div>
    </div>
    <div class="row-fluid">
        <div class="span9">商家账单地址：<?php echo $partnerInfo['address']?></div>
    </div>
</div>
<div class="span12" style="margin-left: 0px;">
    <h4>结算信息</h4>
    <div class="row-fluid">
        <div class="span3"><?php echo $month?>月总报单：<?php
        echo $arrayData == '' ? 0 : (isset($arrayData[0]['count_complete']) ? $arrayData[0]['count_complete'] : 0);?> 单</div>
        <?php if($partnerInfo['pay_sort'] == 3):?>
            <div class="span3"><?php echo $month?>月总消费：
            <?php
            echo $arrayData == '' ? 0 : (isset($arrayData[0]['count_fee']) ? $arrayData[0]['count_fee'] : 0);?> 元
            </div>
            <div class="span3">账户余额：<?php echo Partner::model()->getPartnerName($channel, 1)?> 元</div>
        <?php endif;?>
        <?php if($partnerInfo['pay_sort'] == 2):?>
            <div class="span3"><?php echo $month?>月使用优惠券数量：
            <?php
                $BonusInfo = CustomerBonus::model()->getBonusUsedSummary($partnerInfo['bonus_phone'], $partnerInfo['bonus_sn'],strtotime($time_arr[0]), strtotime($time_arr[1]));
                echo $BonusInfo['used_num'] ? $BonusInfo['used_num'] : 0;
            ?>
            </div>
        <?php endif;?>
        <?php if($partnerInfo['pay_sort'] == 1):?>
            <div class="span3">每单分成金额：<?php echo intval($partnerInfo['sharing_amount']);?>元</div>
            <div class="span3">共分成金额：
            <?php
            echo $arrayData == '' ? 0 : (isset($arrayData[0]['count_complete']) ? $arrayData[0]['count_complete'] * intval($partnerInfo['sharing_amount']) : 0);?> 元

            </div>
        <?php endif;?>
    </div>
</div>
<div class="span12" style="margin-left: 0px;">
    <h4>结算明细</h4>
<?php
    echo $this->renderPartial('order_list', array('data'=>$dataProvider, 'price_visible'=>$price_visible));
?>
</div>
</div>
