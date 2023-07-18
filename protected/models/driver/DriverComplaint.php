<?php

/**
 * This is the model class for table "{{driver_complaint}}".
 *
 * The followings are the available columns in table '{{driver_complaint}}':
 * @property integer $id
 * @property integer $order_id
 * @property string $driver_user
 * @property string $customer_name
 * @property integer $city
 * @property integer $customer_phone
 * @property integer $order_type
 * @property integer $complaint_type
 * @property string $complaint_content
 * @property integer $driver_time
 * @property integer $complaint_status
 * @property integer $create_time
 */
class DriverComplaint extends CActiveRecord
{
    const DM_PROCESS_1=1;   //排除投诉
    const DM_PROCESS_2=2;   //暂不处罚
    const DM_PROCESS_3=3;   //屏蔽7天
    const DM_PROCESS_4=4;   //永久屏蔽
    public static $customer_pulish_type=array(
        '1'=>'排除投诉',
        '2'=>'暂不处罚',
        '3'=>'屏蔽7天',
        '4'=>'永久屏蔽',
    );

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverComplaint the static model class
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
		return '{{driver_complaint}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, city, customer_phone, order_type, complaint_type, driver_time, complaint_status, create_time', 'numerical', 'integerOnly'=>true),
			array('driver_user', 'length', 'max'=>20),
			array('customer_name', 'length', 'max'=>50),
			array('complaint_content', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, order_id, driver_user, customer_name, city, customer_phone, order_type, complaint_type, complaint_content, driver_time, complaint_status, create_time', 'safe', 'on'=>'search'),
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
			'order_id' => '订单号',
			'driver_user' => '司机工号',
			'customer_name' => '客户姓名',
			'city' => '城市',
			'customer_phone' => '客户电话',
			'order_type' => '报单类型',
			'complaint_type' => '投诉类型',
			'complaint_content' => '投诉内容',
			'driver_time' => '代驾时间',
			'complaint_status' => '处理状态',
			'create_time' => '创建时间',
		);
	}
	
	/*
	 * 保存司机投诉，用于在司机报单时调用
	 */
	public function saveDriverComplaint($data){
			$data = isset($data) ? $data :'';
			$res = '';
			$complaint = new DriverComplaint();
			//判断是否存在
			$res = $this->find('order_id=:order_id', array (
			':order_id'=>$data['order_id']));
			
			if(empty($res)){
				$data['create_time'] = time();
				$complaint->attributes = $data;
				$complaint->insert();
				return true;
			}
			return false;
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
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('driver_user',$this->driver_user,true);
		$criteria->compare('customer_name',$this->customer_name,true);
		$criteria->compare('city',$this->city);
		$criteria->compare('customer_phone',$this->customer_phone);
		$criteria->compare('order_type',$this->order_type);
		$criteria->compare('complaint_type',$this->complaint_type);
		$criteria->compare('complaint_content',$this->complaint_content,true);
		$criteria->compare('driver_time',$this->driver_time);
		$criteria->compare('complaint_status',$this->complaint_status);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 通过order_id获取司机投诉信息
	 * @param int $order_id
	 * @return array $result
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-06-26
	 */
	public function getByOrderID($order_id = 0) {
		if (0 == $order_id) {
			return '';
		}
		$result = Yii::app()->db_readonly->createCommand()
		               ->select('*')
		               ->from('t_driver_complaint')
		               ->where('order_id = :order_id' , array(':order_id' => $order_id))
		               ->queryRow();
		return $result;
	}
}