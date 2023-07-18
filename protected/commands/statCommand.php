<?php

class statCommand extends CConsoleCommand {
	
	/**
	 * 统计当前时刻的司机工作状态
	 */
	public function actionStatusOld() {
		$snap_time = time();
	
		//加上限制，不计算解约司机和被屏蔽司机
		$sql = 'SELECT city_id, status state, count( * ) total
				FROM `t_driver_position` dp, `t_driver` d
				WHERE dp.user_id=d.id and d.city_id !=0 and d.mark=0
				GROUP BY city_id, status';
		$logs = Yii::app()->db->createCommand($sql)->queryAll();
		print_r($logs);
		foreach($logs as $item){
			$item['snap_time'] = $snap_time;
			$item['minute'] = substr(date('i',$snap_time), 1,1) ;
			Yii::app()->dbreport->createCommand()->insert('t_work_log',$item);
		}
		//如果北京空闲司机低于40人，发送短信
	}
	
	/**
	 * @author sunhongjing 2013-07-05
	 * 
	 */
	public function actionStatus() {
		$snap_time = time();
	
		//加上限制，不计算解约司机和被屏蔽司机
		$sql = 'SELECT city_id, status state, count( * ) total
				FROM `t_driver_position` dp, `t_driver` d
				WHERE dp.user_id=d.id and d.city_id !=0 and d.mark=0
				GROUP BY city_id, status';
		$logs = Yii::app()->db_readonly->createCommand($sql)->queryAll();
		
		print_r($logs);
		//整理数据,保证每个状态都有一条记录。
		$city_drivers = array();
		foreach ($logs as $info) {
			$city_drivers[$info['city_id']][$info['state']] = $info['total'];
		}
		$new_logs = array();
		foreach ($city_drivers as $c=>$v) {	
			$new_data = array();	
			if( 3==count($v) ){
				foreach ($v as $state=>$num) {
					$new_data['city_id'] = $c;
					$new_data['state']   = $state;
					$new_data['total']   = $num;
					$new_logs[] = $new_data;
				}
			}else{
				for($i=0;$i<3;$i++){
					$new_data['city_id'] = $c;
					$new_data['state']   = $i;
					$new_data['total']   = isset($v[$i]) ? $v[$i] : 0;
					$new_logs[] = $new_data;
				}
			}	
		}	
		
		print_r($new_logs);
		
		foreach($new_logs as $item){
			$item['snap_time'] = $snap_time;
			$item['minute'] = substr(date('i',$snap_time), 1,1) ;
			Yii::app()->dbreport->createCommand()->insert('t_work_log',$item);
		}
		//如果北京空闲司机低于40人，发送短信
	}
	
	
	/**
	 * 
	 * 设置上次上报位置时间超过10分钟的司机状态为下班状态
	 */
	public function actionUpdateStatus() {
		$message = '师傅，您的定位手机已经超过10分钟没有上报状态，系统已自动将您设为下班；您可先将手机调为下班状态，1分钟后再重设为空闲。';
		$drivers = Employee::model()->checkStatus();
		foreach($drivers as $driver) {
			Sms::SendSMS($driver->phone, $driver->name.$message);
		}
		
		$sql = 'update t_employee set state=2 where unix_timestamp(now()) - unix_timestamp(update_time) >=600 and mark=0 and state =0';
		Yii::app()->db->createCommand($sql)->execute();
	}

}
