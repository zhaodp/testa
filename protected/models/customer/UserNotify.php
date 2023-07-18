<?php

/**
 * This is the model class for table "t_user_notify".
 *
 * The followings are the available columns in table 't_user_notify':
 * @property integer $Id
 * @property string $city_id
 * @property integer $user_type
 * @property integer $notify_type
 * @property integer $client_os_type
 * @property string $client_version_lowest
 * @property string $sdate
 * @property string $edate
 * @property integer $status
 */
class UserNotify extends CActiveRecord
{
    public static $NOTIFY_TYPE_BANNER = 1;
    public static $NOTIFY_TYPE_MSG = 0;
    public static $NOTIFY_STATUS_ON = 0;
    public static $NOTIFY_STATUS_OFF = 1;
    public static $NOTIFY_USER_TYPE_ALL = -1;
    public static $NOTIFY_USER_TYPE_NEW = 1;
    public static $CLIENT_OS_TYPE_ALL = -1;
    public static $CLIENT_OS_TYPE_ANDROID = 1;
    public static $CLIENT_OS_TYPE_IOS = 0;
    public static $TRIGGER_CONDITION_NEARBY = 1;
    public static $TRIGGER_CONDITION_NOW = 2;
    public static $CITY_ID_ALL = -1;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_notify}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('user_type, sdate, edate,notify_type,client_os_type,city_id', 'required'),
			array('notify_type, client_os_type, status', 'numerical', 'integerOnly'=>true),
			array('client_version_lowest,user_type', 'length', 'max'=>255),
            array('city_id', 'length', 'max'=>500),
			array('ope_time', 'length', 'max'=>20),
            array('sdate, edate', 'validateDate'),
            array('city_id', 'validateCityId'),
            array('client_version_lowest', 'verisonValidate'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Id, city_id,user_type,notify_type, client_os_type, client_version_lowest,sdate, edate, status', 'safe', 'on'=>'search'),
		);
	}
    public function validateCityId(){
        if(!$this->hasErrors())
        {
            if (empty($this->city_id)) {
                $this->addError('city_id','城市不可空');
            }

        }
    }
    public function validateDate(){
        if(!$this->hasErrors())
        {
            if ($this->edate<$this->sdate) {
                $this->addError('sdate','开始时间不能晚于截止时间');
            }

        }
    }
    public function verisonValidate(){
        if(!$this->hasErrors())
        {
            if (!preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $this->client_version_lowest)) {
                $this->addError('client_version_lowest','版本号不符合规则');
            }

        }
    }
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        return array(
            'UserNotifyBanner'=>array(self::HAS_ONE, 'UserNotifyBanner', 't_user_notify_id'),
            'UserNotifyMsg'=>array(self::HAS_ONE, 'UserNotifyMsg', 't_user_notify_id'),
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'city_id' => '选择适用的城市（多选）',
			'user_type' => '选择用户属性',//（多选）
			'notify_type' => '选择通知的类型',
			'client_os_type' => '客户手机操作系统类型',
			'client_version_lowest' => '填写客户端试用最低版本',
			'sdate' => '生效的时间区间',
			'edate' => '结束时间',
            'ope_time' => '配置日期',
            'ope_people' => '操作人',
           // 'user_count' => '影响用户数',
			'status' => '通知状态',
		);
	}


	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search($pageSize = 20)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('Id',$this->Id);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('user_type',$this->user_type);
		$criteria->compare('notify_type',$this->notify_type);
		$criteria->compare('client_os_type',$this->client_os_type);
		$criteria->compare('client_version_lowest',$this->client_version_lowest,true);
		$criteria->compare('sdate',$this->sdate,true);
		$criteria->compare('edate',$this->edate,true);
		$criteria->compare('status',$this->status);

        $criteria->order="id DESC";
        return new CActiveDataProvider($this, array(
            'pagination' => array(
                'pageSize' => $pageSize
            ),
            'criteria' => $criteria
        ));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserNotify the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * $cityId 一个城市，app_ver可能为空，为空所有，1.1.1格式，is_new_user：不可，1新，0老用户，notify_type不可空，type操作系统，不可空，
     * @param array $param
     * 返回对象 UserNotify，可为空，不发
     */
	public  function getUserNotify($param){
		//返回最后一个UserNofify对象，最新的
        //getUserNotify(array("city_id"=>$cityId,"app_ver"=>$appVer,"is_new_user"=>$isNewUser,"notify_type"=>$notifyType));
        if (!isset($param['city_id']) || !isset($param['is_new_user'] )|| !isset($param['notify_type']) || !isset($param['type'])) {
            return;
        }
        $now = time();
        $criteria=new CDbCriteria;
        $criteria->compare('status',self::$NOTIFY_STATUS_ON);

        $criteria->addCondition('sdate<=:now');
        $criteria->params[':now'] = $now;

        $criteria->addCondition('edate>=:now2');
        $criteria->params[':now2'] = $now;

       if (isset($param['app_ver'])) {
           $criteria->addCondition('client_version_lowest<=:client_version_lowest');
           $criteria->params[':client_version_lowest'] = $param['app_ver'];
       }
        if (isset($param['city_id'])) {
            if($param['city_id']!=0){
                $criteria->addCondition("((city_id = :all_city) OR (city_id LIKE :city_id))");
                $criteria->params[':all_city']=self::$CITY_ID_ALL;
                $criteria->params[':city_id'] ='%,'.strtr($param['city_id'],array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).',%';
            }
        } else {
            $criteria->compare('city_id',self::$CITY_ID_ALL);
        }
        if (isset($param['is_new_user']) && $param['is_new_user']==1) {

            $criteria->addCondition("((user_type = :all_user )OR (user_type LIKE :user_type))");
//            $params[':all_user'] ='%'. self::$NOTIFY_USER_TYPE_ALL.'%';
            $criteria->params[':all_user']=self::$NOTIFY_USER_TYPE_ALL;
            $criteria->params[':user_type'] ='%,'.strtr($param['is_new_user'],array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).',%';;

        } else {
            $criteria->compare('user_type',self::$NOTIFY_USER_TYPE_ALL);
        }
        if (isset($param['notify_type'])) {
            $criteria->compare('notify_type',$param['notify_type']);
        }
        if (isset($param['type'])) {
            $criteria->addCondition('(client_os_type=:client_os_type or client_os_type=:client_all)');
            $criteria->params[':client_os_type'] = $param['type'];
            $criteria->params[':client_all'] = self::$CLIENT_OS_TYPE_ALL;
        }
        $criteria->order="id DESC";
        $userNotify=$this->find($criteria);
        return $userNotify;
	}


}
