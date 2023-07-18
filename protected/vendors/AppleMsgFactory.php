<?php
// <!-- aiguoxin -->

class AppleMsgFactory{
	private static $_models;

	const TYPE_MSG_UNCOMMENTED_ORDER='2'; //uncommented order
	const TYPE_MSG_DRIVER_RECEIVE_ORDER='3'; //driver receive order
    const TYPE_MSG_DRIVER_REJECT_ORDER='4'; //driver reject order
    const TYPE_MSG_DRIVER_CANCEL_ORDER='5'; //driver cancel order
    const TYPE_MSG_DRIVER_REACH_TARGET='6'; //driver reach order
    const TYPE_MSG_COUPON='7'; //优惠券绑定、快到期推送
    const TYPE_MSG_ACTIVE='8'; //活动推送
    const TYPE_MSG_FEEDBACK='9';//用户反馈
    const TYPE_MSG_BILL='10';//发票寄出
    const TYPE_MSG_SHARE = '11';//司机接单和开车时给客户push分享活动链接
    const TYPE_MSG_ONLINEPAY = '12';//司机点结束代驾给客户push支付的余额信息或支付状态
    const TYPE_MSG_USERNOTIFY = '13';//司机点结束代驾给客户push支付的余额信息或支付状态

	/**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Order the static model class
     */
    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }


	/**
	*
	* complete params
	*
	*/
	public function orgPushMsg($params, $type=0) {
        $message = array();
        //public params
        $params['badge'] = isset($params['badge']) ? $params['badge'] : 1;
        $params['sound'] = isset($params['sound']) ? $params['sound'] : 'ping1';
        $params['message']=isset($params['message']) ? $params['message'] : '';
        $message['aps'] = array(
                'alert' => $params['message'],
                'badge' => $params['badge'],
                'sound' => $params['sound']
                 );
        $message['type'] = $type;
        $message['timestamp'] =time();
        //private params
        switch ($type) {
            case self::TYPE_MSG_UNCOMMENTED_ORDER:
	            $message['orderId'] = $params['orderId'];
	            $message['orderNum'] = $params['orderNum'];
                $message['messageid'] = $params['messageid'];
                break;
            case self::TYPE_MSG_DRIVER_RECEIVE_ORDER:
	            $message['orderId'] = $params['orderId'];
	            $message['bookingId'] = $params['bookingId'];
	            $message['driverId'] = $params['driverId'];
            	break;
            case self::TYPE_MSG_DRIVER_REJECT_ORDER:
                $message['bookingId'] = $params['bookingId'];
                $message['driverId'] = $params['driverId'];
                break;
            case self::TYPE_MSG_DRIVER_CANCEL_ORDER:
                $message['orderId'] = $params['orderId'];
                $message['bookingId'] = $params['bookingId'];
                $message['driverId'] = $params['driverId'];
                $message['driverName'] = $params['driverName'];
                break;
            case self::TYPE_MSG_DRIVER_REACH_TARGET:
                $message['orderId'] = $params['orderId'];
                $message['bookingId'] = $params['bookingId'];
                $message['driverId'] = $params['driverId'];
                break;
            case self::TYPE_MSG_COUPON:
                $message['messageid'] = $params['messageid'];
                break;
            case self::TYPE_MSG_ACTIVE:
                $message['url'] = $params['url'];
                $message['messageid'] = $params['messageid'];
                $message['title']=isset($params['title']) ? $params['title'] : '';
                $message['content']=isset($params['content']) ? $params['content'] : '';
                break;
            case self::TYPE_MSG_FEEDBACK:
                $message['messageid'] = $params['messageid'];
                $message['suggestionid'] = $params['suggestionid'];
                break;
            case self::TYPE_MSG_BILL:
                $message['messageid'] = $params['messageid'];
                break;
            case self::TYPE_MSG_SHARE:
                $message['url'] = $params['url'];
                $message['order_id'] = $params['order_id'];
                $message['activity_type'] = '1' ;
                $message['show_type'] = '1' ;
                break;
            case self::TYPE_MSG_ONLINEPAY:
                $message['order_id'] = $params['order_id'];
                break;
            case self::TYPE_MSG_USERNOTIFY:
                $message['title'] = isset($params['title'])?$params['title']:'';
                $message['content'] = isset($params['content'])?$params['content']:'';
                $message['url'] = isset($params['url'])?$params['url']:'';
                $message['show_page'] = isset($params['show_page'])?$params['show_page']:'';
                $message['btn_name'] = isset($params['btn_name'])?$params['btn_name']:'';
                break;
            default:
                break;
        }
        return $message;
    }

    /**
    *
    *push uncommented order 
    */
    public function orgPushMsgForUncommentedOrder($params){
        $message = $this->orgPushMsg($params,self::TYPE_MSG_UNCOMMENTED_ORDER);
        $body = json_encode($message,JSON_UNESCAPED_UNICODE);
    	return $body;
    }

}
