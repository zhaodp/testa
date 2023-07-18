<?php
/**
 * 用户发票列表
 * @author zhangtongkai 2014-04-25
 * @see
 * @since
 */
$token = isset($params['token'])?$params['token']:'';
$pageNo = isset($params['pageNo'])?$params['pageNo']:0;
$pageSize = isset($params['pageSize'])?$params['pageSize']:10;

if(!empty($token)){
    $validate = CustomerToken::model()->validateToken($token);
    if ($validate){
        $attributes=array();
        $time=time();
        $condition='';$vlaues=array();
        $condition='customer_phone=:customer_phone';
        $condition.=' and status=:status';
        $vlaues[':customer_phone']=$validate['phone'];
        $vlaues[':status']=1;
        $customerInvoice = CustomerInvoice::model()->find($condition, $vlaues);
        if($customerInvoice){
            $customerInvoice->created=date('Y-m-d H:i:s',$customerInvoice->created);
            $customerInvoice->updatetime=date('Y-m-d H:i:s',$customerInvoice->updatetime);
            $attributes=$customerInvoice->attributes;
        } else {
            $customerInvoice=CustomerInvoice::model();
            $customerInvoice->unsetAttributes();  // clear any default values
            foreach($customerInvoice->attributes as $k=>$v){
            	$attributes[$k]='';
            }
        }
	//获取最大开票金额
        $transList   = CustomerTrans::model()->getCustomerTransList($validate['phone'],0);
        $totalAmount = 0;
        if($transList){
            foreach($transList as $trans){
                $amount = $trans['amount'];
                $totalAmount = $totalAmount+$amount;
            }
        }
	$attributes['amount'] = $totalAmount;
	
	$attributes['minamount'] = 19;//最小开票金额
	
        $ret = array(
            'code'=>0,
            'data'=>$attributes,
            'message'=>'获取成功');
    }else{
    	$ret = array(
        'code'=>1,
        'message'=>'token无效');
    }
}else{
	$ret = array(
    'code'=>1,
    'message'=>'获取token失败');
}

echo json_encode($ret);
