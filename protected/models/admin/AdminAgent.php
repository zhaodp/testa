<?php
/**
 * This is the model class for table "{{admin_agent}}".
 *
 * The followings are the available columns in table '{{admin_agent}}':
 * @property string $agent_num
 * @property string $agent_name
 * @property string $password
 * @property integer $user_id
 * @property integer $is_lock
 * @property string $created
 */
class AdminAgent extends CActiveRecord {
        public $department;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AdminAgent the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{admin_agent}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'agent_num, agent_name, password, user_id', 
				'required'), 
			array (
				'user_id,is_lock', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'agent_num', 
				'length', 
				'max'=>4), 
			array (
				'agent_name', 
				'length', 
				'max'=>12), 
			array (
				'phone', 
				'length', 
				'max'=>15), 
			array (
				'password', 
				'length', 
				'max'=>20), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'agent_num, agent_name, password, user_id, phone, is_lock, created', 
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
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'agent_num'=>'工号', 
			'agent_name'=>'工号名称', 
			'password'=>'密码', 
			'user_id'=>'用户名', 
			'phone'=>'绑定电话', 
			'is_lock'=>'锁定', 
			'created'=>'日期');
	}
	
	public function getAgent($user_id) {
		return $this->find('user_id=:user_id', array (
			':user_id'=>$user_id));
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		self::$db = Yii::app()->db_readonly;
		
		$criteria = new CDbCriteria();
		
		$criteria->compare('agent_num', $this->agent_num, true);
		$criteria->compare('agent_name', $this->agent_name, true);
		$criteria->compare('phone', $this->phone, true);
		$criteria->compare('user_id', $this->user_id);
		$criteria->order = 'is_lock,agent_num';
		
		return new CActiveDataProvider($this, array (
			'pagination'=>array (
				'pageSize'=>500), 
			'criteria'=>$criteria));
	}
}
