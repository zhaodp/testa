<?php
/**
* by jiajingtao
* 司机代驾分恢复策略
*/
class driverPointsRecoveryCommand extends CConsoleCommand
{
	
	
	public function run($params){
		
		$this->actionPointsRecovery($params[0],$params[1]);
	}
	
	/**
	*	by jiajingtao
	*	$user 司机号
	*	$recenttime  上一次统计时间
	*	$day  统计时间间隔
	*/
	public function getDriverPoints($user,$recentdate,$days,$onoff='online'){
		$timestamp = $days*24*60*60;
		$recentdate = strtotime($recentdate);
		$currenttime = time();
		
		if(($timestamp+$recentdate)<$currenttime){
			$cycles = floor(($currenttime-$recentdate)/$timestamp); //确定几个周期
			
			$points = 0;
			for($i=1;$i<=$cycles;$i++){
				//$endtime = $timestamp+$recentdate;
				$start_time = $recentdate+($i-1)*$timestamp;
				$end_time = $start_time+$timestamp;
				$start_date = date("Y-m-d H:i:s",$start_time);
				$end_date = date("Y-m-d H:i:s",$end_time);
				
				$orderinfo = Order::model()->getOrderNumDistanceByTime($user,$start_date,$end_date);
				$goodcomment = $this->getHighOpinionNum($user,$start_date,$end_date);
				
				if($orderinfo['ordernum']>=180 && $orderinfo['totaldistance']>1800 && $goodcomment>30){
					$points += 3;
					echo ' driverPoints: 3 ';
				} elseif($orderinfo['ordernum']>=120 && $orderinfo['totaldistance']>1200 && $goodcomment>20){
					$points += 2;
					echo ' driverPoints: 2 ';
				} elseif($orderinfo['ordernum']>=60 && $orderinfo['totaldistance']>600 && $goodcomment>10){
					$points += 1;
					echo ' driverPoints: 1 ';
				}
				
				echo ' start:  '.$start_date.' end:  '.$end_date.'  goodcomment: '.$goodcomment.' ordernum: '.$orderinfo['ordernum'].' distance: '.$orderinfo['totaldistance'];
			}
			if($onoff=='online'){
				$ress = DriverExt::model()->updateStaticsTime($user,$end_date);//更新统计时间点
				$res = DriverExt::model()->addScore($user,$points);//更新代价分
				//增加日志到t_driver_punish_log
			
				$param = array(
                        'driver_id' => $user,
                        'customer_complain_id' => 0,
                        'complain_type_id' => DriverPunishLog::DRIVER_POINTS_RECOVERY,  //10003
                        'operator' => 'system',
                        'driver_score'=>$points,
                        'block_day' =>0,
                        'comment_sms_id' => 0,
                        'city_id'=>1,
                        'create_time' => date('Y-m-d H:i:s'),
                        'deduct_reason' =>'季度代驾分恢复',
                        'revert'=> DriverPunishLog::REVERT_NO,
                    );
                    $res = DriverPunishLog::model()->addData($param);
			}
			
			
			echo  "  driver: ".$user.'  newDriverPoints: '.$points.' end_date: '.$end_date."  cycles: ".$cycles;
		}
		echo " \n ";
		
		
	}
	
	/***
     * 获得司机某段时间的好评数
     * @param $driver_id 司机id
     * @param $date_start $date_end如 2013-08-21
     * @return int
     */
    public function getHighOpinionNum($driver_id, $date_start,$date_end) {
        //$date_start = date('Y-m-d', strtotime($date));
        //$date_end = date('Y-m-d', strtotime($date_start)+86400);
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('count(*)');
        $command->from('t_comment_sms');
        $command->where('level>=:level and driver_id=:driver_id and created>=:date_start and created<:date_end', array(':level'=>5, ':driver_id'=>$driver_id, ':date_start'=>$date_start, ':date_end'=>$date_end));
        return intval($command->queryScalar());
    }
	
	/**
	* by jiajingtao
	* 司机代驾分恢复
	*$action 默认test  修改线上数据需要online ； $area 为all时为全国城市，数字时具体城市；
	*/
	public function actionPointsRecovery($action='test',$area=1){
		echo Common::jobBegin('driverPointsRecovery');
		$start_time = date('Y-m-d H:i:s');
        $offset = 0;
        $pageSize = 1000;
        $i = 0;
		
		while(true){
			$criteria = new CDbCriteria();
            $criteria->select = "user";         
            $criteria->addCondition('mark != :mark');          
			
			if($area!='all'){
				$criteria->addCondition('city_id = :city_id');
				$criteria->params = array(
					':mark' => 3,
					':city_id' => intval($area)
				);	
			} else {
				$criteria->params = array(
				':mark' => 3
				);	
			}
			
			//$criteria->addCondition('id <= 100');			
            $criteria->order = 'id asc';
            $criteria->offset = $offset;
            $criteria->limit = $pageSize;

            $driver = Driver::model()->findAll($criteria);
			
			if ($driver) {
                foreach ($driver as $v) {
                    echo $v->user." ";
                    //self::actionUpdateExt($v->user);
					$exts = DriverExt::model()->getExt($v->user);
					echo ' oldDriverPoints: '.$exts['score'];
					if($exts['statics_created'] != '0000-00-00 00:00:00'){
						$res = $this->getDriverPoints($v->user,$exts['statics_created'],90,$action);
					} elseif($exts['start_score_time'] != '0000-00-00 00:00:00'){
						$res = $this->getDriverPoints($v->user,$exts['start_score_time'],90,$action);
					}
                    $i ++;
                }
            } else {
                $content = '代驾分恢复更新完毕。开始时间'.$start_time.'结束时间'.date('Y-m-d H:i:s').'共更新司机'.$i.'个';
                //if($start_id)$content.=' 开始id :'.$start_id.' 升序执行';
                //echo $content;
                //Mail::sendMail(array("dengxiaoming@edaijia-inc.cn",'dongkun@edaijia-inc.cn','yangmingli@edaijia-inc.cn'),$content, "司机扩展信息更新状态每日邮件");
                break;
            }

            $offset += $pageSize;
			
		}
		
		
		
		echo Common::jobEnd('driverPointsRecovery');
		
		
	}
	
	
	
	
	
}


?>