<?php

/**
 * This is the model class for table "{{api_key}}".
 *
 * The followings are the available columns in table '{{api_key}}':
 * @property string $appkey
 * @property string $secret
 * @property string $description
 * @property integer $created
 */
class ApiKey extends CActiveRecord {
	
	private static $_keys = array ();
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ApiKey the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{api_key}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'appkey, secret, description, created', 
				'required'), 
			array (
				'created, enable', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'appkey', 
				'length', 
				'max'=>8), 
			array (
				'secret', 
				'length', 
				'max'=>36), 
			array (
				'description', 
				'length', 
				'max'=>128), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'appkey, secret, enable, description, created', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	public static function key($appkey) {
		if (!isset(self::$_keys[$appkey]))
			self::loadKeys();
		return isset(self::$_keys[$appkey]) ? self::$_keys[$appkey] : false;
	}
	
	private static function loadKeys() {
		self::$_keys = array ();
		$keys = self::model()->findAll();
		foreach($keys as $key)
			self::$_keys[$key->appkey] = array (
				'secret'=>$key->secret, 
				'enable'=>$key->enable);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'appkey'=>'Appkey', 
			'secret'=>'Secret', 
			'enable'=>'Enable', 
			'description'=>'Description', 
			'created'=>'Created');
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('appkey', $this->appkey, true);
		$criteria->compare('secret', $this->secret, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('created', $this->created);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
	
	public function addNewApp($enable, $description, $accessRight=""){
		$app = new ApiKey;
		$app->appkey = IDsequence::model()->nextId("t_api_key");
		$app->secret = UUID::v4();
		$app->enable=$enable;
		$app->description=$description;
		$app->created = time();
		$app->channel= $app->appkey;
		$app->accessRight = $accessRight;
		$app->save();
		return $app;
	}

	public function enable($appkey, $enable){
		$app = self::model()->findByPk($appkey, "appkey = :appkey", array(":appkey"=>$appkey));
		$app->enable = $enable;
		$app->updateTime = time();
		$return = $app->update();
		return $return;
	}

	public function searchByDescription($description){
		$model = self::model();

		$criteria = new CDbCriteria();
		$criteria->select = "*";
		#$criteria->compare('description', $description);
		$criteria->condition = "description like :description";
		$criteria->params = array(":description"=> "%".$description."%");
		$app = $model->findAll($criteria);
		#$app = $model->findAll();
		#$app = $model->findAllBySql("select * from t_api_key where description like :description", array(":description"=>"'%".$description."%'"));
		return $app;
	
	}

	public function getAll(){
		$apps = self::model()->findAll();
		self::model()->buildAppkey($apps);
	}

	public function buildAppkey($statement){

		$app=array();
		foreach($statement as $i){
			$tmp=array("appkey"=>$i->appkey,"secret"=>$i->secret);
			#echo json_encode($tmp);
		}
	}

    /**
     * 根据 appkey 获取对应的 channel 值
     *
     * @param $appkey
     * @return mixed
     */
    public function getChannelByAppkey($appkey){
        $app = self::model()->findByPk($appkey, "appkey = :appkey", array(":appkey"=>$appkey));
        if(!empty($app)){
            return $app->channel;
        }
    }
}
