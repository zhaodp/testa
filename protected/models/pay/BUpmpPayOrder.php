<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bidong
 * Date: 13-12-23
 * Time: 下午4:06
 * To change this template use File | Settings | File Templates.
 */

Yii::import('application.models.schema.pay.*');
Yii::import("application.models.customer.*");
Yii::import("application.models.redis.*");
Yii::import("application.models.order.*");
class BUpmpPayOrder extends CarPayOrder
{

    //交易类型
    const TRANS_TYPE_PUSH = "01"; // 交易类型 订单推送
    const TRANS_TYPE_VOID = "31"; // 交易类型 消费撤销
    const TRANS_TYPE_REFUND = "04"; // 交易类型 退货交易

    //交易状态
    const TRANS_STATUS_FRIST = 1; //提交支付订单
    const TRANS_STATUS_SECOND = 2; //支付完成

    //各种支付方式的通道
    const PAY_CHANNEL_UNIONPAY =1;//银联
    const PAY_CHANNEL_ALIPAY   =5;//支付宝支付
    const PAY_CHANNEL_ALIRECHARGE =26;//支付宝充值　
    const PAY_CHANNEL_PP_RECHARGE=28;//pp钱包充值
    const PAY_CHANNEL_PP_PAY=29;//pp钱包支付
    const PAY_CHANNEL_WX_RECHARGE=65;//微信充值
    const PAY_CHANNEL_WX_PAY=66;//微信支付
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /*
        新的方式生成充值订单号
        返回当前的unix时间减去1000000000，后接2位redis生成的顺序数，如 39720812271
    */
    public function makeOrderNo()
    {
        $uniq_order_id=(int)RPay::model()->incrOrderId();
	/*redis已经挂掉，开始抛异常，因此暂时没法执行到这里，todo：上面一行出错时不出异常
	if(empty($uniq_order_id))
	{
	    $uniq_order_id=rand(0,99);
	}
	*/
        return sprintf('%09d',(time()-1000000000)).sprintf('%02d',$uniq_order_id%100);
    }

    /*
	生成订单号的方式
共14位
0 支付人种类，司机还是用户
1-2 支付渠道
3-12 当前时间
13-18 电话号码后6位
19-24 redis自增后6位
    */
     public function newMakeOrderNo($params){
	if(empty($params['channel'])||empty($params['port'])||empty($params['phone'])){
		EdjLog::info("make order no params empty ".serialize($params));
		return 0;
	}	
	if(strlen($params['channel'])>2||strlen($params['port'])>1||strlen($params['phone'])<6){
		EdjLog::info('params length not ok '.serialize($params));
		return 0;
	}
	$uniq_order_id=(int)RPay::model()->incrOrderId();
	return $params['port'].sprintf("%02d",(int)$params['channel']).sprintf('%10d',time()).substr($params['phone'],-6).sprintf('%06d',$uniq_order_id%1000000);
     }

     /*
	拆解订单号取信息
     */
	public function splitOrderId($order_id){
		if(strlen($order_id)!=25){
			EdjLog::info("error order_id is $order_id");
			return false;
		}
		$ret=array();
		$ret['port']=substr($order_id,0,1);
		$ret['channel']=(int)substr($order_id,1,2);
		$ret['time']=substr($order_id,3,10);
		$ret['half_phone']=substr($order_id,13,6);
		return $ret;
	}	

    /**
     * 以前的方式生成充值订单号
     * @return string tradeNo  年月日小时分秒+微妙 例：201304281552023075
     * @author bidong 20131223
    public function makeOrderNo()
    {

        $tradeNo= $usec = $sec = '';
        list($usec, $sec) = explode(" ", microtime());
        $usec=round($usec * 10000000);
        $tradeNo = date('YmdHis', $sec).str_repeat('0',8-strlen($usec)).$usec;
        return $tradeNo;
    }
    */


    /**
     * 创建支付订单
     * @param $user_id
     * @param $order_id
     * @param $resp_tn
     * @param $channel
     * @param $amount
     * @param $trans_time
     * @param $resp_msg
     * @param string $currency
     * @param string $trans_type
     * @author bidong
     */
    public function insertOrder($user_id, $order_id, $resp_tn, $channel, $amount, $trans_time, $resp_msg,
                                $trans_type = self::TRANS_TYPE_PUSH ,$source=0  ,$callback_url='' ,$order_number='')
    {

        $flag = FALSE;

        $payTrade = new CarPayOrder();
        $payTrade->user_id = $user_id;
        $payTrade->order_id = $order_id;
        $payTrade->resp_tn = $resp_tn;
        $payTrade->channel = $channel;
        $payTrade->order_amount = $amount;
        $payTrade->trans_time = $trans_time;
        $payTrade->resp_msg_fist = $resp_msg;
        //交易币种
        //$payTrade->currency=$currency;
        $payTrade->trans_type = $trans_type;
        $payTrade->create_time = date('Y-m-d H:i:s');
        $payTrade->trans_status = self::TRANS_STATUS_FRIST;
        //订单来源
        $payTrade->source = $source;
        $payTrade->callback_url = $callback_url;
        $payTrade->order_number = $order_number;

        if ($payTrade->insert()) {
            $flag = TRUE;
        }

        return $flag;
    }
    /**  司机点结束代驾，校验客户是否需要在线支付
     * @param $user_id
     * @param int $pay_number
     * @param int $fee 需要支付的余额
     * @return bool
     */
    public  function  validateOnlineOrder($phone, $fee=0){
        $payOnline_fee = 0;//需要在线支付的金额
        EdjLog::info('---CCCC------ '.$phone."VVVV".$fee);
        if (!empty($phone)) {
            $isVip = VipService::service()->isVip($phone);//验证客户手机号是否为VIP 禁用的vip不算vip
            if ($isVip) {
                //查出vip余额
                $vipArray = VipPhone::model()->getVipInfoByPhone($phone,true);
                $vip_balance = $vipArray['total_balance'];//vip主卡或副卡对应的主卡卡余额+信誉度
                //计算出需要从余额中扣除的费用
                if ($vip_balance >= $fee) {
                    $payOnline_fee = 0;
                } else {
                    $payOnline_fee = $fee - $vip_balance;
                }
            } else {
                //查出普通用户余额
                $customerInfo = CustomerService::service()->getCustomerInfo($phone,1);//通过电话得到该客户的信息
                $user_id = $customerInfo ? $customerInfo->id : '0.1';//客户id对应于t_customer_trans的userid 如果没有该号码信息则查询出数据
                $cAcountModel = CarCustomerAccount::model()->getCustomerAccountInfo($user_id);//普通账户信息
                $cust_balance = $cAcountModel ? $cAcountModel['amount'] : 0;
                //计算出需要从余额中扣除的费用
                if ($cust_balance >= $fee) {
                    $payOnline_fee = 0;
                } else {
                    $payOnline_fee = $fee - $cust_balance;
                }
            }
        }
        return $payOnline_fee;
    }

    public function insertDriverOrder($user_id, $order_id, $resp_tn, $channel, $amount, $trans_time, $resp_msg, $trans_type = self::TRANS_TYPE_PUSH)
    {

        $flag = FALSE;

        $payTrade = new CarPayDriverOrder();
        $payTrade->user_id = $user_id;
        $payTrade->order_id = $order_id;
        $payTrade->resp_tn = $resp_tn;
        $payTrade->channel = $channel;
        $payTrade->order_amount = $amount;
        $payTrade->trans_time = $trans_time;
        $payTrade->resp_msg_fist = $resp_msg;
        //交易币种
        //$payTrade->currency=$currency;
        $payTrade->trans_type = $trans_type;
        $payTrade->create_time = date('Y-m-d H:i:s');
        $payTrade->trans_status = self::TRANS_STATUS_FRIST;

        if ($payTrade->insert()) {
            $flag = TRUE;
        }

        return $flag;
    }

    /*
	根据用户电话号码到写库强制拿到用户现在的余额
    */
    public function forceGetBalance($user_phone){
	$vip_card=VipPhone::model()->getPrimary($user_phone);
	$vip=false;
	if($vip_card&&!empty($vip_card['vipid'])){
		$vip=Yii::app()->db_finance->createCommand()->select('(balance+credit) balance')->from('t_vip')->where('id=:id and status!=:status',
		array(':id'=>$vip_card['vipid'],':status'=>Vip::STATUS_DISABLE))->queryRow();
	}
	if($vip){
		return $vip['balance'];
	}
	else{
		$objCustomer=CustomerService::service()->getCustomerInfo(array('phone'=>$user_phone));
		if(isset($objCustomer['code'])&&$objCustomer['code']==0&&is_object($objCustomer['data'])){
			$user_id=$objCustomer['data']->id;
			$customer=Yii::app()->db_finance->createCommand()->select('amount')->from('t_customer_account')->where('user_id=:user_id',
			array(':user_id'=>$user_id))->queryRow();
			return $customer['amount'];
		}	
		else{
			EdjLog::info('not vip,not customer for phone '.$user_phone);
			return 0.0;
		}
	}
    }

    //phone 用户（含vip）的电话号码，通知对该用户当前有订单的司机该用户余额已经变化了
    //is_pay 是否为充值的通知
    public function noticeBalanceChange($phone,$is_pay=false){
	$balance=$this->forceGetBalance($phone);
	$vip_card=VipPhone::model()->getPrimary($phone);
	$all_phones=array();
	if($vip_card&&!empty($vip_card['vipid'])&&Vip::model()->getPrimary($vip_card['vipid'])){
		$all_vip=VipPhone::model()->getVipCardPhone($vip_card['vipid'],true);
		foreach($all_vip as $each_vip){
			if(!empty($each_vip['phone'])&&$each_vip['status']<VipPhone::STATS_DISABLE)
				$all_phones[]=$each_vip['phone'];
		}
	}
	else{
		$all_phones[]=$phone;
	}
	$has_send_driver_phones=array();
	foreach($all_phones as $user_phone){
		$orders=CustomerApiOrder::model()->getServiceDrivers($user_phone);
		foreach($orders as $order){
			if(empty($order['driver_id'])){
				EdjLog::info('driver_id is empty for order '.serialize($order));
				return false;
			}
			if(empty($order['order_id'])){
				EdjLog::info('order_id is empty for order '.serialize($order));
				return false;
			}
			$long_order_id=$order['order_id'];
			$my_order = Order::model()->find('order_number = :order_number' , array(':order_number' => $long_order_id));
			if(!$my_order){
                                $my_order=Order::model()->findByPk($long_order_id);
                                if(!$my_order){//没这个订单
                                        continue;
                                }
			}
			if($my_order&&is_object($my_order)&&$my_order->status!=0){//订单已经结算
				continue;
			}
			if(RSubmitOrder::model()->getCashOnly($my_order->order_id)){//仅收现金的订单
				continue;
			}
			$driver_id=$order['driver_id'];
			$driver=Driver::model()->getProfile($driver_id);
                	if(!$driver||empty($driver->phone)){
                        	EdjLog::info("get profile for driver ".$driver_id." is empty");
                        	continue;
                	}
        		$driverPosition=DriverPosition::model()->getDriverPosition($driver->id);
        		if(empty($driverPosition['app_ver'])||$driverPosition['app_ver']<'2.3.4'){
                		return;
        		}
			$pushResult=DriverPush::model()->pushCustomAccountChange($driver_id,$balance,$long_order_id);
			if(!$pushResult){
				EdjLog::info('push order_id '.$long_order_id.' for driver '.$driver_id.' fail');
				$key=RApiKey::model()->key('30000001');
				$md5content='EDJCACCOUNTCHANGE'.$long_order_id.sprintf("%.2f",$balance).$key['secret'];
				$sig=md5($md5content);
				$sms_content='EDJCACCOUNTCHANGE#'.$long_order_id.'#'.sprintf("%.2f",$balance).'#'.substr($sig,0,8).'#';
				$smsResult=Sms::SendSMS($driver->phone,$sms_content);
				if(!$smsResult){
					EdjLog::info("send sms to ".$driver->phone." content ".$sms_content." fail");
				}
			}
			if(!$is_pay&&$balance>=500){
				//订单结算后客户还有500元或以上，没有必要给司机发短信
				continue;
			}
			if(!empty($has_send_driver_phones[$driver->phone])){
				continue;
			}
			$driver_content=$driver_id."师傅，客户的账户余额已发生变化，请按照计价器显示的最新余额收取费用。";
			$driverResult=Sms::SendSMS($driver->phone,$driver_content);
			if(!$driverResult){
				EdjLog::info("send sms to ".$driver->phone." content ".$driver_content." fail");
			}
			else{
				$has_send_driver_phones[$driver->phone]=true;
			}
		}	
	}
    }

    //版本号    version 1.0.0
    //签名方法  signMethod
    //签名信息  signature
    //交易类型  transType
    //商户代码  merId
    //交易状态  transStatus    00:交易成功结束
    //响应码        respCode
    //查询流水号  qn
    //商户订单号  orderNumber
    public function updateOrder($order_id, $qn, $resp_msg)
    {

	EdjLog::info('updateOrder.orderId:'.$order_id);
        $pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
        if($pay_order && is_object($pay_order)){
	    $user_phone=$pay_order->user_id;
            $pay_order->trans_status=self::TRANS_STATUS_SECOND;
            $pay_order->resp_qn=$qn;
            $pay_order->update_time=$pay_order->trans_end_time=date('Y-m-d H:i:s');
            $pay_order->resp_msg_second=$resp_msg;
            $ret = $pay_order->update();

            if($ret){

		EdjLog::info('update tarnsstatus success orderId='.$order_id);	
		
		$amount=$pay_order->order_amount/100;
		//vip充值
		$vip_phone=VipPhone::model()->getPrimary($user_phone);
                $vip=Vip::model()->getPrimaryPhone($user_phone,Vip::STATUS_DISABLE);
		$incomeResult=false;	
		if($vip_phone&&$vip) {
			//echo "incomeVipAccount orderId:".$order_id."\n";
			EdjLog::info('updateOrder.incomeVipAccount suucess orderid='.$order_id);
			$incomeResult=$this->incomeVipAccount($vip->attributes['id'],$amount,$pay_order->order_id);
		}else{
			//echo "incomeUserAccount orderId:".$order_id."\n";
			$customer=  CustomerService::service()->getCustomerInfo(array('phone'=>$user_phone));
			if($customer['code']==0 && is_object($customer['data'])){
			  $incomeResult=$this->incomeUserAccount($customer['data']->id,$amount,$pay_order->order_id);
			}else{
			  EdjLog::info('updateOrder.error 用户是被禁止的VIP,但同时也不是普通用 orderid='.$order_id); 
			  //echo 'updateOrder.error 用户是被禁止的VIP,但同时也不是普通用 orderid='.$order_id; 
			  return false;
			}

		}
		if(!$incomeResult){
			EdjLog::info("order_id $order_id for user_phone $user_phone fail");
			return $incomeResult;
		}
		//充值加e币
		CustomerMain::model()->addWealthForRecharge($user_phone,$amount);
                //去掉该地，为了测试vip主副卡余额变化的及时通知
//		if($pay_order->channel!=EmployeeAccount::CHANNEL_ALIPAY && $pay_order->channel!=EmployeeAccount::CHANNEL_PP_PAY && $pay_order->channel!=EmployeeAccount::CHANNEL_WX_PAY){
			$this->noticeBalanceChange($user_phone,true);//通知和该用户当前有订单的司机该用户余额已经变化
//		}
		return $incomeResult;


		/**
                $customer=  BCustomers::model()->getCustomerInfo(array('phone'=>$user_phone));
                if($customer['code']==0 && is_object($customer['data'])){
                    //vip充值
                    $vip=Vip::model()->getPrimaryPhone($user_phone,Vip::STATUS_DISABLE);
                    if($vip){
                        $amount=$pay_order->order_amount/100;
                        $this->incomeVipAccount($vip->attributes['id'],$amount,$pay_order->order_id);
                    }else{
                        $amount=$pay_order->order_amount/100;
                        $this->incomeUserAccount($customer['data']->id,$amount,$pay_order->order_id);
                    }
                }else{
                    //vip充值
                    $vip=Vip::model()->getPrimaryPhone($user_phone,Vip::STATUS_DISABLE);
                    if($vip){
                        $amount=$pay_order->order_amount/100;
                        $this->incomeVipAccount($vip->attributes['id'],$amount,$pay_order->order_id);
                    }
                }  **/
			
            }else{
		EdjLog::info('updateOrder update trans status error,order_id='.$order_id);
        FinanceUtils::sendFinanceAlarm("finance error","BUpmpPayOrder->updateOrder,支付宝支付后，修改t_pay_order失败");
		//echo 'updateOrder update trans status error,order_id='.$order_id.'\n';	
		return false;
	    }
        }
	else{//数据库可能有写入延迟 pay.php中的语句尚未写入，需要删除相应的redis锁
            if(RPay::model()->delOrderId($order_id)!=1){
                EdjLog::log('delete redis key order_id '.$order_id.' fail! not set key?');
            }
	    EdjLog::info('delete redis lock for order_id '.$order_id);
	    return false;
	}
    }


    /**
     * 洗车业务的回调处理  1。修改订单数据库（改为已支付） 2.给司机端加钱 3。给洗车客户端返回消息 4.充值E币
     * @param $order_id
     * @param $qn
     * @param $resp_msg
     * @return bool
     */
    public function updateAllOrder($order_id, $qn, $resp_msg)
    {
	    EdjLog::info('updateAllOrder.orderId:'.$order_id);
        $pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
        if($pay_order && is_object($pay_order)){
	    $user_phone=$pay_order->user_id;
            $pay_order->trans_status=self::TRANS_STATUS_SECOND;
            $pay_order->resp_qn=$qn;
            $pay_order->update_time=$pay_order->trans_end_time=date('Y-m-d H:i:s');
            $pay_order->resp_msg_second=$resp_msg;
            $ret = $pay_order->update();

            if($ret){
                EdjLog::info('update tarnsstatus success orderId='.$order_id);
                $amount=$pay_order->order_amount/100;
                $incomeResult = true ;
                //**********************在这里调用文强的接口，为虚拟账号加钱--start**********************
                Yii::import('application.models.pay.settlement.*');
                Yii::import('application.models.pay.subsidy.*');
                $companySettle = new CompanySubsidySettlement('', 1);
                //给对应的公司账户充值并插入流水 当前为一口价洗车
                $rechargeResult = $companySettle->reChargeCompanyAccount($pay_order->user_id,$pay_order->source,$order_id,$amount);
                if(!$rechargeResult){
                    EdjLog::info("order_id $order_id for reChargeCompanyAccount $user_phone fail");
                    FinanceUtils::sendFinanceAlarm("finance error","BUpmpPayOrder->updateAllOrder,支付宝支付后，公司账号加钱失败");
                }

                //**********************在这里调用洗车端接口，通知消息--start*******************
                $format=$pay_order->callback_url."?order_id=%s&fee=%s&user_id=%s";
                if(strpos($pay_order->callback_url,"?")>0){
                    $format=$pay_order->callback_url."&order_id=%s&fee=%s&user_id=%s";
                }
                $url = sprintf($format, $order_id, $pay_order->order_amount,  $pay_order->user_id);
                $ch=curl_init();
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 0);
                $incomeResult=curl_exec($ch);
                $count=0 ;
                $callBackStatus = trim($incomeResult) != 'success';
                while(!$callBackStatus && $count < 5){
                    sleep(1);
                    $count++;
                    $incomeResult=curl_exec($ch);
                    $callBackStatus = trim($incomeResult) != 'success';
                }
                curl_close($ch);
                //**********************在这里调用洗车端接口，通知消息--end**********************

                if(!$callBackStatus){
                    EdjLog::info("order_id $order_id for user_phone $user_phone fail");
                    FinanceUtils::sendFinanceAlarm("finance error","BUpmpPayOrder->updateAllOrder,支付宝支付后，通知客户端失败".serialize($incomeResult));
                    return $incomeResult;
                }
                //**********************在这里调用文强的接口，为虚拟账号加钱--end**********************
                //vip充值
                try {
                    $vip_phone = VipPhone::model()->getPrimary($user_phone);
                    $vip = Vip::model()->getPrimaryPhone($user_phone, Vip::STATUS_DISABLE);
                    if ($vip_phone && $vip) {
                        $type = VipTrade::TYPE_ALIPAY_RECHARGE;
                        $source = VipTrade::TRANS_SOURCE_ALIPAY_RECHARGE;
                        $remarks = '支付宝支付，支付ID'.$order_id;
                        Vip::model()->vipTrade($vip['id'], $amount, $vip['balance'],  $pay_order->order_number, $remarks, $type, $source);
                    } else {
                        $user_info = NormalAccountService::getUserAmount($user_phone);
                        $trans = array();
                        $trans['user_id'] = isset($user_info['id']) ? $user_info['id']:0;
                        $trans['balance'] = isset($user_info['amount']) ? $user_info['amount'] : 0;
                        if($trans['user_id'] == 0){
                            $user_info = CustomerService::service()->forceGetCustomerInfo( $user_phone);
                            $trans['user_id'] = $user_info->id;
                        }

                        $trans['trans_order_id'] =  $pay_order->order_number;
                        $trans['trans_card'] = 0;
                        $trans['trans_type'] = CarCustomerTrans::TRANS_TYPE_ALIPAY_RECHARGE;
                        $trans['amount'] = $amount;
                        $trans['source'] = CarCustomerTrans::TRANS_SOURCE_ALIPAY_RECHARGE;
                        $trans['operator'] = '系统';
                        $trans['remark'] = '支付宝支付，支付ID'.$order_id;
                        BCustomers::model()->addCustomerTrade($trans);
                    }
                } catch (Exception $e) {
                    EdjLog::error($e->getMessage());
                }

                //充值加e币
                CustomerMain::model()->addWealthForRecharge($user_phone,$amount);
                if($pay_order->channel!=EmployeeAccount::CHANNEL_ALIPAY && $pay_order->channel!=EmployeeAccount::CHANNEL_PP_PAY){
                    $this->noticeBalanceChange($user_phone,true);//通知和该用户当前有订单的司机该用户余额已经变化
                }
                return $incomeResult;

            }else{
                EdjLog::info('updateAllOrder update trans status error,order_id='.$order_id);
                FinanceUtils::sendFinanceAlarm("finance error","BUpmpPayOrder->updateAllOrder,支付宝支付后，修改t_pay_order失败");
                return false;
            }
        }
	    else{//数据库可能有写入延迟 pay.php中的语句尚未写入，需要删除相应的redis锁 -- 在往支付宝发送消息前就验证过了。这里肯定不会执行到
            if(RPay::model()->delOrderId($order_id)!=1){
                EdjLog::log('delete redis key order_id '.$order_id.' fail! not set key?');
            }
	    EdjLog::info('delete redis lock for order_id '.$order_id);
	    return false;
	}
    }


    /**
     * 用于给客户端查询订单状态
     * @param $order_id
     * @return bool
     */
    public static function checkOrderStatus($order_id){
        $pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
        if($pay_order && $pay_order->trans_status==self::TRANS_STATUS_SECOND){
            return true ;
        }
        return false ;
    }


    /**
     * 用于给客户端查询订单状态
     * @param $phone
     * @param $sdate
     * @return mix
     */
    public static function getPayOrderDetail($phone,$sdate){
        $criteria = new CDbCriteria();
        $criteria->select = 'order_id,channel,user_id,order_amount,trans_type,trans_status,create_time,trans_end_time,description';
        $criteria->condition = ' user_id=:phone and create_time like :sdate';
        $criteria->order = 'create_time';
        //$criteria->limit = 1;
        $criteria->params = array(
            ':phone' => $phone,
            ':sdate' => $sdate.'%'
        );

        $customerBonus = CarPayOrder::model()->findAll($criteria);

        return $customerBonus;
    }




    public function updateDriverOrder($order_id, $qn, $resp_msg)
    {

	EdjLog::log('updateDriverOrder.orderId:'.$order_id);
        $pay_order = CarPayDriverOrder::model()->find('order_id=:order_id',array(':order_id'=>$order_id));
        //$driver_id=$pay_order->user_id;
        if($pay_order && is_object($pay_order)){
	    $driver_id=$pay_order->user_id;
            $pay_order->trans_status=self::TRANS_STATUS_SECOND;
            $pay_order->resp_qn=$qn;
            $pay_order->update_time=$pay_order->trans_end_time=date('Y-m-d H:i:s');
            $pay_order->resp_msg_second=$resp_msg;
            $ret = $pay_order->update();

            if($ret){
		EdjLog::info('update trans_status succsss --OrderId:'.$order_id);
                $amount=$pay_order->order_amount/100;
                $driverModel= Driver::model()->getProfile($driver_id);

                //添加司机交易流水
                return $this->incomeDriverAccount($driver_id,$amount,$pay_order->order_id,$driverModel->city_id);

            }
	    else{
		EdjLog::info('update trans_status fail! order_id is '.$order_id);
		return false;
	    }

        }else{
		//写入延迟，直接删除相应redis中对应的order_id锁
		if(RPay::model()->delOrderId($order_id)!=1){
		    EdjLog::log('delete redis key order_id '.$order_id.' fail! not set key?');
		}
               EdjLog::log('updateDriverOrder.pay_driver_order not exist orderId:'.$order_id);
	       //echo "order not exist orderId:".$order_id."\n";
	       return false;	
			
	}
    }

    /**
     * 获取用户银联充值流水
     * @param array $colParams (col_name=value)
     * @return object
     * @author  bidong
     */
    public function getUserPayOrderList($colParams=array()){
        $data='';
        if(!empty($colParams) && is_array($colParams)){
            $criteria=new  CDbCriteria();
            $criteria->addColumnCondition($colParams);
            $data=CarPayOrder::model()->findAll($criteria);
        }

        return $data;
    }

    /**
     * 获取司机银联充值流水
     * @param array $colParams (col_name=value)
     * @return object
     * @author  bidong
     */
    public function getDriverPayOrderList($colParams=array()){
        $data='';
        if(!empty($colParams) && is_array($colParams)){
            $criteria=new  CDbCriteria();
            $criteria->addColumnCondition($colParams);
            $data=CarPayDriverOrder::model()->findAll($criteria);
        }

        return $data;
    }


    public function incomeDriverAccount($driver_id,$amount,$order_id,$city_id){


	EdjLog::info('incomeDriverAccount.orderId:'.$order_id);
        //检查是否已经存在此支付订单
        EmployeeAccount::$table_name = date('Ym');
        $employeeAccount =new EmployeeAccount();
	$employeeAccount->refreshMetaData();
        $criteria = new CDbCriteria();
        $criteria->compare('order_id', '='.$order_id);
	$count= $employeeAccount->count($criteria);

        //不存在，则入账
        if($count<1){
	    $arrOrderId=$this->splitOrderId($order_id);
	    if($arrOrderId && $arrOrderId['channel']==EmployeeAccount::CHANNEL_ALIPAY){
		$params=array(
			'channel'=>EmployeeAccount::CHANNEL_ALIPAY,
			'user'=>$driver_id,
			'comment'=>'支付宝支付',
			'cast'=>$amount,
			'city_id'=>$city_id,
			'order_id'=>$order_id,
		);
	    }
	    else if($arrOrderId && $arrOrderId['channel']== EmployeeAccount::CHANNEL_ALIRECHARGE){
		$params=array(
                        'channel'=>EmployeeAccount::CHANNEL_ALIRECHARGE,
                        'user'=>$driver_id,
                        'comment'=>'支付宝充值',
                        'cast'=>$amount,
                        'city_id'=>$city_id,
                        'order_id'=>$order_id,
		);
	    }
	    else{
            	//添加司机交易流水
           	 $params=array(
                	'channel'=>25,
                	'user'=>$driver_id,
                	'comment'=>'银联充值',
                	'cast'=>$amount,
                	'city_id'=>$city_id,
                	'order_id'=>$order_id,
            	);
	    }
            $ret=  OrderSettlement::model()->driverRecharge($params,true);
            //临时处理 2014-03-24
            if($ret){
                $user=array('user'=>$driver_id,'comment'=>$params['comment']);
                DriverBankResult::model()->activationDriver($user);
            }
            return $ret;
		
        }else{
		
		EdjLog::info('incomeDriverAccount.error order exist. orderId:'.$order_id);
		return false;
	} 

     }
    

    public function incomeUserAccount($user_id,$amount,$order_id){

        $param=array('user_id'=>$user_id,'trans_order_id'=>$order_id);
        $ret=  BCustomers::model()->getCustomerTransCount($param);
        if($ret<1){
	    $arrOrderId=$this->splitOrderId($order_id);
	    if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_ALIPAY){
		$params=array(
			'trans_order_id' => $order_id,
			'trans_type' => CarCustomerTrans::TRANS_TYPE_ALIPAY,
			'source' => CarCustomerTrans::TRANS_SOURCE_ALIPAY,
			'remark'=>'支付宝支付',
		);
	    }
            else if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_ALIRECHARGE){
                $params=array(
                        'trans_order_id' => $order_id,
                        'trans_type' => CarCustomerTrans::TRANS_TYPE_ALIPAY_RECHARGE,
                        'source' => CarCustomerTrans::TRANS_SOURCE_ALIPAY_RECHARGE,
			'remark'=>'支付宝充值',
                );
            }
	    else if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_PP_RECHARGE){
		$params=array(
                        'trans_order_id' => $order_id,
                        'trans_type' => CarCustomerTrans::TRANS_TYPE_PP_RECHARGE,
                        'source' => CarCustomerTrans::TRANS_SOURCE_PP_RECHARGE,
			'remark'=>'pp钱包充值',
                );
	    }
            else if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_PP_PAY){
		$params=array(
                        'trans_order_id' => $order_id,
                        'trans_type' => CarCustomerTrans::TRANS_TYPE_PP_PAY,
                        'source' => CarCustomerTrans::TRANS_SOURCE_PP_PAY,
			'remark'=>'pp钱包支付',
                );
	    }else if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_WX_RECHARGE){
                $params=array(
                    'trans_order_id' => $order_id,
                    'trans_type' => CarCustomerTrans::TRANS_TYPE_WX_RECHARGE,
                    'source' => CarCustomerTrans::TRANS_SOURCE_WX_RECHARGE,
                    'remark'=>'微信充值',
                );
            }else if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_WX_PAY){
                $params=array(
                    'trans_order_id' => $order_id,
                    'trans_type' => CarCustomerTrans::TRANS_TYPE_WX_PAY,
                    'source' => CarCustomerTrans::TRANS_SOURCE_WX_PAY,
                    'remark'=>'微信支付',
                );
            }else{
            	$params=array(
                	'trans_order_id'=>$order_id,
                	'trans_type'=>CarCustomerTrans::TRANS_TYPE_S,
			'source'=>CarCustomerTrans::TRANS_SOURCE_F,
            	);
	    }
            $ret= BCustomers::model()->income($user_id, $amount, $params);
            EdjLog::info('back message='.$ret['message']);
            //echo 'back message='.$ret['message'].'\n' ;
	    return true;
		
        }else{
	    EdjLog::info('incomeUserAccount error ,order_id exist orderId='.$order_id);
	    return false;
	}
    }
    public function incomeVipAccount($user_id,$amount,$order_id){

	EdjLog::info('incomeVipAccount orderId='.$order_id);
        $param=array('vipcard'=>$user_id,'order_id'=>$order_id);
        $ret=  Vip::model()->getVipTradeCount($param);
        if($ret<1){
	    $arrOrderId=$this->splitOrderId($order_id);
	    if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_ALIPAY){
		$type=VipTrade::TYPE_ALIPAY;
		$source=VipTrade::TRANS_SOURCE_ALIPAY;
		$remarks='支付宝支付';
	    }
            else if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_ALIRECHARGE){
                $type=VipTrade::TYPE_ALIPAY_RECHARGE;
                $source=VipTrade::TRANS_SOURCE_ALIPAY_RECHARGE;
                $remarks='支付宝充值';
            }
	    else if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_PP_RECHARGE){
                $type=VipTrade::TYPE_PP_RECHARGE;
                $source=VipTrade::TRANS_SOURCE_PP_RECHARGE;
                $remarks='pp钱包充值';
            }
	    else if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_PP_PAY){
                $type=VipTrade::TYPE_PP_PAY;
                $source=VipTrade::TRANS_SOURCE_PP_PAY;
                $remarks='pp钱包支付';
        }else if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_WX_RECHARGE){
            $type=VipTrade::TYPE_WX_RECHARGE;
            $source=VipTrade::TRANS_SOURCE_WX_RECHARGE;
            $remarks='微信充值';
        }else if($arrOrderId && $arrOrderId['channel']==self::PAY_CHANNEL_WX_PAY){
            $type=VipTrade::TYPE_WX_PAY;
            $source=VipTrade::TRANS_SOURCE_WX_PAY;
            $remarks='微信支付';
        }else{
            	$type=VipTrade::TYPE_PAY;
            	$source=VipTrade::TRANS_SOURCE_F;
            	$remarks ='银联充值';
	    }
            $vipModel = Vip::model()->findByPk($user_id);
            return Vip::model()->vipIncome($vipModel, $amount, $order_id,$remarks,$type,$source);
        }else {
	    EdjLog::info('incomeVipAccount error ,order_id exist orderId='.$order_id);
	    return false;
	}
    }

 /**
     * 获取用户交易数据
     * @param $date '2014-04-25' 如果时间不传，则默认为前一天
     */
    public function getAllPayCount($date) {
	
	//echo "1====".$date."\n";
        EdjLog::info('getAllPayCount====date='.$date);
        $param=array("create_time>"=>$date." 00:00:00","create_time<"=>$date." 23:59:59");
        //开始统计司机充值数据
        $driverData=$this->getDriverPayOrderList($param);
        EdjLog::info('driverData.COUNT===='.count($driverData));	
	$driverRemoteResult=$this->getRealPay($driverData);
        $driverLocalResult=$this->getLocalPayResult($driverRemoteResult);
        $driverLocalSuccess=$driverLocalResult['success'];
        $driverLocalFail=$driverLocalResult['fail'];

        $driverRemoteSuccessCount=count($driverRemoteResult);//司机实际交易笔数
        $driverLocalsSuccessCount=count($driverLocalSuccess);//司机本地回调成功笔数

        $driverRemoteSuccessAmount=$this->countAmount($driverRemoteResult);//司机实际交易额
        $driverLocalSuccessAmount=$this->countAmount($driverLocalSuccess);//司机本地充值成功额度

        //开始统计用户充值数据
        $userData=$this->getUserPayOrderList($param);
        $userRemoteResult=$this->getRealPay($userData);//银联充值成功数据
        $userLocalResult=$this->getLocalPayResult($userRemoteResult,false);
        $userLocalSuccess=$userLocalResult['success'];//本地充值成功数据
        $userLocalFail=$userLocalResult['fail'];//本地充值失败数据

        $userRemoteSuccessCount=count($userRemoteResult);//客户实际交易笔数
        $userLocalsSuccessCount=count($userLocalSuccess);//客户本地回调成功笔数

        $userRemoteSuccessAmount=$this->countAmount($userRemoteResult);//客户实际交易笔数
        $userLocalsSuccessAmount=$this->countAmount($userLocalSuccess);//客户本地回调成功笔数

        //总体交易概况
        $html  =$date."交易概况(总数|成功数):<table border='1'><tr><td>日期</td><td>司机充值额</td>";
        $html .="<td>司机充值笔数</td><td>用户充值额</td><td>用户充笔数</td><td>充值总额</td><td>充值总笔数</td></tr>";
        $html .="<tr><td>".$date."</td><td>".($driverRemoteSuccessAmount/100)."|".($driverLocalSuccessAmount/100)."</td>";
        $html .="<td>".$driverRemoteSuccessCount."|".$driverLocalsSuccessCount."</td>";
        $html .="<td>".($userRemoteSuccessAmount/100)."|".($userLocalsSuccessAmount/100)."</td>";
        $html .="<td>".$userRemoteSuccessCount."|".$userLocalsSuccessCount."</td>";
        $html .="<td>".($driverRemoteSuccessAmount/100+$userRemoteSuccessAmount/100)."|".($driverLocalSuccessAmount/100+$userLocalsSuccessAmount/100)."</td>";
        $html .="<td>".($driverRemoteSuccessCount+$userRemoteSuccessCount)."|".($driverLocalsSuccessCount+$userLocalsSuccessCount)."</td>";
        $html .="</tr></table>";

        //空格
        $html .="</br></br></br>";

         //司机充值失败详情
        $html .="<font color='red'>司机充值失败</font>详情:";
        $html .="<table border='1'><tr><td>订单ID</td><td>司机工号</td><td>交易额</td><td>交易时间</td></tr>";
        //echo "driver order_id \n";
        foreach($driverLocalFail as $item) {
            //echo "order_id.".$item['order_id']."\n";
            $html .="<tr><td>".$item['order_id']."</td><td>".$item['user_id']."</td><td>".($item['order_amount']/100)."</td><td>".$item['create_time']."</td></tr>";
         }
        $html .="</table>";

        //空格
        $html .="</br></br></br>";

        //客户充值失败详情
        $html .="<font color='red'>用户充值失败</font>详情:";
        $html .="<table border='1'><tr><td>订单ID</td><td>用户手机号</td><td>是否VIP</td><td>交易额</td><td>交易时间</td></tr>";
        //echo "user order_id \n";
        foreach($userLocalFail as $item) {
	    $vip=Vip::model()->getPrimaryPhone($item['user_id'],Vip::STATUS_DISABLE);
	    if($vip) {
		$vipInfo="VIP用户.状态=".($vip['status']==2?'禁止':'正常');
	    }else{
		$vipInfo="非VIP用户";	
	   }
            //echo "order_id.".$item['order_id']."\n";
            $html .="<tr><td>".$item['order_id']."</td><td>".$item['user_id']."</td><td>".$vipInfo."</td><td>".($item['order_amount']/100)."</td><td>".$item['create_time']."</td></tr>";
        }

        $html .="</table>";

        return $html;

    }

 /**
     * 获取用户交易数据,返回json字符串
     * @param $date '2014-04-25' 如果时间不传，则默认为前一天
     */
    public function getAllPayCountJson($date) {
        //echo "1====".$date."\n";

        $param=array("create_time>"=>$date." 00:00:00","create_time<"=>$date." 23:59:59");
        //开始统计司机充值数据
        $driverData=$this->getDriverPayOrderList($param);

        $driverRemoteResult=$this->getRealPay($driverData);
        $driverLocalResult=$this->getLocalPayResultForAll($driverRemoteResult);
        $driverLocalSuccess=$driverLocalResult['success'];
        $driverLocalFail=$driverLocalResult['fail'];

        $driverRemoteSuccessCount=count($driverRemoteResult);//司机实际交易笔数
        $driverLocalsSuccessCount=count($driverLocalSuccess);//司机本地回调成功笔数

        $driverRemoteSuccessAmount=$this->countAmount($driverRemoteResult);//司机实际交易额
        $driverLocalSuccessAmount=$this->countAmount($driverLocalSuccess);//司机本地充值成功额度

        //开始统计用户充值数据
        $userData=$this->getUserPayOrderList($param);
        $userRemoteResult=$this->getRealPay($userData);//银联充值成功数据
        $userLocalResult=$this->getLocalPayResultForAll($userRemoteResult,false);
        $userLocalSuccess=$userLocalResult['success'];//本地充值成功数据
        $userLocalFail=$userLocalResult['fail'];//本地充值失败数据

        $userRemoteSuccessCount=count($userRemoteResult);//客户实际交易笔数
        $userLocalsSuccessCount=count($userLocalSuccess);//客户本地回调成功笔数

        $userRemoteSuccessAmount=$this->countAmount($userRemoteResult);//客户实际交易笔数
        $userLocalsSuccessAmount=$this->countAmount($userLocalSuccess);//客户本地回调成功笔数
	foreach($driverLocalFail as &$item){
	    $item['order_amount']=$item['order_amount']/100;
	}
	foreach($userLocalFail as &$item) {
            $vip=Vip::model()->getPrimaryPhone($item['user_id'],Vip::STATUS_DISABLE);
            if($vip) {
                $vipInfo="VIP用户.状态=".($vip['status']==2?'禁止':'正常');
            }else{
                $vipInfo="非VIP用户";
           }
	    $item['vipInfo']=$vipInfo;
	    $item['order_amount']=$item['order_amount']/100;
	}
	$arrPayData=array('pay_date'=>$date,'driverTotalAmount'=>$driverRemoteSuccessAmount/100,'driverSuccessAmount'=>$driverLocalSuccessAmount/100,'userTotalAmount'=>$userRemoteSuccessAmount/100,'userSuccessAmount'=>$userLocalsSuccessAmount/100,'driverTotalCount'=>$driverRemoteSuccessCount,'driverSuccessCount'=>$driverLocalsSuccessCount,'userTotalCount'=>$userRemoteSuccessCount,'userSuccessCount'=>$userLocalsSuccessCount,'driverFail'=>$driverLocalFail,'userFail'=>$userLocalFail);
        return $arrPayData;

    }

      /**
       *对于没有充值成功的用户进行补账
       *用户充值补账
       **/ 
       public function repairPay($orderId,$isDriver='true',$upmpPay=''){
		EdjLog::info("开始进行补账.orderId=".$orderId."|".($isDriver?"司机":"普通用户"));
		$result=array('code'=>0,'msg'=>'orderId为空或者不存在');
		if(!isset($orderId)) {
		  return $result;
		}	
		$param=array('order_id'=>$orderId);
		$trans_type='';
		$order_time='';	
		if($isDriver == 'true'){
		  //echo "=======driver  \n";
		  $payOrder=CarPayDriverOrder::model()->find('order_id=:order_id',array(':order_id'=>$orderId));;
		}else{//user
		   //echo "=========userer  \n";
		  $payOrder=CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$orderId));
		}
		//var_dump($payOrder);
		//echo "payOrder.userId=".$payOrder['user_id']."\n";
               
		//echo "====".count($payOrder);
		 
		if(count($payOrder)){
                        $trans_type=$payOrder['trans_type'];
                        $order_time=$payOrder['trans_time'];

			$ret=true;
			if($upmpPay) {
			   $ret=$upmpPay->query0($trans_type,$order_time,$orderId);
			}
		
			if($ret) {
				//echo "===========isDriver.".$isDriver."\n";	
				if($isDriver == 'true') {
					//echo "updateDriver orderId.".$orderId."\n";
					$re= $this->updateDriverOrder($orderId,'','充值失败、手动补账');	
				}else{
					//echo "updateUser orderId.".$orderId."\n";
					$re= $this->updateOrder($orderId,'','充值失败、手动补账');
				}
				
				if($re) {
					return array('code'=>1,'msg'=>'充值成功');
				}else{
					return array('code'=>0,'msg'=>'充值失败,数据库操作错误');
				}
			}else{
				return array('code'=>0,'msg'=>'充值失败,银行没有交易成功');
			}

				
                }else{
			//echo "order is not exist ,order_id:".$orderId."\n";
			return $result;	
		}
	}

    
        
      /**
      * $param  $param=array('verify'=>0,'trans_time>'=>"$startTime",'trans_time<'=>"$endTime"); 获取order的条件
      **/
      public function payScanAndRepair($param){
	//司机对账
        //获取待对账数据
	EdjLog::info('start to execute payScanAndRepair');
	//echo 'start to execute payScanAndRepair\n';
        $upmpPay=new UpmpPay();
        EdjLog::info(Common::jobBegin('用户充值对账开始'));
        //$param=array('verify'=>0,'trans_time>'=>"$startTime",'trans_time<'=>"$endTime");//注意这种方式比较的正确性
        $userPayData=$this->getUserPayOrderList($param);
        $this->updatePayOrder($userPayData,$upmpPay,false);	
	
	

        $$this->getRealPay($userPayData);
        EdjLog::info(Common::jobEnd('用户充值对账结束'));

        //司机对账
        EdjLog::info(Common::jobBegin('司机充值对账开始'));
        $driverPay=$this->getDriverPayOrderList($param);
        $this->updatePayOrder($driverPay,$upmpPay);

        EdjLog::info(Common::jobEnd('司机充值对账结束'));


      }

      
      /**
      * 执行充值流程1 :核对银联是否成功  2 核对本地是否成功  3 1成功&2不成功  则执行补账程序
      **/
      private function updatePayOrder($payObjData,$upmpPay,$isDriver=true){

	 //echo "===start to execute updatePayOrder isDriver=".$isDriver."\n";
	 foreach($payObjData as $item){   	
     	    $trans_type=$item->trans_type;    //交易类型
            $order_time=$item->trans_time;   //商户交易开始日期时间
            $order_no = $item->order_id;     //商户订单号
	    	
            $result=$upmpPay->query0($trans_type,$order_time,$order_no);//核对银联是否成功
	    //$result=true;//本地模拟远程成功，然后执行数据，看结果 TODO
	    $updateFlag=true;
            if($result) {
		$item->trans_status=2;
		$localResult=$this->checkPaySuccess($order_no,$item,$isDriver);//查看银联已经成功的交易本地是否成功
		if(!$localResult){//银联已经成功，但是本地交易失败，则执行补账流程
			if($isDriver){
				EdjLog::info("司机 充值失败，自动补账 OrderId=".$order_no);
				//echo ("司机 充值失败，自动补账 OrderId=".$order_no);
                                $updateFlag=$this->updateDriverOrder($order_no, '', '充值失败,自动补账');
                        }else{
				EdjLog::info("用户 充值失败，自动补账 OrderId=".$order_no);
				//echo ("用户 充值失败，自动补账 OrderId=".$order_no);
                                $updateFlag=$this->updateOrder($order_no, '', '充值失败,自动补账');
                        }
		}
		
	     }
            //continue;	//TODO 
	    if($updateFlag) {	 
	      $item->verify=1;
              $item->update_time=date('Y-m-d H:i:s');
              $item->update();	
	    }
      } //foreach

     }//updatePayOrder


    /**
     * $data=array("order_id"=>$order_id,"order_amount"=>$order_amount,"create_time"=>$create_time)
     * @param $data array("count"=>$count,"amount"=>sum($order_amount));
     */
    private function countAmount($data) {
        $amount=0;
        foreach($data as $item) {
            $amount += $item['order_amount'];
        }
        return $amount;
    }



    /**
     * 获取用户交易成功数据
     **/
    private function getRealPay($data){
        $upmPay=new UpmpPay();
        $realPayArray=array();
        foreach($data as $item){
            $order_amount=$item->order_amount;
            $create_time=$item->create_time;

            $trans_type=$item->trans_type;    //交易类型
            $trans_time=$item->trans_time;   //商户交易开始日期时间
            $order_id = $item->order_id;     //商户订单号
            $user_id = $item->user_id;     //司机工号或者用户手机号码

            $result=$upmPay->query0($trans_type,$trans_time,$order_id);
	    //$result=true;
            if($result) {
                $realPayArray[]=array("order_id"=>$order_id,"order_amount"=>$order_amount,"create_time"=>$create_time,"user_id"=>$user_id);
             }
        }
        return $realPayArray;
    }


    /**
     *核对银行交易成功的数据是否入账成功
     * $data=array("order_id"=>$order_id,"order_amount"=>$order_amount,"create_time"=>$create_time)
     **/
    private function getLocalPayResult($data,$isDriver=true){

        $successData=array();
        $failData=array();

        foreach($data as $item){
            $order_id=$item['order_id'];
		
		if($isDriver) {
		    $return=$this->checkPaySuccess($order_id,$item);	
		}else{
		    $return=$this->checkPaySuccess($order_id,$item,false);
		}
	
		if($return){	
                    $successData[]=$item;
		}else{
                    $failData[]=$item;
		}
        }//foreach

        return array("success"=>$successData,"fail"=>$failData);
    }

    /**
     *核对银行交易成功的数据是否入账成功,从银联上线以来的所有数据
     * $data=array("order_id"=>$order_id,"order_amount"=>$order_amount,"create_time"=>$create_time)
     **/
    private function getLocalPayResultForAll($data,$isDriver=true){

        $successData=array();
        $failData=array();

        foreach($data as $item){
            $order_id=$item['order_id'];

                if($isDriver) {
                    $return=$this->checkPaySuccessForAll($order_id,$item);
                }else{
                    $return=$this->checkPaySuccessForAll($order_id,$item,false);
                }

                if($return){
                    $successData[]=$item;
                }else{
                    $failData[]=$item;
                }
        }//foreach

        return array("success"=>$successData,"fail"=>$failData);
    }

    /**
    *检查某远程已经成功的交易是否已经在本地充值成功
    **/ 
    private function checkPaySuccess($order_id,$item,$isDriver=true) {
	   if($isDriver){//司机充值
                $times=explode('-',$item['create_time']);
                $YM=$times[0].$times[1];
                $nextMonth=date('Y-m', time()+30*24*60*60);
                $times=explode('-',$nextMonth);
                $YM2=$times[0].$times[1];
                $sql1="select id from t_employee_account_".$YM." where order_id='".$order_id."'";
                $sql2="select id from t_employee_account_".$YM2." where order_id='".$order_id."'";
//                $sql1="select user from t_employee_account_".$YM2." where order_id=".$order_id." or comment like '%".$order_id."%'";
            }else{//用户充值
                $sql1="select id from t_customer_trans where trans_order_id='".$order_id."'";
                $sql2="select id from t_vip_trade where order_id='".$order_id."'";
            }
            if($this->querySQL($sql1) || $this->querySQL($sql2)) {
                return true;
            }else{
                return false;
            }

    }

    /**
    *检查某远程已经成功的交易是否已经在本地充值成功,包含从银联充值上线以来所有时间的
    **/
    private function checkPaySuccessForAll($order_id,$item,$isDriver=true) {

           if($isDriver){//司机充值
                $times=explode('-',$item['create_time']);
                $YM=$times[0].$times[1];
                $nextMonth=date('Y-m', strtotime($times[0].'-'.$times[1].'-10 00:00:00')+30*24*60*60);
                $times=explode('-',$nextMonth);
                $YM2=$times[0].$times[1];
                $sql1="select id from t_employee_account_".$YM." where order_id='".$order_id."' or comment like '%".$order_id."%'";
                $sql2="select id from t_employee_account_".$YM2." where order_id='".$order_id."' or comment like '%".$order_id."%'";
//                $sql1="select user from t_employee_account_".$YM2." where order_id=".$order_id." or comment like '%".$order_id."%'";
            }else{//用户充值
                $sql1="select id from t_customer_trans where trans_order_id='".$order_id."'";
                $sql2="select id from t_vip_trade where order_id='".$order_id."'";
            }

            if($this->querySQL($sql1) || $this->querySQL($sql2)) {
                return true;
            }else{
                return false;
            }

    }

    private function querySQL($sql) {
        $command = Yii::app()->db_finance->createCommand($sql);
        $query = $command->queryAll();
        return count($query);
    }

   


}
