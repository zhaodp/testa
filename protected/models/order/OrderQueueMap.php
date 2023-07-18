<?php

/**
 * This is the model class for table "{{order_queue_map}}".
 *
 * The followings are the available columns in table '{{order_queue_map}}':
 * @property integer $id
 * @property integer $order_id
 * @property integer $queue_id
 * @property string $driver_id
 * @property string $confirm_time
 */
class OrderQueueMap extends OrderActiveRecord
{
	const MAP_NOT_CONFIRM = 0;
	const MAP_CONFIRM = 1;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderQueueMap the static model class
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
        return '{{order_queue_map}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id, queue_id, driver_id, confirm_time', 'required'),
            array('order_id, queue_id, number, flag', 'numerical', 'integerOnly'=>true),
            array('driver_id', 'length', 'max'=>10),
            array('dispatch_time', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, order_id, queue_id, driver_id, number, flag, dispatch_time, confirm_time', 'safe', 'on'=>'search'),
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
            'order_id' => 'Order',
            'queue_id' => 'Queue',
            'driver_id' => 'Driver',
            'number' => 'Number',
            'flag' => 'Flag',
            'dispatch_time' => 'Dispatch Time',
            'confirm_time' => 'Confirm Time',
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
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('queue_id',$this->queue_id);
        $criteria->compare('driver_id',$this->driver_id);
        $criteria->compare('number',$this->number);
        $criteria->compare('flag',$this->flag);
        $criteria->compare('dispatch_time',$this->dispatch_time);
        $criteria->compare('confirm_time',$this->confirm_time);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
	
	/**
	 * 获取映射关系 by order_id（订单列表用）
	 * @param int $order_id
	 * @return object $result
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-06-23
	 */
	public function getByOrderID($order_id) {
		if (empty($order_id)) {
			return false;
		}
		return self::model()->find('order_id = ?' , array($order_id));
	}
	
	/**
	 * 获取预约信息
	 * @param int $queue_id
	 * @return object $result
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-06-23
	 */
	public function getByQueueID($queue_id = 0) {
		if (0 == $queue_id) {
			return false;
		}
		return self::model()->findAll('queue_id = ?' , array($queue_id));
	}
	
	/**
	 * 撤回手动派单的order_id加缓存标识
	 * @param int $queue_id
	 * @return boolean
	 */
	public function noDispatchOrderProcess($queue_id = 0) {
    	if (0 == $queue_id) {
			return false;
		}
		
		$maps = self::model()->findAll('queue_id = ? and driver_id = ?' , array($queue_id, Push::DEFAULT_DRIVER_INFO));
		foreach ($maps as $order) {
			$cache_key = 'receive_detail_'.$order->order_id;
			$cache_value = 'dispatched';
			Yii::app()->cache->set($cache_key, $cache_value, 120);
		}
		return true;
    }
    
    /**
     * 获取派单时间
     * @param int $order_id
     * @return string $dispatch_time
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-31
     */
    public function getDispatchTime($order_id) {
        $map = $this->getByOrderID($order_id);
        if (!empty($map) && $map->confirm_time != Push::DEFAULT_TIME_FORMAT) {
            return date('m-d H:i', strtotime($map->confirm_time));
        }
        return '';
    }

    /**
     *  获取queue_id by order_id  wanglonghuan  2014-1-8
     * @params order_id need_queue=true
     * @return array('map','queue')
     */
    public function getQueueIdByOrderId($order_id,$need_queue=false)
    {
        $result = array(
            'map' => '',
            'queue' => '',
        );
        if(empty($order_id)) {
            return $result;
        }
        
        $map = $this->getByOrderID($order_id);
        if (empty($map)) {
            return $result;
        }
        
        $result['map'] = $map->getAttributes();

        if(!empty($map) && $need_queue){
        	// Yii::app()->db_readonly change into OrderQueue::getDbReadonlyConnection()
            $queue = OrderQueue::getDbReadonlyConnection()->createCommand()
                         ->select('*')
                         ->from('t_order_queue')
                         ->where('id = :queue_id' , array(':queue_id' => $map['queue_id']))
                         ->queryRow();
            $result['queue'] = $queue;
        }
        return $result;
    }
    
    /**
     * 获取组长司机
     */
    public function getLeader($queue_id) {
        $model = $this->findByAttributes(
            array('queue_id' => $queue_id),
            array('select' => 'driver_id', 'order' => 'confirm_time ASC')
        );
        
        if(empty($model)) {
            return null;
        }
        return $model->driver_id;
    }


    /**
     * 获取map list
     */

    public static function getQueueMapList($id,$limit){
        $orders = OrderQueueMap::getDbReadonlyConnection()->createCommand()
            ->select('order_id,id,queue_id')
            ->from('t_order_queue_map')
            ->where('id>:id', array(':id' => $id))
            ->order('id asc')
            ->limit($limit)
            ->queryAll();
        return $orders;
    }
    
    /**
     * 根据orderId获取map，id排序
     * @param unknown $orderId
     * @return unknown
     */
    public function getMapsByOrderId($orderId) {
        $map = OrderQueueMap::getDbReadonlyConnection()->createCommand()
        ->select('*')
        ->from('t_order_queue_map')
        ->where('order_id = :order_id' , array(':order_id' => $orderId))
        ->order('id ASC')
        ->queryRow();
        
        return $map;
    }
}
