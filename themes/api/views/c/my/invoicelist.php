<?php
/**
 * 获取客户发票列表
 * @author cuiluzhe 2014-10-23
 * @see
 * @since
 */
$token = isset($params['token'])?$params['token']:'';
$pageSize = isset($params['pageSize'])?$params['pageSize']:30;
$pageNumber = isset($params['pageNumber'])?$params['pageNumber']:0;
if(!empty($token)){
    $validate = CustomerToken::model()->validateToken($token);
    if ($validate){
        $customer_phone = $validate['phone'];//客户电话
        $invoice_count = CustomerInvoice::model()->getInvoiceListSize($customer_phone);//发票总数
        $list = CustomerInvoice::model()->getInvoiceList($customer_phone,$pageSize, $pageNumber);
        $i = 0;
        $data = array();
        $data['count'] = $invoice_count;
        $data_array = array();
        foreach($list as $item){
            $invoice = $item['attributes'];
            $total_amount = isset($invoice['total_amount']) ? $invoice['total_amount'] : '0';
            $total_amount = intval($total_amount);
            $type = $invoice['type'];

	        $confirm = $invoice['confirm'];//客服确认
	        $client_amount = $invoice['client_amount'];
            $client_amount = intval($client_amount);

            $invoice_number = '';
            $delivery_number = '';
            $invoice_deliveryer = 1;
            $deliveryer = CustomerInvoice::$delivery[$invoice_deliveryer];

            if($type == CustomerInvoice::TYPE_UNDETERMIND){//老客户端申请
                if($total_amount == 0){//客服未确认
                    $content = '申请发票';
                }else{
                    $content = $total_amount.'元发票';
                }
            }else{//5.2
                if($confirm == 0 || $confirm == 2){//客服未确认或取消
                    if($client_amount>0){
                        $content = $client_amount.'元'.CustomerInvoice::$type[$type];
                    }else{
                        $content = CustomerInvoice::$type[$type].'发票';
                    }
                }else{
                    if($total_amount>0){
                        $content = $total_amount.'元'.CustomerInvoice::$type[$type];
                    }else{
                        $content = CustomerInvoice::$type[$type].'发票';
                    }
                }
            }
            $finance_confirm = $invoice['finance_confirm'];//财务确认
	        $this_year = date("Y", time());
            if($confirm == 0){//
                $status = '提交申请';
                if($this_year == date("Y", $invoice['created'])){
                    $date = date("m-d", $invoice['created']);
                }else{
                    $date = date("Y-m-d", $invoice['created']);
                }
            }else if($confirm == 2){
                $status = '客服取消';
                if($this_year == date("Y", $invoice['confirm_time'])){
                    $date = date("m-d", $invoice['confirm_time']);
                }else{
                    $date = date("Y-m-d", $invoice['confirm_time']);
                }
            }else{//$confirm == 1
                if($finance_confirm == 0){
                    $status = '客服确认';
                    if($this_year == date("Y", $invoice['confirm_time'])){
                        $date = date("m-d", $invoice['confirm_time']);
                     }else{
                        $date = date("Y-m-d", $invoice['confirm_time']);
                    }
                }else{
                    $status = '发票寄出';
                    if($this_year == date("Y", $invoice['finance_confirm_time'])){
                        $date = date("m-d", $invoice['finance_confirm_time']);
                    }else{
                        $date = date("Y-m-d", $invoice['finance_confirm_time']);
                    }
                    $invoice_number = $invoice['invoice_number'];
                    $delivery_number = $invoice['delivery_number'];

                    $invoice_deliveryer = $invoice['deliveryer'];
                    if($invoice_deliveryer == 0){
                        $invoice_deliveryer = 1;
                    }
                    $deliveryer = CustomerInvoice::$delivery[$invoice_deliveryer];
                }
            }

            $delivery = $invoice['contact'].'('.$invoice['telephone'].')';
            $data_array[$i]['content'] = $content;//内容
	        $data_array[$i]['title'] = $invoice['title'];//抬头

            $data_array[$i]['date'] = $date;//日期
            $data_array[$i]['status'] = $status;//状态
            $data_array[$i]['delivery'] = $delivery;//快递收件
            $data_array[$i]['address'] = $invoice['address'];//地址
	    
            $data_array[$i]['invoice_number'] = isset($invoice_number) ? $invoice_number : '';
            $data_array[$i]['delivery_number'] = isset($delivery_number) ? $delivery_number : '';
            $data_array[$i]['deliveryer'] = isset($deliveryer) ? $deliveryer : '';
            $i++;
          }
	    $data['dataList'] = $data_array;
        $ret = array('code'=>0, 'data'=>$data, 'message'=>'获取成功');
    }else{
        $ret = array('code'=>1, 'message'=>'token无效');
    }
}else{
    $ret = array('code'=>1, 'message'=>'获取token失败');
}
echo json_encode($ret);
