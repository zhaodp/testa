<?php

class reportCommand extends CConsoleCommand {
	
	public function getECallInfo($callTime, $driver_phone)
	{
		$callInfo = array();
		$model = new Order;
		$criteria = new CDbCriteria();
		
		$start_time = strtotime($callTime) - 300;
		$end_time = strtotime($callTime) + 300;
		$criteria->addCondition("call_time > $start_time");
		$criteria->addCondition("call_time < $end_time");
		$criteria->addCondition("driver_phone = '$driver_phone'");					
		$data = $model->findAll($criteria);
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$callInfoItem = array (
					'call_time' => $value->call_time,
					'phone' => $value->phone,
					'status' => $value->status
				);
				array_push($callInfo, $callInfoItem);				
			}
		}
		return $callInfo;
	}
	
	public function isOrderSuccess($callTime, $imei)
	{
		
		$success = false;
		$model = new Order();
		$criteria = new CDbCriteria();
		$criteria->addCondition("call_time='$callTime'");
		$criteria->addCondition("imei='$imei'");		
		$data = $model->findAll($criteria);
		if (!empty($data))
			$success = true;
		
		return $success;		
	}
	
	public function getDriverImei($phone)
	{
		$imei = '';
		$criteria = new CDbCriteria();
		$criteria->addCondition("phone='$phone'");		
		$model = Driver::model()->find($criteria);
		if (!empty($model))
			$imei = $model->imei;
			
		return $imei;
	}
	

	public function process($filePath, $outFilePath)
	{
		$handle = fopen($filePath ,"r");
		$i = 1;
		$j = 0;
		while(!feof($handle)) {
			$line = fgets($handle);
			$line = trim($line);
			if (!empty($line))
			{
				$info = explode("\t" , $line);
				echo $i++;	
				$info[0] = str_replace('/', '-', trim($info[0]));
				$info[1] = trim($info[1]);
				$callSuccess = '否';
				$orderSuccess = '否'; 
				$ecallTime = '';
				$userPhone = '';
				
				if (preg_match("/^[0-9]+$/", $info[1]))
				{
					$callTime = $info[0];
					$arrCallInfo = $this->getECallInfo($callTime, $info[1]);
					if (!empty($arrCallInfo))
					{
						$callSuccess = '是';							
						foreach ($arrCallInfo as $callInfo)
						{
							$ecallTime = date("Y-m-d H:i:s", $callInfo['call_time']);
							$userPhone = mb_substr($callInfo['phone'], 0, 7) . "****";
							$status = $callInfo['status'];
							
							if ($status == 1)
							{
								$orderSuccess = '是';
								break;
							}
						}
					}
					$info[2] = $callSuccess;
					$info[3] = $orderSuccess;
					$info[4] = $ecallTime;
					$info[5]  = $userPhone;
					
					$content = implode('","', $info);
					$content = '"' . $content . '"';
					file_put_contents($outFilePath, $content . "\n", FILE_APPEND);
					echo $content . "\n";					
							
				}
				
	
			}
		}		
	
	}
	public function actionMucang() {
/*		
		$filePath = dirname(__FILE__) .  '/../../cache/'. 'mucang_201208.txt';
		$outFilePath = dirname(__FILE__) .  '/../../cache/'. 'mucang08.txt';
		$this->process($filePath, $outFilePath);
		$filePath = dirname(__FILE__) .  '/../../cache/'. 'mucang_201209.txt';
		$outFilePath = dirname(__FILE__) .  '/../../cache/'. 'mucang09.txt';			
		$this->process($filePath, $outFilePath);
*/		$filePath = dirname(__FILE__) .  '/../../cache/'. 'mucang_201210.txt';
		$outFilePath = dirname(__FILE__) .  '/../../cache/'. 'mucang10.txt';			
		$this->process($filePath, $outFilePath);		
	}

    /**
     * 统计呼叫历史  统计一个小时前的数据 一次统计一小时的数据
     * @author mengtianxue 2013-05-30
     *
     */
    public function actionCallHoursReport(){
        $call_user_list = AdminUserNew::model()->getCallUserList();
        $date = strtotime(date('Y-m-d H:00:00')) - 7200;
        $day = date('Y-m-d H:00:00', $date);
		if(!empty($call_user_list) && is_array($call_user_list)) {
			foreach($call_user_list as $list){
	            $list['call_count'] = OrderQueue::model()->getCallTotal($day,$list['user_id']);
	            if( $list['call_count'] > 0){
	                $list['order_count'] = OrderQueue::model()->getCallCenterTotle($day,$list['name']);
	                $list['dispatch_count'] = OrderQueue::model()->getdispatchTotle($day,$list['name']);
	                $list['report_time'] = date('Y-m-d H:i:s', strtotime($day));
	                $list['created'] = date('Y-m-d H:i:s');
	                Yii::app()->dbreport->createCommand()->insert('t_call_phone_report',$list);
	                echo $list['name']."\n";
	            }
	        }
		}
        

    }

    /**
     * 当日运营趋势明细统计 每10分钟一次统计
     * @author bidong 2013-08-3
     */
    public function actionDailyTrendDetails(){

        $cityArr=array();
        $start_time=time()-600;
        $end_time=time();

        $openCity= RCityList::model()->getOpenCityList();
        if($openCity){
            echo 'Count_Citys_Start: '.date('Y-m-d H:i:s',time())."\r\n";
            foreach($openCity as $k=>$v){
                if($k>0){
                    $city=$k;
                    $cityArr[$city]= Order::model()->countOrderTrendDetails($city,$start_time,$end_time);
                    echo 'city_id: ['.$city.'] '.date('Y-m-d H:i:s',time())."\r\n";
                    var_dump($cityArr);
                }
            }
            echo 'Count_Citys_End: '.date('Y-m-d H:i:s',time())."\r\n";
            echo "\r\n";
            echo 'Add_Detail&Collect_Start: '.date('Y-m-d H:i:s',time())."\r\n";
            //添加统计明细，并计算汇总
            DailyTrendDetails::model()->addTrendDetail($cityArr,$start_time);
            echo 'Add_Detail&Collect_End: '.date('Y-m-d H:i:s',time())."\r\n";
        }
    }

    /**
     * 统计前2天订单数据,30分钟已统计，主要统计报单和销单数
     * @author bidong 2013-08-16
     */
    public function actionCollectOrderData($days=0){

        $days=$days?$days:4;
        for ($i = -1; $i < $days; $i++)
        {
            $j=$i+1;
            $start_time=strtotime("-$j day 7:00:00");
            $end_time=strtotime("-$i day 6:59:59");
            $count_date=DailyTrendDetails::model()->formatDate($start_time);

            echo 'count_date: '.$count_date."\r\n";

            $openCity= RCityList::model()->getOpenCityList();
            $yesterdayCityArr=array();
            foreach($openCity as $k=>$v){
                $city_id=$k;
                if($city_id>0){
                    //获取昨日统计数据
                    $yesterdayCityArr[$city_id]= Order::model()->countOrderTrendCollect($city_id,$start_time,$end_time);
                }
            }
            var_dump($yesterdayCityArr);
            echo 'start--time: '.date('Y-m-d H:i:s',$start_time)."\r\n";
            DailyTrendCollect::model()->updateTrendCollect($yesterdayCityArr,$count_date);
            echo 'end--time: '.date('Y-m-d H:i:s',$start_time)."\r\n";
            echo "\r\n";

        }

    }

    //修正当天数据
    public function actionRepairCollectOrderData($days){
        $start_time=strtotime("-$days day 7:00:00");
        $end_time=strtotime("0 day 6:59:59");
        $cityArr=array();
        $openCity= RCityList::model()->getOpenCityList();
        if($openCity){
            echo 'start--time: '.date('Y-m-d H:i:s',$start_time)."\r\n";
            foreach($openCity as $k=>$v){
                if($k>0){
                    $city=$k;
                    $cityArr[$city]= Order::model()->countOrderTrendDetails($city,$start_time,$end_time);
                    var_dump($cityArr[$city]);
                }
            }

            //计算汇总
            $count_date=DailyTrendDetails::model()->formatDate($start_time);
            DailyTrendCollect::model()->updateTrendCollectByDetail($cityArr,$count_date);
            echo 'end--time: '.date('Y-m-d H:i:s',$start_time)."\r\n";
        }
    }
    
    /**
     * 时时订单数据
     * @param <int> $day        开始日期   如：20130509
     * @param <int> $afterDays  多少天
     * @author liuxiaobo
     * @since 2014-1-26
     */
    public function actionTimeOrder($day = null, $afterDays = 2){
        Yii::import('application.models.report.*');
        $time = time();
        if($day === null){
            $day = date('Ymd', $time-86400);    //默认从昨天开始，跑两天的数据
        }
        $arg = '$day='.$day.',$afterDays='.$afterDays;
        echo "\r\nstart=========".$arg."======".date('Y-m-d H:i:s', $time)."======>\r\n";
        echo BOrderTrend::model()->buildReportData($day, $afterDays, TRUE);
        echo "\r\nend=========".$arg."========".date('Y-m-d H:i:s', time())."====>";
    }


}