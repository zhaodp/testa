<?php
/**
 * 深圳司机紧急事件job
 *
 * User: diwenchen
 * Date: 4/16/15
 * Time: 9:03
 */
class PunishCommand extends LoggerExtCommand{

	public function actionComplainPunish($cityId=0, $interval=2, $thredhold=2, $beginTime=null) {
		if ($cityId == 0) {
			return ;
		}

		$now = time();
		if ($beginTime==null){
			$beginTime = $now - $interval * 3600;
		}
		$sql = "select customer_phone from t_driver_complaint where city = $cityId and create_time > $beginTime group by customer_phone having count(customer_phone) >= $thredhold";
		#echo "$sql\n";
		$phones = Yii::app()->db->createCommand($sql)->queryAll();
		
		$remarks = "$interval 小时内司机投诉$thredhold 次";
		$this->addBlack($phones, $remarks, 'customer_phone');
	
        }

	public function actionCancelOrderPunish($cityId=0, $interval=4, $thredhold=5, $beginTime=null, $distance=2) {
		
                if ($cityId == 0) {
                        return ;
                }
		$now = time();
                if ($beginTime==null){
                        $beginTime = $now - $interval * 3600;
                }
		$sql = "select  m.phone from t_order m , t_order_ext n where m.booking_time > $beginTime and m.city_id = $cityId and m.order_id = n.order_id and n.driver_ready_distance <= $distance and (m.status = 6 or m.status = 2) and m.driver_id != 'BJ00000' group by m.phone having count(m.phone) >= $thredhold";
		$phones = Yii::app()->db_order_tmp->createCommand($sql)->queryAll();

                $remarks = "$interval 小时内取消$distance 公里以内的订单$thredhold 次";
                $this->addBlack($phones, $remarks, 'phone');

	}
	
	private function addBlack($phones, $remarks, $phoneColumnName) {
		$count = count($phones);
		for( $i = 0; $i < $count;$i++) {
			$phone = $phones[$i][$phoneColumnName];
			$time = date("Y-m-d H:i:s" , time());
			$ex = date('Y-m-d H:i:s', strtotime('1 days'));
			$userId = '758';//武显赫

			$sql_check = "SELECT id FROM t_customer_blacklist WHERE phone=$phone";
			$result = Yii::app()->db_readonly->createCommand($sql_check)->queryRow();

			if (empty($result) && !empty($phone)) {
			    $sql = "INSERT INTO t_customer_blacklist(`phone` , `user_id` , `expire_time` , `created`, `remarks`) VALUES(:phone , :user_id , :expire_time , :created,:remarks)";
                	    $command = Yii::app()->db->createCommand($sql);
                	    $command->bindParam(":phone" , $phone);
                	    $command->bindParam(":user_id" , $userId);
			    $command->bindParam(":expire_time" , $ex);
                	    $command->bindParam(":created" , $time);
                	    $command->bindParam(":remarks" , $remarks);
                	    $command->execute();
			    echo "$phone|$userId|$ex|$time|$remarks\n";
                	    EdjLog::info("phone=".$phone.',add db black ok');
                	    $command->reset();
                	    // update cache
                	    CustomerStatus::model()->add_black($phone);
                	    EdjLog::info("phone=".$phone.',add redis black ok');
			}
		}

	}

}
