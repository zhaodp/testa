<?php
//优化代码结构 by sunhongjing at 2013-04-22
//需要写清楚注释，增加缓存，封装业务逻辑，写库走队列 add by sunhongjing at 2013-5-19
$imei = empty($params['imei']) ? '' : trim($params['imei']);
$sim =  empty($params['sim']) ? '' : trim($params['sim']);
$driver_id = empty($params['user']) ? '' : strtoupper(trim($params['user']));
$passwd = empty($params['passwd']) ? '' : trim($params['passwd']);

$sim_new=isset($params['sim_new'])&&!empty($params['sim_new']) ? trim($params['sim_new']) : "";

$app_ver=isset($params['app_ver'])&&!empty($params['app_ver']) ? trim($params['app_ver']) : 0;

$variant=isset($params['variant'])&&!empty($params['variant']) ? strtolower(trim($params['variant'])) : "";

if( empty($imei) || empty($sim) || empty($driver_id) || empty($passwd) ){
	EdjLog::info("driver.login params is ".serialize($params)." and some param empty");
	$ret = array ( 'code'=>1, 'message'=>'用户名或密码错误');
	echo json_encode($ret);return;
}

//是否是禁用的司机端版本
if(isset(Yii::app()->params['disable_driver_versions'])
    && is_array(Yii::app()->params['disable_driver_versions'])
    && in_array($app_ver, Yii::app()->params['disable_driver_versions'])) {

    EdjLog::info("driver.login|disable_driver_versions|".$driver_id."|".$app_ver);
    $ret = array(
        'code' => 10,
        'message' => '当前版本司机端已经停止服务，请前往官网下载更新使用。'
    );
    echo json_encode($ret);
    return;
}

if($app_ver == 0 ){
    //记录拒不升级的司机
    $task=array(
        'method'=>'push_no_update_version',
        'params'=>array(
            'driver_id'=>$driver_id,
        ),
    );
    //Queue::model()->putin($task,'task');
    //$ret = array (
    //    'code'=>1,
    //    'message'=>"当前版本不可用,请通过wap.edaijia.cn下载新版本或联系司机管理部",
        //'url'=>$r['url'],
    //);
    //echo json_encode($ret);return;
}

//强升版本
$r = Common::getCheckUpdateVersion($variant,$app_ver,$driver_id);

if($r['flag']){
    $ret = array (
        'code'=>3,
        'message'=>"当前版本不可用,请下载新版本",
        'url'=>$r['url'],
    );
    echo json_encode($ret);return;
}


//按配置小范围强升版本
$r = Common::checkUpdateDrivers($app_ver,$driver_id);

if($r['flag']){
    $ret = array (
        'code'=>3,
        'message'=>"当前版本不可用,请下载新版本",
        'url'=>$r['url'],
    );
    echo json_encode($ret);return;
}



if ( 'V'==substr($driver_id, 0, 1) ){
	$ret = array ( 'code'=>1, 'message'=>'访客');
	echo json_encode($ret);return;
	/*
	 * TODO: 访客登录 新建临时用户于表Driver driverPosition
	 * 新建临时用户缓存
	 * 
	 */
}

$cityPrefix = '';
$city = substr($driver_id, 0, 2);
	
$cityPrefixArray = Dict::items('city_prefix');
	
foreach($cityPrefixArray as $value) {
	if (strtoupper($value)==$city) {
		$cityPrefix = $value;
		break;
	}
}
	
if ( empty($cityPrefix) )  {
	$ret = array ('code'=>1, 'message'=>'司机城市错误');
	echo json_encode($ret);return;
} 

$driver = Driver::getProfile($driver_id);
if ( empty($driver) ) {
	EdjLog::info("no such driver $driver_id");
	$ret = array ('code'=>1, 'message'=>'司机工号错误');
	echo json_encode($ret);return;
}

if ($passwd!=md5($driver->password)) {
	EdjLog::info("driver password is wrong driver_id $driver_id");
	$ret = array ('code'=>1, 'message'=>'用户名或密码错误');
	echo json_encode($ret);return;
}

if ($driver->mark == Driver::MARK_LEAVE) {
    $ret = array ('code'=>1, 'message'=>'您已经解约。');
    echo json_encode($ret);return;
}

if ($driver->mark == Driver::MARK_CHANGE) {
    $ret = array ('code'=>1, 'message'=>'您的手机号已经换了，不能这个手机上登陆。');
    echo json_encode($ret);return;
}

$block_at = isset($driver->block_at) ? $driver->block_at : 0;
$block_mt = isset($driver->block_mt) ? $driver->block_mt : 0;

if ($driver->mark == Driver::MARK_DISNABLE && $block_mt != 0){//兼容2.4.0版本,之前版本都不能登陆
    if($app_ver<'2.4.0' || $block_mt == 1 || $block_mt == 3){
	   $ret = array ('code'=>1, 'message'=>'您已经被屏蔽，如有疑问，请联系品监。');
	   echo json_encode($ret);return;
    }
}

if ($driver->imei!=$imei) {
	EdjLog::info('imei not match driver imei is '.$driver->imei.' imei is '.$imei);
	$ret = array ( 'code'=>2, 'message'=>'用户未激活或手机信息变更，请联系司机管理部');
	echo json_encode($ret);return;
}


//保存新的sim卡标识并刷新缓存 BY zhanglimin 2013-08-19
if($sim != $sim_new && !empty($sim_new)){

        //添加task队列更新数据库
        $task=array(
            'method'=>'update_driver_phone_sim',
            'params'=>array(
                'driver_id'=>$driver_id,
                'sim'=>$sim,
                'sim_new'=>$sim_new,
                'imei'=>$imei,
            ),
        );
        Queue::model()->putin($task,'task');

        $sim = $sim_new;
    
}

$driverPhone = DriverPhone::model()->validateDriverPhone($imei, $sim, $driver_id);
if (!$driverPhone) {
    EdjLog::info('driver phone for driver_id '.$driver_id.' imei '.$imei.' sim '.$sim.' fail');
    $ret = array ('code'=>2, 'message'=>'用户未激活或手机信息变更，请联系司机管理部');
    echo json_encode($ret);return;
}


//生成AuthToken
$token = DriverToken::model()->createAuthtoken($driver_id, $imei, $sim);
if(empty($token)){
    $ret = array ('code'=>1, 'message'=>'抱歉，您的身份已失效,请重新登录');
    echo json_encode($ret);return;
}
//将星级改为new_level BY AndyCong 2013-06-05
if (empty($driver->level)) {
	$new_level = 0;
} else {
	$new_level = $driver->level;
}
//将星级改为new_level BY AndyCong 2013-06-05 END


$driver_info = Helper::foramt_driver_detail( $driver_id,'',0,'driver' );
$pic = "";
$crown  =  $crown_end_time  = 0 ;
if(!empty($driver_info)){
    $pic = $driver_info['picture_small'];
    $crown = $driver_info['recommand'];
    $crown_end_time = $driver_info['recommand_end_time'];
}

// add by aiguoxin for driver.block method
$driver_block = DriverStatus::model()->get($driver_id);
if(!empty($driver_block)){
    //如果缓存没有，直接返回0，为了兼容之前的代码
    $block_at = $driver_block->block_at;
    $block_at = empty($block_at) ? 0 : intval($block_at);
}

//add by aiguoxin
$city_name='';
$open_city = RCityList::model()->getOpenCityList();
foreach($open_city as $key=>$value){
    if($key == $driver->city_id){
        $city_name = $open_city[$key];
    }
}


$ret = array (	'code'=>0,
                'block'=>$block_at,
                'mark' =>$driver->mark,
                'block_mt' =>$block_mt,
                'block_at' =>$block_at,
				'user'=>strtoupper($driver->user),
				'phone'=>$driver->phone, 
				'name'=>$driver->name, 
				'is_bind'=>$driverPhone->is_bind,
				'token'=>$token,
                'city_name'=>$city_name,
                'city_id'=>$driver->city_id,
                'level'=>$new_level, //add zhanglimin 2013-05-25
                                     //modify AndyCong 2013-06-05
                'pic'=>$pic,    //司机头像小图片  add zhanglimin 2013-10-28
                'crown'=>$crown,   //是否是皇冠  add zhanglimin 2013-10-28
                'crown_end_time'=>$crown_end_time ,//皇冠结束时间 add zhanglimin 2013-10-28
                'driver_age'=>$driver->year,
                'entry_time'=>strtotime($driver->created),
                'license'=>$driver->id_card,
				'message'=>'登录成功'
);

echo json_encode($ret);
