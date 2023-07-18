<?php
//这个命令做以下几个事情：
//1.发送邮件给相关人员告知pp钱包单独充值用户数，充值笔数，充值总额
//2.发送邮件给相关人员告知pp钱包单独支付用户数，支付笔数，支付总额
//3.发送邮件给相关人员告知pp钱包总体流入现金数
//暂定为发送前7天的数据
//引入银联API相关文件
Yii::import('application.vendors.upmp.*');
//引入业务逻辑model
Yii::import('application.models.pay.*');
//引入业务redis
Yii::import('application.models.redis.*');
//引入邮箱扩展
Yii::import('application.extensions.mailer.*');
//客户扩展
Yii::import('application.models.customer.*');
Yii::import('application.models.schema.customer.*');
//邮件引用
Yii::import('application.models.demo.*');
class AllpayCommand extends LoggerExtCommand{
	//生成邮件表格，$arrKeys 键值,键值顺序为显示顺序 $arrhead 表头文字，$arrData 表数据
	public function genHtmlTable($arrKeys,$arrHead,$arrData){
		$html="";
		$html.="<table border='1'>";
		$html.="<tr>";
		foreach($arrHead as $key=>$val){
			if($key == 'date') {
				$html.="<td align='center'>$val</td>";
				continue;
			}
			$html.="<td colspan=3 align='center'>$val</td>";
		}
		$html.="</tr>";
		foreach($arrData as $rowData){
			$html.="<tr>";
			foreach($arrKeys as $key){
				$html.="<td>".$rowData[$key]."</td>";
			}
			$html.="</tr>";
		}
		$html.="</table>";
		return $html;
	}
	
	public function getChannelNum($type,$type_recharge,$startCountTime,$endCountTime,&$arr,$channel_name,$usertype) {
			
 			list($sqlPayUser,$sqlPayOrder,$sqlPayAmount,$sqlRechargeUser,$sqlRechargeOrder,$sqlRechargeAmount) =array('','','','','','');
			if($usertype == 'vip') {
				$sqlPayUser=sprintf("select count(distinct(vipcard)) as vipcard  from t_vip_trade where type=%d and created>=%d and created<%d",$type,$startCountTime,$endCountTime);
				$sqlPayOrder=sprintf("select count(id) as myorder  from t_vip_trade where type=%d and created>=%d and created<%d",$type,$startCountTime,$endCountTime);
				$sqlPayAmount=sprintf("select sum(amount) as alipay_amount from t_vip_trade where type=%d and created>=%d and created<%d",$type,$startCountTime,$endCountTime);
				
				$sqlRechargeUser=sprintf("select count(distinct(vipcard)) as vipcard  from t_vip_trade where type=%d and created>=%d and created<%d",$type_recharge,$startCountTime,$endCountTime);
				$sqlRechargeOrder=sprintf("select count(id) as myorder  from t_vip_trade where type=%d and created>=%d and created<%d",$type_recharge,$startCountTime,$endCountTime);
				$sqlRechargeAmount=sprintf("select sum(amount) as alipay_amount from t_vip_trade where type=%d and created>=%d and created<%d",$type_recharge,$startCountTime,$endCountTime);
				
			}
			else if($usertype == 'customer'){
				$sqlPayUser=sprintf("select count(distinct(user_id)) as user from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",$type,$startCountTime,$endCountTime);
				$sqlPayOrder=sprintf("select count(trans_order_id) as myorder from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",$type,$startCountTime,$endCountTime);
				$sqlPayAmount=sprintf("select sum(amount) as alipay_amount from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",$type,$startCountTime,$endCountTime);

				$sqlRechargeUser=sprintf("select count(distinct(user_id)) as user from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",$type_recharge,$startCountTime,$endCountTime);
				$sqlRechargeOrder=sprintf("select count(trans_order_id) as myorder from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",$type_recharge,$startCountTime,$endCountTime);
				$sqlRechargeAmount=sprintf("select sum(amount) as alirecharge_amount from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",$type_recharge,$startCountTime,$endCountTime);
			}
			else {
				return;
			}

			//支付
            $payUser=Yii::app()->db_finance->createCommand($sqlPayUser)->queryScalar();
            $payOrder=Yii::app()->db_finance->createCommand($sqlPayOrder)->queryScalar();
			$payAmount=Yii::app()->db_finance->createCommand($sqlPayAmount)->queryScalar();

			//充值
			$rechargeUser=Yii::app()->db_finance->createCommand($sqlRechargeUser)->queryScalar();
			$rechargeOrder=Yii::app()->db_finance->createCommand($sqlRechargeOrder)->queryScalar();
			$rechargeAmount=Yii::app()->db_finance->createCommand($sqlRechargeAmount)->queryScalar();
			
			/*
			$arr = array(
				'payUser'=>$payUser,
				'payOrder'=>$payOrder,
				'payAmount'=>$payAmount,
				'rechargeUser'=>$rechargeUser,
				'rechargeOrder'=>$rechargeOrder,
				'rechargeAmount'=>$rechargeAmount
			);
			*/

			$arr[$channel_name.'_user']+=(int)$payUser;
			$arr[$channel_name.'_order']+=(int)$payOrder;
			if(isset($payAmount)) {
				$arr[$channel_name.'_amount']+=(double)$payAmount;
			}
			
			if($channel_name == 'bank' && $usertype == 'customer') {
				$sqlBankUser=sprintf("select count(distinct(user_id)) as user from t_pay_driver_order where trans_status=2 and create_time>='%s' and create_time<'%s'",$startCountTime,$endCountTime);
				$sqlBankOrder=sprintf("select count(order_id) as myorder from t_pay_driver_order where trans_status=2 and create_time>='%s' and create_time<'%s'",$startCountTime,$endCountTime);
				$sqlBankAmount=sprintf("select sum(order_amount) as alipay_amount from t_pay_driver_order where trans_status=2 and create_time>='%s' and create_time<'%s'",$startCountTime,$endCountTime);
				
				$bankUser=Yii::app()->db_finance->createCommand($sqlBankUser)->queryScalar();
	            $bankOrder=Yii::app()->db_finance->createCommand($sqlBankOrder)->queryScalar();
				$bankAmount=Yii::app()->db_finance->createCommand($sqlBankAmount)->queryScalar();
				$arr[$channel_name.'_user']+=(int)$bankUser;
				$arr[$channel_name.'_order']+=(int)$bankOrder;

				if(isset($bankAmount)) {
					
					$bankAmount = (double)($bankAmount/100.00);
					$arr[$channel_name.'_amount']+=(double)$bankAmount;
				}

				return;
			}
			
			if($channel_name != 'bank') {
				$arr[$channel_name.'_recharge_user']+=(int)$rechargeUser;
	            $arr[$channel_name.'_recharge_order']+=(int)$rechargeOrder;
				if(isset($rechargeAmount)) {
	        		$arr[$channel_name.'_recharge_amount']+=(double)$rechargeAmount;
				}
			}
			
			
	}
	
	
	public function actionSendMail($startDate,$endDate,$debug){
		//list($startUnixTime,$endUnixTime)=array(0,0);
		
		$startUnixTime=empty($startDate)?( strtotime(date('Y-m-d').' 00:00:00')-21*24*60*60 ):strtotime($startDate);
		$endUnixTime=empty($endDate)?( strtotime(date('Y-m-d').' 00:00:00')-24*60*60 ) : strtotime($endDate);

		$arrResult=array();
		for($countTime=$startUnixTime;$countTime<=$endUnixTime;$countTime+=24*60*60){
			$countDate=date('Y-m-d',$countTime);
			$arrResult[$countDate]=array();
			$arrResult[$countDate]['date']=$countDate;
			//统计时间为[)半开半闭区间 
			$startCountTime=$countTime;
			$endCountTime=$countTime+24*60*60;
			
			//银联充值
			$arrResult[$countDate]['bank_user']=0;
			$arrResult[$countDate]['bank_order']=0;
			$arrResult[$countDate]['bank_amount']=0.00;
			//支付用户数
			$arrResult[$countDate]['alipay_user']=0;
			$arrResult[$countDate]['wx_user']=0;
			$arrResult[$countDate]['pp_user']=0;
			//支付笔数
			$arrResult[$countDate]['alipay_order']=0;
			$arrResult[$countDate]['wx_order']=0;
			$arrResult[$countDate]['pp_order']=0;
			//支付总金额
			$arrResult[$countDate]['alipay_amount']=0.00;
			$arrResult[$countDate]['wx_amount']=0.00;
			$arrResult[$countDate]['pp_amount']=0.00;
			//充值用户数
			$arrResult[$countDate]['alipay_recharge_user']=0;
			$arrResult[$countDate]['wx_recharge_user']=0;
			$arrResult[$countDate]['pp_recharge_user']=0;
			//充值笔数
			$arrResult[$countDate]['alipay_recharge_order']=0;
			$arrResult[$countDate]['wx_recharge_order']=0;
			$arrResult[$countDate]['pp_recharge_order']=0;
			//充值总金额	
			$arrResult[$countDate]['alipay_recharge_amount']=0.00;
			$arrResult[$countDate]['wx_recharge_amount']=0.00;
			$arrResult[$countDate]['pp_recharge_amount']=0.00;
			
			$strCountStartTime=date('Y-m-d H:i:s',$startCountTime);
			$strCountEndTime=date('Y-m-d H:i:s',$endCountTime);

			//vip的银联
			$this->getChannelNum(0,VipTrade::TYPE_PAY,$startCountTime,$endCountTime,$arrResult[$countDate],'bank','vip');
			//普通用户的银联
			$this->getChannelNum(0,CarCustomerTrans::TRANS_TYPE_S,$strCountStartTime,$strCountEndTime,$arrResult[$countDate],'bank','customer');
			
			
			//vip的alipay
			$this->getChannelNum(VipTrade::TYPE_ALIPAY,VipTrade::TYPE_ALIPAY_RECHARGE,$startCountTime,$endCountTime,$arrResult[$countDate],'alipay','vip');
			//普通用户的alipay
			$this->getChannelNum(CarCustomerTrans::TRANS_TYPE_ALIPAY,CarCustomerTrans::TRANS_TYPE_ALIPAY_RECHARGE,$strCountStartTime,$strCountEndTime,$arrResult[$countDate],'alipay','customer');
			
			//vip的WX
			$this->getChannelNum(VipTrade::TYPE_WX_PAY,VipTrade::TYPE_WX_RECHARGE,$startCountTime,$endCountTime,$arrResult[$countDate],'wx','vip');
			//普通用户的WX
			$this->getChannelNum(CarCustomerTrans::TRANS_TYPE_WX_PAY,CarCustomerTrans::TRANS_TYPE_WX_RECHARGE,$strCountStartTime,$strCountEndTime,$arrResult[$countDate],'wx','customer');
			
			//vip的PP
			$this->getChannelNum(VipTrade::TYPE_PP_PAY,VipTrade::TYPE_PP_RECHARGE,$startCountTime,$endCountTime,$arrResult[$countDate],'pp','vip');
			//普通用户的PP
			$this->getChannelNum(CarCustomerTrans::TRANS_TYPE_PP_PAY,CarCustomerTrans::TRANS_TYPE_PP_RECHARGE,$strCountStartTime,$strCountEndTime,$arrResult[$countDate],'pp','customer');
			
			//print_r($arrResult);

			
		}
		krsort($arrResult);
		$keys=array(
			'date',
			'bank_user',
			'bank_order',
			'bank_amount',
			'alipay_user',
			'alipay_order',
			'alipay_amount',
			'alipay_recharge_user',
			'alipay_recharge_order',
			'alipay_recharge_amount',
			'wx_user',
			'wx_order',
			'wx_amount',
			'wx_recharge_user',
			'wx_recharge_order',
			'wx_recharge_amount',
			'pp_user',
			'pp_order',
			'pp_amount',
			'pp_recharge_user',
			'pp_recharge_order',
			'pp_recharge_amount'
		);
		
		$header=array(
			'date'=>'日期',
			'pay'=>'银联充值<br>用户数|单数|总额',
			'alipay_pay'=>'支付宝支付<br>用户数|单数|总额',
			'alipay_recharge'=>'支付宝充值<br>用户数|单数|总额',
			'wx_pay'=>'微信支付<br>用户数|单数|总额',
			'wx_recharge'=>'微信充值<br>用户数|单数|总额',
			'pp_pay'=>'PP钱包支付<br>用户数|单数|总额',
			'pp_recharge'=>'PP钱包充值<br>用户数|单数|总额',
			'pp_recharge'=>'PP钱包充值<br>用户数|单数|总额',
			'pp_recharge'=>'PP钱包充值<br>用户数|单数|总额'
		);
		$html=$this->genHtmlTable($keys,$header,$arrResult);

		$arrMailUser=MailConfig::model()->getMailToUsers(__CLASS__, __FUNCTION__);
			//$arrMailUser=array('liutuanwang@edaijia-inc.cn','hesongtao@edaijia-inc.cn');
		Mail::sendMail($arrMailUser,$html,'各渠道统计数据'.date('Y-m-d H:i:s'));
	}
}
