<?php

/**
 * This is the model class for table "{{customer_visit_batch}}".
 *
 * The followings are the available columns in table '{{customer_visit_batch}}':
 * @property integer $id
 * @property integer $batch
 * @property integer $type
 * @property integer $city_id
 * @property string $comment
 * @property string $created
 */
class CustomerVisitBatch extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerVisitBatch the static model class
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
		return '{{customer_visit_batch}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('batch', 'required'),
			array('batch, type, city_id', 'numerical', 'integerOnly'=>true),
			array('comment', 'length', 'max'=>50),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, batch, type, city_id, comment, created', 'safe', 'on'=>'search'),
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
			'batch' => '批次号',
			'type' => '批次状态',
			'city_id' => '城市ID',
			'comment' => '批次说明',
			'created' => '创建时间',
		);
	}
	
	/**
	 * 添加一个批次
	 */
	public function addVisitBatch(){
		$model = new CustomerVisitBatch();
		$model->batch = date('Ymd',time());
		$model->city_id = $_POST['CustomerVisitBatch']['city_id'];
		$model->comment = $_POST['CustomerVisitBatch']['comment'];
		$model->created = date('Y-m-d H:i',time());
		if ($model->save()){
			return true;
		}else {
			return false;
		}
	}
	
	/**
	 * 更新一个批次
	 */
	public function updateVisitBatch($id){
		$model = CustomerVisitBatch::model()->find('id = :id',array(':id'=>$id));
		$model->type = $_POST['CustomerVisitBatch']['type'];
		$model->city_id = $_POST['CustomerVisitBatch']['city_id'];
		$model->comment = $_POST['CustomerVisitBatch']['comment'];
		if ($model->save()){
			return true;
		}else {
			return false;
		}
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
		$criteria->compare('batch',$this->batch);
		$criteria->compare('type',$this->type);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('comment',$this->comment,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	
}