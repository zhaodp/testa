<?php

/**
 * This is the model class for table "{{customer_sub}}".
 *
 * The followings are the available columns in table '{{customer_sub}}':
 * @property integer $id
 * @property integer $customer_id
 * @property string $name
 * @property string $phone
 * @property string $birthday
 * @property integer $type
 * @property integer $status
 * @property string $create_time
 */
class CustomerSub extends CActiveRecord
{
	//用户状态(正常)
	const STATUS_NORMAL = 1;
	
	//用户状态(禁用)
	const STATUS_FORBIDDEN = 2;
	
	//用户类型(子用户)
	const TYPE_SUB = 0;
	
	//用户类型(主用户)
	const TYPE_MAIN = 1;
	
	public static $status_dict = array(
									self::STATUS_NORMAL => '正常',
									self::STATUS_FORBIDDEN => '禁用',
								);
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerSub the static model class
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
		return '{{customer_sub}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        return array(
            array('customer_id, phone, create_time', 'required'),
            array('customer_id, type, status', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>50),
            array('phone', 'length', 'max'=>32),
            array('birthday', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, customer_id, name, phone, birthday, type, status, create_time', 'safe', 'on'=>'search'),
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
            'customer_id' => 'Customer',
            'name' => '姓名',
            'phone' => '手机号码',
            'birthday' => '生日',
            'type' => 'Type',
            'status' => '状态',
            'create_time' => 'Create Time',
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
        $criteria->compare('customer_id',$this->customer_id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('birthday',$this->birthday,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('status',$this->status);
        $criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getPrimary($customer_id) {
		return self::model()->find("customer_id = :customer_id and type = :type", 
						array(':customer_id' =>$this->customer_id,':type' => self::TYPE_MAIN));
	}
	
}