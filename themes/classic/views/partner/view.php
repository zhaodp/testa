<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-10-23
 * Time: 下午1:16
 * To change this template use File | Settings | File Templates.
 */
?>
<div class="container-fluid">
    <h1>商家详情</h1>
    <div class="row-fluid">
        <div class="span6">
            <!--Sidebar content-->
            <legend>基本信息</legend>
            <dl class="dl-horizontal">
                <dt>城市</dt>
                <dd><?php echo Dict::model()->item("city", $model->city) ?></dd>

                <dt>商家名称</dt>
                <dd><?php echo $model->name; ?></dd>

                <dt>商家联系人</dt>
                <dd><?php echo $model->contact;?></dd>

                <dt>坐席数量</dt>
                <dd><?php echo $model->seat_number;?></dd>
                <dt>账单地址</dt>
                <dd><?php echo $model->address ? $model->address : '&nbsp;'; ?></dd>

                <dt>登录地址</dt>
                <dd><?php echo $loginUrl; ?></dd>

                <dt>是否给用户发送短信</dt>
                <dd><?php echo $model->send_sms ? '是' : '否'; ?></dd>

                <dt>是否显示订单备注</dt>
                <dd><?php echo $model->remark ? '是' : '否'; ?></dd>

                <dt>LOGO</dt>
                <dd>
                    <?php if ($model->logo) {?>
                        <img src="<?php echo $model->logo;?>" />
                    <?php } else { ?>
                    未设置
                    <?php }?>
                </dd>
            </dl>
        </div>

    </div>

    <div class="row-fluid">
        <div class="span6">
            <!--Body content-->
            <legend>配置信息</legend>
            <dl class="dl-horizontal">
                <dt>客户称呼</dt>
                <dd><?php echo $model->sms_call;?></dd>

                <dt><?php echo $model->pay_sort == Partner::PAY_SORT_DIVIDED ? '报单分成' : '预付费';?></dt>
                <dd><?php echo $model->pay_sort == Partner::PAY_SORT_DIVIDED ? '每正常完成一单，分成'.$model->sharing_amount.'元。' : ($model->pay_sort == Partner::PAY_SORT_BONUS ? '优惠劵'.$model->bonus_sn.'，优惠券绑定电话'.$model->bonus_phone.'。' : 'VIP账户 '.$model->vip_card.'，余额'.Vip::model()->getBalance($model->vip_card).'元。'); ?></dd>
            </dl>
        </div>
    </div>
</div>