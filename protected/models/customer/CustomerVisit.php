<?php

/**
 * This is the model class for table "{{customer_visit}}".
 *
 * The followings are the available columns in table '{{customer_visit}}':
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $visit_time
 * @property inn $batch_id
 * @property string $status
 * @property string $created
 */
class CustomerVisit extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Questionnaire the static model class
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
		return '{{customer_visit}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, phone', 'required'),
			array('batch_id','numerical', 'integerOnly'=>true),
			array('name, phone, visit_time,created', 'length', 'max'=>20),
			array('status', 'length', 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, phone, visit_time, status, batch_id,created', 'safe', 'on'=>'search'),
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
			'name' => '客户姓名',
			'phone' => '客户电话',
			'visit_time' => '预约时间',
			'status' => '回访状态',
			'batch_id' => '批次号',
			'created' => '回访时间',
		);
	}
	
	/**
	 * 
	 * 问题调查高级搜索
	 */
	public function getList(){
		$params = array();
		$criteria = new CDbCriteria();
		if(isset($_GET['CustomerVisit'])){
			if ($_GET['CustomerVisit']['batch_id']){
				$criteria->addCondition('batch_id = :batch_id');
				$params[':batch_id'] = $_GET['CustomerVisit']['batch_id'];
			}
			
			if($_GET['CustomerVisit']['name']){
				$criteria->addCondition('name = :name');
				$params[':name'] = $_GET['CustomerVisit']['name'];
			}
			if($_GET['CustomerVisit']['phone']){
				$criteria->addCondition('phone = :phone');
				$params[':phone'] = $_GET['CustomerVisit']['phone'];
			}
			if($_GET['CustomerVisit']['status']){
				$criteria->addCondition('status = :status');
				$params[':status'] = $_GET['CustomerVisit']['status'];
			}
		}
		$criteria->params = $params;
		$dataProvider=new CActiveDataProvider($this,array(
			    'criteria'=>$criteria,
			    'sort'=>array('defaultOrder'=>'status asc,id asc'),
			));
		return $dataProvider;
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
		$criteria->compare('name',$this->name);
		$criteria->compare('phone',$this->phone);
		$criteria->compare('visit_time',$this->visit_time);
		$criteria->compare('status',$this->status);
		$criteria->compare('batch_id', $this->batch);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 导入csv格式数据
	 */
	public function importDriver($data){
		$model = new CustomerVisit();
		$model->name = trim($data[0]);
		$model->phone = trim($data[1]);
		$model->batch_id = $data['batch_id'];
		if($model->insert()){
			return true;
		}else {
			return false;
		}
	}
}