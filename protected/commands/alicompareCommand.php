<?php
//引入银联API相关文件
Yii::import('application.vendors.upmp.*');
//引入业务逻辑model
Yii::import('application.models.pay.*');
//引入业务redis
Yii::import('application.models.redis.*');
//引入邮箱扩展
Yii::import('application.extensions.mailer.*');
//引入支付宝API相关文件
Yii::import('application.vendors.alipay.*');
Yii::import('application.vendors.alipay.lib.*');
require_once('alipay_rsa.function.php');
//支付宝配置文件
require_once('alipay.config.php');
//这个文件主要用于支付宝的对账，当发现对账有问题时，仅仅向lidingcai,liutuanwang,dengxiaoming发送邮件
class alicompareCommand extends LoggerExtCommand{
	//对账方法，默认对前100分钟到20分钟前的账务
	//sendMail=1 forcePay=0 ,需要 forcePay 时，只能设置 forcePay为0或者1，不能为true,false之类的
	public function actionCompare($startTime,$endTime,$sendMail,$forcePay){
		$nowUnixTime=time();
		if(empty($startTime)){
			$startTime=date('YmdHis',$nowUnixTime-100*60);
		}
		if(empty($endTime)){
			$endTime=date('YmdHis',$nowUnixTime-20*60);
		}
		$bpay=new BUpmpPayOrder();
		EdjLog::info(Common::jobBegin("用户支付宝对账开始"));
		$sql=sprintf("select order_id,order_amount,trans_time from t_pay_order where (channel=5 or channel=26) and trans_status<>2 and trans_time>='%s' and trans_time<='%s'",$startTime,$endTime);
		//echo $sql."\n";
		$arrOrders=Yii::app()->db_finance->createCommand($sql)->queryAll();
		//var_dump($arrOrders);
		foreach($arrOrders as $myorder){
			$order_id=$myorder['order_id'];
			$result=$this->actionAliPay($order_id);
			//echo "order_id $order_id result is ".(int)$result."\n";
			if($result&&$sendMail){
				Mail::sendMail(array('lidingcai@edaijia-inc.cn','liutuanwang@edaijia-inc.cn','dengxiaoming@edaijia-inc.cn'),
				'报警邮件，支付宝订单号 '.$order_id.' 异常，请处理','支付宝订单 '.$order_id.' 报警');
			}
			if($result&&$forcePay){
				$this->actionForcePay($order_id);
			}
			sleep(1);					
		}
		EdjLog::info(Common::jobEnd("用户支付宝对账结束"));
	}

	public function child($node){
		foreach($node->childNodes as $item){
			//echo $item->nodeName." ".$item->nodeValue."\n\n";
			if(trim($item->nodeName)=="account_log_list"&&!empty($item->nodeValue)){
				return true;
			}
			if(!empty($item->childNodes)){
				if($this->child($item)){
					return true;
				}
			}
		}
		return false;
	}
	//向支付宝发送请求询问是否充值成功
	public function actionAlipay($order_id){
		if(empty($order_id)){
			return false;
		}
		//arrSign 中的数据必须全部为字符串
		$arrSign = array(
			//'code'=>0,
			//'message'=>'success',
			'_input_charset'=>'utf-8',
			'merchant_out_order_no'=>$order_id,
			'partner'=>'2088411863680533',
			'service'=>'account.page.query',
			//'sign_type'=>'RSA',
			//'sign'=>'',
			//'notify_url'=>urlencode($notify_url),
			//'out_trade_no'=>$order_id,
			//'subject'=>$user_id,
			//'payment_type'=>'1',//消费类型
			//'seller_id'=>'app@edaijia-inc.cn',
			//'total_fee'=>(string)round($fee/100.0,2),
			//'body'=>'edaijia',
			//'it_b_pay'=>'1d',
			//'show_url'=>'m.alipay.com',
			//'return_url'=>'m.alipay.com',
		);
		$prestr='';
		foreach($arrSign as $key=>$value){
			$prestr=$prestr."$key=$value&";
		}
		$strData='';
		foreach($arrSign as $key=>$value){
			$strData=$strData."$key=".$value."&";
		}
		$prestr=substr($prestr,0,strlen($prestr)-1);
		EdjLog::info('prestr is '.$prestr);
		$sign=rsaSign($prestr,'/etc/rsa_pkcs_private_key.pem');
		$arrSign['sign_type']='RSA';
		$arrSign['sign']=urlencode($sign);
		//$strData=$prestr.'&sign_type="RSA"&sign="'.$arrSign['sign'].'"';
		$strData=$strData."sign=".urlencode($sign)."&sign_type=RSA";
		$req_url="https://mapi.alipay.com/gateway.do?".$strData;
		$content=file_get_contents($req_url);
		//echo $content."\n";
		$xml=new DOMDocument();
		$xml->loadXML($content);
		$node=$xml->documentElement;
		$pay_result=$this->child($node);
		EdjLog::info("order_id $order_id pay_result is ".(int)$pay_result);
		return $pay_result;
	}
	//强制对某一订单号进行支付
	public function actionForcePay($order_id){
		EdjLog::info("支付宝手动强制补账 ".$order_id);
		BUpmpPayOrder::model()->updateOrder($order_id,'0','支付宝手动补账');
	}
}
?>
