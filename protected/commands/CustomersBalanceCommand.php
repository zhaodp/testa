<?php
/**
 * Created by PhpStorm.
 * User: an
 * Date: 2/12/15
 * Time: 18:04
 */
Yii::import('application.models.pay.activitySettlementImpl.*');
Yii::import('application.models.pay.orderSettlementImpl.*');
Yii::import('application.models.pay.calculator.*');
Yii::import('application.models.pay.settlement.*');
Yii::import('application.models.schema.customer.*');
Yii::import('application.models.pay.param_settle.*');
Yii::import('application.models.pay.subsidy.*');
class CustomersBalanceCommand extends LoggerExtCommand{

    private  $normalUser=0;
    private  $vipUser=1;

    private $publishUser=0;
    private $testUser=1;

    public function actioncustomersBalanceReport($testType=0){

         $date8=date('Ymd',time());
        //普通用户
        EdjLog::info("Start customers balance:".$date8);
        if($testType==1 || $testType==0) {
            //测试用户
           $testUserList = $this->getTestCustomerIdList();
            $testUserIdList=array();
            if(!empty($testUserList)){
                foreach($testUserList as $testUser){
                    $testUserIdList[]=$testUser['user_id'];
                }
            }
            //用户数据
            $normalList = $this->getCustomerAccountList();
            foreach ($normalList as $user) {
                $testUserStatus=in_array($user['user_id'],$testUserIdList)?$this->testUser:$this->publishUser;
                $this->insertCustomerBalance($this->normalUser, $user['user_id'], $user['balance'], $user['city_id'], $user['credit'],$testUserStatus, $date8);
            }
            EdjLog::info("End normal customers balance:" . $date8);
        }
        //vip 用户
        if($testType==2 || $testType==0) {
            $vipList = $this->getVipAccountList();
            foreach ($vipList as $user) {
                $this->insertCustomerBalance($this->vipUser, $user['user_id'], $user['balance'], $user['city_id'], $user['credit'],$user['test_user'], $date8);
            }
            EdjLog::info("End vip customers balance:" . $date8);
        }
    }

    private function getVipAccountList()
    {
        $sql = 'select id as user_id,balance,city_id,credit,test_user from t_vip';
        return Yii::app()->db_finance->createCommand($sql)->queryAll();
    }

    private function getTestCustomerIdList()
    {
        $sql = 'select id as user_id from t_customer_main where test_user='.$this->testUser;
        return Yii::app()->db_readonly->createCommand($sql)->queryAll();
    }

    private function getCustomerAccountList()
    {
        $sql = 'select user_id,amount as balance,city_id,credit from t_customer_account';
        return Yii::app()->db_finance->createCommand($sql)->queryAll();
    }
	
	private function insertCustomerBalance($type,$user_id,$balance,$city_id,$credit,$test_user,$date8) {
        try {
            $sql = "INSERT INTO t_customers_balance(type, source_id, balance, city_id,credit,test_user,date8,create_time) " . " VALUES(:type, :source_id, :balance, :city_id,:credit,:test_user,:date8,now()) ";
            $params = array(
                ":type" => $type,
                ":source_id" => $user_id,
                ":balance" => $balance,
                ":city_id" => $city_id,
                ":credit" => $credit,
                ":test_user" => $test_user,
                ":date8" => $date8
            );

            Yii::app()->db_finance->createCommand($sql)->execute($params);
        }catch(Exception $e){
            EdjLog::error($e->getMessage());
        }
	}

}