<?php
/**
 * 后台客户端活动配置
 */
class PageConfig extends CActiveRecord
{

    const TRIGGER_RECEIVE = 1;//司机接单
    const TRIGGER_START = 2;//司机开车
    const TRIGGER_COMMENT = 3;//订单评价
    const TRIGGER_DETAILS = 4;//历史订单详情

    static $trigger_time = array(
        self::TRIGGER_RECEIVE => '接单',
        self::TRIGGER_START => '开车',
        self::TRIGGER_COMMENT => '评价',
        self::TRIGGER_DETAILS => '历史订单详情',
    );
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{page_config}}';
	}

	
        /**
         * Returns the static model of the specified AR class.
         * Please note that you should have this exact method in all your CActiveRecord descendants!
         * @param string $className active record class name.
         * @return TicketUser the static model class
         */
        public static function model($className=__CLASS__)
        {
                return parent::model($className);
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			 array('begintime, endtime, title, url, order_begin, order_end, city_ids,trigger_time, created','required'),
            		 array('title', 'length', 'max'=>50),
            		 array('url', 'length', 'max'=>255),
            		// The following rule is used by search().
            		// Please remove those attributes that should not be searched.
            		//array('id, title, begintime, endtime, city_ids, customer, platform, status','safe', 'on'=>'search'),

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
			'title' => '活动标题',
			'url' => '页面地址', 
		);
	}



	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->order = " id desc ";
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                		'pageSize'=>30,
            ),
		));
	}

    /**
     * 通过order_id,活动分享时机获取分享活动的信息
     * @param $order_id
     * @param $trigger_time 后台设置的活动触发时机
     */
    public function getSharedInfoByOrderIdAndTriggerTime($order, $trigger_time){
        EdjLog::info('begin to get url..order_id='.$order['order_id'].',trigger_time='.$trigger_time);
        $city_id = $order['city_id'];
        $booking_time = date("Y-m-d H:i:s", $order['booking_time']);
        $comment_time = date("Y-m-d H:i:s", time());//因为异步执行,取当前时间为评论/接单/开车/查看订单详情时间
        $cache_key = 'page_Config';
        $obj = Yii::app()->cache->get($cache_key);
        EdjLog::info('-------get from cache-------'.serialize($obj));
        if(!$obj){
            EdjLog::info('cache not exist');
            return false;
        }
        $config_array = unserialize($obj);
        foreach($config_array as $config){
            $city_ids = $config->city_ids;
            $city_array = explode(',', $city_ids);
            if(!in_array($city_id, $city_array)){//判断是否包含本城市
                EdjLog::info('-------activity is not include city-------'.$city_id);
                continue;
            }
            if(($comment_time<$config->begintime || $comment_time>$config->endtime)
                || (($booking_time<$config->order_begin || $booking_time>$config->order_end))){//判断是否过期等
                EdjLog::info('-------activity is expired-------');
                continue;
            }
            $trigger_array = explode(',', $config->trigger_time);//触发时间
            if(!in_array($trigger_time, $trigger_array)){//判断是否在这个阶段触发
                EdjLog::info('----该活动不在'.$trigger_time.'期间触发----');
                continue;
            }
            //判断是否被分享过--该逻辑改到客户端记录
           /* $isShared = RPageConfig::model()->isShared($order_id);
            if($isShared){
                EdjLog::info('----这笔订单已经分享过活动----');
                continue;
            }*/
            $share_activity = array();
            $share_activity['title'] = $config->title;
            $share_activity['url'] = $config->url;
            //$ret['share_activity'] = $share_activity;
            EdjLog::info('-------share_activity is -------'.json_encode($share_activity));
           /*
           改为客户端判断是否分享过
           $set_ret = RPageConfig::model()->setShared($order_id);
            if(!$set_ret){
                EdjLog::info($order_id.' put into redis failed');
            }*/
            return $share_activity;
        }
        EdjLog::info('----不存在可用的活动----');
        return false;

    }

}
