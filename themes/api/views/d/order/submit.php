<?php
    /**
     * 判断有走不到的逻辑，小孟看到注释，检查一遍，找不到逻辑错误来问我，add by sunhongjing 2013-06-12
     * 
     * 报单
     * 调用的url
     * @author mengtianxue 2013-05-26
     * @param $params
     */

    //接受参数
   if( isset($params['token'])
        && isset($params['order_number'])
        && isset($params['name'])
        && isset($params['distance'])
        && isset($params['income'])
        && isset($params['price'])
        && isset($params['car_number'])
        && isset($params['cost_type'])
        && isset($params['log'])
        && isset($params['waiting_time'])
        && isset($params['lng'])
        && isset($params['lat'])
        && isset($params['card'])
        && isset($params['other_cost'])
        && isset($params['cost_mark'])){
      
       //判断token存在
       $driver_id = DriverToken::model()->getDriverIdByToken($params['token']);
//       $driver_id = $params['token'];
       if(!$driver_id){
           $ret = array (
                         'code'=>1,
                         'message'=>'请重新登录');
           echo json_encode($ret);return;
       }else{
           $params['driver_id'] = $driver_id;
       }
       
       //判断 order_number order_id 是否存在
       if(empty($params['order_id']) && empty($params['order_number'])){
           $ret = array (
                         'code'=>2,
                         'message'=>'订单ID或订单号不能为空');
           echo json_encode($ret);
           return;
       }
       
       $params['start_time'] = isset($params['start_time']) ? $params['start_time'] : 0;
       $params['end_time'] = isset($params['end_time']) ? $params['end_time'] : date('Y-m-d H:i:s');
       $params['gps_type'] = isset($params['gps_type']) ? $params['gps_type'] : "wgs84";
       $params['tip'] = isset($params['tip']) ? $params['tip'] : 0;
       $params['car_cost'] = isset($params['car_cost']) ? $params['car_cost'] : 0;
       $params['log_time'] = $params['end_time'];
       
       //增加等候时间和优惠券张数 BY AndyCong 2013-11-05
       $params['stop_wait_time'] = isset($params['midway_wait_time']) ? trim($params['midway_wait_time']) : ''; //中途等候时间
       $params['coupon'] = isset($params['cash_card']) ? intval($params['cash_card']) : '';                     //现金/定额卡张数
       $params['car_type'] = isset($params['car_type']) ? $params['car_type'] : '';                     //车型
       
       $params['invoiced'] = isset($params['invoice']) ? intval($params['invoice']) : '';                       //发票
       
       $params['cost_mark'] = $params['cost_mark']."[自动]";
       //增加等候时间和优惠券张数 BY AndyCong 2013-11-05 END
       
//       $queueProcess = QueueProcess::model()->order_submit($params);
       //选司机下单订单报单 ，将虚拟的order_id转化成数据库中的order_id才可以报单
       if (strlen($params['order_id']) > 11 && is_numeric($params['order_id'])) {
       	   //获取数据库中的order_id
       	   $order_id = ROrder::model()->getOrder($params['order_id'] , 'order_id');
       	   if (empty($order_id)) {
       	   	   $ret = array('code' => 2 , 'message' => '订单异常,请稍后再报单');
       	   	   echo json_encode($ret);return ;
       	   }
       	   $params['unique_order_id'] = $params['order_id'];
       	   $params['order_id'] = $order_id;
       } 
       //添加task队列向数据中添加
       $task = array(
                     'method'=>'order_submit',
                     'params'=>$params
                     );

		Queue::model()->putin($task,'settlement');
       
       $ret = array (
                     'code'=>0,
                     'message'=>'提交成功！');
       echo json_encode($ret);
       
   }else{
   	
   	   //添加task队列向数据中添加
       $task = array(
                     'method'=>'order_submit_tracking',
                     'params'=>$params
               );

	   Queue::model()->putin($task,'tmporder');
   	
       if(!$driver_id){
           $ret = array (
                         'code'=>2,
                         'message'=>'参数不正确');
           echo json_encode($ret);return;
       }
   }






