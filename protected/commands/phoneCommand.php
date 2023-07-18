<?php
class phoneCommand extends CConsoleCommand {
	public function actionReference(){
		$content = '您还在忍受400电话排队的痛苦吗?现在就免费下载e代驾手机客户端>>http://t.cn/zjzS6M1 直接呼叫最近的司机,11月20日前下载并输入优惠码4139更能立即获得39元优惠';
		$w_path = dirname(__FILE__) .  '/../../cache/'. 'dianhua_sx.txt';
		file_put_contents($w_path,"");
		$model = new Order();
		$criteria = new CDbCriteria();
		$criteria->select = 'phone';
		$criteria->addCondition("source=1");
		$criteria->group = "phone";
		$list = $model->findAll($criteria);
		foreach ($list as $li){
			$phone = trim($li['phone']);
			if(strlen($phone) == 11 && preg_match("/^1\d{10}$/",$phone)){
				if(!$this->Contrast($li['phone'])){
					file_put_contents($w_path, $li['phone'] . "\n", FILE_APPEND);
					Sms::SendSMS($li['phone'], $content);
					echo $li['phone']."\n";
				}
			}
		}
	}
	
	public function actionSendbonus()
	{
		$content = "凛冽寒冬送神马，当然e代驾优惠券！将此短信转给没有使用过e代驾的朋友，并帮他免费下载客户端>>http://t.cn/zjzIw8F\n输入优惠码7139，他即可获得39元优惠！";
		$w_path = dirname(__FILE__) .  '/../../cache/'. 'dianhua_sms_bonus.txt';
		$log_path = dirname(__FILE__) .  '/../../cache/'. 'dianhua_sms_bonus.log';		
		$fileInfo = fopen($w_path ,"r");
		$xinxi = array();
		while(!feof($fileInfo)) {
			$line =  fgets($fileInfo);
			$phone = trim($line);
			$len = strlen($phone);
			if($len==11)
			{
				echo $phone . "\n";
				file_put_contents($log_path, $phone . "\n", FILE_APPEND);
				Sms::SendSMS($phone, $content);
			}	
		}
	}
	
	public function actionSendshunting()
	{
		$content = "全国最大代驾公司代驾费39元起?!现在就免费下载e代驾手机客户端>> http://t.cn/zjzIw8F 直接呼叫最近的司机,12月31日前下载并输入优惠码6139更能立即获得39元优惠！";
		$w_path = dirname(__FILE__) .  '/../../cache/'. 'shunting.txt';		
		$fileInfo = fopen($w_path ,"r");
		$xinxi = array();
		while(!feof($fileInfo)) {
			$line =  fgets($fileInfo);
			$phone = trim($line);
			$len = strlen($phone);
			if($len==11)
			{
				echo $phone . "\n";
				Sms::SendSMS($phone, $content);
			}	
		}
	}	
	
	//发给好评用户
	public function actionSendByCommentGood()
	{
		
		$content = "【e代驾~饭局有礼！】饭局推荐朋友下载e代驾,绑定优惠码7139和新手机号，新用户即可免费体验39元的首次代驾!（限晚10点前,10公里内.如超出,需补差额代驾费。仅限在App绑定手机号充值,出示优惠码给司机无效) 活动终止:12月1日 APP下载:http://t.cn/zjzIw8F\n";
		$w_path = dirname(__FILE__) .  '/../../cache/'. 'comment_good.txt';		
		$fileInfo = fopen($w_path ,"r");
		$xinxi = array();
		while(!feof($fileInfo)) {
			$line =  fgets($fileInfo);
			$phone = trim($line);
			$len = strlen($phone);
			if($len==11)
			{
				echo $phone . "\n";
				Sms::SendSMS($phone, $content);
			}	
		}		
		
	}
	
	
	public function Contrast($phone){
		$success = false;
		$model = new Order();
		$criteria = new CDbCriteria();
		$criteria->addCondition("phone='$phone'");	
		$criteria->addCondition("source=0");	
		$data = $model->find($criteria);
		if (!empty($data))
			$success = true;
		return $success;
	}
	
	public function actionIndex(){
		$r_path = dirname(__FILE__) .  '/../../cache/'. 'dianhua.txt';
		$w_path = dirname(__FILE__) .  '/../../cache/'. 'dianhua_sx.txt';
		$this->phoneSX($r_path, $w_path);
	}
	public function phoneSX($r_path,$w_path){
		$fileInfo = fopen($r_path ,"r");
		$xinxi = array();
		while(!feof($fileInfo)) {
			$line =  fgets($fileInfo);
			$phone = trim($line);
			$len = strlen($phone);
			if($len==11 && !in_array($phone, $xinxi)){
				array_push($xinxi, $phone);
				if($this->is_sel($phone)== false)
				{
					file_put_contents($w_path, $phone . "\n", FILE_APPEND);
					echo $phone."\n";
				}
			}
		}
	}
	public function is_sel($phone){
		$success = false;
		$model = new CallHistory();
		$criteria = new CDbCriteria();
		$criteria->addCondition("phone='$phone'");		
		$data = $model->find($criteria);
		if (!empty($data))
			$success = true;
		return $success;
	}
	
	/**
	 * 导入回访数据
	 * Enter description here ...
	 */
	public function actionCustomerPhone()
	{
		$w_path = dirname(__FILE__) .  '/../../cache/'. 'phone.txt';
		$fileInfo = fopen($w_path ,"r");
		while(!feof($fileInfo)) {
			$line =  fgets($fileInfo);
			if(trim($line)){
				$info = explode("," , $line);
				$name =trim($info[0]);
				$phone = trim($info[1]);
				if(preg_match("/^[0-9]+$/", $phone)){
					$customerVisit = new CustomerVisit();
					$customer = $customerVisit->attributes;
					$customer['name']= $name;
					$customer['phone']= $phone;
					$customerVisit->attributes = $customer;
					$customerVisit->insert();
					echo $name . "\n";
				}
			}
		}
	}
}