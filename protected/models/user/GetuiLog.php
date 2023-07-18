<?php

/**
 * This is the model class for table "{{getui_log}}".
 *
 * The followings are the available columns in table '{{getui_log}}':
 * @property integer $id
 * @property string $client_id
 * @property string $message
 * @property integer $level
 * @property string $version
 * @property string $driver_user
 * @property integer $offline_time
 * @property string $result
 * @property string $created
 */
class GetuiLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GetuiLog the static model class
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
		return '{{getui_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, message, version, result, created', 'required'),
			array('level, offline_time', 'numerical', 'integerOnly'=>true),
			array('client_id, result', 'length', 'max'=>50),
			array('message', 'length', 'max'=>3000),
			array('version, driver_user', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, message, level, version, driver_user, offline_time, result, created', 'safe', 'on'=>'search'),
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
			'client_id' => 'Client',
			'message' => 'Message',
			'level' => 'Level',
			'version' => 'Version',
			'driver_user' => 'Driver User',
			'offline_time' => 'Offline Time',
			'result' => 'Result',
			'created' => 'Created',
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
		$criteria->compare('client_id',$this->client_id,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('level',$this->level);
		$criteria->compare('version',$this->version,true);
		$criteria->compare('driver_user',$this->driver_user,true);
		$criteria->compare('offline_time',$this->offline_time);
		$criteria->compare('result',$this->result,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
     * 获取司机注册信息
     * @param string $driver_id 司机用户ID
     */
    public function getDriverInfoByDriverID($driver_id) {
        if(empty($driver_id)) return "";
        
        $driver = GetuiClient::getDbMasterConnection()->createCommand()
			        ->select('*')
			        ->from('t_getui_client')
			        ->where('driver_id=:driver_id and version =:version',array(':driver_id'=>$driver_id,':version'=>'driver'))
        			->queryRow();

        return $driver;
    }
}
