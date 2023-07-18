<?php
//需要写清楚注释 add by sunhongjing at 2013-5-19
/*
 * modify  zhanglimin 2013-06-08
 * 切换验证token方式 操作走队列
 */

    $driver = DriverStatus::model()->getByToken($params['token']);
    if (empty($driver) ||  $driver->token===null||$driver->token!==$params['token']) {
        $ret=array(
            'code'=>1,
            'message'=>'请重新登录'
        );
        echo json_encode($ret);
        return;
    }

    $order = Order::model()->getOrderById($params['order_id']);
    if(empty($order)){
        $ret=array(
            'code'=>1,
            'message'=>'请重新登录'
        );
        echo json_encode($ret);
        return;
    }

    if(strtoupper($order['driver_id']) != strtoupper($driver->driver_id)){
        $ret=array(
            'code'=>1,
            'message'=>'请重新登录'
        );
        echo json_encode($ret);
        return;
    }

    //添加task队列
    $task=array(
        'method'=>'push_order_invoice_update',
        'params'=>array(
            'order_id'=>$order['order_id'],
            'invoice_title'=>$params['invoice_title'],
            'invoice_content'=>$params['invoice_content'],
            'invoice_contact'=>$params['invoice_contact'],
            'invoice_telephone'=>$params['invoice_telephone'],
            'invoice_address'=>$params['invoice_address'],
            'invoice_zipcode'=>$params['invoice_address'],
        )
    );

    //Queue::model()->task($task);
	//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
	Queue::model()->putin($task,'task');

    $ret=array(
        'code'=>0,
        'message'=>'成功!'
    );
    echo json_encode($ret);
    return;
