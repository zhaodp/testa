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
class PPpayCommand extends LoggerExtCommand{
	//生成邮件表格，$arrKeys 键值,键值顺序为显示顺序 $arrhead 表头文字，$arrData 表数据
	public function genHtmlTable($arrKeys,$arrHead,$arrData){
		$html="";
		$html.="<table border='1'>";
		$html.="<tr>";
		foreach($arrKeys as $key){
			$html.="<td>$arrHead[$key]</td>";
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
	public function actionSendMail($startDate,$endDate,$debug){
		$startUnixTime=0;
		$endUnixTime=0;
		if(empty($startDate)){
			$startUnixTime=strtotime(date('Y-m-d').' 00:00:00')-21*24*60*60;
		}
		else{
			$startUnixTime=strtotime($startDate);
		}
		if(empty($endDate)){
			$endUnixTime=strtotime(date('Y-m-d').' 00:00:00')-24*60*60;
		}
		else{
			$endUnixTime=strtotime($endDate);
		}
		$arrResult=array();
		for($countTime=$startUnixTime;$countTime<=$endUnixTime;$countTime+=24*60*60){
			$countDate=date('Y-m-d',$countTime);
			$arrResult[$countDate]=array();
			$arrResult[$countDate]['date']=$countDate;
			//统计时间为[)半开半闭区间 
			$startCountTime=$countTime;
			$endCountTime=$countTime+24*60*60;
			//支付用户数
			$arrResult[$countDate]['alipay_user']=0;
			//支付笔数
			$arrResult[$countDate]['alipay_order']=0;
			//支付总金额
			$arrResult[$countDate]['alipay_amount']=0.00;
			//充值用户数
			$arrResult[$countDate]['alirecharge_user']=0;
			//充值笔数
			$arrResult[$countDate]['alirecharge_order']=0;
			//充值总金额	
			$arrResult[$countDate]['alirecharge_amount']=0.00;

			//vip的pp钱包支付
			$sqlAlipayUser=sprintf("select count(distinct(vipcard)) as vipcard  from t_vip_trade where type=%d and created>=%d and created<%d",VipTrade::TYPE_PP_PAY,$startCountTime,$endCountTime);
                        $alipayUser=Yii::app()->db_finance->createCommand($sqlAlipayUser)->queryScalar();
                        $arrResult[$countDate]['alipay_user']+=$alipayUser;
			
			$sqlAlipayOrder=sprintf("select count(id) as myorder  from t_vip_trade where type=%d and created>=%d and created<%d",VipTrade::TYPE_PP_PAY,$startCountTime,$endCountTime);
                        $alipayOrder=Yii::app()->db_finance->createCommand($sqlAlipayOrder)->queryScalar();
                        $arrResult[$countDate]['alipay_order']+=$alipayOrder;
	
			$sqlAlipayAmount=sprintf("select sum(amount) as alipay_amount from t_vip_trade where type=%d and created>=%d and created<%d",VipTrade::TYPE_PP_PAY,$startCountTime,$endCountTime);
			$alipayAmount=Yii::app()->db_finance->createCommand($sqlAlipayAmount)->queryScalar();
			$arrResult[$countDate]['alipay_amount']+=(double)$alipayAmount;

			//vip的pp钱包充值
			$sqlAlirechargeUser=sprintf("select count(distinct(vipcard)) as vipcard  from t_vip_trade where type=%d and created>=%d and created<%d",VipTrade::TYPE_PP_RECHARGE,$startCountTime,$endCountTime);
                        $alirechargeUser=Yii::app()->db_finance->createCommand($sqlAlirechargeUser)->queryScalar();
                        $arrResult[$countDate]['alirecharge_user']+=$alirechargeUser;
                        $sqlAlirechargeOrder=sprintf("select count(id) as myorder  from t_vip_trade where type=%d and created>=%d and created<%d",VipTrade::TYPE_PP_RECHARGE,$startCountTime,$endCountTime);
                        $alirechargeOrder=Yii::app()->db_finance->createCommand($sqlAlirechargeOrder)->queryScalar();
                        $arrResult[$countDate]['alirecharge_order']+=$alirechargeOrder;
        
                        $sqlAlirechargeAmount=sprintf("select sum(amount) as alipay_amount from t_vip_trade where type=%d and created>=%d and created<%d",VipTrade::TYPE_PP_RECHARGE,$startCountTime,$endCountTime);
                        $alirechargeAmount=Yii::app()->db_finance->createCommand($sqlAlirechargeAmount)->queryScalar();
                        $arrResult[$countDate]['alirecharge_amount']+=(double)$alirechargeAmount;
			
			$strCountStartTime=date('Y-m-d H:i:s',$startCountTime);
			$strCountEndTime=date('Y-m-d H:i:s',$endCountTime);
		
			//普通用户的pp钱包支付
			$sqlAliPayUser=sprintf("select count(distinct(user_id)) as user from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",CarCustomerTrans::TRANS_TYPE_PP_PAY,$strCountStartTime,$strCountEndTime);
			$alipayUser=Yii::app()->db_finance->createCommand($sqlAliPayUser)->queryScalar();
			$arrResult[$countDate]['alipay_user']+=$alipayUser;
			$sqlAliPayOrder=sprintf("select count(trans_order_id) as myorder from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",CarCustomerTrans::TRANS_TYPE_PP_PAY,$strCountStartTime,$strCountEndTime);
			$alipayOrder=Yii::app()->db_finance->createCommand($sqlAliPayOrder)->queryScalar();
			$arrResult[$countDate]['alipay_order']+=$alipayOrder;
			$sqlAliPayAmount=sprintf("select sum(amount) as alipay_amount from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",CarCustomerTrans::TRANS_TYPE_PP_PAY,$strCountStartTime,$strCountEndTime);
			$alipayAmount=Yii::app()->db_finance->createCommand($sqlAliPayAmount)->queryScalar();	
			$arrResult[$countDate]['alipay_amount']+=(double)$alipayAmount;

			//普通用户的pp钱包充值
			$sqlAliRechargeUser=sprintf("select count(distinct(user_id)) as user from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",CarCustomerTrans::TRANS_TYPE_PP_RECHARGE,$strCountStartTime,$strCountEndTime);
                        $alirechargeUser=Yii::app()->db_finance->createCommand($sqlAliRechargeUser)->queryScalar();
                        $arrResult[$countDate]['alirecharge_user']+=$alirechargeUser;
                        $sqlAliRechargeOrder=sprintf("select count(trans_order_id) as myorder from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",CarCustomerTrans::TRANS_TYPE_PP_RECHARGE,$strCountStartTime,$strCountEndTime);
                        $alirechargeOrder=Yii::app()->db_finance->createCommand($sqlAliRechargeOrder)->queryScalar();
                        $arrResult[$countDate]['alirecharge_order']+=$alirechargeOrder;
                        $sqlAliRechargeAmount=sprintf("select sum(amount) as alirecharge_amount from t_customer_trans where trans_type=%d and create_time>='%s' and create_time<'%s'",CarCustomerTrans::TRANS_TYPE_PP_RECHARGE,$strCountStartTime,$strCountEndTime);
                        $alirechargeAmount=Yii::app()->db_finance->createCommand($sqlAliRechargeAmount)->queryScalar();
                        $arrResult[$countDate]['alirecharge_amount']+=(double)$alirechargeAmount;
		}
		krsort($arrResult);
		$keys=array('date','alipay_user','alipay_order','alipay_amount','alirecharge_user','alirecharge_order','alirecharge_amount');
		$header=array('date'=>'日期','alipay_user'=>'pp钱包支付用户数','alipay_order'=>'pp钱包支付单数','alipay_amount'=>'pp钱包支付总额',
		'alirecharge_user'=>'pp钱包充值用户数','alirecharge_order'=>'pp钱包充值单数','alirecharge_amount'=>'pp钱包充值总额');
		$html=$this->genHtmlTable($keys,$header,$arrResult);
		$arrMailUser=array();
			$arrMailUser=array('liutuanwang@edaijia-inc.cn');
		Mail::sendMail($arrMailUser,$html,'pp钱包统计数据'.date('Y-m-d H:i:s'));
	}
}
