<?php

/**
 * This is the model class for table "{{message_log}}".
 *
 * The followings are the available columns in table '{{message_log}}':
 * @property integer $push_msg_id
 * @property string $client_id
 * @property integer $queue_id
 * @property string $type
 * @property string $message
 * @property integer $level
 * @property string $version
 * @property string $driver_id
 * @property integer $flag
 * @property integer $offline_time
 * @property string $created
 */
class MessageLog extends ReportActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MessageLog the static model class
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
		return '{{message_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, type, content, version, created', 'required'),
			array('queue_id, level, flag, offline_time', 'numerical', 'integerOnly'=>true),
			array('client_id', 'length', 'max'=>50),
			array('version, driver_id', 'length', 'max'=>10),
			array('type', 'length', 'max'=>20),
			array('content', 'length', 'max'=>3000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('push_msg_id, client_id, queue_id, type, content, level, version, driver_id, flag, offline_time, created', 'safe', 'on'=>'search'),
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
			'push_msg_id' => 'Push Msg',
			'client_id' => 'Client',
			'queue_id' => 'Queue',
			'type' => 'Type',
			'content' => 'Content',
			'level' => 'Level',
			'version' => 'Version',
			'driver_id' => 'Driver User',
			'flag' => 'Flag',
			'offline_time' => 'Offline Time',
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

		$criteria->compare('push_msg_id',$this->push_msg_id);
		$criteria->compare('client_id',$this->client_id,true);
		$criteria->compare('queue_id',$this->queue_id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('level',$this->level);
		$criteria->compare('version',$this->version,true);
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('offline_time',$this->offline_time);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
