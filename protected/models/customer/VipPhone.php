<?php

/**
 * This is the model class for table "{{vip_phone}}".
 *
 * The followings are the available columns in table '{{vip_phone}}':
 * @property integer $id
 * @property integer $vipcard
 * @property string $name
 * @property string $phone
 * @property string $status
 * @property integer $ctime
 */
class VipPhone extends FinanceActiveRecord
{
    /**
     * 正常
     */
    const STATS_NORMAL = 1;

    /**
     * 禁用
     */
    const STATS_DISABLE = 2;

    /**
     * 删除
     */
    const STATS_DELETE = 3;

    /**
     * 副卡
     */
    const TYPE_VICE = 0;
    /**
     * 主卡
     */
    const TYPE_MAIN = 1;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return VipPhone the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{vip_phone}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'created',
                'numerical',
                'integerOnly' => true),
            array(
                'vipid',
                'length',
                'max' => 15),
            array(
                'name',
                'length',
                'max' => 50),
            array(
                'phone',
                'length',
                'max' => 16),
            array(
                'status,type',
                'length',
                'max' => 1),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'id, vipid, name, phone, status, type, created',
                'safe',
                'on' => 'search'));
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'vipid' => 'VIP 卡号',
            'name' => '姓名',
            'phone' => '手机号',
            'status' => '状态',
            'type' => '卡号类型',
            'created' => '创建/更新时间');
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.


        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('vipid', $this->vipid);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('type', $this->type);
        $criteria->compare('created', $this->created);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria));
    }

    public function updateVipPhone($data, $is_sms = true)
    {
        $return = FALSE;
        $phone = trim($data['phone']);
        $model = $this->findByPk($data['id']);
        $oldPhone = $model['phone'];

        $dataVipPhone = $model->attributes;

        if ($phone != $dataVipPhone['phone']) {
            if ($this->getPrimary($phone))
                return $return;
        }

        $dataVipPhone['name'] = trim($data['name']);
        $dataVipPhone['phone'] = trim($data['phone']);
        $dataVipPhone['status'] = $data['status'];
        $dataVipPhone['type'] = $data['type'];
        $model->attributes = $dataVipPhone;
        if ($model->update()) {

            //重新设置用户token,暂停发送短信  bidong 2014-1-27
            $customerLogic = new CustomerLogic();
            $customerLogic->setCustomerTokenCache($phone);
            //重新设置用户token

            $return = TRUE;
            $dataVipPhone['operator'] = Yii::app()->user->getId();
            $dataVipPhone['description'] = '修改副卡信息';
            $dataVipPhone['created'] = time();
            $dataVipPhone['vipPhone_id'] = $model->id;
            $this->vipPhoneLog($dataVipPhone);
            if ($is_sms) {
                //给绑定的vip副卡发信息
                $vip = Vip::model()->getPrimary($dataVipPhone['vipid']);
                if (!empty($vip) && $data['status'] == self::STATS_NORMAL) {
				  $message = '您已经成为e代驾VIP副卡客户,主卡联系电话' . $vip->phone . '.叫代驾请拨打4006913939或下载e代驾客户端.下载链接 http://wap.edaijia.cn';
                    Sms::SendSMS($phone, $message);
                }
                // 如果修改的是主卡 更新vip表里面的值
                if(1 == $data['type']){
                    $vip['phone']       = $dataVipPhone['phone'];
                    $vip['send_phone']  = $dataVipPhone['phone'];
                    $vip->update();

                    //删除缓存
                    RCustomerInfo::model()->deleteCustomerMain($oldPhone);
                    RCustomerInfo::model()->deleteCustomerMain($dataVipPhone['phone']);
                }
            }
        }
        return $return;
    }

    /**
     * 把vipPhone的手机号置为删除
     * @param $id
     * @return bool
     * author mengtianxue
     */

    public function deleteVipPhone($id)
    {
        $return = false;
        $model = $this->findByPk($id);
        $dataVipPhone = $model->attributes;
        $dataVipPhone['status'] = self::STATS_DELETE;
        $model->attributes = $dataVipPhone;
        if ($model->update()) {
            $return = TRUE;
            $dataVipPhone['operator'] = Yii::app()->user->getId();
            $dataVipPhone['description'] = '删除副卡信息';
            $dataVipPhone['created'] = time();
            $this->vipPhoneLog($dataVipPhone);
        }
        return $return;
    }


    public function createVipPhone($data, $is_sms = true)
    {
        $return = FALSE;
        $phone = trim($data['phone']);

        if (!$this->getPrimary($phone)) {

            $model = new VipPhone();
            $dataVipPhone = $model->attributes;
            $dataVipPhone['vipid'] = $data['vipid'];
            $dataVipPhone['type'] = $data['type'];
            $dataVipPhone['name'] = $data['name'];
            $dataVipPhone['phone'] = $phone;
            $dataVipPhone['status'] = trim($data['status']);
            $dataVipPhone['created'] = time();
            $model->attributes = $dataVipPhone;

            if ($model->save()) {
                //重新设置用户token  bidong 2014-1-27
                $customerLogic = new CustomerLogic();
                $customerLogic->setCustomerTokenCache($phone);
                //重新设置用户token


                $return = TRUE;
                $dataVipPhone['vipPhone_id'] = $model->id;
                $dataVipPhone['operator'] = Yii::app()->user->getId();
                $dataVipPhone['description'] = $data['type'] == 1 ? '添加主卡信息' : '添加副卡信息';
                $this->vipPhoneLog($dataVipPhone);

                if ($is_sms) {
                    //给绑定的vip副卡发信息
                    $vip = Vip::model()->getPrimary($data['vipid']);
                    if(!empty($vip)){
                        $message = '您已经成为e代驾VIP副卡客户,主卡联系电话' . $vip->phone . '.叫代驾请下载e代驾客户端.下载链接 http://wap.edaijia.cn';
                        Sms::SendSMS($phone, $message);
                    }
                }

                //初始化vip用户redis中的信息
                CustomerService::service()->updateLastLoginAndStatus($phone);

            }
        }
        return $return;
    }
    /**
     * 根据电话获取vip卡信息不区分状态
     */
    public function getVipByphone($phone){
        $vipPhone = VipPhone::model()->getPrimary($phone);
        if($vipPhone){
            $vip = Vip::model()->getPrimary($vipPhone['vipid']);
            if($vip){
                return $vip;
            }
        }
        return false;
    }

    /**
     * 根据电话获取vip卡信息
     */
    public function getPrimary($phone)
    {
		$criteria = new CDbCriteria();
		$criteria->compare('phone', $phone);
		$criteria->addCondition('status < :status');
		$criteria->params[':status'] = self::STATS_DISABLE;
		$primary = self::model()->find($criteria);
		if(!empty($primary)){
			return $primary->getAttributes();
		}
		return false;
    }

    /**
     * 根据手机号（可能是副卡）取得vip信息
     *
     * @author sunhongjing 2013-12-28
     * @param string $phone 手机号
     * @param bool $need_balance 是否需要返回余额
     * @return array
     */
    public function getVipInfoByPhone($phone, $need_balance = false)
    {
        $ret = false;
        if (empty($phone)) {
            return $ret;
        }

		$ret = $vip_phone = $this->getPrimary(trim($phone));

        if ($need_balance && !empty($vip_phone)) {
            $vip = Vip::model()->getPrimary($vip_phone['vipid']);
	    if(!$vip){
			EdjLog::info($phone." is ok but vipid ".$vip_phone['vipid'].' is disable');
			return false;
	    }
            $ret['vipcard'] = $vip->id;
            $ret['phone'] = $phone;
            $ret['card_customer_name'] = $vip->name;
            $ret['customer_name'] = $vip_phone['name'];
            $ret['balance'] = $vip->balance;
            $ret['credit'] = $vip->credit;
            $ret['total_balance'] = $vip->balance + $vip->credit;
        }
        return $ret;
    }

	/**
	 * 根据vip卡号获取vip卡所有副卡电话号码
	 *
	 * @param string $vipid
	 * @param bool $getAll 是否获取全部信息(主卡+副卡),否只返回副卡信息
	 * @return array|CActiveRecord|mixed|null
	 */
	public function getVipCardPhone($vipid = '', $getAll = false)
    {
        if (empty($vipid)) {
            return array();
        }
		$criteria = new CDbCriteria();
		$criteria->compare('vipid', $vipid);
        if (!$getAll) {
            $criteria->compare('type', self::TYPE_VICE);
        }
        $criteria->addCondition('status < :status');
        $criteria->params[':status'] = self::STATS_DELETE;
        $criteria->order = "status asc,type desc";//主卡显示在第一个 禁用的副卡显示在最后
		$vipCardPhone = self::model()->findAll($criteria);
        return $vipCardPhone;
    }

	public function getVipCardPhoneByVipId($vipid)
	{
		return $this->getVipCardPhone($vipid, true);
	}


	/**
     * 修改vip状态
     */
    public function updateStatus($id, $status)
    {
        $params = array('status' => $status);
        return $this->updateAll($params,
            'vipid = :vipid', array(':vipid' => $id));
    }

    /**
     * 修改vip类型
     * @param $id
     * @param $phone
     * @param $type
     * @return int
     * @auther mengtianxue
     */
    public function updateType($id, $phone, $type)
    {
        $params = array('type' => $type);
        return $this->updateAll($params,
            'vipid = :vipid and phone = :phone', array(':vipid' => $id, ':phone' => $phone));
    }

    /**
     * vipPhoneLog 修改日志
     * Enter description here ...
     * @param unknown_type $data
     */
    public function vipPhoneLog($data)
    {
        $phone = trim($data['phone']);
		$vipPhoneLog = new VipPhoneLog();
		$vipPhoneLog->setAttributes($data);
		if(!$vipPhoneLog->save()){
			EdjLog::error('save  vip phone log fail vip phone '. $phone. ' error is '.json_encode($vipPhoneLog->getErrors()));
		}
    }

    /**
     * 修改vip phone 手机号
     */
    public function updatePhoneNum($id,$extra)
    {
        //更新状态
        $params = array('status'=>VipPhone::STATS_DISABLE);
        $this->updateAll($params,
            'vipid=:vipid', array(':vipid'=>$id));
        //更新手机号
        $criteria = new CDbCriteria();
        $criteria->compare('vipid', $id);
        $list=self::model()->findAll($criteria);
        foreach($list as $vipphone){
            $phone=$vipphone['phone'];
            $paramsPhone = array('phone'=>$phone.$extra);
            $this->updateAll($paramsPhone,
                'phone=:phone and vipid=:vipid', array(':phone'=>$phone,':vipid'=>$id));
        }
    }

    public function createVipPhoneThirdStage($data, $is_sms = false)
    {
        $return = 0;
        $phone = trim($data['phone']);

        if (!$this->getPrimary($phone)) {

            $model = new VipPhone();
            $dataVipPhone = $model->attributes;
            $dataVipPhone['vipid'] = $data['vipid'];
            $dataVipPhone['type'] = $data['type'];
            $dataVipPhone['name'] = $data['name'];
            $dataVipPhone['phone'] = $phone;
            $dataVipPhone['status'] = trim($data['status']);
            $dataVipPhone['created'] = time();
            $model->attributes = $dataVipPhone;

            if ($model->save()) {
                $return = $model->id;
            }
        }
        return $return;
    }

    public function updateVipPhoneThirdStage($data, $is_sms = true)
    {
        $return = FALSE;
        $phone = trim($data['phone']);
        $model = $this->findByPk($data['id']);
        $oldPhone = $model['phone'];

        $dataVipPhone = $model->attributes;

        if ($phone != $dataVipPhone['phone']) {
            if ($this->getPrimary($phone))
                return $return;
        }

        $dataVipPhone['name'] = trim($data['name']);
        $dataVipPhone['phone'] = trim($data['phone']);
        $dataVipPhone['status'] = $data['status'];
        $dataVipPhone['type'] = $data['type'];
        $model->attributes = $dataVipPhone;
        if ($model->update()) {

            $return = true;
        }
        return $return;
    }


}


