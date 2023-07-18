<?php
/**
 * 订单信息
 * @author  liuxiaobo
 */

class OrderInfoWidget extends CWidget
{
    public $phone;              //客户手机
    public $orderId;            //订单id


    public function run()
    {
        $phone = $this->phone;
        $orderId = $this->orderId;
       
        $params = array();
        $db = Order::model()->getDbReadonlyConnection();
        $sql_head = 'SELECT * FROM t_order';
        $sql_tail = 'SELECT * FROM t_order';
        if($phone){
            $sql_head .= ' WHERE phone = :phone_head';
            $sql_tail .= ' WHERE contact_phone = :phone_tail';
            $params[':phone_head'] = $phone;
            $params[':phone_tail'] = $phone;
        }
        if($orderId){
            $where_or_and = '';
            if($phone) {
                $sql_head .= ' AND ';
                $sql_tail .= ' AND ';
            }
            else {
                $sql_head .= ' WHERE ';
                $sql_tail .= ' WHERE ';
            }

            $sql_head .= 'order_id = :order_id_head';
            $sql_tail .= 'order_id = :order_id_tail';
            $params[':order_id_head'] = $orderId;
            $params[':order_id_tail'] = $orderId;
        }

        $sql = $sql_head.' UNION '.$sql_tail;
        $count = $db->createCommand('SELECT COUNT(order_id) FROM ('.$sql.') as tmp')
            ->queryScalar($params);

        $orders = new CSqlDataProvider($sql, array(
            'db' => $db,
            'keyField' => 'order_id',
            'totalItemCount' => $count,
            'params' => $params,
            'pagination' => array(
                'pageSize' => 10
            )
        ));

        $this->render('info', array(
            'data' => $orders,
        ));
    }

    /**
     *
     * 显示订单状态css class
     * @param int $row
     * @param object $data
     */
    protected function orderStatus($row, $order)
    {
        if(is_array($order))
            $order = (object)$order;

        $css_class = 'wait';
        switch ($order->status) {
            case Order::ORDER_READY :
                $css_class = 'ready';
                break;
            case Order::ORDER_COMPLATE :
                $css_class = 'complate';
                break;
            case Order::ORDER_DRIVER_REJECT_CANCEL:
            case Order::ORDER_CUSTOMER_CANCEL:
            case Order::ORDER_NO_DISPATCH_CANCEL:
            case Order::ORDER_CANCEL :
                $css_class = 'cancel';
                break;
            case Order::ORDER_COMFIRM :
                $css_class = 'confirm';
                break;
            case Order::ORDER_NOT_COMFIRM :
                $css_class = 'not_confirm';
                break;
        }
        return $css_class;
    }

    /**
     * 把订单号和流水号返回
     * Enter description here ...
     * @param unknown_type $data
     */
    public function orderIdAndNumber($data)
    {
        if(is_array($data))
            $data = (object)$data;

        $str = '';
        $str .= $data->order_id . "<br/>";
        $str .= CHtml::link(($data->order_number) ? $data->order_number : "查看", array('order/view', 'id'=>$data->order_id), array('target'=>'_blank',"onclick" => "{//orderDialogdivInit($data->order_id);}"));
        $str .= "<br />" . CHtml::link("关系", array('order/relation', 'id'=>$data->order_id), array('target'=>'_blank',"onclick" => "{//orderRelation($data->order_id);}"));
        return $str;
    }

    protected function adminDriverInfo($data)
    {
        if(is_array($data))
            $data = (object)$data;

        $url = Yii::app()->createUrl('driver/archives', array(
            'id' => $data->driver_id));
        $driver_url = CHtml::link($data->driver, $url, array(
            'onclick' => "//driverDialogdivInit('" . $url . "');",
            'target' => '_blank',
        ));
        $driver_order = $data->driver_id;
        return $driver_url . ' ' . $driver_order;
    }

    /**
     * 订单的时间
     * Enter description here ...
     * @param unknown_type $data
     */
    public function orderTime($data)
    {
        if(is_array($data))
            $data = (object)$data;

//        $created = OrderQueueMap::model()->getDispatchTime($data->order_id);
        $created = isset($data->order_queue_map->confirm_time) ? date('m-d H:i', strtotime($data->order_queue_map->confirm_time)) : '';
        $time = '';
        $time .= '呼叫:' . date("m-d H:i", $data->call_time);
        $time .= '<br/>预约:' . date("m-d H:i", $data->booking_time);

        if ($created != '') {
            $time .= '<br/>派单:' . $created;
        }
        return $time;
    }

    /**
     * 订单费用详情
     * Enter description here ...
     * @param unknown_type $data
     */
    public function orderFee($data)
    {
        if(is_array($data))
            $data = (object)$data;

        $fee = '';
        if ($data->status == 1 || $data->status == 4) {
            $fee .= '总费用：' . $data->income;
            if ($data->price != 0)
                $fee .= '<br/>实收：' . $data->price;
            $fee .= '<br/>信息费：' . $data->cast;
        } else {
            $fee = 0;
        }
        return $fee;
    }

    /**
     * 销单类型和消单原因
     * Enter description here ...
     * @param unknown_type $data
     */
    public function orderCancel($data)
    {
        if(is_array($data))
            $data = (object)$data;

        $cancel = '';
        $cancel .= self::orderCacnelType($data);
        $cancel .= '<br/>' . $data->cancel_desc;
        return $cancel;
    }

    protected function orderPhone($data)
    {
        if(is_array($data))
            $data = (object)$data;

        $bonus = CustomerBonus::getOrderBonus($data->phone, $data->order_id);
        $link = '';
        //客户名字
        $customer = @Customer::getCustomer($data->phone)->attributes["name"];
        if ($customer) {
            if ($data->vipcard) {
                $link .= '<span class="vip" title="卡号：' . $data->vipcard . '"></span>' . $customer;
            } else if (empty($data->name)) {
                $link .= $customer;
            }
        } else {
            if ($data->vipcard) {
                $link .= '<span class="vip" title="卡号：' . $data->vipcard . '"></span>' . $data->name;
            } else {
                $link .= $data->name;
            }
        }
        if ($link)
            $link .= '<br/>';
        //客户电话 bidong up 2013-10-22 展示用户所有电话
        $link .= '呼叫：' . Common::parseCustomerPhone($data->phone);
        if (!empty($data->contact_phone)) {
            $link .= '<br/> 联系：' . Common::parseCustomerPhone($data->contact_phone);
        }

        //客户优惠劵
        if (!empty($bonus)) {
            $bonus_sn = $bonus['bonus'];
            $link .= '<br />优惠码:' . $bonus_sn;
        }

        if($data->status == 1){
//            $ext = OrderExt::model()->getPrimary($data->order_id);
//            if(!empty($ext['coupon'])){
//                $link .= '<br />贵宾卡: ' . $ext['coupon'] .'张 X 39元';
            if(isset($data->order_ext->coupon) && $data->order_ext->coupon){
                $link .= '<br />贵宾卡: ' . $data->order_ext->coupon .'张 X 39元';
            }
        }
        return $link;

    }

    /**
     * 客户的起始时间
     * Enter description here ...
     * @param unknown_type $data
     */
    public function OrderAddr($data)
    {
        if(is_array($data))
            $data = (object)$data;

        $addr = '';
        $start_time = date('Y-m-d H:i:s', $data->created);
        if ($data->status == 1 || $data->status == 4) {
            $addr .= '开始：' . $data->location_start;
            $addr .= '<br/>结束：' . $data->location_end;
            $end_time = date('Y-m-d H:i:s', ($data->end_time + 1800));
            $addr .= '<br />' . CHtml::link('查看轨迹', array('driver/orderposition&driver_id=' . $data->driver_id . '&startDate=' . $start_time . '&endDate=' . $end_time), array('target' => '_blank'));
        } else {
            $end_time = date('Y-m-d H:i:s', $data->created + 7200);
            $addr .= CHtml::link('查看轨迹', array('driver/orderposition&driver_id=' . $data->driver_id . '&startDate=' . $start_time . '&endDate=' . $end_time), array('target' => '_blank'));
        }
        return $addr;
    }

    protected function driverState($data)
    {
        if(is_array($data))
            $data = (object)$data;

        if ($data->driver_state) {
            $driver_state = '';
            for ($i = 0; $i < strlen($data->driver_state->state); $i++) {
                switch ($data->driver_state->state[$i]) {
                    case 0 :
                        $driver_state .= '空闲-';
                        break;
                    case 1 :
                        $driver_state .= '服务-';
                        break;
                    case 2 :
                        $driver_state .= '下班-';
                        break;
                }
            }
            echo $driver_state = rtrim($driver_state, '-');
        }
    }

    protected function orderCacnelType($data)
    {
        if(is_array($data))
            $data = (object)$data;

    	$str = '';
        $cancel_type = $data->cancel_type;
        
        if( 0 != $cancel_type ){
        	$cancel_type = Common::convertCancelType( $cancel_type );//转换老销单原因为新的id
        	$str = Dict::item('qx_o_type', $cancel_type );
        }
       	return $str;
        
    }

    protected function confirmOrderCacnel($order)
    {
        if(is_array($order))
            $order = (object)$order;

        switch ($order->status) {
            case Order::ORDER_READY :
                return '未报单';
                break;
            case Order::ORDER_COMPLATE :
                return '已报单';
                break;
            case Order::ORDER_COMFIRM :
                return '待审核';
                return CHtml::link('待审核', array('order/view','id'=>$order->order_id), array('target'=>'_blank',
                    "onclick" => "{orderDialogdivInit($order->order_id);}"));
                break;
            case Order::ORDER_CANCEL :
                return '已销单';
                return CHtml::link('已销单', array('order/view','id'=>$order->order_id), array('target'=>'_blank',
                    "onclick" => "{orderDialogdivInit($order->order_id);}"));
                break;
            case Order::ORDER_NOT_COMFIRM :
                return '拒绝销单';
                return CHtml::link('拒绝销单', array('order/view','id'=>$order->order_id), array('target'=>'_blank',
                    "onclick" => "{orderDialogdivInit($order->order_id);}"));
                break;
        }

        //(($data->cancel_desc!="")?$data->cancel_desc:CHtml::link("销单", array("order/cancel", "id"=>$data->order_id))):$data->cancel_desc
    }

    protected function invoice($data){
        if(is_array($data))
            $data = (object)$data;

        $invoice = '未开发票';
        if($data->status == 1){
//            $ext = OrderExt::model()->getPrimary($data->order_id);
//            $invoiced = isset($ext['invoiced']) ? $ext['invoiced'] : 0;
            $invoiced = isset($data->order_ext->invoiced) ? $data->order_ext->invoiced : 0;
            if($invoiced == 1){
                $invoice = '已开发票';
            }
        }
        return $invoice;
    }

    protected function description($data) {
        if(is_array($data))
            $data = (object)$data;

        return ($data->source == "0") ? "客户呼叫" : (($data->source == 1) ? CHtml::link("呼叫中心" , array("order/queue","order_id"=>$data->order_id,"dialog"=>1) , array("target" => "_blank")) : (($data->source == 2) ? "客户呼叫补单" : (($data->source == 3) ? "呼叫中心补单" : "")));
    }
}
