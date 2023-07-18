<?php

/**
 * This is the model class for table "{{vip_cost_ext}}".
 *
 * The followings are the available columns in table '{{vip_cost_ext}}':
 * @property integer $id
 * @property string $vip_id
 * @property string $ave_count
 * @property string $ave_cost
 * @property string $last_week_cost
 * @property string $last_week_count
 * @property string $last_second_week_cost
 * @property string $last_second_week_count
 * @property integer $change_cost
 * @property integer $change_count
 * @property string $change_rate_cost
 * @property string $change_rate_count
 * @property integer $create_time
 * @property integer $update_time
 */
class CarVipCostExt extends FinanceActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{vip_cost_ext}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('change_cost, change_count, create_time, update_time', 'numerical', 'integerOnly'=>true),
			array('vip_id', 'length', 'max'=>15),
			array('ave_count, ave_cost, last_week_cost, last_week_count, last_second_week_cost, last_second_week_count, change_rate_cost, change_rate_count', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, vip_id, ave_count, ave_cost, last_week_cost, last_week_count, last_second_week_cost, last_second_week_count, change_cost, change_count, change_rate_cost, change_rate_count, create_time, update_time', 'safe', 'on'=>'search'),
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
			'vip_id' => 'VIP 卡号',
			'ave_count' => '当前平均周订单数',
			'ave_cost' => '当前平均周消费',
			'last_week_cost' => '上周消费金额',
			'last_week_count' => '上周单数',
			'last_second_week_cost' => '上上周消费金额',
			'last_second_week_count' => '上上周单数',
			'change_cost' => '金额变化量',
			'change_count' => '订单数量变化量',
			'change_rate_cost' => '金额变化率',
			'change_rate_count' => '订单数量变化率',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('vip_id',$this->vip_id,true);
		$criteria->compare('ave_count',$this->ave_count,true);
		$criteria->compare('ave_cost',$this->ave_cost,true);
		$criteria->compare('last_week_cost',$this->last_week_cost,true);
		$criteria->compare('last_week_count',$this->last_week_count,true);
		$criteria->compare('last_second_week_cost',$this->last_second_week_cost,true);
		$criteria->compare('last_second_week_count',$this->last_second_week_count,true);
		$criteria->compare('change_cost',$this->change_cost);
		$criteria->compare('change_count',$this->change_count);
		$criteria->compare('change_rate_cost',$this->change_rate_cost,true);
		$criteria->compare('change_rate_count',$this->change_rate_count,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('update_time',$this->update_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CarVipCostExt the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
