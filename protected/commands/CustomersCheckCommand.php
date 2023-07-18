<?php
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2/12/15
 * Time: 18:04
 *
 */
Yii::import('application.models.pay.activitySettlementImpl.*');
Yii::import('application.models.pay.orderSettlementImpl.*');
Yii::import('application.models.pay.calculator.*');
Yii::import('application.models.pay.settlement.*');
Yii::import('application.models.schema.customer.*');
Yii::import('application.models.pay.param_settle.*');
Yii::import('application.models.pay.subsidy.*');
class CustomersCheckCommand extends LoggerExtCommand{

    private  $normalUser=0;
    private  $vipUser=1;

    private $publishUser=0;
    private $testUser=1;

    private $limitBalance=100;

    public function actionCheckCustomers($testType=0){

        $yesterday=date("Ymd",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
        $yesterdayFormat=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
        $beforeYesterday=date("Ymd",mktime(0,0,0,date("m"),date("d")-2,date("Y")));
        $needEmail=false;
        $html="";
        $html.="<table>";

        $html.=$yesterday.' 用户流水与余额不配的用户：';
        //普通用户
        EdjLog::info("Start check balance:".$yesterday);
        if($testType==1 || $testType==0) {
            $normalRet = $this->getBalanceCheckList($this->normalUser,$yesterday,$beforeYesterday,$yesterdayFormat);
            if(!empty($normalRet)){
                $needEmail=true;
                $html.=$normalRet;
            }
            EdjLog::info("End normal check balance:" . $yesterday);
        }
        //vip 用户
        if($testType==2 || $testType==0) {
            $vipRet = $this->getBalanceCheckList($this->vipUser,$yesterday,$beforeYesterday,$yesterdayFormat);
            if(!empty($vipRet)){
                $needEmail=true;
                $html.=$vipRet;
            }
            EdjLog::info("End vip check balance:" . $yesterday);
        }
        //发邮件
        $html.="</table>";
        $arrMailUser=array();
        $arrMailUser=array('mengxiangan@edaijia-inc.cn');
        if($needEmail) {
            Mail::sendMail($arrMailUser, $html, '用户流水余额差异');
        }
    }

    private function checkDistinctOverStep($balance,$sum){
        $extra=abs(abs($balance)-abs($sum));
        if($extra>$this->limitBalance){
            return true;
        }
        return false;
    }

    private function getCustomersList($type,$date)
    {
        $sql = 'select source_id,balance from t_customers_balance where type='.$type.' and date8='.$date;
        return Yii::app()->db_finance->createCommand($sql)->queryAll();
    }

    private function getBalance($type,$source_id,$date)
    {
        $sql = 'select balance from t_customers_balance where type='.$type.' and source_id='.$source_id.' and date8='.$date;
        return Yii::app()->db_finance->createCommand($sql)->queryScalar();
    }

    private function getVipSum($vipId,$dateFormat)
{
    $sql = 'select sum(amount) as amount_sum from t_vip_trade where vipcard='.$vipId.' and date(from_unixtime(created))=date('.$dateFormat.')';
    return Yii::app()->db_finance->createCommand($sql)->queryScalar();
}
    private function getCustomersSum($userId,$dateFormat)
    {
        $sql = 'select sum(amount) as amount_sum from t_customer_trans where user_id='.$userId.' and date(create_time)=date('.$dateFormat.')';
        return Yii::app()->db_finance->createCommand($sql)->queryScalar();
    }

    private function getBalanceCheckList($type,$yesterday,$beforeYesterday,$yesterdayFormat){
        $html="";
        $userList = $this->getCustomersList($type,$yesterday);
        foreach ($userList as $user) {
            $beforeYestodayBalance =  $this->getBalance($type,$user['source_id'],$beforeYesterday);
            $balanceDistinct=$beforeYestodayBalance-$user['balance'];
            $amountSum=0;
            if($type==$this->normalUser) {
                $amountSum = $this->getCustomersSum($user['source_id'], $yesterdayFormat);
            }else{
                $amountSum=$this->getVipSum($user['source_id'],$yesterdayFormat);
            }
            if(empty($amountSum)){
                $amountSum=0;
            }
            $ret =  $this->checkDistinctOverStep($balanceDistinct, $amountSum);
            if($ret){
                $msg=sprintf("type=%s|user_id=%s|balance=%s|balanceDistinct=%s|sum=%s",$type,$user['source_id'],$user['balance'],$balanceDistinct,$amountSum);
                EdjLog::info("check warning:" .$msg);
                $html.="<tr>";
                $html.=$msg;
                $html.="</tr>";
            }
        }
        return $html;
    }

}