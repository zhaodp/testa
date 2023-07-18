<?php

/**
 * This is the model class for table "{{admin_special_auth}}".
 *
 * The followings are the available columns in table '{{admin_special_auth}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $driver_phone
 * @property integer $user_phone
 * @property integer $bonus
 * @property string $update_time
 * @property string $create_time
 */
class AdminSpecialAuth extends CActiveRecord
{

    //driver_phone
    CONST DRIVER_PHONE_YES = 1;
    CONST DRIVER_PHONE_NO  = 0;

    //USER_phone
    CONST USER_PHONE_YES = 1;
    CONST USER_PHONE_NO  = 0;

    //BONUS
    CONST BONUS_YES = 1;
    CONST BONUS_NO  = 0;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{admin_special_auth}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, driver_phone, user_phone, bonus', 'numerical', 'integerOnly'=>true),
            array('update_time, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, driver_phone, user_phone, bonus, update_time, create_time', 'safe', 'on'=>'search'),
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
            'user_id' => 'Userid',
            'driver_phone' => '查看司机电话权限',
            'user_phone' => '查看用户电话权限',
            'bonus' => '查看优惠券权限',
            'update_time' => '更新时间',
            'create_time' => '创建时间',
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
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('user_id',$this->user_id);
//        $criteria->compare('driver_phone',$this->driver_phone);
//        $criteria->compare('user_phone',$this->user_phone);
//        $criteria->compare('bonus',$this->bonus);
//        $criteria->compare('update_time',$this->update_time,true);
//        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->dbadmin;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AdminSpecialAuth the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    public static function getDriverPhoneStatus($driver_phone_status = ''){
        $array = array(
            self::DRIVER_PHONE_YES => '可以查看司机电话',
            self::DRIVER_PHONE_NO => '查看部分内容'
        );
        if($driver_phone_status !== ''){
            if(isset($array[$driver_phone_status]))
                return $array[$driver_phone_status];
            else return false;
        }
        return $array;
    }


    public static function getUserPhoneStatus($user_phone_status = ''){
        $array = array(
            self::USER_PHONE_YES => '可以查看用户电话',
            self::USER_PHONE_NO => '查看部分内容'
        );
        if($user_phone_status !== ''){
            if(isset($array[$user_phone_status]))
                return $array[$user_phone_status];
            else return false;
        }
        return $array;
    }


    public static function getBonusStatus($bonus_status = ''){
        $array = array(
            self::BONUS_YES => '可以查看优惠券内容',
            self::BONUS_NO => '查看部分内容'
        );
        if($bonus_status !== '') {
            if(isset($array[$bonus_status]))
                return $array[$bonus_status];
            else return false;
        }
        return $array;
    }

    public function haveSpecialAuth($field,$user_id =''){
        if(!$user_id && isset(Yii::app()->user) && isset(Yii::app()->user->user_id))
            $user_id = Yii::app()->user->user_id;
        if(!$user_id ) return false;
        $info = $this->find('user_id = :user_id',array(':user_id'=>$user_id));
        if(!$info) return false;
        else if($info[$field]) return true;
        return false;
    }



}