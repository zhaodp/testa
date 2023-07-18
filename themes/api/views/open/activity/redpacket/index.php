<?php
Yii::import('application.models.schema.activity.*');
Yii::import('application.extensions.qrcode.*');
$url = 'http://h5.edaijia.cn/redpack/index.html?';
$ret = array(
    'code' => 2,
    'message' => '您已绑定手机号,<br/>活动只能参加一次'
);

if (empty($params['token']) &&  empty($params['order_id'])) {
    $ret = array('code' => 4, 'message' => '访问错误');
    echo json_encode($ret);
    return;
}
$type = '';
$token = $order_id = '';

if(time() > strtotime('2015-01-26 00:00:00')){
    $ret = array(
        'code' => 15,
        'message' => '活动已经结束。'
    );
    echo json_encode($ret);
    return;
}

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
$qrCode = '';
if($type == RedPacket::TYPE_TOKEN){
    $url.='token='.$token;
    $checkpacket = $redpacket_mod->find('type=:type and phone = :phone',array(':type'=>$type,':phone'=>$validate['phone']));
    if($checkpacket){
        $qrCode = $checkpacket->qr_code;
        if($checkpacket->share_times < 10){
            $ret = array(
                'code' => 0,
                'message' => '可以领取红包'
            );
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
        $qr_name = time().'.jpg';
        $qrCode = create_qrcode($url,'/tmp/qr_tmp'.$qr_name);
        $array = array(
            'token'=>$token,
            'phone'=>$validate['phone'],
            'type'=>$type,
            'qr_code'=>$qrCode,
            'share_times'=>0,
            'create_time'=>date('Y-m-d H:i:s')
        );
        $mod = new RedPacket();
        $mod->attributes = $array;
        $mod->save();
        $ret = array(
            'code' => 0,
            'message' => '可以领取红包'
        );
        $received_num = 0 ;//已经领取过红包的人数。
    }
}else {
    $url.='order_id='.$params['order_id'];
    $checkpacket = $redpacket_mod->find('type=:type and order_id = :order_id',array(':type'=>$type,':order_id'=>$order_id));
    if($checkpacket){
        $qrCode = $checkpacket->qr_code;
        if($checkpacket->share_times < 10){
            $ret = array(
                'code' => 0,
                'message' => '可以领取红包'
            );
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
            $received_num = 10; //已经领取过红包的人数。
        }
    }
    else {
        $qr_name = time().'.jpg';
        $qrCode = create_qrcode($url,'/tmp/qr_tmp'.$qr_name);
        $array = array(
            'order_id'=>$order_id,
            'type'=>$type,
            'qr_code'=>$qrCode,
            'share_times'=>0,
            'create_time'=>date('Y-m-d H:i:s')
        );
        $mod = new RedPacket();
        $mod->attributes = $array;
        $mod->save();
        $ret = array(
            'code' => 0,
            'message' => '可以领取红包'
        );
        $received_num = 0; //已经领取过红包的人数。
    }
}
if($ret['code'] == 0){
    $ret['data']['pic'] = $qrCode;
    $ret['data']['received_num'] = $received_num;
    $ret['data']['canreceive_money'] = (40 * (10 - $received_num));


}
elseif($ret['code'] == 5){
    $ret['data']['received_num'] = $received_num;
    $ret['data']['canreceive_money'] = (40 * (10 - $received_num));
}
echo json_encode($ret);
return true;


function create_qrcode($url,$tmp_file_name){
    $time = time();
    $pic_url = '';

    $res = QRcode::png($url, $tmp_file_name, 'H', 8, 2);
    if(file_exists($tmp_file_name)){
        $bucketname =  'edriver';
        $base_path = 'activity/redPacket';
        $img_name = $time.'.jpg';

        $upload_model = new UpyunUpload($bucketname);
        $is_upload = $upload_model->uploadFile($tmp_file_name, $base_path, $img_name);
        $is_upload['img_name'] = $img_name;
        if (is_array($is_upload) && count($is_upload)) {
            $pic_url = 'http://pic.edaijia.cn/'.$base_path.'/'.$img_name;
        }



    }
    return $pic_url;
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