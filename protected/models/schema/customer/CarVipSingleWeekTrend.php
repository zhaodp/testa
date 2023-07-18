<?php

/**
 * This is the model class for table "{{vip_single_week_trend}}".
 *
 * The followings are the available columns in table '{{vip_single_week_trend}}':
 * @property integer $id
 * @property integer $vip_id
 * @property string $weekth
 * @property string $ave_count
 * @property string $ave_cost
 * @property string $week_order_price
 * @property integer $week_order_count
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $create_time
 */
class CarVipSingleWeekTrend extends ReportActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{vip_single_week_trend}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('vip_id, start_time, end_time, create_time', 'required'),
			array('week_order_count, start_time, end_time, create_time', 'numerical', 'integerOnly'=>true),
			array('weekth', 'length', 'max'=>32),
			array('ave_cost, ave_count, week_order_price', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, vip_id, weekth, ave_cost, ave_count, week_order_price, week_order_count, start_time, end_time, create_time', 'safe', 'on'=>'search'),
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
			'vip_id' => 'vipId',
			'weekth' => '第几周(201351)',
			'ave_count' => '当前平均周订单数',
			'ave_cost' => '当前平均周消费',
			'week_order_price' => '本周消费金额',
			'week_order_count' => '本周单数',
			'start_time' => '开始时间',
			'end_time' => '结束时间',
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
	public function search($extCriteria=null, $pageSize=null)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('vip_id',$this->vip_id);
		$criteria->compare('weekth',$this->weekth,true);
		$criteria->compare('ave_cost',$this->ave_cost,true);
		$criteria->compare('ave_count',$this->ave_count,true);
		$criteria->compare('week_order_price',$this->week_order_price,true);
		$criteria->compare('week_order_count',$this->week_order_count);
		$criteria->compare('start_time',$this->start_time);
		$criteria->compare('end_time',$this->end_time);
		$criteria->compare('create_time',$this->create_time);
                
                if($extCriteria!==null){
                    $criteria->mergeWith($extCriteria);
                }
                
                $pagination = array(
                    'pageSize'=>10,
                );
                
                if($pageSize===0){
                    $pagination = FALSE;
                }
                
                $params = array(
                    'criteria'=>$criteria,
                    'pagination'=>$pagination,
                );

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
	 * @return CarVipSingleWeekTrend the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
