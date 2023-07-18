<?php
/**
 *  商品兑换
 * @author aiguoxin
 * @version 2014-07-21
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$productId = isset($params['productId']) ? trim($params['productId']) : '';

if(empty($token)){
    $ret=array('code'=>2 , 'message'=>'token参数不正确!');
    echo json_encode($ret);return;
}

if(empty($productId) || $productId < 1){
    $ret=array('code'=>2 , 'message'=>'productId参数不正确!');
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
$limit_num = DriverStatus::model()->getCrownVal($city_id);

/*******兑换********/
//判断司机e币够不够
$product = DriverWealthProduct::model()->getProduct($productId);
if(empty($product)){
    EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'找不到对应商品');
    $ret=array('code'=>2 , 
    'message'=>'找不到对应商品');
    echo json_encode($ret);return;
}

if($limit_num<1){
    EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'商品已经兑换完');
     $ret=array('code'=>2 , 
    'message'=>'商品已经兑换完');
    echo json_encode($ret);return;
}
$driver_ext = DriverExt::model()->getExt($driver_id);
if(empty($driver_ext)){
    EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'找不到司机信息');
    $ret=array('code'=>2 , 
    'message'=>'找不到司机信息');
    echo json_encode($ret);return;
}
if($driver_ext['total_wealth']<$product['wealth']){
    EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'剩余e不够兑换');
    $ret=array('code'=>2 , 
    'message'=>'剩余e不够兑换');
    echo json_encode($ret);return;
}

//是皇冠的不能兑换
$driver_recommand=$driver->recommand;
if($driver_recommand != null && !empty($driver_recommand) 
            && $driver_recommand['end_time'] >= date("Y-m-d H:i:s")){
     EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'兑换失败，您已经是皇冠');
   $ret=array('code'=>2 , 
    'message'=>'兑换失败，您已经是皇冠');
    echo json_encode($ret);return; 
}



$data = array(
            'product' => $product,
            'city_id' => $city_id,
            'driver_id'=>$driver_id,
            'token'=>$token,
        );
//添加task队列更新数据库
$task=array(
    'method'=>'e_shop_charge',
    'params'=>$data,
);
Queue::model()->putin($task,'default');




//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'兑换成功，从现在起至'.date("m月d日 H:i分",strtotime("+1 day")).'，您将保持皇冠状态',
    'total'=>($driver_ext['total_wealth']-$product['wealth']),
    );
EdjLog::info('driver_id='.$driver_id.',productId='.$productId.'兑换成功');
echo json_encode($ret);return;
