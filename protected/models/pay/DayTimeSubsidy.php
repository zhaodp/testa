<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 2/5/15
 * Time: 15:51
 */

class DayTimeSubsidy extends Settlement {

    /**  公司的补贴,司机统一周信息费补助 */
    private static $EMPLOYEE_ACCOUNT_TYPE = EmployeeAccount::TYPE_FORFEIT;
    /** 信息费充值 channel 值 */
    private $channel ;

    private $comment;

    private $cast;

    private $driver = array();

    private $order_id = 0;

    function __construct($driverId, $cityId)
    {
        $tmp = array(
            'driver_id' => $driverId,
            'city_id'   => $cityId,
        );
        $this->driver = $tmp;
    }


    /**
     * 结账,必须重写
     */
    public function settlement()
    {
        $ret = $this->orderDriver();
        if(FinanceConstants::CODE_SUCCESS == $ret){
            return true;
        }
        return false;
    }

    /**
     * 给司机补贴钱,type 使用信息费充值, channel 各不相同
     *
     * @return int
     */
    private function orderDriver(){
        $code = FinanceConstants::CODE_FAIL;
        $cast = $this->getCast();
        $employeeAccountAttributes = array();
        $employeeAccountAttributes['type'] = self::$EMPLOYEE_ACCOUNT_TYPE;
        $employeeAccountAttributes['channel'] = $this->getChannel();
        $employeeAccountAttributes['city_id'] =  $this->getDriverCityId();
        $employeeAccountAttributes['user'] = $this->getDriverId();
        $employeeAccountAttributes['order_id'] = $this->getOrderId();
        $employeeAccountAttributes['order_date'] = time();
        $employeeAccountAttributes['cast'] = $cast;
        $employeeAccountAttributes['comment'] = $this->getComment();
        $ret = $this->chargeDriver($employeeAccountAttributes);
        if($ret){
            $code = FinanceConstants::CODE_SUCCESS;
        }else{
            $message = json_encode($employeeAccountAttributes);
            $this->alarm($message);
        }
        return $code;
    }

    /**客户通过余额支付 扣除客户账户需要支付的钱并插入流水
     * @param $user_id 普通用户id
     * @param $channel  订单号
     * @param $cast 补贴金额
     * @param $orderId 客户所在城市
     * wq 2015-01-22
     */
    public  function  chargeCustomerPay($user_id,$channel,$orderId,$cast){
        $flag = false;
        $model = CarCustomerAccount::model()->getCustomerAccountInfo($user_id);
        if(empty($model)){
            return false;//账户不存在
        }
        $curr_amount = $model->amount;//账户当前金额
        $remark = '';
        if(in_array($channel,CompanyAccount::$companyChannel)){
            $remark =  '一口价洗车消费[' . $cast. ']元 操作人:system';
        }
        $customerTransAttributes = array(
            'user_id' => $user_id,
            'trans_order_id' => $orderId,
            'trans_type' => CarCustomerTrans::TRANS_TYPE_FR,//扣款
            'amount' => $cast * -1,//此处应该为负数
            'balance' => $curr_amount - $cast,//余额 = 当前金额 - 交易金额
            'source' =>CarCustomerTrans::TRANS_SOURCE_S,//系统扣除
            'remark' =>$remark,
            'operator' => 'system',
        );
        $customerAccountAttributes = array(
            'user_id' => $user_id,
            'amount' => $cast * -1,//此处应该传当前账户交易的金额*-1
        );
        $ret = $this->chargeNormal($customerTransAttributes, $customerAccountAttributes);//操作普通用户并且插入流水
        if($ret){
            $flag = true;
        }
        return $flag;
    }

    /**
     * vip客户消费扣款余额 当前为一口价洗车支付
     * @param $phone
     * @param $channel
     * @param $orderId
     * @param $cast
     * @return bool
     * 2015-01-22
     */
    public  function  chargeVipPay($phone,$channel,$orderId,$cast){
        $vipArray = VipPhone::model()->getVipInfoByPhone($phone,true);
        $_balance = $vipArray['balance'];//vip主卡或副卡对应的主卡卡余额
        $vipCard = $vipArray['vipcard'];//主卡id
        $comment = '';
        if(in_array($channel,CompanyAccount::$companyChannel)){
            $comment = "一口价洗车消费[ $cast] 元 单号:$orderId";
        }
        $vipTradeAttributes = array(
            'vipcard' => $vipCard,
            'order_id' => $orderId,
            'type' => VipTrade::TYPE_ORDER,
            'source' => VipTrade::TRANS_SOURCE_S,
            'amount' => $cast,//交易金额
            'comment' => $comment,
            'balance' => $_balance - $cast,//当前金额-消费金额
            'order_date' => time(),
        );

        $vipBalanceAttributes = array(
            'vipCard' => $vipCard,
            'delta' => $cast * -1,//此处必须传负数
        );
        $ret = $this->chargeVip($vipTradeAttributes, $vipBalanceAttributes);
        return $ret;
    }
    /**
     * 将客户支付的钱充值到对应的公司账户 当前:一口价洗车
     * @param $user_id
     * @param $channel
     * @param $order_id
     * @param $cast
     * 2015-01-22
     */
    public  function reChargeCompanyAccount($user_id,$channel,$order_id,$cast){
        $model = CompanyAccount::model()->getAccountInfoByChannel($channel);
        $isNewAccount = false;
        //step1.------检查账户是否存在,如果不存在则新建一个
        if(!$model){
            //如果不存在该渠道的公司账户则新建立一个账户
            $ret = CompanyAccount::model()->buildNewCompanyAccount($channel);
            if(!$ret){
                return false;
            }
            $isNewAccount = true;
            $model = CompanyAccount::model()->getAccountInfoByChannel($channel);
        }
        //step2.-----更新对应账户的金额
        $id = $model->id;
        $currentAmount = $model->amount;
        $companyAccountAttributes = array(
            'id' => $id,
            'amount' => $cast,//此处传当前账户交易的金额即可
        );
        $ret = CompanyAccount::model()->updateCompanyAccountBalance($companyAccountAttributes);//更新账户金额
        if(!$ret){
            return false;
        }
        //step3.---------插入对应公司账户的流水记录
        $remark = '';
        if(in_array($channel,CompanyAccount::$companyChannel)){
            $remark = "客户通过一口价洗车消费[ $cast ]元充值到该账户 操作人:system";
        }
        $companyAccountTransAttributes = array(
            'account_id' => $id,
            'user_id' => $user_id,
            'trans_order_id' => $order_id,
            'trans_type' => CompanyAccountTrans::TRANS_TYPE_CAST,//充值
            'cast' => $cast,//交易金额
            'balance' => $isNewAccount ? $cast : ($currentAmount+$cast),//如果为新账户 余额=当前交易金额,否则=账户余额+交易金额
            'operator' =>'system',
            'remark' => $remark,
            'operator' => 'system',
        );
        $add_trans = CompanyAccountTrans::model()->addCompanyAccountTrade($companyAccountTransAttributes);//插入客户交易流水到公司账户流水表
        if ($add_trans['code'] !== 0) {
            $ret = Sms::SendSMS('18301221389', "洗车客户支付更新账户余额成功后插入流水失败accountId: $id");
            EdjLog::info("--客户支付后添加流水信息失败---- " . serialize($companyAccountTransAttributes));
        }
        return true;
    }
    private function getDriverCityId(){
        $driver = $this->getDriver();
        if(!empty($driver)){
            return isset($driver['city_id']) ? $driver['city_id'] : 0;
        }
        return 0;
    }

    private function getDriverId(){
        $driver = $this->getDriver();
        if(!empty($driver)){
            return isset($driver['driver_id']) ? $driver['driver_id'] : 0;
        }
        return 0;
    }

    /**
     * @return mixed
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return array
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param array $driver
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getCast()
    {
        return $this->cast;
    }

    /**
     * @param mixed $cast
     */
    public function setCast($cast)
    {
        $this->cast = $cast;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * @param mixed $cast
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * 报警
     *
     * @param $message
     */
    protected function alarm($message){
        try {
            $title = $this->getComment();
            FinanceUtils::sendFinanceAlarm($title, $message);
        } catch (Exception $e) {
        }
    }
}