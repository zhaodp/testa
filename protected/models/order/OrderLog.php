<?php

/**
 * This is the model class for table "{{order_log}}".
 *
 * The followings are the available columns in table '{{order_log}}':
 * @property integer $id
 * @property string $description
 * @property string $operator
 * @property integer $created
 */
class OrderLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderLog the static model class
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
		return '{{order_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, description, operator, created', 'required'),
			array('order_id,created', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>255),
			array('operator', 'length', 'max'=>20),
			array('id', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, description, operator, created', 'safe', 'on'=>'search'),
		);
	}
	
	public function getOrderLogByOrderId($order_id){
		$orderLog = Yii::app()->db_readonly->createCommand()
					->select('*')
					->from('t_order_log')
					->where('order_id=:order_id', array(':order_id'=>$order_id))
					->order('created DESC')
					->queryAll();
		return $orderLog;
	}


    public function getOrderLogDeclarationByOrderId($order_id, $description = '报单')
    {
        $orderLog = Yii::app()->db_readonly->createCommand()
            ->select('*')
            ->from('t_order_log')
            ->where('order_id = :order_id and description = :description', array(':order_id' => $order_id,':description' => $description))
            ->order('created DESC')
            ->queryRow();
        return $orderLog;
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
			'order_id'=>'订单号',
			'description' => '说明',
			'operator' => '操作人',
			'created' => '操作时间',
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
		$criteria->compare('description',$this->description);
		$criteria->compare('operator',$this->operator);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


    /**
     * 插入日志
     * @author zhanglimin 2013-05-31
     * @param array $params
     * @return mixed
     */
    public function insertLog($params = array()){
        $params['description'] = isset($params['description']) ? $params['description'] : "自动派单" ;
        $params['created'] = time();
        return Yii::app()->db->createCommand()->insert('t_order_log',$params);

    }
}
