<?php

/**
 * This is the model class for table "{{driver_app_traffic}}".
 *
 * The followings are the available columns in table '{{driver_app_traffic}}':
 * @property string $id
 * @property string $driver_id
 * @property string $e_receive_total
 * @property string $e_send_total
 * @property string $phone_receive_total
 * @property string $phone_send_total
 * @property string $device
 * @property string $app_ver
 * @property string $created
 * @property string $update_time
 * @property string $in_date
 */
class DriverAppTraffic extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverAppTraffic the static model class
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
		return '{{driver_app_traffic}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_id, created, update_time, in_date', 'required'),
			array('driver_id, e_receive_total, e_send_total, phone_receive_total, phone_send_total, app_ver', 'length', 'max'=>10),
			array('device', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_id, e_receive_total, e_send_total, phone_receive_total, phone_send_total, device, app_ver, created, update_time, in_date', 'safe', 'on'=>'search'),
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
			'driver_id' => '司机工号',
			'e_receive_total' => '司机客户端接收流量(Kb)',
			'e_send_total' => '司机客户端发送流量(Kb)',
			'phone_receive_total' => '手机当日接收流量(Kb)',
			'phone_send_total' => '手机当日发送流量(Kb)',
			'device' => '手机型号',
			'app_ver' => '软件版本号',
			'created' => '创建时间',
			'update_time' => '更新时间',
			'in_date' => '日期',
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
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('e_receive_total',$this->e_receive_total,true);
		$criteria->compare('e_send_total',$this->e_send_total,true);
		$criteria->compare('phone_receive_total',$this->phone_receive_total,true);
		$criteria->compare('phone_send_total',$this->phone_send_total,true);
		$criteria->compare('device',$this->device,true);
		$criteria->compare('app_ver',$this->app_ver,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('in_date',$this->in_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


    public function insertInfo($params){
        $params['created'] = date("Y-m-d H:i:s");
        $params['update_time'] = date("Y-m-d H:i:s");
        return Yii::app()->db->createCommand()->insert('t_driver_app_traffic',$params);
    }

    public function updateInfo($params){
        $params['update_time'] = date("Y-m-d H:i:s");
        return Yii::app()->db->createCommand()->update('t_driver_app_traffic',$params,
            ' driver_id=:driver_id and in_date=:in_date',array(
            'driver_id'=>$params['driver_id'],
            'in_date'=>$params['in_date'],
        ));
    }
}
