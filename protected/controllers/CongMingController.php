<?php
/**
 * 丛铭测试Controller
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-08-04
 */
class CongMingController extends Controller
{
	public $layout = '//layouts/column1';
	public function actions()
	{
		return array(
			'drivers' => 'application.controllers.congming.dispatch.DriversAction',
			'dispatch' => 'application.controllers.congming.dispatch.AutoDispatchAction',
			'pushmsg' => 'application.controllers.congming.dispatch.PushTestAction',
			'pushmsg' => 'application.controllers.congming.dispatch.PushTestAction',
			
			'recommend' => 'application.controllers.congming.driver.RecommendAction',
			'ordercache' => 'application.controllers.congming.customer.OrderCacheAction',
			'mucang' => 'application.controllers.congming.stat.MucangAction',
			'weixin' => 'application.controllers.congming.stat.WeiXinAction',
			
			'daohangquan' => 'application.controllers.congming.stat.DaohangquanAction',
		);
	}
	
	/**
     * 客户端补单插入t_order_queue
     * @param array $params
     * @param string $source
     * @return array $queue_arr
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-27
     */
    public function OrderQueueSave($params , $type , $agent_id) {
    	if (empty($params) || empty($agent_id)) {
    		return '';
    	}
    	//参数整理
    	$time = time();
    	if (isset($params['booking_time'])) {
    		$booking_time = date("Y-m-d H:i:s" , $params['booking_time']);
    	} else {
			$booking_time = date('Y-m-d H:i:s' ,time()+1200);
    	}
//		$call_id = md5(time().floor(microtime()*1000).rand(1000,9999));
        $call_id = md5(uniqid(rand(), true));
		//判定是否为VIP
		$str = '';
		if ($agent_id != OrderQueue::QUEUE_AGENT_CLIENT) {
			$str = ",该单是".$agent_id;
		}
		$is_vip = Vip::model()->getPrimaryPhone($params['phone']);
		if ($is_vip && $is_vip->attributes['status'] == Vip::STATUS_NORMAL) {
			$comments = '此用户是vip'.$str;
		} elseif ($is_vip && $is_vip->attributes['status'] == Vip::STATUS_ARREARS) {
			$comments = '此用户是vip,余额是'.$is_vip->attributes['balance'].$str;
		} else {
			$comments = substr($str,1);
		}
		//判定是否为VIP END
		$queue_arr = array(
		    'phone' => $params['phone'],              //客户电话
		    'contact_phone' => $params['phone'],      //客户电话
		    'city_id' => $params['city_id'],          //需要gps反推
		    'callid' => $call_id,                     //callid 时间戳加密
		    'name' => $params['name'],                //需要传进来
		    'number' => isset($params['number']) ? $params['number'] : 1,                            //司机数量
//		    'dispatch_number' => 1,                   //司机数量
		    'address' => $params['address'],          //地址                
		    'comments' => $comments,                  //说明
		    'booking_time' => $booking_time,          //传进来的时间+20分钟
		    'flag' => OrderQueue::QUEUE_WAIT_COMFIRM,            //派单状态
		    'type' => $type ,     //派单状态
		    'update_time' => '0000-00-00 00:00:00',   //更新时间
		    'agent_id' => $agent_id,                    //操作员 --- 
		    'dispatch_agent' => '',                   //下单的时间
		    'dispatch_time' => $booking_time, //下单的时间
		    'created' => date('Y-m-d H:i:s' , $time), //下单的时间
		);
		$model = new OrderQueue();
		$model->attributes = $queue_arr;
		$model->lng = '116.462494';
		$model->lat = '39.910469';
		$result = $model->save();
		if ($result) {
			$queue_arr['id'] = $model->id;
			if (isset($params['call_time'])) {
				$queue_arr['call_time'] = $params['call_time'];
			} else {
				$queue_arr['call_time'] = time();
			}
			return $queue_arr;
		} else {
			return '';
		}
    }
}