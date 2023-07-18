<?php
Yii::import('application.models.schema.activity.*');

$bonus_arr = array('old_user'=>'2014122601','new_user'=>'2014122600');
$ret = array(
    'code' => 0,
    'message' => '新客：好暖！<br/>20元e代驾红包已经收入囊中'
);

if (empty($params['token']) &&  empty($params['order_id'])) {
    $ret = array('code' => 4, 'message' => '访问错误');
    echo json_encode($ret);
    return;
}

if (empty($params['phone']) || !is_numeric(trim($params['phone'])) || strlen(trim($params['phone'])) != 11) {
    $ret = array('code' => 6, 'message' => '请输入正确的手机号码');
    echo json_encode($ret);
    return;
}

if(time() > strtotime('2015-01-26 00:00:00')){
    $ret = array(
        'code' => 15,
        'message' => '活动已经结束。'
    );
    echo json_encode($ret);
    return;
}

$type = '';
$token = $order_id = '';
$phone = trim($params['phone']);


if(isset($params['order_id']) && $params['order_id']){
    $type = RedPacket::TYPE_ORDER;
    $order_id = trim(Common::decrypt($params['order_id'],'order@edai%jia~!'));
    if(!is_numeric($order_id)){
        $ret = array(
            'code' => 1,
            'message' => '分享的链接错误。'
        );
        echo json_encode($ret);
        return;
    }
}

if(isset($params['token']) && $params['token']){
    $type = RedPacket::TYPE_TOKEN;
    $token = $params['token'];
    $validate = CustomerToken::model()->validateToken($token);
    if (!$validate) {
        $ret = array('code' => 1 , 'message' => '分享的链接错误。');
        echo json_encode($ret);
        return ;
    }
}

if($type === ''){
    $ret = array('code' => 4, 'message' => '访问错误');
    echo json_encode($ret);
    return false;
}


$redpacket_mod = RedPacket::model();




if($type == RedPacket::TYPE_TOKEN) {
    $checkpacket = $redpacket_mod->find('type=:type and phone = :phone',array(':type'=>$type,':phone'=>$validate['phone']));

    if($checkpacket){
        if($checkpacket->share_times < 10){
            $ret = bindBonus($phone,$bonus_arr,$type,$checkpacket->id);

            if($ret['code'] == 0 ){
                $checkpacket->share_times = $checkpacket->share_times + 1;
                $checkpacket->save();

                $prize_list = getlog($checkpacket->id);
                $ret['data']['prize_list'] = $prize_list;
                $ret['data']['pic'] = $checkpacket->qr_code;
            }else if($ret['code'] == 8){
                $prize_list = getlog($checkpacket->id);
                $ret['data']['prize_list'] = $prize_list;
                $ret['data']['pic'] = $checkpacket->qr_code;
            }

            $received_num = (int)$checkpacket->share_times; //已经领取过红包的人数。
        }
        else{
            $prize_list = getlog($checkpacket->id);
            $ret = array(
                'code' => 5,
                'message' => '您来晚了<br/>用e代驾下单给自己一个红包温暖下',
                'data'=>array(
                    'prize_list'=>$prize_list,
                    'pic'=>$checkpacket->qr_code
                )
            );
            $received_num = 10;//已经领取过红包的人数。
        }
    }
    else {
        $ret = array(
            'code' => 4,
            'message' => '分享链接错误'
        );
    }
}else{
    //检测订单id 是否存在
    $checkorderinfo = Order::model()->getOrderById($order_id);
    if($checkorderinfo == false){
        $ret = array('code' => 4, 'message' => '访问错误');
        echo json_encode($ret);
        return false;
    }
    $checkpacket = $redpacket_mod->find('type=:type and order_id = :order_id',array(':type'=>$type,':order_id'=>$order_id));
    if($checkpacket){
        if($checkpacket->share_times < 10){
            $ret = bindBonus($phone,$bonus_arr,$type,$checkpacket->id);

            if($ret['code'] == 0 ){
                $checkpacket->share_times = $checkpacket->share_times + 1;
                $checkpacket->save();
                $prize_list = getlog($checkpacket->id);
                $ret['data']['prize_list'] = $prize_list;
                $ret['data']['pic'] = $checkpacket->qr_code;
            }else if($ret['code'] == 8) {
                $prize_list = getlog($checkpacket->id);
                $ret['data']['prize_list'] = $prize_list;
                $ret['data']['pic'] = $checkpacket->qr_code;
            }
            $received_num = (int)$checkpacket->share_times; //已经领取过红包的人数。
        }
        else{
            $prize_list = getlog($checkpacket->id);
            $ret = array(
                'code' => 5,
                'message' => '您来晚了<br/>用e代驾下单给自己一个红包温暖下',
                'data'=>array(
                    'prize_list'=>$prize_list,
                    'pic'=>$checkpacket->qr_code
                )
            );
            $received_num = 10;//已经领取过红包的人数。
        }
    }
    else {
        $ret = array(
            'code' => 4,
            'message' => '分享链接错误'
        );
    }
}

if($ret['code'] == 0){
    $ret['data']['received_num'] = $received_num;
    $ret['data']['canreceive_money'] = (40 * (10 - $received_num));
}
else if($ret['code'] == 5 || $ret['code'] == 8){
    $ret['data']['received_num'] = $received_num;
    $ret['data']['canreceive_money'] = (40 * (10 - $received_num));
}

echo json_encode($ret);
return true;


function bindBonus($phone,$bonus_arr,$type,$redpacket_id){
    $redpacket_log_mod = RedPacketLog::model();
    $checkIsGetPacket = $redpacket_log_mod->find('share_type = :type and phone = :phone and rp_id = :rp_id ',array(':type'=>$type,':phone'=>$phone,':rp_id'=>$redpacket_id));
    if($checkIsGetPacket){
        $ret = array(
            'code' => 8,
            'message' => '该手机号码已经领取过红包'
        );
    }else{
        //判断新老客
        $isNewCustomer=CustomerOrderReport::model()->isNewCustomer($phone);
        if($isNewCustomer){
            $res = bindBonusReal($bonus_arr['new_user'], $phone);
            $msg_head = '好暖！';
            $money = 40;
        }else {
            $res = bindBonusReal($bonus_arr['old_user'], $phone);
            $msg_head = '暖啊！';
            $money = 20;
        }

        if($res['code'] == 0) {
            $bonus_id = $res['bind_id'];
            $array= array(
                'phone'=>$phone,
                'rp_id'=>$redpacket_id,
                'is_new_customer'=>$isNewCustomer ? 1 : 0,
                'share_type'=>$type,
                'money'=>$money,
                'bonus_id'=>$bonus_id,
                'status'=>1,
                'create_time'=>date('Y-m-d H:i:s')

            );
            $res_log = new RedPacketLog();
            $res_log->attributes = $array;
            $log_res = $res_log->save();
            $ret = array(
                'code' => 0,
                'message' => $msg_head.$money.'元红包已经收入囊中',
                'data'=>array(
                    'money'=>$money
                )

            );
        }
        else {
            $res['message'] = str_replace('优惠券','红包',$res['message']);
            $res['message'] = str_replace('绑定','领取',$res['message']);
            $ret = $res;
        }
    }

    return $ret;
}


function getlog($rp_id){
    $prize_list = RedPacketLog::model()->findAll('rp_id='.$rp_id);
    if($prize_list){
        $pattern = "/(1\d{1,2})\d\d(\d{0,3})/";
        $replacement = "\$1*****\$3";
        foreach($prize_list as $obj){
            $phone_xing = preg_replace($pattern, $replacement, $obj->phone);

            $prize[]=array('money'=>$obj->money,'phone'=>$phone_xing);
        }
        //krsort($prize);
        return $prize;
    }
    return false;
}

function bindBonusReal ($bonus_sn,$phone) {

    $bonus_library = BonusLibrary::model()->getBonusByBonus_sn($bonus_sn, 1); //1:固定码
    $params = array();
    $params['bonus_id'] = $bonus_library['bonus_id'];
    $params['bonus_sn'] = $bonus_sn;
    $params['num'] = 1;
    $params['sms'] = '';
    $params['phone'] = trim($phone);

    //添加task队列向数据中添加
    $task = array(
        'method' => 'addCustomerBonusBatch',
        'params' => $params
    );
    Queue::model()->putin($task, 'order');
}


