<?php

/**
 * This is the model class for table "{{getui_client}}".
 *
 * The followings are the available columns in table '{{getui_client}}':
 * @property integer $id
 * @property string $client_id
 * @property string $udid
 * @property string $version
 * @property string $city
 * @property string $driver_user
 * @property string $created
 */
class GetuiClient extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GetuiClient the static model class
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
		return '{{getui_client}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('client_id, udid, version', 'required'),
			array('client_id, udid, city', 'length', 'max'=>50),
			array('version, driver_user', 'length', 'max'=>10),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, udid, version, city, driver_user, created', 'safe', 'on'=>'search'),
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
			'udid' => 'Udid',
			'version' => 'Version',
			'city' => 'City',
			'driver_user' => 'Driver User',
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
		$criteria->compare('udid',$this->udid,true);
		$criteria->compare('version',$this->version,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('driver_user',$this->driver_user,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}



    /**
     * 获取客户端注册信息
     * @param string $driver_user 司机用户ID
     */
    public static function getGetuiClientUserInfo($driver_user = '') {
        $driver = GetuiClient::model()->find(
            'driver_user=:driver_user and version =:version',
            array(
                ':driver_user'=>$driver_user,
                'version'=>'driver'
            )
        );
        return $driver;
    }





}