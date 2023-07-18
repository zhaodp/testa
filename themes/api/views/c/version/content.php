<?php
//获取App文字内容
$ret = Yii::app()->params['appContent'];
$ret['priceContent'] = '';
$return = array(
    'expireAt'=>date('Y-m-d', time()+24*3600),
    'MicBlogMessage'=>array(
        'zh'=>'亲，告诉你一个开车也能喝酒的好办法！体验一下呗！ http://wap.edaijia.cn',
        'en'=>''),
    'SMSBodyText'=>array(
        'zh'=>'亲，告诉你一个开车也能喝酒的好办法！体验一下呗！http://wap.edaijia.cn',
        'en'=>''),
    'RechargeText'=>array(
        'zh'=>'您可以通过e代驾发放的优惠券或者短信获取优惠券号码；\n一个新手机号只能使用一次优惠券；\n充值成功后，只要通过app呼叫司机使用代驾，该优惠即可立即生效；\n优惠券使用的最终解释权归e代驾所有，如有疑问请拨打4006-91-3939咨询；',
        'en'=>''),
);
echo json_encode($return);return ;