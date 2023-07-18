<?php
/**
 * 用户发票增加
 * @author zhangtongkai 2014-04-28
 * @see
 * @since
 */
$token = isset($params['token'])?$params['token']:'';


$client_amount = isset($params['client_amount'])?$params['client_amount']:0;//客户端传入的开票金额
$client_remark = isset($params['client_remark'])?$params['client_remark']:'';//客户端备注

$minamount = 19;//服务端设置最小开票金额

if($client_amount<0){
   $ret = array('code'=>3,'message'=>'请输入正确的开票金额');
   echo json_encode($ret);return;
}else if($client_amount>0 && $client_amount<$minamount){
   $ret = array('code'=>3,'message'=>'最小开票金额为19元');
   echo json_encode($ret);return;
}

if(!empty($token)){
    $validate = CustomerToken::model()->validateToken($token);
    $phone = $validate['phone'];
    if ($validate){
      if($client_amount>0){
	//获取最大开票金额
        $transList   = CustomerTrans::model()->getCustomerTransList($phone, 0);
        $totalAmount = 0;
        if($transList){
            foreach($transList as $trans){
                $amount = $trans['amount'];
                $totalAmount = $totalAmount+$amount;
            } 
        }else{
	    $ret = array('code'=>3,'message'=>'对不起，您不存在交易记录');
            echo json_encode($ret);return;
	}  
	if($client_amount > $totalAmount){
	    $ret = array('code'=>3,'message'=>'您的可开发票总金额小于输入金额,请重新输入');
            echo json_encode($ret);return;
	}
       }

        $time=time();
        $condition='';$values=array();
        $invoice['customer_phone'] = $phone;


        $invoice['title'] = isset($params['invoice_title'])?$params['invoice_title']:'';
        $invoice['content'] = isset($params['invoice_content'])?$params['invoice_content']:'';
        $invoice['contact'] = isset($params['invoice_contact'])?$params['invoice_contact']:'';
        $invoice['telephone'] = isset($params['invoice_telephone'])?$params['invoice_telephone']:'';
        $invoice['address'] = isset($params['invoice_address'])?$params['invoice_address']:'';
        $invoice['zipcode'] = isset($params['invoice_zipcode'])?$params['invoice_zipcode']:'';
        $invoice['status'] = isset($params['status'])?$params['status']:'1';
        $invoice['isdeal'] = 0;//0-未处理
        $invoice['description'] = isset($params['invoice_description'])?$params['invoice_description']:'';
	$invoice['type'] = isset($params['type'])?$params['type']:'3';//3为待定 兼容老客户端
	$invoice['pay_type'] = isset($params['pay_type'])?$params['pay_type']:'3';//3为待定 兼容老客户端
	$invoice['wealth'] = isset($params['wealth'])?$params['wealth']:'0';
        $invoice['created'] = $time;
        $invoice['updatetime'] = $time;
	
	if($invoice['pay_type'] == '1'){//e币支付
		$ret = CustomerMain::model()->payForWealth($invoice['customer_phone'],$invoice['wealth']);
		if($ret['state'] == 1){
			$ret = array(
                		'code'=>3,
                		'message'=>'剩余e币不足');
			echo json_encode($ret);return;
		}
	}
	
        $condition.='customer_phone=:customer_phone';
        $condition.=' and title=:title';
        //$condition.=' and content=:content';
        $condition.=' and contact=:contact';
        $condition.=' and telephone=:telephone';
        $condition.=' and address=:address';
        //$condition.=' and zipcode=:zipcode';
        //$condition.=' and description=:description';
	//$condition.=' and type=:type';
	//$condition.=' and pay_type=:pay_type';

        $values[':customer_phone']=$invoice['customer_phone'];
        $values[':title']=$invoice['title'];
        //$values[':content']=$invoice['content'];
        $values[':contact']=$invoice['contact'];
        $values[':telephone']=$invoice['telephone'];
        $values[':address']=$invoice['address'];
        //$values[':zipcode']=$invoice['zipcode'];
        //$values[':description']=$invoice['description'];
	//$values[':type']=$invoice['type'];
	//$values[':pay_type']=$invoice['pay_type'];
	
	$exists = CustomerInvoice::model()->exists($condition, $values);

        $customerInvoice = new CustomerInvoice();
        $customerInvoice->attributes=$invoice;
	if($exists){
	    $customerInvoice->times = 1;//非首次开票
	}else{
	    $customerInvoice->times = 0;//首次开票
	}
	$customerInvoice->client_amount = $client_amount;
	$customerInvoice->remark = $client_remark;//20141120将客户备注和客服备注合并	   


        $flag = $customerInvoice->insert();
        if($flag){
	  $invoiceId = $customerInvoice->attributes['id'];
   	  if($client_amount>0){
	    //处理订单、充值等
	       //$invoiceId = $customerInvoice->attributes['id'];
	       if($client_amount == $totalAmount){//全开 
		    foreach($transList as $trans){
			$id   = $trans['id'];
                        $type = $trans['table_name'];
                        $t_order_table_num = $trans['booking_year'];//区分t_order表名字
			if($type == '1'){////customer_trans
			    CustomerTrans::model()->dealCustomerTrans($id,$invoiceId);
			}else if($type == '2'){
			    OrderExt::model()->dealOrderExt($id,$invoiceId);
			}else if($type == '3'){//vip_trade
                            VipTrade::model()->dealVipTrade($id,$invoiceId);
                        }
		    }
		    CustomerInvoice::model()->dealCustomerInvoiceSetAmount($invoiceId,$totalAmount);
               }else{//只开部分
		   $currentAmount = 0;
		   $currentTrans;
		   $used_trans = array();
		   $i = 0;
		   $first;//记录最后一个遍历过且累加和大于客户端参数的节点
		  //example client_amount:3000....data:4000 | 2000 500  400 | 300 200 50
		   foreach($transList as $trans){

			if($trans['amount']>$client_amount){
			    $currentTrans = $trans;//记录遍历过的最后一个比客户端传入值大的节点
			    continue;
			}else if($trans['amount'] == $client_amount){ //如果正好找到相等的则直接返回
			    $used_trans[0] = $trans;
			    $currentTrans = $trans;
			    break;
			}else{
			    if($currentAmount<$client_amount){//若累积和小于客户端参数
				if(($currentAmount+$trans['amount'])>$client_amount){
                                    $first = $trans;
				    continue;
				}else if(($currentAmount+$trans['amount']) == $client_amount){//20141015
				    $currentAmount += $trans['amount'];
                                    $used_trans[$i++] = $trans;
				    break;
				}else{
				    if(isset($first)){
					break;
				    }
				    $currentAmount += $trans['amount'];
                                    $used_trans[$i++] = $trans;
				}
                            }else{//累积和大于等于客户端参数 停止
				break;
			    }
			}
                   }
		   
		   if(empty($used_trans)){//说明后面元素的和小于客户端参数
			$used_trans[0] = $currentTrans;
		   }else{
			
                       /** if($currentAmount<$client_amount && isset($first)){//20140115
                            $currentAmount += $first['amount'];
                            $used_trans[$i++] = $first;
                        }**/
			if($currentAmount<$client_amount){
			    if(!isset($first)){
				$used_trans = array();//20141125
			        $used_trans[0] = $currentTrans;
			    }else{
				$currentAmount += $first['amount'];
                                $used_trans[$i++] = $first;
			    }
			}
		   }
			
		   $total_amount = 0;
		   foreach($used_trans as $trans){
			$total_amount += $trans['amount'];
                        $id   = $trans['id'];
                        $type = $trans['table_name'];
                        $t_order_table_num = $trans['booking_year'];//区分t_order表名字
                        if($type == '1'){////customer_trans
                            CustomerTrans::model()->dealCustomerTrans($id,$invoiceId);
                        }else if($type == '2'){
                            OrderExt::model()->dealOrderExt($id,$invoiceId);
                        }else if($type == '3'){//vip_trade
                            VipTrade::model()->dealVipTrade($id,$invoiceId);
                        }
                   }
                    CustomerInvoice::model()->dealCustomerInvoiceSetAmount($invoiceId,$total_amount);
	       }
	       //CustomerMessage::model()->addBillMsg($phone);后台确认的时候Push
	   }
	
	    $update_condition = 'customer_phone=:customer_phone ';
            $update_condition.=' and id <> :id';

 	    $update_values[':id']=$invoiceId;
	    $update_values[':customer_phone']=$invoice['customer_phone'];
	    $attributes=array();
            $attributes['status'] = 0;
            $flag = $customerInvoice->updateAll($attributes,$update_condition,$update_values);
            $ret = array('code'=>0,'message'=>'提交成功');
        }else{
            $ret = array('code'=>2,'message'=>'提交失败');
        }
    }else{
    	$ret = array('code'=>1,'message'=>'token无效');
    }
}else{
	$ret = array('code'=>1,'message'=>'获取token失败');
}

echo json_encode($ret);
