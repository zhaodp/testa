<?php
/**
 * 司机端根据电话查询客户信息
 */

$phone = $params['phone'];
$imei = $params['imei'];

//检验imei
if (Driver::validateImei($imei)) {
	$customer = null;
	
	$criteria = new CDbCriteria();
	
	$criteria->select = 'name,phone';
	$criteria->condition = 'phone=:phone';
	$criteria->params = array (
		':phone'=>$phone);
	
	$model = CustomerMain::model()->find($criteria);
	if ($model) {
		$customer = array('code'=>0,'name'=>$model->name);
		//如果客户信息存在，检查是否VIP
		$vip = UserVip::model()->getByPhone($phone);
		if($vip){
			$vip = array('id'=>$vip->id,'name'=>$vip->name,'remain'=>$vip->remain);
			$customer['vip'] = $vip;
		}
	} else {
		$criteria = new CDbCriteria();
		$criteria->select = 'CONCAT(user, name) AS name';
		
		$criteria->compare('phone', $phone);
		$criteria->compare('ext_phone', $phone, TRUE, 'OR');
		
		$driver = Driver::model()->find($criteria);
		if ($driver){
			$customer = array('code'=>1,'name'=>$driver->name);
		} else {
			$customer = array('code'=>2001,'message'=>'没有找到客户信息');
		}
	}
	echo json_encode($customer);
}

