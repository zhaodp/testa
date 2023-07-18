<?php
//引入银联API相关文件
Yii::import('application.vendors.upmp.*');
//引入业务逻辑model
Yii::import('application.models.pay.*');
//引入业务redis
Yii::import('application.models.redis.*');
//引入邮箱扩展
Yii::import('application.extensions.mailer.*');

class payCommand extends LoggerExtCommand {

    /**
     * 交易额统计
     *
     * */
    public function actionPayCount($date) {
        echo "date:" . $date . "\n";

        if (empty($date)) {
            $date = date("Y-m-d", strtotime("-1 day"));
        }
        $bpay = new BUpmpPayOrder();
        $result = $bpay->getAllPayCount(trim($date));
        echo $result;
        Mail::sendMail(array("liutuanwang@edaijia-inc.cn","dengxiaoming@edaijia-inc.cn", "yuyang@edaijia-inc.cn", "kevin.li@edaijia-inc.cn", "lidingcai@edaijia-inc.cn", "shenguangliang@edaijia-inc.cn", "yuanrong@edaijia-inc.cn", "yangmingli@edaijia-inc.cn"), $result, "银行充值对账---" . $date);
    }

    public function genHtmlMail($arrTotalResult) {
        //总体交易概况
        $html = "交易概况(总数|成功数):<table border='1'><tr><td>日期</td><td>司机充值额</td>";
        $html .="<td>司机充值笔数</td><td>用户充值额</td><td>用户充笔数</td><td>充值总额</td><td>充值总笔数</td></tr>";
        foreach ($arrTotalResult as $arrResult) {
            $date = $arrResult['pay_date'];
            $driverRemoteSuccessAmount = $arrResult['driverTotalAmount'];
            $driverLocalSuccessAmount = $arrResult['driverSuccessAmount'];
            $userRemoteSuccessAmount = $arrResult['userTotalAmount'];
            $userLocalsSuccessAmount = $arrResult['userSuccessAmount'];
            $driverRemoteSuccessCount=$arrResult['driverTotalCount'];
            $driverLocalsSuccessCount=$arrResult['driverSuccessCount'];
            $userRemoteSuccessCount=$arrResult['userTotalCount'];
            $userLocalsSuccessCount=$arrResult['userSuccessCount'];
            $html .="<tr><td>" . $date . "</td><td>" . ($driverRemoteSuccessAmount) . "|" . ($driverLocalSuccessAmount) . "</td>";
            $html .="<td>" . $driverRemoteSuccessCount . "|" . $driverLocalsSuccessCount . "</td>";
            $html .="<td>" . ($userRemoteSuccessAmount) . "|" . ($userLocalsSuccessAmount) . "</td>";
            $html .="<td>" . $userRemoteSuccessCount . "|" . $userLocalsSuccessCount . "</td>";
            $html .="<td>" . ($driverRemoteSuccessAmount + $userRemoteSuccessAmount) . "|" . ($driverLocalSuccessAmount + $userLocalsSuccessAmount) . "</td>";
            $html .="<td>" . ($driverRemoteSuccessCount + $userRemoteSuccessCount) . "|" . ($driverLocalsSuccessCount + $userLocalsSuccessCount) . "</td>";
        }
        $html .="</tr></table>";

        return $html;
    }

    //得到 [startDay,endDay]之间的所有充值统计数据
    public function getPayResult($startDay, $endDay) {
        return Yii::app()->dbreport->createCommand()->select('pay_date,driverTotalAmount,'
                                . 'driverSuccessAmount,driverTotalCount,driverSuccessCount,userTotalAmount,userSuccessAmount,'
                                . 'userTotalCount,userSuccessCount')
                        ->from('t_pay_result')->where("pay_date>='$startDay' and pay_date<='$endDay'")->order('pay_date desc')->queryAll();
    }

    //给相关人员发送邮件，默认为前30天的
    public function actionSendMail($startDay,$endDay,$debug){
        $nowUnixTime=time();
        if(empty($startDay)){
            $startDay=date('Y-m-d',$nowUnixTime-30*24*60*60);
        }
        if(empty($endDay)){
            $endDay=date('Y-m-d',$nowUnixTime-24*60*60);
        }
        $arrTotalResult=$this->getPayResult($startDay, $endDay);
        $html=$this->genHtmlMail($arrTotalResult);
	if(!empty($debug)&&$debug==1){
		Mail::sendMail(array("lidingcai@edaijia-inc.cn"), $html, "银行充值对账---" . $startDay."至".$endDay);
	}
	else{
        	Mail::sendMail(array("liutuanwang@edaijia-inc.cn", "dengxiaoming@edaijia-inc.cn", "yuyang@edaijia-inc.cn", "kevin.li@edaijia-inc.cn",
            	"lidingcai@edaijia-inc.cn", "shenguangliang@edaijia-inc.cn", "yuanrong@edaijia-inc.cn",
           	 "yangmingli@edaijia-inc.cn"), $html, "银行充值对账---" . $startDay."至".$endDay);
	}
    }
    /**
      交易统计入库
     */
    public function actionPayCountToDb($startDate, $endDate) {
        $timeStart = strtotime(date('Y-m-d') . ' 00:00:00') - 24 * 60 * 60; //跑昨天的数据
        if (empty($startDate)) {
            $startDate = date('Y-m-d', $timeStart);
        }
        if (empty($endDate)) {
            $endDate = date('Y-m-d', $timeStart);
        }
        $startUnixTime = strtotime($startDate);
        $endUnixTime = strtotime($endDate);
        $oneDayTime = 24 * 60 * 60;
        $tableName = "t_pay_result";
        $fp_user = fopen("/data/logs/user_data.txt", "w+");
        $fp_driver = fopen("/data/logs/driver_data.txt", "w+");
        for ($checkTime = $startUnixTime; $checkTime <= $endUnixTime; $checkTime+=$oneDayTime) {
            $date = date('Y-m-d', $checkTime);
            $bpay = new BUpmpPayOrder();
            $result = $bpay->getAllPayCountJson(trim($date));
            //var_dump($result);
            $date = date('Y-m-d', strtotime("$date +1 day"));
            $arrCols = array('pay_date' => $result['pay_date'], 'driverTotalAmount' => $result['driverTotalAmount'],
                'driverSuccessAmount' => $result['driverSuccessAmount'], 'driverTotalCount' => $result['driverTotalCount'],
                'driverSuccessCount' => $result['driverSuccessCount'], 'userTotalAmount' => $result['userTotalAmount'], 'userSuccessAmount' => $result['userSuccessAmount'], 'userTotalAmount' => $result['userTotalAmount'], 'userTotalCount' => $result['userTotalCount'], 'userSuccessCount' => $result['userSuccessCount'], 'update_time' => date('Y-m-d H:i:s'));
            //var_dump($arrCols);
            Yii::app()->dbreport->createCommand()->insert($tableName, $arrCols);
            //echo $date."\n";
            foreach ($result['driverFail'] as $key => $item) {
                $logString = $item['order_id'] . " " . $item['order_amount'] . " " . $item['user_id'] . " " . $item['create_time'] . "\n";
                fwrite($fp_driver, $logString);
            }
            foreach ($result['userFail'] as $key => $item) {
                $logString = $item['order_id'] . " " . $item['order_amount'] . " " . $item['user_id'] . " " . $item['vipInfo'] . " " . $item['create_time'] . "\n";
                fwrite($fp_user, $logString);
            }
        }
        fclose($fp_user);
        fclose($fp_driver);
    }

    /**
     *  
     *  运行过去36小时到最近前半个小时之前的数据，如果没有支付成功，则执行补账流程
     *
     * */
    public function actionPayRepair() {
        echo "start to repair pay=============\n";
        EdjLog::info("start to repair pay");
        //$bpay=new BUpmpPayOrder();

        $nowUnixTime = time(); //这个系统时间一定要准确啊！
        $startTime = date('YmdHis', $nowUnixTime - 36 * 60 * 60); //从这个时候开始的数据
        $endTime = date('YmdHis', $nowUnixTime - 30 * 60); //到这个结束时间的数据
        $param = array('verify' => 0, 'trans_time>' => "$startTime", 'trans_time<' => "$endTime"); //注意这种方式比较的正确性

        echo "startTime:" . $startTime . "\n";
        EdjLog::info("startTime:" . $startTime);
        echo "endTime:" . $endTime . "\n";
        EdjLog::info("endTime:" . $endTime);
        //$bpay->payScanAndRepair($param);      
        BUpmpPayOrder::model()->payScanAndRepair($param);
    }

    /**
     * 支付交易 查询，对账
     */
    public function actionPayData($startTime, $endTime) {
        $nowUnixTime = time();
        if ($startTime == '') {
            $startTime = date('YmdHis', $nowUnixTime - 60 * 60); //从这个时候开始的数据
        }
        if ($endTime == '') {
            $endTime = date('YmdHis', $nowUnixTime - 5 * 60); //到这个结束时间的数据
        }
        //获取待对账数据
        $bpay = new BUpmpPayOrder();
        $upmpPay = new UpmpPay();

        EdjLog::info(Common::jobBegin('用户充值对账开始'));
        $param = array('channel'=>1,'verify' => 0, 'trans_time>' => "$startTime", 'trans_time<' => "$endTime"); //注意这种方式比较的正确性
        $userPayData = $bpay->getUserPayOrderList($param);
        $this->updatePayOrder($userPayData, $upmpPay, false);
        EdjLog::info(Common::jobEnd('用户充值对账结束'));

        //司机对账
        EdjLog::info(Common::jobBegin('司机充值对账开始'));
        $param = array('channel'=>1,'verify' => 0, 'trans_time>' => "$startTime", 'trans_time<' => "$endTime");
        $driverPay = $bpay->getDriverPayOrderList($param);
        $this->updatePayOrder($driverPay, $upmpPay);

        EdjLog::info(Common::jobEnd('司机充值对账结束'));
    }

    protected function updatePayOrder($payObjData, $upmpPay, $isDriver = true) {
        $arrInvalidData = array();
        foreach ($payObjData as $item) {
            $trans_type = $item->trans_type;    //交易类型
            $order_time = $item->trans_time;   //商户交易开始日期时间
            $order_no = $item->order_id;     //商户订单号
            $qn = $item->resp_qn;
            $trans_status = $item->trans_status;
            if ($isDriver) {
				$employeeList = $this->getEmployeeAccountByOrder($order_no, time());
				if(!empty($employeeList)){
					continue;
				}
            } else {
				$query_for_vip = VipTrade::model()->getByOrderID($order_no);
                if ($query_for_vip) {
                    continue;
                }
                $customer_sql = sprintf("select id from t_customer_trans where trans_order_id='%s'", $order_no);
                $command = Yii::app()->db_finance->createCommand($customer_sql);
                $query_for_customer = $command->queryAll();
                if (count($query_for_customer) >= 1) {
                    continue;
                }
            }
            $query_result = $upmpPay->query($trans_type, $order_time, $order_no);
            if (!isset($query_result['succ']) || !$query_result['succ']) {//查询没成功，不做任何操作
                EdjLog::info("not succ,query unionpay for order_id $order_no fail! result=" . serialize($query_result));
                continue;
            }
            if (!isset($query_result['data']['respCode'])) {//查询还是没成功，不做任何操作
                EdjLog::info("respCode error,query unionpay for order_id $order_no fail! result=" . serialize($query_result));
                continue;
            }
            if ($query_result['data']['respCode'] != '00') {//各种问题，更新数据库
		if($query_result['data']['respCode']!='11'){
		    EdjLog::info("respCode not 11 result is ".serialize($query_result));
		    continue;
		}
                EdjLog::info("respCode not 00,order_id $order_no fail,result=" . serialize($query_result));
                $item->verify = 1;
                $item->update_time = date('Y-m-d H:i:s');
                $item->trans_status = 11; //11 为失败状态
                if (isset($query_result['data']['respMsg'])) {
                    $item->resp_msg_second = $query_result['data']['respMsg'];
                }
                $ret = $item->update();
            } else if (isset($query_result['data']['transStatus'])) {
                if ($query_result['data']['transStatus'] == '00') {//支付成功了！
                    EdjLog::info("unionpay notify too long,but pay success order_id $order_no result=" . serialize($query_result));
                    $needCheckRedis = true;
                    if ($trans_status == 2) {
                        $needCheckRedis = false; //这是已经回调成功，由于我方系统bug弄坏了的
                    }
                    $addMoneySuccess=$this->addMoney($query_result['data'], $needCheckRedis);
		    if(!$addMoneySuccess){
			EdjLog::info('add money not success order_id is '.$item->order_id);
			continue;
		    }
		    $item=CarPayDriverOrder::model()->findByPk($item->id);
	            $item->verify = 1;
		    $item->update_time = date('Y-m-d H:i:s');
		    $item->trans_status = 2;
		    if (isset($query_result['data']['respMsg'])) {
                        $item->resp_msg_second = $query_result['data']['respMsg'];
                    }
                    $ret = $item->update();
                    $arrInvalidData[] = array('user_id' => $item->user_id, 'trans_time' => $item->trans_time,
                        'order_id' => $item->order_id, 'amount' => $item->order_amount, 'qn' => isset($query_result['data']['qn']) ? $query_result['data']['qn'] : '');
                } else if ($query_result['data']['transStatus'] == '01') {//还在处理中？这不可能！
                    EdjLog::info("unionpay cost too much time,order_id $order_no result=" . serialize($query_result));
                    continue; //这种不能更新
                } else if ($query_result['data']['transStatus'] == '03') {//支付确认失败了
                    EdjLog::info("order_id $order_no pay fail,result=" . serialize($query_result));
                    $item->verify = 1;
                    $item->update_time = date('Y-m-d H:i:s');
                    $item->trans_status = 11; //11定义为失败状态
                    if (isset($query_result['data']['respMsg'])) {
                        $item->resp_msg_second = $query_result['data']['respMsg'];
                    }
                    $ret = $item->update();
                }
            } else {//没transStatus字段，一定是失败了
                EdjLog::info("order_id $order_no not transStatus,fail,result=" . serialize($query_result));
                $item->verify = 1;
                $item->update_time = date('Y-m-d H:i:s');
                $item->trans_status = 11; //定义11为失败充值字段
                if (isset($query_result['data']['respMsg'])) {
                    $item->resp_msg_second = $query_result['data']['respMsg'];
                }
                $item->update();
            }
        }
    }

    protected function addMoney($data, $needCheckRedis) {
        unset($data['appkey']);
        unset($data['method']);
        unset($data['timestamp']);
        unset($data['ver']);
        unset($data['sig']);
        $upmpPay = new UpmpPay();
        $result = $upmpPay->notify($data);
        //版本号         version 1.0.0
        //签名方法      signMethod
        //签名信息      signature
        //交易类型      transType
        //商户代码      merId
        //交易状态      transStatus    00:交易成功结束
        //响应码             respCode
        //查询流水号     qn
        //商户订单号     orderNumber
        if ($result['succ']) {
            $qn = $result['data']['qn'];
            $order_id = $result['data']['orderNumber'];
            $resp_msg = json_encode($result['data']);
            //如果没有，就加它，返回true，否则有的话就不管它，返回false，打印log,应当用redis只用一行语句来保证
            if ($needCheckRedis && false == RPay::model()->addOrderIdIfNotExist($order_id)) {
                EdjLog::info("add order_id $order_id fail"); //should print log message here
		return false;
            } else {
                //接收支付交易回调
                if ($result['transType'] == UpmpPay::TRANS_TYPE_PUSH) {
		    $arrParam=json_decode($result['data']['reqReserved'],true);
                    //司机充值回调
                    if ($arrParam['p'] == 1) {
                        EdjLog::info("system repair driver order_id" . $order_id . "\n");
                        //更新银联交易流水//记录交易记录-司机
                        $ret = BUpmpPayOrder::model()->updateDriverOrder($order_id, $qn, $resp_msg);
			return $ret;
                    }
                    //用户充值回调
                    if ($arrParam['p'] == 2) {
                        //更新银联交易流水//记录交易记录-用户
                        EdjLog::info("system repair user order_id " . $order_id . "\n");
                        $ret = BUpmpPayOrder::model()->updateOrder($order_id, $qn, $resp_msg);
			return $ret;
                    }
                }
            }
        }
	else{
	    EdjLog::info('add money not succ result is '.serialize($result));
	    return false;
	}
    }

	private  function getEmployeeAccountByOrder($orderId, $orderTime){
		$currentMonth = date('Ym', $orderTime);//订单当月
		$beforeMonth = date('Ym',strtotime('-1 month',$orderTime));//订单上一个月
		$currentList = $this->getEmployeeAccountListByOrderIdAndMonth($orderId, $currentMonth);
		$nextList = $this->getEmployeeAccountListByOrderIdAndMonth($orderId, $beforeMonth);
		if(empty($currentList)){
			return $nextList;
		}
		if(empty($nextList)){
			return $currentList;
		}
		return array_merge($currentList, $nextList);
	}

	private  function getEmployeeAccountListByOrderIdAndMonth($orderId, $month){
		$where = 'order_id = :orderId';
		$params = array(
			':orderId'      => $orderId,
		);
		return Yii::app()->db_finance->createCommand()
			->select('*')
			->from('t_employee_account_'.$month)
			->where($where,$params)
			->order('id asc')
			->queryAll();
	}

}
