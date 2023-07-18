<?php
/**
 *  司机e币商城商品列表
 * @author aiguoxin
 * @version 2014-07-21
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';

if(empty($token)){
    $ret=array('code'=>2 , 'message'=>'token参数不正确!');
    echo json_encode($ret);return;
}

// //验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver===null||$driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}

$city_id=$driver->city_id;
$driver_id=$driver->driver_id;
//当天剩余产品数目
$limit_num = DriverStatus::model()->getCrownVal($city_id);

$list=array();
//获取产品列表
$flag = 1;//默认可兑换
$reason = '';
//可用e币
$total_emoney=0;
$driver_ext = DriverExt::model()->getExt($driver_id);
if($driver_ext){
    $total_emoney = $driver_ext['total_wealth'];
}

$product_list = DriverWealthProduct::model()->getProductList();
foreach ($product_list as $product) {
    //皇冠判断
    if($product['type'] == DriverWealthProduct::PRODUCT_CROWN_TYPE){
        //判断是否已经是皇冠
        $driver_recommand=$driver->recommand;
        if($driver_recommand != null && !empty($driver_recommand) 
            && $driver_recommand['end_time'] >= date("Y-m-d H:i:s")){
            $flag = 0;
            $reason ='您当前已经处于皇冠状态请稍后兑换';
        }
        //判断商品数量
        if($limit_num < 1){
            $flag = 0;
            $reason = '当前城市无剩余皇冠，请明天再来兑换';
        }
        //判断e币是否足够
        if($total_emoney < $product['wealth']){
            $flag = 0;
            $reason = '您的e币不足，请稍候再兑换';
        }
    }
    $list[]=array(
        'id'=>$product['id'],
        'name'=>$product['name'],
        'emoney'=>$product['wealth'],
        'url'=>$product['url'],
        'introduction'=>$product['introduction'],
        'number'=>$limit_num,
        'flag'=>$flag,
        'reason'=>$reason,
    );

}


//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
	'list'=>$list);
echo json_encode($ret);return;


