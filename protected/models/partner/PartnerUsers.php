<?php

/**
 * This is the model class for table "{{partner_users}}".
 *
 * The followings are the available columns in table '{{partner_users}}':
 * @property string $id
 * @property string $partner_id
 * @property string $username
 * @property string $password
 * @property string $created
 * @property integer $status
 * @property string $login_ip
 * @property string $login_time
 */
class PartnerUsers extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PartnerUsers the static model class
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
		return '{{partner_users}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('partner_id, username, password', 'required'),
			array('partner_id', 'length', 'max'=>11),
			array('username', 'length', 'max'=>30),
			array('password', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, partner_id, username, password, created, status, login_ip, login_time', 'safe', 'on'=>'search'),
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
			'partner_id' => 'Partner',
			'username' => 'Username',
			'password' => 'Password',
			'created' => 'Created',
			'status' => 'Status',
			'login_ip' => 'Login Ip',
			'login_time' => 'Login Time',
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
		$criteria->compare('partner_id',$this->partner_id,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('login_ip',$this->login_ip,true);
		$criteria->compare('login_time',$this->login_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 新建商家用户
     * @param $partnerId
     * @param $seatNumber
     * @param $created
     * @return bool
     */
    public function createPartnerUser($partnerId, $seatNumber, $created)
    {
        if(!empty($partnerId) && !empty($seatNumber) && !empty($created)){
            $partnerUserArray = array();
            $returnInsertArray = array();
            for($i = 1; $i <= $seatNumber; $i++){
                $partnerCommon = new PartnerCommon();
                $partnerUserArray['partner_id'] = $partnerId;
                if(strlen($i) < 3){
                    $partnerUserArray['username'] = $partnerId.str_pad($i, 3, '0', STR_PAD_LEFT);
                }else{
                    $partnerUserArray['username'] = $partnerId.$i;
                }
                $partnerUserArray['password'] = $partnerCommon->passwordEncrypt(Common::createRandNumber(7, 1));
                $this->setIsNewRecord(true);
                $this->attributes = $partnerUserArray;
                $this->id = null;
                $this->created = $created;
                if($this->save()){
                    $returnInsertArray[] = $this->id;
                }
            }
            if(count($returnInsertArray) == $seatNumber){
                return true;
            }else{
                return false;
            }
        }else
            return false;
    }

    /**
     * 导出商家用户
     * @param $partner_id
     * @return array
     */
    public function getPartnerUserInfo($partner_id)
    {
        if(!empty($partner_id)){
            $list = $this->model()->findAll('partner_id = :partner_id', array(':partner_id' => $partner_id));
            $userList = array();
            $partnerCommon = new PartnerCommon();
            foreach($list as $k => $v){
                $userList[$k]['username'] = $v['username'];
                $userList[$k]['password'] = $partnerCommon->passwordDecrypt($v['password']);
            }
            return $userList;
        }else{
            return false;
        }
    }
}