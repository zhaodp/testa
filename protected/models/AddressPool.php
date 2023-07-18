<?php

/**
 * This is the model class for table "{{address_pool}}".
 *
 * The followings are the available columns in table '{{address_pool}}':
 * @property integer $id
 * @property string $hashkey
 * @property string $address
 * @property string $lng
 * @property string $lat
 * @property string $created
 */
class AddressPool extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AddressPool the static model class
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
		return '{{address_pool}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('hashkey, lng, lat, created', 'required'),
			array('hashkey, address', 'length', 'max'=>100),
			array('lng, lat', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, city_id, hashkey, address, lng, lat, ,times ,created', 'safe', 'on'=>'search'),
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
			'city_id' => '城市ID',
			'hashkey' => 'Hashkey',
			'address' => '地址名',
			'lng' => '经度',
			'lat' => '纬度',
			'created' => '创建时间',
            'times'=>'使用次数',
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
		$criteria->compare('city_id',$this->city_id, false);//false表示不使用like
		$criteria->compare('hashkey',$this->hashkey, false);
		$criteria->compare('address',$this->address, true);
		$criteria->compare('lng',$this->lng, true);
		$criteria->compare('lat',$this->lat, true);
        $criteria->compare('times',$this->times, false);
		$criteria->compare('created',$this->created, true);
		//2014年9月28日修改 先从从库查出数据,构建一个arrayDataProvider 然后把链接改回主库
		$criteria->limit = '50';//和黄涛确认,不用翻页
		self::$db = Yii::app()->db_readonly;
		$rowData = self::model()->findAll($criteria);
		self::$db = Yii::app()->db;
		return new CArrayDataProvider($rowData, array(
			'id'	=> 'id',
			'pagination'=>array(
				'pageSize' => 50,
			),
		));
	}
	
	/**
	 * 计算hashkey
	 * 
	 * @author sunhongjing 2013-06-07
	 * 
	 * @param unknown_type $key
	 * @param unknown_type $city_id
	 */
	private function _getHashkey($key,$city_id=0){
		$hashkey = false;
		
		if(empty($key)){
        	return $hashkey;
        }
        $key = trim($key);
        if( 0 == $city_id ){
        	$hashkey = md5($key);
        }else{
        	$hashkey = md5($city_id.'-'.$key);
        }
        return $hashkey;
        
	}
	
	
    /**
     * 通过地址和城市，取得地址池订单数据
     * 
     * @param unknown_type $hashid
     */
    public function getAddressFromPool($address,$city_id)
    {
    	$ret = array();
    	$hashkey = $this->_getHashkey($address,$city_id);
        if(empty($hashkey)){
        	return $ret;
        }
    	
	    //用HASH KEY 取坐标
	    $sql3 = "select hashkey, lng,lat from t_address_pool where hashkey =:hashkey and city_id=:city_id ";
	    $ret = Yii::app()->db->createCommand($sql3)->queryRow(true,array(':hashkey'=>$hashkey,':city_id'=>$city_id));
    	return $ret;
    	
    }

    /**
     * 测试当前地址是否存在地址池中。
     * @author zhanglimin
     * @param $key
     * @return bool
     */
    public function checkAddressExists($key,$city_id=0){
    	//修改默认返回参数为false，add by sunhongjing 
        $hashkey = $this->_getHashkey($key,$city_id);
        if(empty($hashkey)){
        	return false;
        }
        
        $sql = " select hashkey from t_address_pool where hashkey=:hashkey and city_id=:city_id ";
        $result = Yii::app()->db_readonly->CreateCommand($sql)->queryRow(true,array('hashkey'=>$hashkey,'city_id'=>$city_id));
        if(empty($result)) {
            return false;
        }else{
            return true;
        }
    }
    
    /**
     * 将地址存入地址池,注意，此处接收的坐标为百度坐标
     * 
     * @param array $params  参数：$params = array('address'=>$address,'city_id'=>$city_id,'lat'=>$lat,'lng'=>$lng); 
     * 
     * @author sunhongjing 2013-06-07
     */
    public function putAddress2Pool($params=array())
    {
    	$ret = false;
    	if(empty($params)){
    		return $ret;
    	}
    	
    	if( empty( $params['address'] ) || empty($params['lat']) || empty($params['lng']) ){
    		return $ret;
    	}
    	if( empty($params['city_id']) ){
    		$cityName = GPS::model()->getCityByBaiduGPS($params['lng'] , $params['lat']);
			if (empty($cityName)) {
				$cityName = '北京';
			}
			
			$citys = Dict::items('city');
			$city_id = 0;
			foreach($citys as $key=>$value) {
				if ($value==$cityName){
					$city_id = $key;
					break;
				}
			}
    	}else{
    		$city_id = $params['city_id'];
    	}

    	$address = trim($params['address']);	
        $hashkey = md5($city_id.'-'.$address);
    	$attr = array(
			'hashkey'=>$hashkey,
			'city_id'=>$city_id,
			'address'=>$address,
			'lat'=>$params['lat'],
			'lng'=>$params['lng'],
            'times'=>isset($params['times']) ? $params['times'] : 0 ,
			'created'=>date("Y-m-d H:i:s"),
		);

        $ret = Yii::app()->db->createCommand()->insert('t_address_pool', $attr);

        $this->onAddressAdd($hashkey);

		return $ret;
    }

    private function onAddressAdd($hashkey)
    {
        return $this->onAddressChanged('add', array('hashkey' => $hashkey));
    }

    private function onAddressDelete($hashkey)
    {
        return $this->onAddressChanged('delete', array('hashkey' => $hashkey));
    }

    private function onAddressUpdate($hashkey)
    {
        return $this->onAddressChanged('update', array('hashkey' => $hashkey));
    }

    private function onAddressChanged($action, $param)
    {
        $base_param = array('es_source' => 'AddressPool', 'es_action' => $action);
        Queue::model()->putin(
            array(
                'method'=>'synchronize_elasticsearch',
                'params'=>array_merge($base_param, $param)
            ),
            'synchronize_elasticsearch'
        );
    } 

    /**
     * 更新地址池GPS
     * @author zhanglimin 2013-06-17
     * @param array $params
     * @return bool
     */
    public function putUpdateress2Pool($params=array()){
        $ret = false;
        if(empty($params)){
            return $ret;
        }

        $hashkey = $this->_getHashkey($params['address'],$params['city_id']);

        $ret = Yii::app()->db->createCommand()->update('t_address_pool',
            array('lat'=>$params['lat'],'lng'=>$params['lng']),
            'hashkey=:hashkey',
            array(':hashkey'=>$hashkey)
        );

        $this->onAddressUpdate($hashkey);

        return $ret;
    }

    /**
     * 通过HASHKEY删除数据
     * @author zhanglimin 2013-06-17
     * @param $address
     * @param $city_id
     * @return mixed
     */
    public function putDelAddressPool($address,$city_id){
        $hashkey = $this->_getHashkey($address,$city_id);
        $ret = Yii::app()->db->createCommand()->delete('t_address_pool','hashkey=:hashkey',array(':hashkey'=>$hashkey));

        $this->onAddressDelete($hashkey);

        return $ret;
    }

    public function deleteAddressById($id)
    {
        $sql = " select address, city_id from t_address_pool where id=:id";
        $result = Yii::app()->db->CreateCommand($sql)->queryRow(true,array('id'=>$id));

        if(empty($result)) {
            return false;
        }
        
        $this->putDelAddressPool($result['address'], $result['city_id']);
    }


    /**
     * 更新地址池使用次数
     * @param $address
     * @param $city_id
     * @param int $times
     * @return bool
     */
    public function putUpdateUseCount($address,$city_id,$times =  0 ){
        $hashkey = $this->_getHashkey($address,$city_id);
        $count=AddressPool::model()->updateCounters(array('times'=>$times),'hashkey=:hashkey',array(':hashkey'=>$hashkey));
        if($count>0){
            $this->onAddressUpdate($hashkey);
            return true;
        }else{
           return false;
        }
    }
    
}
