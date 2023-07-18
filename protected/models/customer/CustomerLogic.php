<?php
/**
 * 注释在哪里~！
 * 
 *
 *
 */
class CustomerLogic
{
	/**
	 * 清空其他token
	 * @author mengtianxue 2013-05-08
	 * @param unknown_type $phone
	 */
	public function deleteCustomerTokenCache($phone,$business){
		$phone_token_list = Yii::app()->db_readonly->createCommand()
										->select("authtoken")
										->from("t_customer_token")
										->where("phone = :phone and business=:business", array(':phone' => $phone, ':business'=> $business))
										->queryAll();
		foreach ($phone_token_list as $list){
			//$cache_key = 'customer_token_'.$list['authtoken'];
			//Yii::app()->cache->delete($cache_key);
            //切换为 redis
            CustomerStatus::model()->delete('token',$list['authtoken']);
		}
	}
	
	/**
	 * 清楚当前cache
	 * @author mengtianxue 2013-05-08
	 * @param unknown_type $token
	 */
	public function  clearCustomerTokenCache($token){
//		$cache_key = 'customer_token_'.$token;
//		return Yii::app()->cache->delete($cache_key);

        return CustomerStatus::model()->delete('token',$token);
	}
	
	/**
	 * 通过phone和udid获取用户token的缓存
	 * @param string $phone
	 * @param string $udid
	 */
	public function getTokenCacheByPhoneUdid($phone, $udid){
	    if(empty($phone) || empty($udid)) {
	        return array();
	    }

            $token_params = array(
                'phone' => $phone,
                'udid'  => $udid,
            );
	    $token = CustomerToken::model()->checkCustomerToken($token_params);
            if($token && isset($token['authtoken'])) {
                return self::getCustomerTokenCache($token['authtoken']);
            }

            return array();
	}

	/**
	 * 多业务单设备登录逻辑
	 * @param string $phone
	 * @param string $udid
	 *
	 */
	public function multiLoginCheck($phone, $udid, $from) {
	    if(empty($phone) || empty($udid) || empty($from)) {
	        return null;
	    }

	    $last_token_cache = self::getTokenCacheByPhoneUdid($phone, $udid);
	    if(!empty($last_token_cache)) {
	        $last_token_from = !empty($last_token_cache['from'])
		    ? $last_token_cache['from'] : CustomerToken::EDJ_TOKEN_FROM;

	        if($last_token_from != $from) {
	            return $last_token_cache['authtoken'];
                }
		else {
		    return null;
                }
	    }

	    return null;
	}
	
	/**
	 * 获取CustomerToken 缓存
	 * @author mengtianxue 2013-05-08
	 * @param unknown_type $params
	 * @param unknown_type $cache_key
	 */
	public function getCustomerTokenCache($token){
		//cache 键值
		$cache_key = "customer_token_".$token;
		$customerTokenInfo = array();
		//$customerTokenCache = Yii::app()->cache->get($cache_key);
        $customerTokenCache=CustomerStatus::model()->get('token',$token);
		if($customerTokenCache){
			$customerTokenInfo = json_decode($customerTokenCache,TRUE); 
		}
		return $customerTokenInfo;
	}
	
	/**
	 * 设置token缓存
	 * @author mengtianxue 2013-05-08
	 * @param unknown_type $phone
	 * @param unknown_type $token
	 */
	public function setCustomerTokenCache($phone, $token = null, $from = CustomerToken::EDJ_TOKEN_FROM){
		if($token === null){
			//生成token
			$token = md5(uniqid(md5(rand()), true).$phone.$from);
		}
		//cache 键值
		$cache_key = "customer_token_".$token;
		
		$customerToken = array();
		$customerToken['authtoken'] = $token;
		$customerToken['phone'] = $phone;
		$customerToken['from'] = !empty($from) ? $from
                    : CustomerToken::EDJ_TOKEN_FROM;
		//Yii::app()->cache->set($cache_key, json_encode($customerToken), 86400);
        CustomerStatus::model()->set('token',$token,$customerToken);
		return $token;
	}
	
	
	/**
	 * 获取CustomerPass缓存
	 * @author mengtianxue 2013-05-08
	 * @param unknown_type $params
	 */
	public static function getCustomerPass($phone, $cache_key){
		$customerPass_cache = Yii::app()->cache->get($cache_key);
		if($customerPass_cache){
			$prelogin_customer = json_decode($customerPass_cache, true);
			if($prelogin_customer['login_date'] != date('Y-m-d')){
				$prelogin_customer = self::setCustomerPass($phone, $cache_key);
			}
		}else{
			$prelogin_customer = self::setCustomerPass($phone, $cache_key);
		}
		return $prelogin_customer;
	}
	
	/**
	 * 获取客户的验证码
	 * @author sunhongjing 2013-05-19
	 * @param unknown_type $macaddress
	 * @param unknown_type $phone
	 */
	public function getCustomerSmsPasswd($phone, $macaddress)
	{
		$customerPass = array();
		$cache_key = 'prelogin_customer_'.md5($phone.$macaddress);
		$customerPassCache = Yii::app()->cache->get($cache_key);
		if($customerPassCache){
			$customerPass = json_decode($customerPassCache,TRUE);
            // 如果更新时间和当前不是一天  返回空的数组
            if(date('Y-m-d',$customerPass['update_time']) != date('Y-m-d')){
                $customerPass = array();
            }
		}
		return $customerPass;
	}
	
	/**
	 * 把验证码添加到缓存中数据缓存
	 * @author mengtianxue 2013-05-08
	 * @param unknown_type $phone
	 * @param unknown_type $macaddress
	 */
	public function setCustomerPasswdCache($params, $isNew = FALSE){
		$phone = $params['phone'];
		$macaddress = $params['macaddress'];
		$cache_key = 'prelogin_customer_'.md5($phone.$macaddress);
		$customerPass = array();
		//如果是老数据  把send_times + 1
		if(!$isNew){
			if( isset($customerPass['update_time']) && date('Y-m-d', $customerPass['update_time']) == date('Y-m-d')){
				
				$customerPass['send_times'] = $params['send_times'] + 1;
				
			} else {
				$customerPass['send_times'] = 1;
			}
		}else{
			$customerPass['send_times'] = 1;
		}
        $customerPass['update_time'] = time();//记录年月日，时分秒
		$customerPass['phone'] = $phone;
		$customerPass['passwd'] = $params['passwd'];//生成验证码
		$customerPass['expired'] =  time() + CustomerPass::PASSEXPIRED;
		$customerPass['macaddress'] = $macaddress;
		Yii::app()->cache->set($cache_key, json_encode($customerPass), 86400);
		return $customerPass;
	}

    /**
     * 设置一键登录短信内容 cache
     * @param $phone
     * @param $macaddress
     * @author bidong 2014-1-18
     */
    public function setQuickLoginSms($phone,$sms,$business=CustomerToken::EDJ_TOKEN_FROM){
        $cache_key = 'quick_login_customer_sms_'.md5($phone.$business);
        $ret= Yii::app()->cache->set($cache_key, $sms, 600);
        return $ret;
    }

    /**
     * 获取一键登录短信内容
     * @param $phone
     * @return mixed
     */
    public function getQuickLoginSms($phone,$business=CustomerToken::EDJ_TOKEN_FROM){
        $cache_key = 'quick_login_customer_sms_'.md5($phone.$business);
        $ret= Yii::app()->cache->get($cache_key);
        return $ret;
    }


    /**
     * 记录一键登录 登录状态
     * @param $phone
     * @param $status
     * @author bidong 2014-1-18
     */
    public function setQuickLoginStatus($phone,$status,$business=CustomerToken::EDJ_TOKEN_FROM){
        $cache_key = 'quick_login_customer_status_'.md5($phone.$business);
        $ret= Yii::app()->cache->set($cache_key, $status, 600);
        return $ret;
    }


    /**
     * 获取一键登录 登录状态
     * @param $phone
     * @param $status
     * @author bidong 2014-1-18
     */
    public function getQuickLoginStatus($phone,$business=CustomerToken::EDJ_TOKEN_FROM){
        $cache_key = 'quick_login_customer_status_'.md5($phone.$business);
        $ret= Yii::app()->cache->get($cache_key);
        return $ret;
    }

    /**
     * 比对用户上发短信内容，是否是一键登录，并写入登录状态
     * @param $phone
     * @param $sms
     * @author bidong 2014-1-18
     */
    public function customerSmsCompare($phone,$sms){
    	//遍历所有业务
    	foreach (CustomerToken::$business_list as $business) {
    		$cache_sms=$this->getQuickLoginSms($phone,$business);
    		if($cache_sms){
	            echo "cache短信内容\r\n";
	            var_dump($cache_sms);
	            echo "收取的内容 \r\n";
	            var_dump($sms);
	            //比较前7位
	            if(strncasecmp(trim($cache_sms),trim($sms),7)==0){
	                echo "验证OK \r\n";
	                //短信内容相同
	                $this->setQuickLoginStatus($phone,true,$business);
	            }
        	}
    	}
        
    }


    /**
     * 把cache设置为过期
     * @author mengtianxue 2013-05-29
     * @param $phone
     * @param $macaddress
     */
    public function expiredCustomerPasswdCache($phone, $macaddress){
        $cache_key = 'prelogin_customer_'.md5($phone.$macaddress);
        $customerPass = $this->getCustomerSmsPasswd($phone, $macaddress);
        if(!empty($customerPass)){
            $customerPass['expired'] =  time() - 30;
        }
        Yii::app()->cache->set($cache_key, json_encode($customerPass), 86400);
    }
	
	/**
	 * 重新设置缓存
	 * @author mengtianxue 2013-05-21
	 * @param unknown_type $params
	 */
	public function resetCustomerPasswdCache($params){
		$phone = $params['phone'];
		$macaddress = $params['macaddress'];
		$cache_key = 'prelogin_customer_'.md5($phone.$macaddress);
        //重置过期时间 和 修改时间
        $params['expired'] =  time() + CustomerPass::PASSEXPIRED;
        $params['update_time'] = time();//记录年月日，时分秒
		Yii::app()->cache->set($cache_key, json_encode($params), 86400);
		return $params;
	}
	
	/**
	 * 获取customerpass
	 * @author mengtianxue 2013-05-21
	 * @param unknown_type $phone
	 * @param unknown_type $cache_key
	 */
	public static function setCustomerPass($phone, $cache_key){
		$prelogin_customer = array();
		//获取次数
		$customerPass = Yii::app()->db_readonly->createCommand()
							->select("*")
							->from('t_customer_pass')
							->where('phone = :phone and FROM_UNIXTIME(expired, "%Y-%m-%d") = :expired',
										array(':phone' => $phone, ':expired' => date('Y-m-d')))
							->order('expired desc')
							->queryAll();
		
		if($customerPass){
			$prelogin_customer['num'] = count($customerPass);
			$prelogin_customer['login_date'] = date('Y-m-d');
			$prelogin_customer['phone'] = $phone;
			$passwd = array();
			foreach ($customerPass as $v){
				$passwd[$v['passwd']] = $v['expired'];
			}
			$prelogin_customer['passwd'] = $passwd;
			
			Yii::app()->cache->set($cache_key, json_encode($prelogin_customer), 86400);
		}
		return $prelogin_customer;
	}
	
	/**
	 * @author mengtianxue 2013-05-08
	 * @param unknown_type $data
	 * @param unknown_type $phone
	 * @param unknown_type $cache_key
	 */
	public static function reload($data, $phone, $cache_key){
		$customerPass = array();
		$date_now = date('Y-m-d');
		if(!empty($data) && $data['login_date'] == $date_now)
			$customerPass['num'] = $data['num'] + 1;
		else 
			$customerPass['num'] = 1;
		$customerPass['login_date'] = $date_now;
		$customerPass['phone'] = $phone;
		$passwd = array();
		$pass =  rand(1000, 9999);
		$passwd[$pass] =  time() + CustomerPass::PASSEXPIRED;
		$data[$passwd['passwd']] = $passwd;
		$prelogin_customer['passwd'] = $data;
		
		Yii::app()->cache->set($cache_key, json_encode($customerPass),86400);
		return $customerPass;
	}
}
