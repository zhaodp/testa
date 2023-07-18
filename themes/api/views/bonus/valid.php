<?php
/**
 * 
 * 校验优惠码是否有效
 * @param int $id
 */
	//header('Content-Type: application/x-json; charset=utf-8');
//if (Yii::app()->request->isAjaxRequest) {
	$bonus_code = (isset($_GET['bonus_code'])) ? $_GET['bonus_code'] : null;

	if ($bonus_code==null) {
		echo '';
	}else{

		//如果$bonus_code是5位，是单独为微信准备的优惠码，计算一个校验位
		if(preg_match('%\d{5}%', $bonus_code)){
			$bonus_code .= Helper::CheckCode($bonus_code);
		}
		
		echo BonusType::validCode($bonus_code);
		//echo BonusType::validCode($bonus_code);
	}
	
	//print_r(json_encode($vip->attributes,true));

//}
