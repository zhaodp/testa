<?php

/**
 * This is the model class for table "{{customer_complain_deduct}}".
 *
 * The followings are the available columns in table '{{customer_complain_deduct}}':
 * @property integer $id
 * @property integer $complain_id
 * @property integer $city_id
 * @property string $driver_id
 * @property integer $complain_type_id
 * @property integer $order_id
 * @property string $mark
 * @property string $create_time
 */
class CustomerComplainDeduct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{customer_complain_deduct}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('complain_id, city_id, driver_id, complain_type_id, order_id, mark, create_time', 'required'),
			array('complain_id, city_id, complain_type_id, order_id', 'numerical', 'integerOnly'=>true),
			array('driver_id', 'length', 'max'=>10),
			array('mark', 'length', 'max'=>8),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, complain_id, city_id, driver_id, complain_type_id, order_id, mark, create_time', 'safe', 'on'=>'search'),
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
			'complain_id' => '投诉ID',
			'city_id' => '城市',
			'driver_id' => '司机工号',
			'complain_type_id' => '投诉类型',
			'order_id' => '订单ID',
			'mark' => '分数',
			'create_time' => '创建时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('complain_id',$this->complain_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('complain_type_id',$this->complain_type_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('mark',$this->mark,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TCustomerComplainDeduct the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


    /**
     * 统计客户投诉数量（城市）
     * @param int $city_id
     * @return mixed
     */
    public function getComplaints($city_id = 0)
    {
        $where = 'driver_id != ""';
        $params = array();
        if ($city_id != 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        $num = Yii::app()->db_readonly->createCommand()
            ->select('count(1)')
            ->from('t_customer_complain_deduct')
            ->where($where, $params)
            ->queryScalar();
        return $num;
    }

}
