<?php

/**
 * This is the model class for table "{{trade_order}}".
 *
 * The followings are the available columns in table '{{trade_order}}':
 * @property integer $id
 * @property string $phone
 * @property integer $channel
 * @property string $fee
 * @property string $trade_no
 * @property string $out_trade_no
 * @property integer $billamount
 * @property integer $ordertime
 * @property integer $status
 * @property string $finished
 * @property string $created
 */
class TradeOrder extends CActiveRecord
{
	/**
	 * 支付待确认
	 */
	const ORDERCREATE = 0;
	/**
	 * 支付完成状态
	 */
	const ORDERFINISHED = 1;
	
	/**
	 * 渠道银联
	 */
	const CHANNELUNIONPAY = 0;
	/**
	 * 渠道支付宝
	 */
	const CHANNELALIPAY = 1;
	/**
	 * 渠道其他
	 */
	const CHANNELOTHER = 2;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TradeOrder the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{trade_order}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('phone, channel, fee, trade_no, created', 'required'),
            array('channel, billamount, ordertime, status', 'numerical', 'integerOnly'=>true),
            array('phone, trade_no, out_trade_no', 'length', 'max'=>32),
            array('fee', 'length', 'max'=>10),
            array('finished', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, phone, channel, fee, trade_no, out_trade_no, billamount, ordertime, status, finished, created', 'safe', 'on'=>'search'),
        );
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
        return array(
            'id' => 'ID',
            'phone' => 'Phone',
            'channel' => 'Channel',
            'fee' => 'Fee',
            'trade_no' => 'Trade No',
            'out_trade_no' => 'Out Trade No',
            'billamount' => 'Billamount',
            'ordertime' => 'Ordertime',
            'status' => 'Status',
            'finished' => 'Finished',
            'created' => 'Created',
        );
	}

    /**
     * 生成充值订单
     * @param $trans_orderNo  本地订单号
     * @param $phone        充值手机号
     * @param $fee          充值金额
     * @param $transactionId   交易流水号
     * @param $orderTime    交易时间
     * @param int $channel  充值通道
     * @param int $status   订单状态
     * @return TradeOrder
     * @author bidong 20130424
     */
    public function orderInsert($phone, $fee,$transactionId,$orderTime,$orderNo,$channel = 0, $status = 0){

        if(isset($phone)  && isset($fee) && isset($transactionId)){
            $customer = CustomerService::service()->getCustomerInfo($phone,1);
            if($customer){
                $tradeOrder = new TradeOrder();
                $attributes = array(
                    'phone' =>$phone,
                    'channel' => $channel,
                    'fee' => $fee,
                    'trade_no' => $orderNo,
                    'out_trade_no' =>$transactionId,
                    'ordertime' => $orderTime,
                    'status' => $status,
                    'created'=>date('Y-m-d H:i:s')
                );
                $tradeOrder->attributes = $attributes;
                return $tradeOrder->insert();
            }else{
                return false;
            }
        }
    }

    /**
     * 生成充值订单号
     * @return string tradeNo  年月日小时分秒+4位毫秒 例：201304281552023075
     * @author bidong 20130424
     */
    public function createOrderNo(){
        $usec=$sec='';
        list($usec, $sec) = explode(".", microtime(true));
        $tradeNo = date('YmdHis') .$sec;
        return $tradeNo;
    }

	public function getOrderByAttr($phone, $fee, $channel = 0, $status = 0){
		$customer = CustomerService::service()->getCustomerInfo($phone,1);
		$customer_id = $customer->id;
		$tradeOrder = self::model()->find(
			'phone=:phone and customer_id=:customer_id and fee=:fee and status=:status',
				array(
					':phone'=>$phone,
					':customer_id'=>$customer_id,
					':fee'=>$fee,
					':status'=>$status,
				)
				);
		if(!$tradeOrder){
			$tradeOrder = new TradeOrder();
			$tradeNo = date('Ymdhis') . rand(10000, 99999);
			if (self::model()->count('trade_no=:trade_no', array(':trade_no'=>$tradeNo)) > 0){
				$tradeNo = date('Ymdhis') . rand(10000, 99999);
			}
			$attributes = array(
				'phone'=>$phone,
				'customer_id'=>$customer->id,
				'fee'=>$fee,
				'channel'=>$channel,
				'trade_no'=>$tradeNo,
				'status'=>$status,
				'out_trade_no'=>'',
				'created'=>time(),	
			);
			
			$tradeOrder->attributes = $attributes;
			
			$tradeOrder->insert();
		}
		return $tradeOrder;
	}

    /**
     * 根据订单号获取订单信息
     * @author bidong 20130503
     * @param $tradeNo
     * @return CActiveRecord|null
     */
    public function getOrderByNo($tradeNo){
		$tradeOrder = self::model()->find('trade_no=:trade_no',array(':trade_no'=>$tradeNo));
		if($tradeOrder){
			return $tradeOrder;
		} else {
			return null;
		}
	}


    /**
     * 更新支付订单状态（是否支付成功,银联回调专用）
     * @author bidong 20130424
     * @param $tradeNo  订单号
     * @param $outTradeNo   交易流水号
     * @param $fee      订单金额
     * @param $status  支付状态 0.待确认  1.支付成功
     * @param $channel  支付渠道
     * @return bool
     */
    public function updateOrderStatus($tradeNo,$outTradeNo,$fee,$status,$channel){
        $tradeOrder = self::getOrderByNo($tradeNo);
        if ($tradeOrder){
            if ($tradeOrder->fee == $fee){
                $tradeOrder_attr = array(
                    'status'=>$status,
                    'out_trade_no'=>$outTradeNo,
                    'trade_no'=>$tradeNo,
                    'channel'=>$channel,
                    'finished'=>date('Y-m-d h:i:s')
                );
                $tradeOrder->attributes = $tradeOrder_attr;

               if( $tradeOrder->update()){
                   //确认支付成功，修改用户账户总额
                   $customer = CustomerService::service()->getCustomerInfo($tradeOrder->phone,1);
                   if ($customer){
                       $customerAccount = new CustomerAccount();
                       switch ($tradeOrder->channel){
                           case TradeOrder::CHANNELALIPAY:
                               $type = CustomerAccount::TYPEALIPAY;
                               $comment = '支付宝充值';
                               break;
                           case TradeOrder::CHANNELUNIONPAY:
                               $type = CustomerAccount::TYPEUNIONPAY;
                               $comment = '银联充值';
                               break;
                       }
                       $customer_id = $customer->id;
                       $customerAccount_attr = array(
                           'order_id'=>$tradeNo,
                           'action_type'=>$type,
                           'customer_id'=>$customer_id,
                           'amount'=>$fee,
                           'remark'=>$comment,
                           'operator'=>'system',
                           'order_type'=>'1',
                           'create_time'=>date('Y-m-d h:i:s')
                       );
                       $customerAccount->attributes = $customerAccount_attr;
                       $ret= $customerAccount->insert();

                       //用户流水表插入成功，更新主用户表
                       if ($ret){
                           $new_amount = $customer->amount + $fee;
                           $attr = array('amount'=>$new_amount,'update_time'=>date('Y-m-d h:i:s'));
                           $customer->attributes = $attr;
                           if ($customer->update()){
                               return true;
                           }
                       }
                   }
               }else{
                   return false;
               }
            }
        }
    }

	public function updateOrderByNo(
					$outTradeNo, 
					$tradeNo, 
					$fee, 
					$accountNumber = '', 
					$transSerialNumber = '', 
					$billAmount = 0,
					$settleDate = '',
					$transmitTime = ''){
		$tradeOrder = self::getOrderByNo($tradeNo);
    	
    	if ($tradeOrder){
    		if ($tradeOrder->fee == $fee){
    			if ($tradeOrder->status == TradeOrder::ORDERCREATE){
    				
    				$tradeOrder_attr = array(
    						'status'=>TradeOrder::ORDERFINISHED,
    						'out_trade_no'=>$outTradeNo,
    						'accountNumber1' => $accountNumber,
				            'transSerialNumber' => $transSerialNumber,
				            'billAmount' => $billAmount,
				            'settleDate' => $settleDate,
				            'transmitTime' => $transmitTime,
    						'finished'=>time(),
    				); 
    				
    				$tradeOrder->attributes = $tradeOrder_attr;
    				
    				if ($tradeOrder->update()){
    					$customer = CustomerMain::model()->getCustomerByAttr($tradeOrder->customer_id, $tradeOrder->phone);
    					if ($customer){
    						$customerAccount = new CustomerAccount();
    						//type, customer_id, cast, comment, created
    						switch ($tradeOrder->channel){
    							case TradeOrder::CHANNELALIPAY:
    								$type = CustomerAccount::TYPEALIPAY;
    								$comment = '支付宝充值';
    								break;
    							case TradeOrder::CHANNELUNIONPAY:
    								$type = CustomerAccount::TYPEUNIONPAY;
    								$comment = '银联充值';
    								break;
    						}
    						$customer_id = $customer->id;
    						$customerAccount_attr = array(
    									'type'=>$type,
    									'customer_id'=>$customer_id,
    									'cast'=>$fee,
    									'comment'=>$comment,
    									'created'=>time(),
    							);
    						$customerAccount->attributes = $customerAccount_attr;
    						if ($customerAccount->insert()){
	    						$balance = $customer->balance + $fee;
	    						$attr = array('balance'=>$balance);
	    						
	    						$customer->attributes = $attr;
	    						
	    						if ($customer->update()){
	    							return true;
	    						}
    						}
    					}
    				}
    			}
    		}
    	}
    	
    	return FALSE;
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('channel',$this->channel);
        $criteria->compare('fee',$this->fee,true);
        $criteria->compare('trade_no',$this->trade_no,true);
        $criteria->compare('out_trade_no',$this->out_trade_no,true);
        $criteria->compare('billamount',$this->billamount);
        $criteria->compare('ordertime',$this->ordertime);
        $criteria->compare('status',$this->status);
        $criteria->compare('finished',$this->finished,true);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
	}
}