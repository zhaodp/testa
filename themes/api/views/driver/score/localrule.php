<?php
/**
 * 司机端代驾分规则 标准规则，每个城市相同  
 * @author aiguoxin
 * @version 2014-05-28
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? $params['token'] : '';
$lng = $params['longitude'];
$lat = $params['latitude'];

if (empty($token) || empty($lng) || empty($lat)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

//验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver===null||$driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}

//find city
$cityName = GPS::model()->getCityByBaiduGPS($lng,$lat);

$citys = Dict::items('city');
$cityId = 0;
foreach($citys as $key=>$value) {
	if ($value==$cityName){
		$cityId = $key;
		break;
	}
}
$driver_id = $driver->driver_id;
            
//test
// $cityId=1; 
// $driver_id='BJ9030';           


$list = array();
//find city rule by config
$ruleArray=Common::checkOpenScoreCity($cityId,'rule');
if(empty($ruleArray)){
	$ret=array('code'=>2 , 
	'message'=>'can not find local city data',
	'list'=>$list
	);
	echo json_encode($ret);return;
}


$rank = '100%';
$data= 0.0000;

foreach($ruleArray as $k=>$val){

	$type = $val['type'];
	if($type=='0'){//reject
		$res = DriverInspireData::model()->getRejectRateByDriverId($driver_id);

		if($res){
			$data = $res['reject_rate'];
			$rank = $res['ranking'];
		}
	}elseif ($type == '1') { //cancel
		$res = DriverInspireData::model()->getCancelRateByDriverId($driver_id);
		
		if($res){
			$data = $res['cancel_rate'];
			$rank = $res['ranking'];
		}
	}

    $list[] = array(
    	'norm'=>$val['norm'],
    	'mark_norm'=>$val['mark_norm'],
    	'rank'=>$rank,
    	'data'=>$data,
    );
}


//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
	'list'=>$list
	);
echo json_encode($ret);return;
