<?php

/**
 * This is the model class for table "{{partner}}".
 *
 * The followings are the available columns in table '{{partner}}':
 * @property string $id
 * @property string $name
 * @property integer $city
 * @property string $contact
 * @property string $phone
 * @property string $channel_id
 * @property string $seat_number
 * @property string $address
 * @property string $sms_call
 * @property integer $pay_sort
 * @property string $sharing_amount
 * @property string $vip_card
 * @property string $bonus_sn
 * @property string $bonus_phone
 * @property integer $status
 * @property string $created
 * @property string $updated
 * @property string $ip
 */
class Partner extends CActiveRecord
{
    //用户付费类别
    const  PAY_SORT_DIVIDED = 1;    //报单分成
    const  PAY_SORT_BONUS = 2;      //优惠券减免
    const  PAY_SORT_VIP = 3;        //VIP全额免单
    //商家状态
    const PARTNER_STATUS_ENABLE = 0;    //正常
    const PARTNER_STATUS_SHIELDED = 1;  //屏蔽
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Partner the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{partner}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, city, contact, seat_number, phone, channel_id, sms_call', 'required'),
			array('city, pay_sort, status, send_sms, remark,show_balance', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>100),
			array('contact', 'length', 'max'=>50),
			array('phone, vip_card, bonus_phone', 'length', 'max'=>15),
			array('channel_id', 'length', 'max'=>5),
			array('seat_number', 'length', 'max'=>11),
			array('address', 'length', 'max'=>200),
			array('sms_call, bonus_sn, ip', 'length', 'max'=>30),
			array('sharing_amount', 'length', 'max'=>10),
            array('logo', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, city, contact, phone, channel_id, seat_number, address, sms_call, pay_sort, sharing_amount, vip_card, bonus_sn, bonus_phone, status, created, updated, ip', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => '商家名称',
			'city' => '城市',
			'contact' => '商家联系人',
			'phone' => '商家电话',
			'channel_id' => '渠道',
			'seat_number' => '坐席人数',
			'address' => '商家账单地址',
			'sms_call' => '收信人称呼',
			'pay_sort' => '商家付费类型',
			'sharing_amount' => '分成金额',
			'vip_card' => 'VIP手机号',
			'bonus_sn' => '优惠券编码',
			'bonus_phone' => '优惠券绑定电话',
            'send_sms' => '是否给用户发送短信',
            'remark' => '是否显示订单备注',
			'show_balance' => '是否显示余额',
			'status' => '状态',
			'created' => '创建时间',
			'updated' => '更新时间',
            'logo' => 'LOGO',
			'ip' => 'Ip',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
        if($this->city !=0){
		    $criteria->compare('city',$this->city);
        }
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('channel_id',$this->channel_id,true);
		$criteria->compare('seat_number',$this->seat_number,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('sms_call',$this->sms_call,true);
		$criteria->compare('pay_sort',$this->pay_sort);
		$criteria->compare('sharing_amount',$this->sharing_amount,true);
		$criteria->compare('vip_card',$this->vip_card,true);
		$criteria->compare('bonus_sn',$this->bonus_sn,true);
		$criteria->compare('bonus_phone',$this->bonus_phone,true);
        if($this->status != '')
		    $criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('ip',$this->ip,true);
        $criteria->compare('logo',$this->ip,true);

        $criteria->compare('send_sms',$this->send_sms);
        $criteria->compare('remark',$this->remark);
		$criteria->compare('show_balance',$this->show_balance);
        $criteria->order = 'created desc';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


    /**
     * 保存之前更新字段
     * @return bool
     * author daiyihui
     */
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->created = date("Y-m-d H:i:s");
            }else
                $this->updated = date("Y-m-d H:i:s");
            return true;
        }else
            return false;
    }

    /**
     * 更新状态
     * @param $id
     * @param $status
     * @return bool
     */
    public function updateStatus($id, $status){
        $model = Partner::model()->findByPk($id);
        $model = Yii::app()->db;
        if ($model) {
            $model->status = $status;
            if($model->save()){
                return true;
            }else
                return false;
        } else {
            return false;
        }
        /*
        if($updateStatus){
            return true;
        }else
            return false;
        */
    }

    /**
     * 获取商家ID和名称
     * @return array
     */
    public function getPartnerList($type = '')
    {
        $partnerArr = $this->model()->findAll();
        $nameArr = array();
        $channelArr = array();
        foreach($partnerArr as $v){
            $nameArr[$v['channel_id']] = $v['name'];
            $channelArr[] = $v['channel_id'];
        }
        if(empty($type)){
            return $nameArr;
        }else{
            return $channelArr;
        }
    }

    /**
     * 根据channel_id 获取商家名称
     * @param $channel_id
     * @return mixed
     */
    public function getPartnerName($channel_id, $type = '')
    {
        if(!empty($channel_id)){
            $channel = self::model()->find('channel_id = :channel', array(':channel' => $channel_id));

            if(empty($type)){
                return $channel['name'];
            }else if ( $type == 1){
                if($channel['vip_card'] != ''){
                    return self::model()->getVipBalance($channel['vip_card']);
                }else
                    return 0;
            }else if($type == 2){
                if(!empty($channel['bonus_sn']) && $channel['bonus_phone']){
                     $bonus_number = CustomerBonus::model()->getBonusUsedSummary($channel['bonus_phone'], $channel['bonus_sn']);
                     return $bonus_number['totle_num'] - $bonus_number['used_num'];
                }else{
                    return 0;
                }
            }elseif($type == 3){
                return $channel['sharing_amount'] ? $channel['sharing_amount'] : 0;
            }else if($type == 4){
                return $channel;
            }else{

            }
        }else
            return false;

    }

    public function getPartnerSharingTotal($channel_id, $count_complate)
    {
        if(!empty($channel_id)){
            if($count_complate == 0)
                return 0;
            $sharing_amount = $this->getPartnerName($channel_id, 3);
            return $sharing_amount > 0 ? $sharing_amount * $count_complate : 0;
        }else
            return false;
    }

    /**
     *查询VIP或优惠券是否已被商家使用
     * @param string $param
     * @param int $type
     * @return bool
     */
    public function isCheckVipAndBonus($param = '', $type = 1)
    {
        if(!empty($param)){
            if($type == 1)
                $partnerInfo = self::model()->find('vip_card = :vip_card', array(':vip_card' => $param));
            elseif($type == 2)
                $partnerInfo = self::model()->find('bonus_phone = :bonus_phone', array(':bonus_phone' => $param));
            else
                $partnerInfo = self::model()->find('bonus_sn = :bonus_sn', array(':bonus_sn' => $param));
            return $partnerInfo['name'];
        }else
            return false;
    }

    public function getVipBalance($vip_card)
    {
        if(!empty($vip_card)){
            $PartnerCommon = new PartnerCommon();
            return $PartnerCommon->getVipBalance($vip_card);
        }else
            return 0;

    }

    public function afterSave() {
        $common = new PartnerCommon();
        $common->loadPartnerRedis($this->channel_id);
        return parent::afterSave();
    }
}