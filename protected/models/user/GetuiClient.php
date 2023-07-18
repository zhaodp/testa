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
 * @property string $driver_id
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
	 * Get db connection
	 */
	public function getDbConnection()
	{
	    return self::getDbMasterConnection();
	}

	/**
	 * Master db connection
	 */
	public static function getDbMasterConnection()
	{
	    return Yii::app()->dborder;
	}

	/**
	 * Slave db connection
	 */
	public static function getDbReadonlyConnection()
	{
	    return Yii::app()->dborder_readonly;
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
			array('version, driver_id', 'length', 'max'=>10),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, client_id, udid, version, city, driver_id, created', 'safe', 'on'=>'search'),
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
			'client_id' => 'client_id',
			'udid' => 'udid',
			'version' => '版本',
			'city' => '城市',
			'driver_id' => '司机工号',
			'created' => '创建时间',
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
		$criteria->compare('client_id',$this->client_id);
		$criteria->compare('udid',$this->udid);
		$criteria->compare('version',$this->version);
		$criteria->compare('city',$this->city);
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
            'pageSize'=>30,
      ),
		));
	}



    /**
     * 获取司机注册信息(废弃)
     * @param string $driver_id 司机用户ID
     */
    public function getDriverInfo($driver_id) {
        if(empty($driver_id)) return "";
        
        $driver = GetuiClient::getDbMasterConnection()->createCommand()
			        ->select('*')
			        ->from('t_getui_client')
			        ->where('driver_id=:driver_id and version =:version',array(':driver_id'=>$driver_id,':version'=>'driver'))
        			->queryRow();

        return $driver;
    }


    public function getClientIdInfo($client_id) {
        if(empty($client_id)) return "";

        $driver = GetuiClient::getDbMasterConnection()->createCommand()
            ->select('*')
            ->from('t_getui_client')
            ->where('client_id=:client_id and version =:version',array(':client_id'=>$client_id,':version'=>'driver'))
            ->queryRow();

        return $driver;
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

    /**
     * 获取客户注册信息
     * @param string $udid
     * @return CActiveRecord
     */
    public function getCustomerInfo($udid){
		if (empty($udid)) return "";
		
		$customer = GetuiClient::getDbMasterConnection()->createCommand()
				->select('*')
				->from('t_getui_client')
				->where('udid=:udid and version =:version',array(':udid'=>$udid,'version'=>'customer'))
				->queryRow();
		
		return $customer;
    }


    /**
     * 设置个推缓存数据
     * @param string $id
     * @param array $attr
     * @return array
     */
    public function setCache( $id = '' , $attr = array() ){
        $cache_key=Yii::app()->params['CACHE_KEY_GETUI_CLIENT_INFO'].$id;
        Yii::app()->cache->set($cache_key, json_encode($attr), 86400);
    }


    /**
     * 获取个推缓存数据
     * @param string $id
     * @param string $version
     * @return array|mixed|string
     */
    public function getClientInfo( $id = '' ,  $version = 'driver'){
        if(empty($id)){
            return false;
        }
        $cache_key=Yii::app()->params['CACHE_KEY_GETUI_CLIENT_INFO'].$id;
        $json=Yii::app()->cache->get($cache_key);
        if ($json) {
            $info=json_decode($json, true);
        } else {
            $info=self::load($id , $version);
        }
        return $info;
    }


    private function load($id , $version = 'driver'){
        if(empty($id) || empty($version)){return '';}

        $data = array();
        $info  = array();
        $cache_key = Yii::app()->params['CACHE_KEY_GETUI_CLIENT_INFO'].$id;

        if($version == 'driver'){
            $data = $this->getDriverInfo($id);
        }elseif($version == 'customer'){
            $data = $this->getCustomerInfo($id);
        }

        if(!empty($data)){
            $info = array(
                'client_id'     => $data->client_id,
                'udid'          => $data->udid,
                'version'       => $data->version,
                'city'          => $data->city,
                'driver_id'     => $data->driver_id,
                'created'       => $data->created,
            );
        }
        Yii::app()->cache->set($cache_key, json_encode($info), 86400);
        return $info;
    }




}
