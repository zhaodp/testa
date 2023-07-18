<?php
//更新司机状态
//检查imei是否存在

$imei=trim($_GET['imei']);
$status=$_GET['status'];

$driver = DriverStatus::model()->getByimei($imei);

if ($driver) {
	if ($status==255) {
		$status=1;
	}
	$driver->status = $status;
	
	//添加task队列
	$task=array(
			'method'=>'status',
			'params'=>array(
					'imei'=>$imei,
					'driver_id'=>$driver->driver_id,
					'status'=>$status
			)
	);
	Queue::model()->task($task);
}
