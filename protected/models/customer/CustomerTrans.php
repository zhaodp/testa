<?php

/**
 *@author cuiluzhe
 * This is the model class for table "{{customer_trans}}".
 */
class CustomerTrans extends FinanceActiveRecord {
    /*
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Customer the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{customer_trans}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array ();
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array ();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array ();
    }

    /**
     * 根据手机号和处理状态获取交易流水
     * @author cuiluzhe
     */
      public  function getCustomerTransListByPhone($phone,$invoiced) {
	$rawData = $this->getCustomerTransList($phone,$invoiced);
        return new CArrayDataProvider($rawData, array(
            'pagination' => array(
               'pageSize' => 99999
            ),
        ));
    }


   public  function getCustomerTransList($customer_phone,$invoiced) {
	$invoice_date = date('m-d',time());

        $rawData_order_now = Order::model()->getInvoiceOrdersByPhone($customer_phone, 0, Order::NOW_TABLE);//普通用户最新订单交易
        $rawData_order_current_year = Order::model()->getInvoiceOrdersByPhone($customer_phone, 0, Order::CURRENT_YEAR_TABLE);//普通用户当年订单交易
	$rawData = array_merge($rawData_order_now, $rawData_order_current_year);

	if($invoice_date <= '01-31'){
            $rawData_order_pre_year = Order::model()->getInvoiceOrdersByPhone($customer_phone, 0, Order::PRE_YEAR_TABLE);//普通用户去年订单交易
	    $rawData = array_merge($rawData, $rawData_order_pre_year);
	}
	
	$customer = CustomerService::service()->getCustomerInfo($customer_phone,1);
	if($customer){
	    $rawData_customer_trans = $this->getCustomerTrans($customer->id, $invoiced);//普通账户交易
	    $rawData = array_merge($rawData, $rawData_customer_trans);

            $vip_phone = VipPhone::model()->getPrimary($customer->phone);
            if(isset($vip_phone) && $vip_phone['type'] == 1){ //如果是vip且是主卡用户
                $phone_array = VipPhone::model()->getVipCardPhoneByVipId($vip_phone['vipid']);
                $phones ="(";
                foreach($phone_array as $phone){
                        $phones .= "'".$phone['phone']."',";
                }
                $phones = substr($phones, 0, -1);
		$phones .= ')';
		$rawData_vip_trade = VipTrade::model()->getVipTrans($vip_phone['vipid'], $invoiced);//vip直充/vip app充值 pp钱包 支付宝等
                $rawData_order_now_vip = Order::model()->getInvoiceOrdersByPhone($phones, 0, Order::NOW_TABLE, true);//vip用户最新订单交易
                $rawData_order_current_year_vip = Order::model()->getInvoiceOrdersByPhone($phones, 0, Order::CURRENT_YEAR_TABLE, true);//vip用户当年订单交易
		$rawData = array_merge($rawData, $rawData_vip_trade, $rawData_order_now_vip, $rawData_order_current_year_vip);
	        if($invoice_date <= '01-31'){
                    $rawData_order_pre_year_vip = Order::model()->getInvoiceOrdersByPhone($phones, 0, Order::PRE_YEAR_TABLE, true);//vip用户去年订单交易
		    $rawData = array_merge($rawData, $rawData_order_pre_year_vip);
		}
            }
	}
	$len = count($rawData);
        for($i=1;$i<$len;$i++){
            for($j=$len-1;$j>=$i;$j--){
                if($rawData[$j]['amount']>$rawData[$j-1]['amount']){
                        $x=$rawData[$j];
                        $rawData[$j]=$rawData[$j-1];
                        $rawData[$j-1]=$x;
                }
            }
        }
	return $rawData;
    }  


	public function getCustomerTransListByUserId($customer, $invoiced)
	{
		$rawData = $this->getCustomerTransList($customer, $invoiced);
		return new CArrayDataProvider($rawData, array(
			'pagination' => array(
				'pageSize' => 99999
			),
		));
	}

    /**
     * 获取被该发票申请处理的交易流水
     * @author cuiluzhe
     */
    public  function getCustomerTransListByInvoiceId($invoiceId) {
	$rawData_customer_trans = $this->getInvoicedCustomerTrans($invoiceId);
	$rawData_order_now = Order::model()->getInvoicedOrders($invoiceId, Order::NOW_TABLE);//普通用户最新订单交易
        $rawData_order_current_year = Order::model()->getInvoicedOrders($invoiceId, Order::CURRENT_YEAR_TABLE);//普通用户当年订单交易
        $rawData_order_pre_year = Order::model()->getInvoicedOrders($invoiceId, Order::PRE_YEAR_TABLE);//普通用户去年订单交易
	$rawData_vip_trade = VipTrade::model()->getInvoicedVipTrans($invoiceId);
	$rawData = array_merge($rawData_customer_trans, $rawData_order_now, $rawData_order_current_year, $rawData_order_pre_year, $rawData_vip_trade);
        return new CArrayDataProvider($rawData,
	     array(
            	'pagination' => array(
                'pageSize' => 30
             ),
        ));
    }   	

    public function dealCustomerTrans($id,$invoiceId){
	  $db =  Yii::app()->db_finance;
	  $sql = "UPDATE t_customer_trans SET invoiced=1, invoice_id=:invoice_id where id =:id";
          $command = $db->createCommand($sql);
          $command->bindParam(":id", $id); 
          $command->bindParam(":invoice_id", $invoiceId);
          $command->execute();	
    }


    public function getCustomerTrans($user_id,$invoiced){
	  $sql = "select id,create_time,trans_order_id as order_id,amount,trans_type, 1 as table_name, 1 as booking_year from t_customer_trans where user_id= :user_id and invoiced=:invoiced and ((source=1  and trans_type=2) or (source=5  and trans_type=10) or (source=28  and trans_type=28) or (source=4  and trans_type=9) or (source=29  and trans_type=29)  or (source=65  and trans_type=65)  or (source=66  and trans_type=66)) and amount>0";
       /* $invoice_date = date('m-d', time());
        if ($invoice_date <= '01-31') {*/
            $last_year = date('Y-01-01 00:00:00', strtotime('-1 years'));
            $sql .= " and create_time>='" . $last_year . "'";
        /*} else {
            $last_year = date('Y-01-01 00:00:00', time());
            $sql .= " and create_time>='" . $last_year . "'";
        }*/
        $trans = Yii::app()->db_finance->createCommand($sql)->queryAll(true, array(':user_id' => $user_id, ':invoiced' => $invoiced));
        return $trans;
    }
	
    public function getInvoicedCustomerTrans($invoiceId){
	  $sql = 'select id,create_time,trans_order_id as order_id,amount,trans_type,1 as table_name,1 as booking_year  from t_customer_trans where invoice_id= :invoice_id';
          $trans =  Yii::app()->db_finance->createCommand($sql)->queryAll(true, array(':invoice_id'=>$invoiceId));
          return $trans;
    }
	
    /**
     *获取一段时间内的银联 支付宝 pp钱包充值记录
    **/
    public function getRechargeList($begin_time, $end_time){
	$sql = 'select * from t_customer_trans where ((source=1  and trans_type=2) or (source=5  and trans_type=10) or (source=28  and trans_type=28)) 
		and amount>0 and create_time>=:begin_time and create_time<=:end_time';
        $trans =  Yii::app()->db_finance->createCommand($sql)->queryAll(true, array(':begin_time'=>$begin_time, ':end_time'=>$end_time));
        return $trans;
    }
}
