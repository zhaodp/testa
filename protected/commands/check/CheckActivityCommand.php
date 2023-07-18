<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/12/29
 * Time: 16:58
 */
Yii::import('application.models.schema.customer.*');
class CheckActivityCommand extends LoggerExtCommand
{

    public function actionRepair($userId, $amount, $debug = 1)
    {
        //1.get user trans is
        $comment = '三周年活动奖励';
        $list = $this->getDistinctUser($userId, $amount, $comment);
        //checkout trans
        $totalCount = count($list);
        $failCount  = 0;
        $successCount = 0;
        $filter = array(787732,);
        foreach($list as $item ){
            $userId = $item['user_id'];
            if(in_array($userId, $filter)){
                continue;
            }
            $status = false;
            try{
                $status = $this->checkUser($userId, $amount, $comment, $debug);
            }catch (Exception $e){

            }
            if($status){
                $successCount += 1;
            }else{
                $failCount += 1;
            }

        }
        $format = 'total|%s|success|%s|fail|%s|';
        EdjLog::info(sprintf($format, $totalCount, $successCount, $failCount));
        //user money
    }

    private function checkUser($userId, $amount, $comment, $debug){
        $list = $this->getDistinctUser($userId, 0, $comment);
        $count = count($list);
        $needTrade = false;
        if($count >  1){
            EdjLog::info('yes have multi trans --- '.json_encode(func_get_args()));
            $needTrade = true;
        }
        $this->orderUser($userId, $needTrade, $amount, $debug);
        if(!$needTrade){
            $this->verifyTradeList($list, $amount, $debug);
        }
    }

    private function verifyTradeList($list, $amount, $debug){
        $format = ' trade ---- user_id|%s|amount|%s|balance|%s|remark|%s| after balance|%s|';
        foreach($list as $item){
            $balance = $item['balance'];
            //TODO ... check 是否是大于0
            $item['balance'] = $balance - $amount;
            $log = sprintf($format, $item['user_id'], $item['amount'], $balance, $item['remark'], $item['balance']);
            if($debug){
                echo $log;
                echo "\n";
                EdjLog::info($log);
            }else{
                if($item->save()){

                }else{

                }
            }
        }
    }

    private function getDistinctUser($userId, $amount, $comment)
    {
        $criteria = new CDbCriteria();
        if(!empty($comment)){
            $criteria->compare('remark', $comment);
        }
        if($amount> 0){
            $criteria->compare('balance', 198);
        }
        if (empty($userId)) {
            $criteria->select = 'distinct user_id as user_id ';
        } else {
            $criteria->compare('user_id', $userId);
        }
        return CarCustomerTrans::model()->findAll($criteria);
    }

    private function orderUser($userId ,$needTrade, $amount , $debug = 1){
        $customer = $this->getCustomerAccount($userId);
        $customerBalance = $customer['amount'];
        $money  = $amount * -1;
        if (0 != $money) {
            $delta = $money;
            $orderId = 0;
            $customerTransAttributes = array(
                'user_id' => $userId,
                'trans_order_id' => $orderId,
                'trans_type' => 8,
                'amount' => $delta,
                'balance' => $customerBalance + $delta,
                'source' => 3,
                'remark' => '三周年活动重复充值退款',
            );
            $customerAccountAttributes = array(
                'user_id' => $userId,
                'amount' => $delta,
            );
            $code = FinanceConstants::CODE_FAIL;
            EdjLog::info('order user -----'.json_encode($customerAccountAttributes));
            if($debug){
               echo('order user -----'.json_encode($customerAccountAttributes));
                echo "\n";
            }else{
                return  $this->chargeNormal($customerTransAttributes, $customerAccountAttributes, $needTrade);
            }
        }
    }


    /**
     * 普通用户的款项
     */
    private function chargeNormal($customerTransAttributes, $customerAccountAttributes, $needTrade){
        EdjLog::info('charge normal amount customerTransAttributes::' . json_encode($customerTransAttributes)
            . '||| customerAccountAttributes' . json_encode($customerAccountAttributes));
        try {
            //1.insert customer trans
            if($needTrade){
                $add_trans = BCustomers::model()->addCustomerTrade($customerTransAttributes);
                EdjLog::info('have multi trans ---- '.json_encode($customerAccountAttributes));
                if ($add_trans['code'] !== 0) {
                    EdjLog::info("add trans fail " . serialize($customerTransAttributes));
                }
            }else{

            }
            $update_account = BCustomers::model()->updateAccount($customerAccountAttributes);
            EdjLog::info('return update_account is : '.serialize($update_account));
            return (0 == $update_account['code']);
        } catch (Exception $e) {
            EdjLog::error('update vip balance error' . $e->getMessage());
            return false;
        }
    }

    private function getCustomerAccount($userId){
        $criteria = new CDbCriteria();
        $criteria->compare('user_id', $userId);
        return CarCustomerAccount::model()->find($criteria);
    }

}



