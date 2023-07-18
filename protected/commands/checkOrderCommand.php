<?php
//引入邮箱扩展
Yii::import('application.extensions.mailer.*');
Yii::import('application.models.driver.*');
class checkOrderCommand extends LoggerExtCommand{
	public function actionCheck($start_time,$end_time,$send_mail,$force_fix=0){
		if(empty($start_time)){
			$start_time=strtotime(date('Y-m-d 00:00:00'))-24*60*60;
		}
		else{
			$start_time=strtotime($start_time);
		}
		if(empty($end_time)){
			$end_time=$start_time+24*60*60;
		}
		else{
			$end_time=strtotime($end_time);
		}
                $step=24*60*60;
                for($booking_start_time=$start_time;$booking_start_time<$end_time;$booking_start_time+=$step){
                        $booking_end_time=$booking_start_time+$step;
                        $arrOrders = Order::model()->getOrders_Status_BookingId(1, $booking_start_time, $booking_end_time);

                        $table_name=date('Ym',$booking_start_time);
                        $sql=sprintf("select distinct(order_id) as order_id from t_employee_account_%s where created>=%d and created<=%d",$table_name,$booking_start_time-2*60*60,$booking_end_time+24*60*60);
                        //echo $sql."\n";
                        $arrEmployee=Yii::app()->db_finance->createCommand($sql)->queryAll();
                        $arrMyOrders=array();
                        foreach($arrEmployee as $row){
                                $arrMyOrders[$row['order_id']]=true;
                        }
                        foreach($arrOrders as $order){
                                if(!empty($arrMyOrders[$order['order_id']])){
                                        continue;
                                }
                                else{
					$now_year=date('Y',$booking_start_time);
					$now_month=date('m',$booking_start_time);
					$pre_year=$now_year;
					$pre_month=$now_month-1;
					$next_year=$now_year;
					$next_month=$now_month+1;
					if($pre_month==0){
						--$pre_year;
						$pre_month=12;
					}
					if($next_month==13){
						++$next_year;
						$next_month=1;
					}
					//上个月
                                        $sql=sprintf("select order_id from t_employee_account_%s where order_id='%d'",sprintf('%04d',$pre_year).sprintf('%02d',$pre_month),$order['order_id']);
                                        $result=Yii::app()->db_finance->createCommand($sql)->queryAll();
                                        if(!empty($result)){
                                        	continue;
                                       	}
					//下个月
                                        $sql=sprintf("select order_id from t_employee_account_%s where order_id='%d'",sprintf('%04d',$next_year).sprintf('%02d',$next_month),$order['order_id']);
                                        $result=Yii::app()->db_finance->createCommand($sql)->queryAll();
                                        if(!empty($result)){
                                        	continue;
                                        }
					//本月再查一次
                                        $sql=sprintf("select order_id from t_employee_account_%s where order_id='%d'",sprintf('%04d',$now_year).sprintf('%02d',$now_month),$order['order_id']);
                                        $result=Yii::app()->db_finance->createCommand($sql)->queryAll();
                                        if(!empty($result)){
                                                continue;
                                        }
                                        EdjLog::info($order['order_id']);
					if(!empty($send_mail) && $send_mail){
						Mail::sendMail(array('lidingcai@edaijia-inc.cn','liutuanwang@edaijia-inc.cn',/*'dengxiaoming@edaijia-inc.cn'*/),print_r($order,true),'[线上报警]有订单未入流水表');
					}
					if(!empty($force_fix) && $force_fix){
						OrderSettlement::model()->OrderSettle($order['order_id']);
					}
                                        //echo $order['order_id'].' '.$order['income'].' '.$order['price'].' '.$order['cast']."\n";
                                }
                        }
                }
	}
}
?>
