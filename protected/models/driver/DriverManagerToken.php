<?php

/**
 * This is the model class for table "{{driver_manager_token}}".
 *
 * The followings are the available columns in table '{{driver_manager_token}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $auth_token
 * @property string $create_time
 * @property string $update_time
 */
class DriverManagerToken extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_manager_token}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('update_time', 'required'),
            array('driver_id, auth_token', 'length', 'max'=>20),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, driver_id, auth_token, create_time, update_time', 'safe', 'on'=>'search'),
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
            'driver_id' => 'Driver',
            'auth_token' => 'Auth Token',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('auth_token',$this->auth_token,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverManagerToken the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $token
     * 退出登陆
     */
    public function logout($token){
        $driver_id = DriverStatus::model()->getDriverManagerToken($token);
        if(empty($driver_id)){
            return;
        }
        //删除redis中缓存
        DriverStatus::model()->delDriverManagerToken($token);
        //更新数据记录
        $res = $this->rmToken($driver_id);
        return $res;
    }


    public function delTokenByDriverId($driverId){
        $tokenRecords = DriverManagerToken::model()->findAll('driver_id=:driver_id', array(':driver_id'=>$driverId));
        EdjLog::info('driverId='.$driverId.',清空token count='.count($tokenRecords));
        foreach ($tokenRecords as $driverManagerToken) {
            EdjLog::info('driverId='.$driverId.',清空token='.$driverManagerToken['auth_token']);
            //清空redis记录
            DriverStatus::model()->delDriverManagerToken($driverManagerToken['auth_token']);
        }
        //更新数据记录
        $res = $this->rmToken($driverId);
        return $res;
    }

    public function rmToken($driverId){
        $update_sql = "update t_driver_manager_token set `auth_token`='' WHERE `driver_id` = :driver_id ";
        $change = Yii::app()->db->createCommand($update_sql);
        $change->bindParam(":driver_id", $driverId);
        $res = $change->execute();
        return $res;
    }
}
