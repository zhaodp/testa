<?php

/**
 * This is the model class for table "{{vip_cost_month}}".
 *
 * The followings are the available columns in table '{{vip_cost_month}}':
 * @property integer $id
 * @property string $month
 * @property integer $vip_cost_sum_month
 * @property integer $all_cost_sum_month
 * @property integer $vip_order_count_month
 * @property integer $all_order_count_month
 * @property integer $vip_count_month
 * @property integer $customer_count_month
 * @property integer $vip_new_count
 * @property integer $recharge_month
 * @property integer $city_id
 * @property integer $create_time
 * @property integer $update_time
 */
class CarVipCostMonth extends ReportActiveRecord
{
    public $end_month = null;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{vip_cost_month}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, vip_cost_sum_month, all_cost_sum_month, vip_order_count_month, all_order_count_month, vip_count_month, customer_count_month, vip_new_count, recharge_month, city_id, create_time, update_time', 'numerical', 'integerOnly'=>true),
			array('month', 'length', 'max'=>7),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, month, end_month, vip_cost_sum_month, all_cost_sum_month, vip_order_count_month, all_order_count_month, vip_count_month, customer_count_month, vip_new_count, recharge_month, city_id, create_time, update_time', 'safe', 'on'=>'search'),
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
			'month' => '月份',
			'vip_cost_sum_month' => 'vip消费总额',
			'all_cost_sum_month' => '所有订单的消费总额',
			'vip_order_count_month' => 'vip订单数量',
			'all_order_count_month' => '总的订单数量',
			'vip_count_month' => 'VIP总数',
			'customer_count_month' => '客户总数',
			'vip_new_count' => '新增vip数量',
			'recharge_month' => '本月充值',
			'city_id' => '城市id',
			'create_time' => '创建时间',
			'update_time' => '修改时间',
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
	public function search($ext_criteria = NULL, $pagination = null)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
                if(!$this->month && !$this->end_month){
                    $this->month = date("Y-m", strtotime('-12 month'));
                }
                if($this->month){
                    $criteria->addCondition('month >= :month');
                    $criteria->params[':month'] = date('Y-m', strtotime($this->month));
                }
                if($this->end_month){
                    $criteria->addCondition('month <= :end_month');
                    $criteria->params[':end_month'] = date('Y-m', strtotime($this->end_month));
                }
		$criteria->compare('vip_cost_sum_month',$this->vip_cost_sum_month);
		$criteria->compare('all_cost_sum_month',$this->all_cost_sum_month);
		$criteria->compare('vip_order_count_month',$this->vip_order_count_month);
		$criteria->compare('all_order_count_month',$this->all_order_count_month);
		$criteria->compare('vip_count_month',$this->vip_count_month);
		$criteria->compare('customer_count_month',$this->customer_count_month);
		$criteria->compare('vip_new_count',$this->vip_new_count);
		$criteria->compare('recharge_month',$this->recharge_month);
		$criteria->compare('city_id',$this->city_id ? $this->city_id : 0);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('update_time',$this->update_time);
                
                if($ext_criteria !== NULL){
                    $criteria->mergeWith($ext_criteria);
                }

                $params = array('criteria'=>$criteria);
                if($pagination !== null){
                    $params['pagination'] = $pagination;
                }
		return new CActiveDataProvider($this, $params);
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->dbreport;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CarVipCostMonth the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
