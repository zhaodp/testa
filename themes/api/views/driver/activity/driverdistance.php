<?php
/**
 * 品牌部三周年司机里程查询
 * @author cuiluzhe 2014-11-03
 */
    $driverCode = isset($params['driverCode']) ? $params['driverCode'] : '';

    if( empty($driverCode) ){
        $ret = array('code' => 2 , 'data' => '' , 'message' => '请输入司机工号');
        echo json_encode($ret);return ;
    }

    $driverDistance = RDriverDistance::model()->get($driverCode);
    if($driverDistance){
	$ret = array('code' => 0 , 'data' => unserialize($driverDistance) , 'message' => '获取成功。');
        echo json_encode($ret);return ;
    }

    $driver = Driver::model()->getDriver($driverCode);
    if(!$driver || empty($driver)){
	$ret = array('code' => 2 , 'data' => '' , 'message' => '请输入正确的司机工号');
        echo json_encode($ret);return ;
    }
    if( $driver['mark'] == 3){
	$ret = array('code' => 2 , 'data' => '' , 'message' => '对不起，此工号已失效。');
        echo json_encode($ret);return ;
    }

    $tem = DriverDistanceActivity::model()->find('driver_id=:driver_id', array(':driver_id' => $driverCode));
    if(!$tem){
	$ret = array('code' => 2 , 'data' => '' , 'message' => '本活动只能查询2014年12月1日前签约的司机');
        echo json_encode($ret);return ;
    }

    $driverInfo = array();
    //$driverInfo['created'] =  date('Y年m月d日', strtotime($driver['created']));//签约时间
    $driverInfo['created'] =  date('Y年m月d日', strtotime($tem->cd));//签约时间

    $driverInfo['picture'] = $driver['picture'];//头像
    $driverInfo['name']	= $driver['name'].'师傅';
    if(!$tem->d){//根据接单日期而不是距离判断有没接过单
  	$driverInfo['total_distance'] = 0;//总里程
	$driverInfo['badge'] = 1;
	
    }else{
	$driverInfo['total_distance'] = $tem->dis;//总里程
    	if($driverInfo['total_distance'] < 1000){
	    $driverInfo['landmark'] = 0;
    	    $driverInfo['content']['0'] = '完成'.$tem->l.'次全程马拉松';
            $driverInfo['content']['1'] = '环绕钓鱼岛'.$tem->m.'圈';
            $driverInfo['content']['2'] = '往返长安街'.$tem->s.'次';
        }else if($driverInfo['total_distance'] < 10000){
	    $driverInfo['landmark'] = date('Y年m月d日', strtotime($tem->kd));//里程碑时间
            $driverInfo['content']['0'] = '地球到太空空间站距离的'.$tem->l.'倍';
            $driverInfo['content']['1'] = $tem->m.'倍台湾海峡的距离';
	    $driverInfo['content']['2'] = '特斯拉汽车充满电'.$tem->s.'次的驾驶里程';
        }else{
	    $driverInfo['landmark'] = date('Y年m月d日', strtotime($tem->kd));//里程碑时间
	    $driverInfo['content']['0'] = $tem->l.'个长江的距离';
            $driverInfo['content']['1'] = '从北京到西藏自驾游'.$tem->m.'次';
            $driverInfo['content']['2'] = '横穿撒哈拉沙漠'.$tem->s.'次';
        }
        $driverInfo['first_order_time'] = date('Y年m月d日', strtotime($tem->d));//完成第一单的时间
        $driverInfo['start_to_end'] ='从'.$tem->location_start.'到'.$tem->location_end;
        /**if($driverInfo['total_distance'] > 1000){
            $driverInfo['landmark'] = date('Y年m月d日', strtotime($tem->kd));//里程碑时间
        }**/
        if(!isset($tem->dis) || $tem->dis == NULL || $tem->dis <= 1000){
	    $driverInfo['badge'] = 1;
        }else if($tem->dis <= 5000){
	    $driverInfo['badge'] = 2;
        }else if($tem->dis <= 10000){
	    $driverInfo['badge'] = 3;	
        }else{
    	    $driverInfo['badge'] = 4;
        }
    }

    $dis = $driverInfo['total_distance'];
    $need_len = 5-strlen($dis);
    $need_str = '';
    for($i=0; $i<$need_len; $i++){
	$need_str .= '0';
    }
    $dis = $need_str.$dis;
    $driverInfo['total_distance'] = $dis;

    $ret = RDriverDistance::model()->set($driverCode, serialize($driverInfo));
    if($ret){
	$ret = array('code' => 0 , 'data' => $driverInfo , 'message' => '获取成功');
        echo json_encode($ret);return ;
    }else{
	$ret = array('code' => 2 , 'data' => '' , 'message' => '获取数据异常');
        echo json_encode($ret);return ;
    }
?>
