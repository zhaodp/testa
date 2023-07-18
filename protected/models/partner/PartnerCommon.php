<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-10-11
 * Time: �??6:47
 * To change this template use File | Settings | File Templates.
 */
class PartnerCommon
{

    //???�?��??���??�??
    private $partner_user_password_key = 'EDAIJIA';
    private $partner_login_key = 'CDKSDKS';

    public $forbid_sms_channel = array(
        '03007',
        '03008',
        '03009',
        '03010',
        '03011',
        '03014',
    );

    /**
     * ???�??
     * @param $txt  ?????????�?     * @param $key  �??
     * @return string
     */
    private function encrypt($txt, $key) {
        srand((double)microtime() * 1000000);
        //$encrypt_key = md5(rand(0, 32000));
        $encrypt_key = md5(31000);
        $ctr = 0;
        $tmp = '';
        for($i = 0;$i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
        }
        return base64_encode($this->passport_key($tmp, $key));
    }

    /**
     * 解�?
     * @param $txt ???解�????�?     * @param $key �??
     * @return string
     */
    private function decrypt($txt, $key) {
        $txt = $this->passport_key(base64_decode($txt), $key);
        $tmp = '';
        for($i = 0;$i < strlen($txt); $i++) {
            $md5 = $txt[$i];
            $tmp .= $txt[++$i] ^ $md5;
        }
        return $tmp;
    }

    private function passport_key($txt, $encrypt_key) {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';
        for($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }

    /**
     * ??????�?????�?     * @param $password ???�??
     * @return string
     */
    public function passwordEncrypt($password) {
        return $this->encrypt($password, $this->partner_user_password_key);
    }

    /**
     * ??????�????���?     * @param $password ????????     * @return string
     */
    public function passwordDecrypt($password) {
        return $this->decrypt($password, $this->partner_user_password_key);
    }

    /**
     * 合作商家账户加密
     * @param $password
     * @return string
     */
    public function loginEncrypt($password) {
        return $this->encrypt($password, $this->partner_login_key);
    }

    /**
     * 合作商家账户解密
     * @param $password
     * @return string
     */
    public function loginDecrypt($password) {
        return $this->decrypt($password, $this->partner_login_key);
    }

    public function getBonusSurplus($partner_id) {
        $partner = Partner::model()->findByPk($partner_id);
        $surplus = 0;
        if ($partner) {
            $data = CustomerBonus::model()->getBonusUsedSummary($partner->bonus_phone, $partner->bonus_sn);
            if ($data) {
                $surplus = intval($data['totle_num']) - intval($data['used_num']);
            }
        }
        return $surplus;
    }

	/**
	 * 判断商家的优惠券是否可以使用
	 *
	 * @param $partner_id
	 * @param string $bonusSn
	 * @return mixed
	 */
	public function isBonusIllegal($partner_id, $bonusSn = ''){
		$code = 0;
		if(empty($bonusSn)){
			$partner = Partner::model()->findByPk($partner_id);
			if($partner){
				$bonusSn = $partner->bonus_sn;
			}
		}
		//get bonusCode
		$bonusCode = $this->getBonusCodeByBonusSn($bonusSn);
		if(!$bonusCode){
			$code = 1;
			return array(
				'code'		=> $code,
				'bonusSn'	=> $bonusSn,
			);
		}
		$bonusStatus = $bonusCode['status'];
		//未审核不能使用
		if(BonusCode::STATUS_APPROVED  != $bonusStatus){
			$code = 2;
		}
		$nowTime = time();
		//优惠券必须生效
		$effectiveDate = $bonusCode['effective_date'];
		if(empty($effectiveDate) || (strtotime($effectiveDate) > $nowTime)){
			$code = 3;
		}
		//优惠券必须没有结束
		$endDate = $bonusCode['end_date'];
		if(empty($endDate) || (strtotime($endDate) < $nowTime)){
			$code = 4;
		}
		return array(
			'code'		=> $code,
			'bonusSn'	=> $bonusSn,
		);
	}

    public function getVipBalance($phone) {
        $vip = VipPhone::model()->getPrimary($phone);
        if ($vip) {
            $vip_info = Vip::model()->findByPk($vip['vipid']);
            return $vip_info ? $vip_info->balance : 0;
        } else {
            return false;
        }

    }

    public function getCriteria($channel, $call_start, $call_end, $city_id=0, $phone=null, $status=null){
        $criteria=new CDbCriteria;

        $criteria->addCondition("channel='{$channel}'");

        if ($city_id>0) {
            $criteria->addCondition('city_id='.$city_id);
            //$criteria->addCondition('city_id=:city_id');
            //$criteria->params[':city_id'] = $city_id;
        }

        if ($phone) {
            $criteria->addCondition("contact_phone='{$phone}'");
            //$criteria->addCondition('contact_phone=:phone');
            //$criteria->params[':phone'] = $phone;
        }

        if (is_array($status) && count($status)) {
            if (count($status) == 1) {
                //$criteria->addCondition('status=:status'.implode(',', $status));
                //$criteria->params[':status'] = implode(',', $status);
                $criteria->addCondition('status='.implode(',', $status));
            } else {
                $criteria->addInCondition('status', $status);
            }
        } elseif (is_numeric($status)) {
            $criteria->addCondition('status='.$status);
            //$criteria->addCondition('status=:status');
            //$criteria->params[':status'] = $status;
        }

        if ($call_start && $call_end) {
            $criteria->addCondition('call_time>='.intval(strtotime($call_start)).' and call_time<'.intval(strtotime($call_end)+86400));
            //$criteria->addCondition('call_time>=:call_start and call_time<:call_end');
            //$criteria->params[':call_start'] = strtotime($call_start);
            //$criteria->params[':call_end'] = strtotime($call_end)+86400;
        }

        $criteria->order = 'order_id desc';
        return $criteria;
    }

    public function getDataProvider($criteria) {
        $data = new CActiveDataProvider('Order', array (
                'criteria'=>$criteria,
                'pagination'=>array (
                    'pageSize'=>15)
            )
        );
        return $data;
    }

    public function getUsedBonusTotal($phone, $channel_id) {
        $num = 0;
        $order_list = Order::getDbReadonlyConnection()->createCommand()
            ->select('order_id')
            ->from('t_order')
            ->where('channel=:channel and contact_phone =:contact_phone', array(':channel'=>$channel_id, ':contact_phone'=>$phone))
            ->queryAll();
        if (is_array($order_list) && count($order_list)) {
            foreach($order_list as $order) {
                $order_id = $order['order_id'];
                $bonus = CustomerBonus::model()->checkedBonusUseByOrderID($order_id, 1);
                if ($bonus) {
                    $num++;
                }
            }
        }
        return $num;
    }

    /**
     * 通过 order_queue_id 判断该订单渠道是否可以给客户发短信
     * @param $order_queue_id
     * @return bool
     */
    public function checkForbidSmsByQueue($order_queue_id) {
        $order_queue_model = new OrderQueue();
        $channel = $order_queue_model->getOrderQueueChannel($order_queue_id);
        return $this->checkForbidSmsByChannel($channel);
    }

    /**
     * 通过 order_id 判断该订单渠道是否可以给客户发短信
     * @param $order_id
     * @return bool
     */
    public function checkForbidSmsByOrder($order_id) {
        $order_model = new Order();
        $channel = $order_model->getOrderChannel($order_id);
        return $this->checkForbidSmsByChannel($channel);
    }

    /**
     * 通过订单渠道是否可以给客户发短信
     * @param channel
     * @return bool
     */
    public function checkForbidSmsByChannel($channel) {
        return !$this->getForbidSmsChannel($channel);
        /*
        if ($channel && in_array($channel, $this->forbid_sms_channel)) {
            return true;
        } else {
            return false;
        }
        */
    }

    /**
     * 查看某个合作商家是否禁止给客户发送短信
     * @return int
     */
    public function getForbidSmsChannelByDb($channel_id) {
        $send_sms = Yii::app()->db_readonly->createCommand()
            ->select('send_sms')
            ->from('t_partner')
            ->where('channel_id=:channel_id', array(':channel_id'=>$channel_id))
            ->queryScalar();
        return intval($send_sms);
    }

    public function getForbidSmsChannel($channel_id) {
        if ($channel_id) {
            $key = 'PARTNER_CAN_SEND_SMS_'.$channel_id;
            $cached = new DriverInterviewCache();
            $res = $cached->get($key);
            if($res === "0"){
                return false;
            }else return true;
        } else {
            return true;
        }
    }

    public function loadPartnerRedis($channel_id) {
        $key = 'PARTNER_CAN_SEND_SMS_'.$channel_id;
        $cached = new DriverInterviewCache();
        $send_sms = $this->getForbidSmsChannelByDb($channel_id);
        return $cached->set($key, intval($send_sms));
    }

	/**
	 * 根据bonus sn获得bonus code
	 *
	 * @param string $bonusSn
	 * @return mixed|null
	 */
	private function getBonusCodeByBonusSn($bonusSn = ''){
		if(empty($bonusSn)){
			return null;
		}
		//1.get bonus library
		$criteria = new CDbCriteria();
		$criteria->compare('bonus_sn', $bonusSn, false);
		$bonusLibrary = BonusLibrary::model()->find($criteria);
		//get bonus code
		if($bonusLibrary){
			$bonusId = $bonusLibrary->bonus_id;
			$bonusCode = BonusCode::model()->getBonusCodeById($bonusId);
			return $bonusCode;
		}
		return null;
	}

}
