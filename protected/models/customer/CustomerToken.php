<?php

/**
 * This is the model class for table "{{customer_token}}".
 *
 * The followings are the available columns in table '{{customer_token}}':
 * @property integer $id
 * @property string $phone
 * @property string $authtoken
 * @property string $udid
 * @property string $mac_address
 * @property string $device_type
 * @property integer $login_status
 * @property integer $expired
 * @property string $create_time
 */

class CustomerToken extends CActiveRecord
{
    /**
     * 登陆默认状态
     */
    const LOGIN_DEFAULT = 0;
    /**
     * 登陆状态正常
     */
    const LOGIN_NORMAL = 1;

    /**
     * 登陆状态禁止
     */
    const LOGIN_PROHIBIT = 2;

    /**
     * e代驾客户登录token标识
     */
    const EDJ_TOKEN_FROM = 'edaijia';
    const EDJ_TOKEN_FROM_H5 = 'edaijia_h5';
    const XICHE_TOKEN_FROM = 'wash';

    public static $business_list = array(
        self::EDJ_TOKEN_FROM,
        self::XICHE_TOKEN_FROM,
        self::EDJ_TOKEN_FROM_H5,
    );

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerToken the static model class
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
        return '{{customer_token}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('phone, authtoken, udid, mac_address, expired, create_time', 'required'),
            array('login_status, expired', 'numerical', 'integerOnly'=>true),
            array('phone, authtoken', 'length', 'max'=>32),
            array('business', 'length', 'max'=>45),
            array('udid, mac_address, device_type,device_token', 'length', 'max'=>64),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, phone, authtoken, udid, mac_address, device_type, login_status, expired, create_time,device_token', 'safe', 'on'=>'search'),
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
            'phone' => 'Phone',
            'authtoken' => 'Authtoken',
            'udid' => 'Udid',
            'mac_address' => 'Mac Address',
            'device_type' => 'Device Type',
            'login_status' => 'Login Status',
            'expired' => 'Expired',
            'create_time' => 'Create Time',
            'device_token' => 'device_token',
            'business' => 'business',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('phone',$this->phone);
        $criteria->compare('authtoken',$this->authtoken);
        $criteria->compare('udid',$this->udid);
        $criteria->compare('mac_address',$this->mac_address);
        $criteria->compare('device_type',$this->device_type);
        $criteria->compare('login_status',$this->login_status);
        $criteria->compare('expired',$this->expired);
        $criteria->compare('business',$this->business);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     *
     * 登录成功的客户获取token
     * @param string $phone
     */
    public function createAuthtoken($phone, $udid, $macaddress) {
        $data = array("phone" => $phone, "udid" => $udid, "macaddress" => $macaddress);
        $customertoken = $this->checkCustomerToken($data);
        if ($customertoken) {
            if($customertoken['login_status'] == self::LOGIN_PROHIBIT || $customertoken['login_status'] == self::LOGIN_DEFAULT)
                $this->updateAuthToken($data);
            return $customertoken['authtoken'];
        } else {
            return null;
        }
    }

    /**
     * 退出登陆
     * Enter description here ...
     * @param unknown_type $phone
     */
    public function deleteAuthToken($phone,$business){
        $back = FALSE;
        $attributes = array (
            'login_status'=>self::LOGIN_PROHIBIT);
        //删除过期的token
        $updateCount = $this->updateAll($attributes, 'phone = :phone and business=:business', array(':phone' => $phone, ':business'=>$business));
        if($updateCount){
            $back = TRUE;
        }
        return $back;
    }

    /**
     * 退出登陆
     * Enter description here ...
     * @param unknown_type $phone
     */
    public function deleteAuthTokenWithoutOne($phone, $token, $business=self::EDJ_TOKEN_FROM){
        $token_arrs = Yii::app()->db_readonly->createCommand()
	    ->select("authtoken")
	    ->from("t_customer_token")
	    ->where("phone = :phone and business=:business and login_status != :status",
		    array(':phone' => $phone,':business'=>$business, ':status' => self::LOGIN_PROHIBIT))
	    ->queryAll();
	    
	// Delete the tokens
        $customer_logic = new CustomerLogic();
	foreach($token_arrs as $del_token) {
	    // delete the token in cache
	    if($del_token['authtoken'] == $token) {
		continue;
	    }

	    // Delete the tokens
	    $customer_logic->clearCustomerTokenCache($del_token['authtoken']);
	}

        $back = FALSE;
        $attributes = array (
            'login_status'=>self::LOGIN_PROHIBIT);
        //删除过期的token
        $updateCount = $this->updateAll($attributes, 'phone = :phone and business=:business and authtoken != :token', 
		array(':phone' => $phone,'business'=>$business, ':token' => $token));
        if($updateCount){
            $back = TRUE;
        }
        return $back;
    }

    /**
     * 修改登陆信息
     * @author mengtianxue 2013-05-21
     * @param unknown_type $data
     */
    public function updateAuthToken($data){
        $attributes = array (
            'login_status'  =>  self::LOGIN_NORMAL,
            'authtoken'     =>  $data['authtoken'],
            'expired'       =>  strtotime("+50 month"));

        //修改过期的token
        $updateCount = $this->model()->updateAll($attributes,
                                        'phone = :phone and udid = :udid',
                                        array(':phone' => $data['phone'], ':udid' => $data['udid']));
        if($updateCount){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * 修改登陆信息
     * @author aiguoxin
     */
    public function updateAuthTokenByBusiness($data){
        $attributes = array (
            'login_status'  =>  self::LOGIN_NORMAL,
            'authtoken'     =>  $data['authtoken'],
            'expired'       =>  strtotime("+50 month"));

        //修改过期的token
        $updateCount = $this->model()->updateAll($attributes,
                                        'phone = :phone and udid = :udid and business=:business',
                                        array(':phone' => $data['phone'], ':udid' => $data['udid'], ':business' => $data['business']));
        if($updateCount){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * 检查登录Token是否存在,是否登录状态
     * @author bidong 2013-05-10
     * @param string $token
     * @return bool
     */
    public function validateToken($token) {
        if ($token == '2e5893f207e01f99b19f49fc962f7c6b') {
            return false;
        }
        $flag = array();
        $customerLogic = new CustomerLogic();
        //检查token是否存在
        $customerToken = $customerLogic->getCustomerTokenCache($token);
        if($customerToken)
            $flag = $customerToken;
        return $flag;

    }

    /**
     * 添加数据
     * @author mengtianxue 2013-05-21
     * @param unknown_type $params
     */

    public function addCustomerToken($params){
        $customerToken = new CustomerToken();
        $data = array();
        $mac_address = $params['macaddress'];
        unset($params['macaddress']);

        $data = $params;
        $data['mac_address'] = $mac_address;
        $data['login_status'] = self::LOGIN_NORMAL;
        $data['expired'] = strtotime("+50 month");
        $data['create_time'] = date('Y-m-d H:i:s');
        $customerToken->attributes = $data;
        if($customerToken->insert()){
            return TRUE;
        }else{
            return FALSE;
        }
    }


    /**
     *
     * 初始化客户token,注释啊注释，不是这么写的
     * @author mengtianxue 2013-05-21
     * @param unknown_type $data
     */
    public function initCustomerToken($data){
        //是否存在
        $customerToken = $this->checkCustomerToken($data);
        //如果不存在保存数据
        if (!$customerToken){
            $data['mac_address'] = $data['macaddress'];
            unset($data['macaddress']);
            $data['expired'] = strtotime("+50 month");
            $data['create_time'] = date('Y-m-d H:i:s');
            $customerToken = new CustomerToken();
            $customerToken->attributes = $data;
            $customerToken->insert();
        }
        return $customerToken;
    }

    /**
     * 检查客户的token
     * @author mengtianxue 2013-05-21
     * @param unknown_type $data
     */
    public function checkCustomerToken($data){
        $ret = array();
        //先验证数据
        if( empty($data) || empty( $data['phone'] ) || empty($data['udid']) ){
            return $ret;
        }

        return Yii::app()->db_readonly->createCommand()
                        ->select("*")
                        ->from("t_customer_token")
                        ->where("phone = :phone and udid = :udid",
                                    array(':phone' => $data['phone'], ':udid' => $data['udid']))
                        ->queryRow();
    }


    /**
    *   根据业务和手机号检查token
    *   @author aiguoxin
    */
    public function checkCustomerTokenByBusiness($data){
        $ret = array();
        //先验证数据
        if( empty($data) || empty( $data['phone'] ) || empty($data['udid']) || empty($data['business']) ){
            return $ret;
        }

        return Yii::app()->db_readonly->createCommand()
                        ->select("*")
                        ->from("t_customer_token")
                        ->where("phone = :phone and udid=:udid and business = :business",
                                    array(':phone' => $data['phone'], ':udid' => $data['udid'], ':business' => $data['business']))
                        ->queryRow();
    }


    /**
     * 通过udid取得用户token信息
     *
     * @author sunhongjing 2013-08-15
     *
     * @param unknown_type $udid
     * @return mix
     */
    public function getInfobyUdid($udid)
    {
        $ret = array();
        //先验证数据
        if( empty($udid) ){
            return $ret;
        }

        return Yii::app()->db_readonly->createCommand()
                        ->select("*")
                        ->from("t_customer_token")
                        ->where("udid = :udid", array( ':udid' => $udid))
                        ->queryRow();
    }


    /**
     * 设置deviceToken
     * @author zhanglimin 2013-07-12
     * @param $params
     */
    public function setDeviceToken($params){
       $deviceToken =  CustomerToken::model()->find('udid=:udid',
           array(
               'udid'=>$params['udid']
           ));
       if(empty($deviceToken)){
           //插入
           $deviceToken = new CustomerToken();
           $params['expired'] = strtotime("+50 month");
           $params['create_time'] = date('Y-m-d H:i:s');
           $deviceToken->attributes = $params;
           $deviceToken->insert();
       }else{
           //更新
           $deviceToken->device_token = $params['device_token'];
           $deviceToken->device_type = $params['device_type'];
           $deviceToken->update(array('device_token','device_type'));
       }
       return true;
    }


    /**
     * 通过phone取得用户udid信息
     *
     * @author aiguoxin 2014-04-09
     *
     * @param string phone
     * @return mix
     */
    public function getUdidbyPhone($phone)
    {
        $ret = array();
        //先验证数据
        if(empty($phone)){
            return $ret;
        }
        $connection = Yii::app()->db_readonly;
        $sql = "SELECT udid FROM `t_customer_token` where phone=$phone ORDER BY id DESC";
        $command = $connection->createCommand($sql);
        $command->bindParam(':phone',$phone);
        $result = $command->queryRow();
        return $result;
    }
}




