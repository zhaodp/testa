<?php

Yii::import('application.vendors.phpexcel.*');
Yii::import('application.models.schema.pay.*');
//引入邮箱扩展
Yii::import('application.extensions.mailer.*');
class CheckCommand extends LoggerExtCommand {

	const DB_HAVE_NO		= 1;
	const EXCEL_HAVE_NO		= 2;
	const EQUEAL			= 3;
	const NOT_EQUEAL		= 4;

    public function actionWorker($qname='default') {
		echo "is ok?";
    }
	//手动检查
	public function actionManualCheck($dateStart, $dateEnd){

		ini_set('memory_limit', '-1');
		$html = "<table  border='1'> ";
		$html .= "<tr> <td>日期</td><td>充值总额</td><td>用户充值总额</td><td>司机充值总额</td><td>测试充值额</td><td>测试司机充值额</td><td>测试用户充值额</td><td>手续费</td><td>实收金额</td> </tr>";
		$arr = $this->getFromDb($dateStart, $dateEnd);
		echo "arrcount ".count($arr);
		$sums = $this->genMailContentMuilt($arr);
		$index = 1 ;
		$count = count($sums);

		while ( $index <= $count){
			$item = $sums[$index];
			#echo json_encode($item);
			$index += 1;
			$date		= $item["date"];
			$sumIn		= $item["sumIn"];
			$customerIn	= $item["customerIn"];
			$driverIn	= $item["driverIn"];
			$sumTest	= $item["sumTest"];
			$testDriverIn=$item["testDriverIn"];
			$testCustomerIn=$item["testCustomerIn"];
			$sumFee		= $item["sumFee"];
			$sumBalance	= $item["sumBalance"];
			$html .= "<tr> <td>$date</td><td>$sumIn</td><td>$customerIn</td><td>$driverIn</td><td>$sumTest</td><td>$testDriverIn</td><td>$testCustomerIn</td><td>$sumFee</td><td>        $sumBalance</td></tr>";
		}
		 $html .= "</table>";
		 Mail::sendMail(array("liutuanwang@edaijia-inc.cn"), $html, "银联对账每日邮件");
	}
	public function genMailContentMuilt($arr){
		$nextDate = "";

		$driverIn			= 0.00;
		$customerIn			= 0.00;
		$testDriverIn		= 0.00;
		$testCustomerIn		= 0.00;
		$sumIn				= 0.00;
		$sumFee				= 0.00;
		$sumBalance			= 0.00;
		//
		$driverTmp		= 0.00;
		$customerTmp	= 0.00;
		$sumTest		= 0.00;


		$ret = array();
		$index = 0;
		$i = 0;
		$count = count($arr);
		while( $index < $count){
#		foreach($arr as $item){
			$item = $arr[$index];
			$date = $item["clearing_date"];

			if($nextDate == $date){
				$in				=  $item["income"];
				//
				$sumIn			+= $in;
				$sumFee			+= $item["fee"];
				$sumBalance		+= $item["balance"];
				//
				if($item["isDriver"] == 1){
					$driverTmp	= $in;
					$customerTmp= 0.00;
				}else {
					$customerTmp = $in;
					$driverTmp	= 0.00;
				}
	
				if($item["isTest"] == 1 ){
					$testDriverIn	+= $driverTmp;
					$testCustomerIn	+= $customerTmp;
					$sumTest		+= $in;
				} else {
					$driverIn		+= $driverTmp;
					$customerIn		+= $customerTmp;
				}
				$index += 1;
	
			} else {
				if( $i != 0){
				$ret[$i] = array(
					"date"			=> $nextDate,
					"sumIn"			=> $sumIn,
					"customerIn"	=> $customerIn,
					"driverIn"		=> $driverIn,
					"sumTest"		=> $sumTest,
					"testDriverIn"	=> $testDriverIn,
					"testCustomerIn"=> $testCustomerIn,
					"sumFee"		=> $sumFee,
					"sumBalance"	=> $sumBalance,
				);
}
				$driverIn			= 0.00;
				$customerIn			= 0.00;
				$testDriverIn		= 0.00;
				$testCustomerIn		= 0.00;
				$sumIn				= 0.00;
				$sumFee				= 0.00;
				$sumBalance			= 0.00;
				//
				$driverTmp		= 0.00;
				$customerTmp	= 0.00;
				$sumTest		= 0.00;
				$i  += 1;
				$nextDate = $date;
			}
			if( $index == $count - 1 ){
				$ret[$i] = array(
					"date"			=> $nextDate,
					"sumIn"			=> $sumIn,
					"customerIn"	=> $customerIn,
					"driverIn"		=> $driverIn,
					"sumTest"		=> $sumTest,
					"testDriverIn"	=> $testDriverIn,
					"testCustomerIn"=> $testCustomerIn,
					"sumFee"		=> $sumFee,
					"sumBalance"	=> $sumBalance,
				);
			}
		
		}
		return $ret;
	}
	public function getFromDb($dateStart, $dateEnd){
		$sql = "select * from t_excel_order where clearing_date  between " 
		."'".$dateStart."'"
		." and  "
		."'".$dateEnd."'"
		." order by clearing_date asc ";
		$command = Yii::app()->db->createCommand($sql);
		$arr  = $command->queryAll();
		
		return $arr;
	}


	public function actionReport($dateStart, $dateEnd){
		$sql = "select * from t_excel_order where clearing_date  between " 
		."'".$dateStart."'"
		." and  "
		."'".$dateEnd."'";
		$command = Yii::app()->db->createCommand($sql);
		$arr  = $command->queryAll();
		$mailContent =	$this->getMailContent($arr);	
		echo "xxx".$mailContent."\n";
		Mail::sendMail(array("liutuanwang@edaijia-inc.cn"), $mailContent, "test");
	
	}

	public function actionRead( $excel){
		ini_set('memory_limit', '-1');
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($excel);
		$objPHPExcel->setActiveSheetIndex(0);
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$this->excelToDb($sheetData);
	}

	/**
	 * 用来读取银联给的 RD的文件
	 *
	 * @param $fileName
	 * @param $clearDate
	 */
	public function actionReadRD($fileName, $clearDate=''){
		if(empty($clearDate)){
			$clearDate = date('Y-m-d', strtotime('-1 day'));
		}else{
			$clearDate = date('Y-m-d', strtotime($clearDate));
		}
		$year = date("Y");
		$content 	= file_get_contents($fileName);
		$contentArr = preg_split('/[\r\n]+/', $content);
		$dbItems	= array();
		foreach($contentArr as $item){
			$tmp = array();
			$item = trim($item);
			if(empty($item)){
				continue;
			}
			$arr = preg_split('/[\s]+/', $item);
			try{
				$tradeTime = strtotime($year.$arr[1]);
				//为了和 excel 读取兼容,就用字母做索引
				$tmp['N']=$arr[11];
				$tmp['I']=$arr[4];
				$tmp['J']=$arr[5] * -1;
				$tmp['K']=$arr[6];
				$tmp['H']=$arr[2];
				$tmp['E']=$clearDate;
				$tmp['F']=date('Y-m-d', $tradeTime);
				$tmp['G']=date('His', $tradeTime);
				$tmp['M']=$arr[8];
			}catch (Exception $e){
				continue;
			}
			$dbItems[]= $tmp;
		}
		if(empty($dbItems) || count($item) == 0){
			return;
		}
		$this->excelToDb($dbItems);
	}
	

	public function getMailContent($arr){
		$html = "<b> test </b>"."<br>";
		$html .= "<table  border='1'> ";
		$html .= "<tr> <td>日期</td><td>充值总额</td><td>用户充值总额</td><td>司机充值总额</td><td>测试充值额</td><td>测试司机充值额</td><td>测试用户充值额</td><td>手续费</td><td>实收金额</td> </tr>";
		$driverIn			= 0.00;
		$customerIn			= 0.00;
		$testDriverIn		= 0.00;
		$testCustomerIn		= 0.00;
		$sumIn				= 0.00;
		$sumFee				= 0.00;
		$sumBalance			= 0.00;
		$date				= "";
		foreach ($arr as $item) {
			$in				=  $item["income"];
			//
			$sumIn			+= $in;
			$sumFee			+= $item["fee"];
			$sumBalance		+= $item["balance"];
			//
			$driverTmp		= 0.00;
			$customerTmp	= 0.00;
			$sumTest		= 0.00;
			if($item["isDriver"] == 1){
				$driverTmp	= $in;
			}else {
				$customerTmp = $in;
			}

			if($item["isTest"] == 1 ){
				$testDriverIn	+= $driverTmp;
				$testCustomerIn	+= $customerTmp;
				$sumTest		+= $in;
			} else {
				$driverIn		+= $driverTmp;
				$customerIn		+= $customerTmp;
			}
			$date				= $item["clearing_date"];
			
		}
		$html .= "<tr> <td>$date</td><td>$sumIn</td><td>$customerIn</td><td>$driverIn</td><td>$sumTest</td><td>$testDriverIn</td><td>$testCustomerIn</td><td>$sumFee</td><td>$sumBalance</td>";	
		$html .= "</table>";
		return $html;
	
	}


	public function excelToDb($excelArray){
		$i = 0;
		foreach($excelArray as $item){
//			从 excel 表格里面读取需要过滤掉第一行
//			if($i == 0){
//				$i += 1;
//				continue;
//			}

			$ret = $this->checkItem($item);
			if($ret["code"] == 1){
				$status		= self::DB_HAVE_NO;
				$dbCount	= -1;
				$isDriver	= -1;
				$isTest		= -1;
                $userId     = -1;
			}else{
				if( $ret["equal"] == 1 ){
					$status = self::EQUEAL; 
				}else {
					$status = self::NOT_EQUEAL; 
				}
				$dbCount	= $ret["dbCount"];
				$isDriver	= $ret["isDriver"];
				$isTest		= $ret["isTest"];
                $userId     = $ret["user_id"];
			}
			$dbColls  = array(
				'order_id'		=> $item['N'], 
				'income'		=> $item['I'],
				'fee'			=> $item['J'], 
				'balance'		=> $item['K'],
				'bank_card'		=> $item['H'], 
				'clearing_date' => $item['E'], 
				'trade_date'	=> $item['F'],
				'trade_time'	=> $item['G'],
				'trace_id'		=> $item['M'], 
				'check_status'	=> $status, 
				'db_count'		=> $dbCount,
				'isDriver'		=> $isDriver,
				'isTest'		=> $isTest,
                'user_id'       => $userId,
			);
			try{
				Yii::app()->db->createCommand()->insert('t_excel_order', $dbColls);
			}catch (Exception $e){
				continue;
			}
		}
	}
	public function checkItem($item){
		$orderId = $item['N'];
		$order	 = $this->getOrderFromDb($orderId, $item["H"]); 
		if( !empty($order) ){
			$money = $order["order_amount"] / 100;
			$equal = ($money == $item['I']);
			$ret = array(
				"code"			=> 0,
				"isDriver"		=> $order["isDriver"],
				"isTest"		=> $order["isTest"],
				"equal"			=> $equal,
				"dbCount"		=> $money,
                "user_id"       => $order["user_id"],
			);
		} else {
			$ret = array(
				"code" => 1,
			);
		}
		return $ret;
	}
	public function getOrderFromDb($orderId,$bank_card){
		$sql = "select * from t_pay_driver_order where order_id = '" . $orderId . "'" ;
		$command = Yii::app()->db_finance->createCommand($sql);
		$payDriverOrder = $command->queryAll();
		$test = $this->isTestCard($bank_card);
		if(!empty($payDriverOrder)){
			$tmp =  $payDriverOrder[0];
			//是司机充值
			$tmp["isDriver"] = 1;
			//是否是测试 先默认是false 0
			$tmp["isTest"] = $test;
			return $tmp;
		}else{
			$sql = "select * from t_pay_order where order_id = '" . $orderId . "'" ;
			$command = Yii::app()->db_finance->createCommand($sql);
			$payCustomerOrder = $command->queryAll();
			if(!empty($payCustomerOrder)){
				$tmp = $payCustomerOrder[0];
				$tmp["isDriver"] = 0;
				//是否是测试 先默认是false 0
				$tmp["isTest"] = $test;
				return $tmp;
			
			} else {
				return array();
			}
		}

	}
	public function isTestCard($cardId){
		$tmp = substr($cardId, -4);
		$tmp1 = substr($cardId, 2, 4);
		$sql = "select * from t_test_card";
		$command = Yii::app()->db->createCommand($sql);
		$ids = $command->queryAll();
		foreach($ids as $id){
			// todo
			if( substr($id["card_id"], -4 ) == $tmp && substr($id["card_id"], 4 ) == $tmp1){
				return 1;
			}
		}
		return  0 ;
	}
}
