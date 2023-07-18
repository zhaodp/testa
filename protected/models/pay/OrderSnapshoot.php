<?php

/**
 * This is the model class for table "t_order_snapshoot".
 *
 * The followings are the available columns in table 't_order_snapshoot':
 * @property integer $id
 * @property string $order_id
 * @property integer $source
 * @property string $channel
 * @property integer $booking_time
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $wait_time
 * @property string $wait_price
 * @property integer $serve_time
 * @property integer $distance
 * @property integer $income
 * @property integer $price
 * @property string $coupon_money
 * @property string $time_cost
 * @property string $subsidy
 * @property string $subsidy_back
 * @property string $unit_cost
 * @property integer $created
 * @property string $meta
 */
class OrderSnapshoot extends FinanceActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_order_snapshoot';
	}

	/**
	 *
	 * 取快照里面的 meta 信息,如果提供了 index 值,那么会返回这个值
	 *
	 * @param $orderId
	 * @param string $index
	 * @return array|mixed
	 */
	public function getSnapshootMeta($orderId, $index = ''){
		$model = $this->getLatestSnapshoot($orderId);
		if($model && isset($model['meta'])){
			$meta = json_decode($model['meta'], true);
			if(!empty($meta)){
				if(empty($index)){
					return $meta;
				}
				$metaIndex1Arr = isset($meta[$index]) ? $meta[$index] : array();
                if(isset($metaIndex1Arr[$index])){
                    $metaIndexArr2 = $metaIndex1Arr[$index];
                    return $metaIndexArr2;
                }else{
                    return $metaIndex1Arr;
                }
			}else{
				return $meta;
			}
		}
		return array();
	}

	/**
	 * 最后一份快照, 也就是最后结账的快照
	 *
	 * @param $orderId
	 * @return CActiveRecord
	 */
	public function getLatestSnapshoot($orderId){
		$criteria = new CDbCriteria();
		$criteria->compare('order_id', $orderId);
		$criteria->order = 'id desc ';
		return self::model()->find($criteria);
	}

	/**
	 * 保存一个订单的快照
	 * 原则上说,没结账一次保存一次
	 *
	 * @param $orderId
	 * @param $source
	 * @param $income
	 * @param $channel
	 * @param $params
	 */
	public function saveSnapshoot($orderId, $source, $channel, $income, $params){
		$model = new OrderSnapshoot();
		$model->attributes      = $params;
		$model['order_id']      = $orderId;
		$model['source']  = $source;
		$model['channel']  = $channel;
		$model['income']     = $income;
		$model['created']       = time();
		if(!$model->save()){
			EdjLog::info(json_encode($model->getErrors()));
			return false;
		}
		return true;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, source, created', 'required'),
			array('source, booking_time, start_time, end_time, wait_time, serve_time,  created', 'numerical', 'integerOnly'=>true),
			array('order_id', 'length', 'max'=>32),
			array('channel', 'length', 'max'=>5),
			array('wait_price, coupon_money, time_cost, subsidy, subsidy_back, unit_cost, distance, income, price', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('meta', 'length', 'max'=>1024),
			array('id, order_id, source, channel, booking_time, start_time,wait_price, end_time, wait_time, serve_time, distance, income, price, coupon_money, time_cost, subsidy, subsidy_back, unit_cost, created, meta', 'safe', 'on'=>'search'),
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
			'id' => 'Id',
			'order_id' => 'Order',
			'source' => 'Source',
			'channel' => 'Channel',
			'booking_time' => 'Booking Time',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'wait_time' => 'Wait Time',
			'wait_price' => 'Wait Price',
			'serve_time' => 'Serve Time',
			'distance' => 'Distance',
			'income' => 'Income',
			'price' => 'Price',
			'coupon_money' => 'Coupon Money',
			'time_cost' => 'Time Cost',
			'subsidy' => 'Subsidy',
			'subsidy_back' => 'Subsidy Back',
			'unit_cost' => 'Unit Cost',
			'created' => 'Created',
			'meta' => 'Meta',
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
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('order_id',$this->order_id,true);

		$criteria->compare('source',$this->source);

		$criteria->compare('channel',$this->channel,true);

		$criteria->compare('booking_time',$this->booking_time);

		$criteria->compare('start_time',$this->start_time);

		$criteria->compare('end_time',$this->end_time);

		$criteria->compare('wait_time',$this->wait_time);

		$criteria->compare('wait_price',$this->wait_price,true);

		$criteria->compare('serve_time',$this->serve_time);

		$criteria->compare('distance',$this->distance);

		$criteria->compare('income',$this->income);

		$criteria->compare('price',$this->price);

		$criteria->compare('coupon_money',$this->coupon_money,true);

		$criteria->compare('time_cost',$this->time_cost,true);

		$criteria->compare('subsidy',$this->subsidy,true);

		$criteria->compare('subsidy_back',$this->subsidy_back,true);

		$criteria->compare('unit_cost',$this->unit_cost,true);

		$criteria->compare('created',$this->created);

		$criteria->compare('meta',$this->meta,true);

		return new CActiveDataProvider('OrderSnapshoot', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return OrderSnapshoot the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @param $beyond_time
     * @param $beyond_time_cost
     * @param $beyond_distance
     * @param $beyond_distance_cost
     * @param array $metaArr
     * @return mixed 获取存储快照meta里面的json信息
     */
    public  function appendSnapshootDetail($beyond_time,$beyond_time_cost,$beyond_distance,$beyond_distance_cost,$metaArr=array()){
        $dayTimeArr = array(
            'beyond_time'=>$beyond_time,
            'beyond_time_cost'=>$beyond_time_cost,
            'beyond_distance'=>$beyond_distance,
            'beyond_distance_cost'=>$beyond_distance_cost,
        );
        $tmpArr['daytime_beyondcost'] = $dayTimeArr;
        return $tmpArr;
    }
}