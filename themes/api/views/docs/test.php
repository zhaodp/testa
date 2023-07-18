<?php
$name = strtoupper('bj9000');
$password = md5('8866');
$appkey = '20000001';
$imei = '353419036320567';
$imeis = '"353419036320567","353419036328925","353419036321599"';

$params = array (
	'ver'=>'2', 
	'ac'=>'list', 
	'appkey'=>$appkey, 
	'imeis'=>$imeis);
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
?>

<span class="nav-header">获取多个司机信息</span>
<div><?php echo Yii::app()->createUrl('api/driver', $params);?></div>



<?php 
echo '记录司机track<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'track', 
	'appkey'=>$appkey, 
	'latitude'=>'39.951679', 
	'longitude'=>'116.43016', 
	'status'=>DriverPosition::POSITION_IDLE, 
	'token'=>'4a5c29a254378e5f2313a3e3f0edfcf8', 
	'street'=>'三元桥');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/driver', $params);
echo '<br/>';

//register
echo '手机注册<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'register', 
	'appkey'=>$appkey, 
	'imei'=>'354707041227317', 
	'phone'=>'190999900000');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/driver', $params);
echo '<br/>';

echo '司机登录<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'login', 
	'appkey'=>$appkey, 
	'imei'=>'353419036320567', 
	'phone'=>'13810349756');

$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/driver', $params);
echo '<br/>';
//测试登录
//$ret = Driver::authenticate($name, $password, $imei);
//print_r($ret);


echo '查询客户信息<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'info', 
	'appkey'=>$appkey, 
	'phone'=>'13011192513', 
	'imei'=>'353419036321599');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/customer', $params);
echo '<br/>';

echo '接收订单预约<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'confirm', 
	'appkey'=>$appkey, 
	'phone'=>'13810349756', 
	'token'=>'1db69b14a332d069abd7744b04da1ef4', 
	'call_time'=>1342178251, 
	'booking_time'=>1342181880, 
	'latitude'=>'39951679', 
	'longitude'=>'11643016', 
	'street'=>'北京市东城区后永康胡同一巷4号');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/order', $params);
echo '<br/>';
//www.edaijia.cn/v2/index.php?r=api/order&ac=confirm&appkey=90000001&booking_time=1342181880
//&call_time=1342178251&latitude=39.951679&longitude=116.43016&phone=13810349756&
//street=北京市东城区后永康胡同一巷4号&token=1db69b14a332d069abd7744b04da1ef4&ver=2&sig=4e383b70be2a8ca5e1ec00d062e1aacc


echo '代驾开始<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'start', 
	'appkey'=>$appkey, 
	'token'=>'7b545fc2f55664aee419829928f24909', 
	'order_id'=>90549, 
	'latitude'=>'139', 
	'longitude'=>'26', 
	'street'=>'三元桥');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/order', $params);
echo '<br/>';

echo '代驾结束<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'end', 
	'appkey'=>'90000001', 
	'name'=>'测试代驾结束', 
	'token'=>'4a5c29a254378e5f2313a3e3f0edfcf8', 
	'distance'=>'59', 
	'order_number'=>'ab9999999999', 
	'order_id'=>'91602', 
	'wait_before'=>'125', 
	'wait_on'=>'45', 
	'latitude'=>'39.951679', 
	'longitude'=>'116.43016', 
	'street'=>'北京市东城区后永康胡同一巷4号');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/order', $params);
echo '<br/>';

echo '订单取消<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'cancel', 
	'appkey'=>$appkey, 
	'order_id'=>90548, 
	'token'=>'7b545fc2f55664aee419829928f24909', 
	'log'=>'测试取消', 
	'latitude'=>'139', 
	'longitude'=>'26', 
	'street'=>'三元桥');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/order', $params);
echo '<br/>';

echo '获取个人订单统计信息 当日订单<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'query', 
	'appkey'=>$appkey, 
	'token'=>'7b545fc2f55664aee419829928f24909', 
	'range'=>'today');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/order', $params);
echo '<br/>';
echo '获取个人订单统计信息 昨日订单<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'query', 
	'appkey'=>$appkey, 
	'token'=>'7b545fc2f55664aee419829928f24909', 
	'range'=>'yestoday');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/order', $params);
echo '<br/>';
echo '获取个人订单统计信息 本周订单<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'query', 
	'appkey'=>$appkey, 
	'token'=>'7b545fc2f55664aee419829928f24909', 
	'range'=>'week');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/order', $params);
echo '<br/>';
echo '获取个人订单统计信息 本月订单<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'query', 
	'appkey'=>$appkey, 
	'token'=>'7b545fc2f55664aee419829928f24909', 
	'range'=>'month');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/order', $params);
echo '<br/>';

echo '获取周边的司机<br/>';
$params = array (
	'ver'=>'2', 
	'ac'=>'request', 
	'appkey'=>$appkey, 
	'uuid'=>'3ff3b4aed8749843eedcbbc6ca45d21b78ebfd0b', 
	'longitude'=>'116.1825', 
	'latitude'=>'39.5834');
$sig = Api::createSigV2($params, $appkey);
$params['sig'] = $sig;
echo Yii::app()->createUrl('api/driver', $params);
echo '<br/>';

?>
