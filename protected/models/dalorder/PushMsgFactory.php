<?php
/**
 * 组装推送消息---只负责组装消息 其他什么都不做
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-12-30
 */
class PushMsgFactory {
    private static $_models;


    //定义发送类型
    const TYPE_MSG='msg'; //司机消息
    const TYPE_MSG_DRIVER='msg_driver'; //司机消息
    const TYPE_MSG_LEADER='msg_leader'; //组长消息
    const TYPE_MSG_CUSTOMER='msg_customer'; //customer
    const TYPE_NOTICE='notice'; //司机公告
    const TYPE_NOTICE_DRIVER='notice_driver'; //司机公告
    const TYPE_UNREAD_MSG_DRIVER_COUNT = 'driver_unread_msg_count';
    const TYPE_ORDER_DETAIL='order_detail'; //订单-订单详情
    const TYPE_ORDER_AUDIO_DETAIL='order_audio_detail'; //订单-订单详情
    const TYPE_BLACK_CUSTOMER='black_customer'; //黑名单
    const TYPE_UPDATE_CONFIG='update_config'; //黑名单
    const TYPE_CMD='cmd'; //黑名单
    const TYPE_ORDER_SUBMIT='order_submit'; //报单
    const TYPE_ORDER_CANCEL='order_cancel'; //取消订单
    const TYPE_ORDER_NEW_DETAIL='order_new'; //新订单详情
    const TYPE_NOTICE_DRIVER_AUDIO='notice_driver_audio'; //语音公告
    const TYPE_NOTICE_DRIVER_UPY='notice_driver_upy';     //又拍云
    const TYPE_CUSTOM_ACCOUNT_CHANGE='custom_account_change';//用户余额变更通知    
    const TYPE_CUSTOM_PAY_CHANGE='custom_pay_change';//客户支付信息变更通知  
    const TYPE_DRIVER_LOG_DEBUG='driver_log_debug';//司机端打开调试日志上传功能
    const TYPE_DRIVER_CROWN='driver_crown';//司机端皇冠通知
    const TYPE_DRIVER_BALANCE_CHANGE='driver_balance_change';//司机余额变化通知
    /** 司机端下发红包push 类型 */
    const DRIVER_PUSH_TYPE_ENVELOPE = FinanceConstants::DRIVER_PUSH_TYPE_ENVELOPE;

    const CATEGORY_FOR_RE_ORDER = 20; //退单补信息category  

    const ORDER_RECEIVE_TIMEOUT=120; //订单超时时间
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
     * 组织短信内容推送
     * @param array/string $content
     * @param string $type
     * @param string $queue_id
     * @param string $is_new
     * @return array $message
     */
    public function orgPushMsg($params , $type=self::TYPE_ORDER_DETAIL , $push_msg_id = 0) {
        $message = array();
        switch ($type) {
            case self::TYPE_ORDER_DETAIL:  //推送订单详情
            case self::TYPE_ORDER_NEW_DETAIL : //推送订单详情（400手工派单）
                //整理坐标
                $google_lng = isset($params['google_lng']) ? $params['google_lng'] : '0.000000';
                $google_lat = isset($params['google_lat']) ? $params['google_lat'] : '0.000000';
                $lng = isset($params['lng']) ? $params['lng'] : '0.000000';
                $lat = isset($params['lat']) ? $params['lat'] : '0.000000';
                if (($lng == $google_lng && $lat == $google_lat) || (intval($google_lng) == 0 && intval($google_lat))) {
                    $gps_type = 'baidu';
                } else {
                    $lng = $google_lng;
                    $lat = $google_lat;
                    $gps_type = 'google';
                }

                $is_new = isset($params['is_new']) ? intval($params['is_new']) : 1;

                //内容
                $content = array(
                    'address' => isset($params['address']) ? $params['address'] : '',
                    'customer_name' => isset($params['name']) ? $params['name'] : '',
                    'phone' => isset($params['phone']) ? $params['phone'] : '',
                    'contact_phone' => isset($order['contact_phone']) ? $params['contact_phone'] : '',
                    'booking_time' => isset($params['booking_time']) ? $params['booking_time'] : '',
                    'number' => isset($params['number']) ? intval($params['number']) : 1,
                    'vipcard' => isset($params['vipcard']) ? $params['vipcard'] : '',
                    'role' => '',
                    'leader_phone' => '',
                    'bonus' => isset($params['bonus']) ? $params['bonus'] : '',
                    'card'=>isset($params['card']) ? $params['card'] : '',
                    'balance'=>isset($params['balance']) ? $params['balance'] : 0,
                    'user_money'=>isset($params['user_money']) ? $params['user_money'] : 0,
                    'source'=> isset($params['type']) ? $params['type'] : 0 ,
                    'cost_type'=> isset($params['cost_type']) ? $params['cost_type'] : '' ,
                    'lng'=> $lng,
                    'lat'=> $lat ,
                    'gps_type'=> $gps_type,
                    'dist' => '',
                    'is_new' => $is_new,
                    'order_id'=> isset($params['order_id']) ? $params['order_id'] : '' ,
		            'channel' => $params['channel']
                );

                if (isset($params['order_number']) && !empty($params['order_number'])) {
                    $content['order_number'] = $params['order_number'];
                }

                // 调整费用 add by liutuanwang
                EdjLog::info("--MODEIFY-- ".$params['type']."-".$content['order_id'].".");
                $modifyFee =  @FinanceUtils::getModifyFeeConfig($params['type'], $params['city_id'], $params);
                EdjLog::info("--MODEIFYreturn-- ".json_encode($modifyFee));
                if(!empty($modifyFee)){
                    $content['modify_fee'] = $modifyFee;
                }
                //消息格式
                $message=array(
                        'push_msg_id' => $push_msg_id,
                        //为了短信下发Push,在此生成distinct_id
                        'push_distinct_id' => Tools::getUniqId('nomal'),
                        'type'=>$type,
                        'queue_id'=>isset($params['queue_id']) ? intval($params['queue_id']) : 0,
                        'content'=>$content,
                        'timestamp'=>time()
                );
                if ($is_new == 1) {
                    $message['timeout'] = self::ORDER_RECEIVE_TIMEOUT;
                }
                break;
            case self::TYPE_MSG :
            case self::TYPE_MSG_DRIVER :          //推送普通消息
            case self::TYPE_MSG_LEADER :          //推送组长消息
                $content = isset($params['content']) ? $params['content'] : '';
                $message=array(
                        'type'=>self::TYPE_MSG,
                        'content'=>$content,
                        'timestamp'=>time()
                );
                break;
            case self::TYPE_NOTICE :
            case self::TYPE_NOTICE_DRIVER :    //推送普通公告
                $content = isset($params['content']) ? $params['content'] : '';
                $message=array(
                        'type'=>self::TYPE_NOTICE,
                        'content'=>$content,
                        'timestamp'=>time()
                );
                break;
            //add by aiguoxin
            case self::TYPE_MSG_CUSTOMER:         //客户
                $content = isset($params['content']) ? $params['content'] : '';
                $orderId = isset($params['orderId']) ? $params['orderId'] : 0;
                $messageid = isset($params['messageid']) ? $params['messageid'] : 0;
                $message=array(
                        "push_msg_id"=>$push_msg_id,
                        "content"=>$content,
                        "type"=>2, //see http://wiki.edaijia.cn/dwiki/doku.php?id=push_%E5%8D%8F%E8%AE%AE%E5%AE%9A%E4%B9%89
                        "orderId"=>$orderId,
                        "orderNum"=>$params['orderNum'],
                        "messageid"=>$messageid,
                        "timestamp"=>time()
                    );
                break;
            case AppleMsgFactory::TYPE_MSG_DRIVER_RECEIVE_ORDER:
                $content = isset($params['content']) ? $params['content'] : '';
                $orderId = isset($params['orderId']) ? $params['orderId'] : 0;
                $message=array(
                        "push_msg_id"=>$push_msg_id,
                        "content"=>$content,
                        "type"=>$type, 
                        "orderId"=>$orderId,
                        "driverId"=>$params['driverId'],
                        "bookingId"=>$params['bookingId'],
                        "timestamp"=>time()
                    );
                break;
            case AppleMsgFactory::TYPE_MSG_DRIVER_REJECT_ORDER:
                $content = isset($params['content']) ? $params['content'] : '';
                $orderId = isset($params['orderId']) ? $params['orderId'] : 0;
                $message=array(
                        "push_msg_id"=>$push_msg_id,
                        "content"=>$content,
                        "type"=>$type, 
                        "driverId"=>$params['driverId'],
                        "bookingId"=>$params['bookingId'],
                        "timestamp"=>time()
                    );
                break;
            case AppleMsgFactory::TYPE_MSG_DRIVER_CANCEL_ORDER:
                $content = isset($params['content']) ? $params['content'] : '';
                $orderId = isset($params['orderId']) ? $params['orderId'] : 0;
                $message=array(
                        "push_msg_id"=>$push_msg_id,
                        "content"=>$content,
                        "type"=>$type, 
                        "orderId"=>$orderId,
                        "driverId"=>$params['driverId'],
                        "bookingId"=>$params['bookingId'],
                        "driverName"=>$params['driverName'],
                        "cancelType"=>$params['cancelType'],
                        "timestamp"=>time()
                    );
                break;
            case AppleMsgFactory::TYPE_MSG_DRIVER_REACH_TARGET:
                $content = isset($params['content']) ? $params['content'] : '';
                $orderId = isset($params['orderId']) ? $params['orderId'] : 0;
                $message=array(
                        "push_msg_id"=>$push_msg_id,
                        "content"=>$content,
                        "type"=>$type, 
                        "orderId"=>$orderId,
                        "driverId"=>$params['driverId'],
                        "bookingId"=>$params['bookingId'],
                        "timestamp"=>time()
                    );
                break;
            case AppleMsgFactory::TYPE_MSG_COUPON:
                $content = isset($params['content']) ? $params['content'] : '';
                $messageid = isset($params['messageid']) ? $params['messageid'] : 0;
                $message=array(
                        "push_msg_id"=>$push_msg_id,
                        "content"=>$content,
                        "messageid"=>$messageid,
                        "type"=>$type, 
                        "timestamp"=>time()
                    );
                break;
            case AppleMsgFactory::TYPE_MSG_ACTIVE:
                $title = isset($params['title']) ? $params['title'] : '';
                $content = isset($params['content']) ? $params['content'] : '';
                $url = isset($params['url']) ? $params['url'] : '';
                $messageid = isset($params['messageid']) ? $params['messageid'] : 0;
                $message=array(
                        "push_msg_id"=>$push_msg_id,
                        "title"=>$title,
                        "content"=>$content,
                        "messageid"=>$messageid,
                        "type"=>$type, 
                        "url"=>$url,
                        "timestamp"=>time()
                    );
                break;
            case AppleMsgFactory::TYPE_MSG_FEEDBACK:
                $content = isset($params['content']) ? $params['content'] : '';
                $url = isset($params['url']) ? $params['url'] : '';
                $messageid = isset($params['messageid']) ? $params['messageid'] : 0;
                $suggestionid = isset($params['suggestionid']) ? $params['suggestionid'] : 0;
                $message=array(
                        "push_msg_id"=>$push_msg_id,
                        "content"=>$content,
                        "messageid"=>$messageid,
                        'suggestionid'=>$suggestionid,
                        "type"=>$type, 
                        "timestamp"=>time()
                    );
                break;
            case AppleMsgFactory::TYPE_MSG_BILL:
                $content = isset($params['content']) ? $params['content'] : '';
                $url = isset($params['url']) ? $params['url'] : '';
                $messageid = isset($params['messageid']) ? $params['messageid'] : 0;
                $message=array(
                        "push_msg_id"=>$push_msg_id,
                        "content"=>$content,
                        "messageid"=>$messageid,
                        "type"=>$type, 
                        "timestamp"=>time()
                    );
                break;
            case self::TYPE_UNREAD_MSG_DRIVER_COUNT: 
                $message=array(
                    'type' => 'new_msg',
                    'content'=>array(
                        'category' => 10,
                        'message'=>array(
                            'type'=>$params['type'],
                            'count'=>$params['count']
                            ),
                        ),
                    );
                break;
            case self::TYPE_DRIVER_LOG_DEBUG : //推送打开日志调试上传功能
                $message=array(
                    'type'=>$type,
                    );
                break;
            case self::TYPE_DRIVER_CROWN: //皇冠通知
                $open = isset($params['open']) ? $params['open'] : 0;
                $message=array(
                    'type'=>$type,
                    'content'=>array(
                        'open'=>$open,
                        ),
                    );
                break;
            case self::TYPE_DRIVER_BALANCE_CHANGE://余额变化通知
                $balance = isset($params['balance']) ? $params['balance'] : 0;
                $change_type = isset($params['change_type']) ? $params['change_type'] : 1;
                $message=array(
                    'type'=>$type,
                    'content'=>array(
                        'balance'=>$balance,
                        'change_type'=>$change_type,
                        ),
                    );
                break;
            case self::TYPE_ORDER_CANCEL :        //推送消单消息
            case self::TYPE_NOTICE_DRIVER_UPY :   //推送报单消息
            case self::TYPE_NOTICE_DRIVER_AUDIO : //又拍云
            case self::TYPE_BLACK_CUSTOMER :      //黑名单客户
            case self::TYPE_UPDATE_CONFIG :       //开关配置
            case self::TYPE_ORDER_SUBMIT :        //公告 司机端
                $content = isset($params['content']) ? $params['content'] : '';
                $message=array(
                        'type'=>$type,
                        'content' => $content,
                        'timestamp'=>time()
                );
                break;

            case self::TYPE_CUSTOM_ACCOUNT_CHANGE : //用户余额变更通知
            case self::TYPE_CUSTOM_PAY_CHANGE://用户支付信息变更通知
                    $content=isset($params['content'])?$params['content']:'';
                    $message=array(
                        'type'=>$type,
                        'content'=>$content,
                        'timestamp'=>time()
                    );
                    break;
            case self::DRIVER_PUSH_TYPE_ENVELOPE:
                $content = isset($params['content']) ? $params['content'] : '';
                $message = array(
                    'type'=>$type,
                    'content'=>$content,
                    'timestamp'=>time()
                );
                break;
            case AppleMsgFactory::TYPE_MSG_SHARE:
                $content = isset($params['content']) ? $params['content'] : '';
                $url = isset($params['url']) ? $params['url'] : '';
                $order_id = isset($params['order_id']) ? $params['order_id'] : 0;
                $message=array(
                    "push_msg_id"=>$push_msg_id,
                    "content"=>$content,
                    "type"=>$type,
                    "url"=>$url,
                    'order_id'=>$order_id,
                    "timestamp"=>time(),
                    "activity_type"=>'1',
                    "show_type"=>'1',
                );
                break;
            case AppleMsgFactory::TYPE_MSG_ONLINEPAY:
                $content = isset($params['content']) ? $params['content'] : '';
                $order_id = isset($params['order_id']) ? $params['order_id'] : '';
                $income = isset($params['income']) ? $params['income'] : 0;
                $message=array(
                    "push_msg_id"=>$push_msg_id,
                    "content"=>$content,
                    "type"=>$type,
                    'order_id'=>$order_id,
                    'income'=>$income,
                    "timestamp"=>time(),
                );
                break;
            case AppleMsgFactory::TYPE_MSG_USERNOTIFY;
                $message = AppleMsgFactory::model()->orgPushMsg($params,$type);
            default:
                break;
        }
        return $message;
    }
}

