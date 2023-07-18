<?php
/**
 * 客户端API:open.customer.activity 获取本城市最新市场活动 
 */
//MarketingActivityRedis::model()->clearCache();
//echo 'over';return;

$city = isset($params['city']) ? trim($params['city']) : '';
$phone = isset($params['phone']) ? trim($params['phone']) : '';
$version = isset($params['app_ver']) ? trim($params['app_ver']) : '';
$platform  = isset($params['platform']) ? trim($params['platform']) : '3';
$token = isset($params['token'])? trim($params['token']) : '';
//参数有效性验证
if (empty($city)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}
//验证是新客户还是老客户
$customer = '3';//不登陆显示不限
if(!empty($phone)){//已登陆
	$key = 'activity_'.$phone;
	$cache = Yii::app()->cache;
	if($cache->get($key)!=null){
		$customer = '2';
	}else{
		$count = Order::model()->getOrderCountByCustomerPhone($phone);
		if($count>0){
			$cache->set($key,'1');
                	$customer = '2';
        	}else{
                	$customer = '1';
        	}
	}
}
//根据城市名字获取城市id
$city_id = 0;
$cities = Dict::items('city');
foreach($cities as $code => $name){
	if($name == $city){
		$city_id = $code; 
		break;
	}
}

if($city_id==0){
    $ret = array(
        'code' => 3,
        'message' => '城市不存在'
    );
    echo json_encode($ret);
    return; 
}
$values=MarketingActivityRedis::model()->get($city_id);
if(!isset($values)||empty($values)){//缓存失效,查询数据库 获取最新数据 并放到缓存中
	$dbvalues = MarketingActivity::model()->getActivitiesByCityId($city_id);
	if(isset($dbvalues)&&!empty($dbvalues)){
		foreach($dbvalues as $v){
			$marketingActivity = new MarketingActivity();
			$marketingActivity->attributes = $v;
			$marketingActivity->city_ids = $city_id;
			$marketingActivity->id = $v['id'];
			MarketingActivityRedis::model()->set($marketingActivity);
		}
		$values=MarketingActivityRedis::model()->get($city_id);
	}
}

$current;
if(isset($values)){
	foreach($values as $value){
		$v=unserialize($value);
		if(($v->platform==$platform||$v->platform == '3') && ($v->customer==$customer||$v->customer=='3') && ($v->version==$version||$v->version=='')){
			if($v->endtime<=date('Y-m-d H:i:s',time())){
                                continue;
                        }
			if($v->begintime<=date('Y-m-d H:i:s',time()) && $v->endtime>date('Y-m-d H:i:s',time())){
				$current=$v;
				break;
			}
			if(!isset($current)){
                        	$current = $v;
                        	continue;
                	}	
			if($v->begintime<$current->begintime){
				$current=$v;
			}	
		}

	}
}
if(isset($current)){
	$ret = array(
              'code' => 0,
              'title' => $current->title,
              'url' => $current->url,
              'message' => '成功'
             );
             echo json_encode($ret);
             return;
}
$ret = array(
        'code' => 4,
        'message' => '本城市暂无活动'
    );
    echo json_encode($ret);
    return;
