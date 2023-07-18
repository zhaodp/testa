<?php

/**
 * This is the model class for table "{{app_auth_token}}".
 *
 * The followings are the available columns in table '{{app_auth_token}}':
 * @property integer $id
 * @property string $udid
 * @property string $appKey
 * @property string $appSecret
 * @property string $secretToken
 * @property string $accessToken
 * @property integer $timestamp
 */
class AppAuthToken extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AppAuthToken the static model class
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
		return '{{app_auth_token}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('timestamp', 'numerical', 'integerOnly'=>true),
			array('udid, appKey, appSecret, secretToken, accessToken', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, udid, appKey, appSecret, secretToken, accessToken, timestamp', 'safe', 'on'=>'search'),
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
			'udid' => 'Udid',
			'appKey' => 'App Key',
			'appSecret' => 'App Secret',
			'secretToken' => 'Secret Token',
			'accessToken' => 'Access Token',
			'timestamp' => 'Timestamp',
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
		$criteria->compare('udid',$this->udid,true);
		$criteria->compare('appKey',$this->appKey,true);
		$criteria->compare('appSecret',$this->appSecret,true);
		$criteria->compare('secretToken',$this->secretToken,true);
		$criteria->compare('accessToken',$this->accessToken,true);
		$criteria->compare('timestamp',$this->timestamp);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}