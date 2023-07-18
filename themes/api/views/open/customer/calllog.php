<?php
//记录客户呼叫位置

$task=array(
		'method'=>'app_calllog',
		'params'=>$params
);
//Queue::model()->dumplog($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'appcalllog');

echo json_encode(array(
		'code'=>0,
		'callTime'=>time()
));