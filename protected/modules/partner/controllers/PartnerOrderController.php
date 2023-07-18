<?php
Yii::import('application.controllers.OrderController');
class PartnerOrderController extends Controller
{

    /**
     * 订单查询
     */
    public function actionIndex() {
        $param['call_time_start'] = isset($_REQUEST['order']['call_time_start'])&&$_REQUEST['order']['call_time_start'] ? trim($_REQUEST['order']['call_time_start']) : date('Y-m-d', strtotime('-2 months'));
        $param['call_time_end'] = isset($_REQUEST['order']['call_time_end'])&&$_REQUEST['order']['call_time_end'] ? trim($_REQUEST['order']['call_time_end']) : date('Y-m-d', time());
        $param['contact_phone'] = isset($_REQUEST['order']['contact_phone']) ? intval($_REQUEST['order']['contact_phone']) : '';
        $param['city_id'] = isset($_REQUEST['order']['city_id']) ? intval($_REQUEST['order']['city_id']) : 0;
        $param['status'] = isset($_REQUEST['order']['status'])&&is_numeric($_REQUEST['order']['status']) ? array($_REQUEST['order']['status']) : array();
        $partner = Yii::app()->partner->info;

        $param['channel'] = $partner['channel_id'];
        $common = new PartnerCommon();
        $criteria = $common->getCriteria($param['channel'], $param['call_time_start'], $param['call_time_end'], $param['city_id'], $param['contact_phone'], $param['status']);
        $data = $common->getDataProvider($criteria);

        $total = Order::model()->count($common->getCriteria($param['channel'], $param['call_time_start'], $param['call_time_end'], $param['city_id'], $param['contact_phone'])); //订单总数
        $complete = Order::model()->count($common->getCriteria($param['channel'], $param['call_time_start'], $param['call_time_end'], $param['city_id'], $param['contact_phone'], array(1,4))); //报单数
        $cancel = Order::model()->count($common->getCriteria($param['channel'], $param['call_time_start'], $param['call_time_end'], $param['city_id'], $param['contact_phone'], array(2,3))); //销单数

        $price_visible = $partner['pay_sort'] == Partner::PAY_SORT_DIVIDED ? false : true;
        $this->pageTitle = '订单';
        $this->render('index', array(
            'data' => $data,
            'total' => $total,
            'complete' => $complete,
            'cancel' => $cancel,
            'param' => $param,
            'price_visible' => $price_visible
        ));
    }

    public function actionView($id, $part=null){
        $parts = array('bonus','comment_sms','complain','order_log');
        $view = 'view';
        if($part !== null && in_array($part, $parts)){
            $this->layout = FALSE;
            $order = Order::model()->findByPk($id);
            if(!$order){
                Yii::app()->end();
            }
            $view = 'info/'.$part;
        }else{
            $order = OrderCache::model()->getOrderInfo($id);
            if($order){
                $ext = json_decode(json_encode(array(
                    'wait_time' => $order->wait_time,
                    'mark' => $order->mark,
                )));
            }else{
                $order = Order::model()->findByPk($id);
                $ext = $order ? $order->order_ext : '';
            }
        }
        $this->render($view, array(
            'order' => $order,
            'order_ext' => isset($ext) ? $ext : NULL));

    }

    /**
     * 历史账单
     */
    public function actionBill() {
        $partner = Yii::app()->partner->info;
        if ($partner && $partner['channel_id']) {
            $criteria = new CDbCriteria();
            $common = new PartnerCommon();
            $channel = $partner['channel_id'];
            $month = isset($_GET['Bill']['listMonth']) ? $_GET['Bill']['listMonth'] : date('m', strtotime(date('Y-m-01', time()))-86400);

            if(strlen($month) == 1){
                $monthStr = date("Y").str_pad($month, 2, 0, STR_PAD_LEFT);
            }else
                $monthStr = date("Y").$month;

            $monthArray = CompanyKpiCommon::getMonthFirstAndLastDay($monthStr);
            $criteria = $common->getCriteria($channel, $monthArray[0], $monthArray[1], 0, null, null);
            $dataProvider = $common->getDataProvider($criteria);
            $price_visible = $partner['pay_sort'] == Partner::PAY_SORT_DIVIDED ? false : true;
            $partnerInfo = $partner;
            $orderStats = Order::model()->getPartnerOrderStats($channel, strtotime($monthArray[0]), strtotime($monthArray[1]));
            $arrayData = $orderStats->getData();
            $this->render('partner_bill', array (
                'arrayData' => $arrayData,
                'time_arr' => $monthArray,
                'partnerInfo' =>$partnerInfo,
                'dataProvider' => $dataProvider,
                'price_visible' =>$price_visible,
                'month' => $month
            ));
        } else {
            throw new CHttpException(404,'此页面不存在');
        }
    }

    public function actionQueueList() {
        $partner = Yii::app()->partner->info;
        if ($partner && $partner['channel_id']) {
            $criteria = new CDbCriteria();
            $criteria->compare('channel', $partner['channel_id']);
            $criteria->compare('created', '>='.date("Y-m-d",strtotime("-1 day")));
            $criteria->order = 'flag,booking_time,created,number desc';
            $dataProvider = new CActiveDataProvider('OrderQueue',
                array (
                    'criteria'=>$criteria,
                    'pagination' => array(
                        'pageSize' => 20
                    ),
                )
            );
            $this->render('queue_list', array(
                'dataProvider' => $dataProvider
            ));
        }  else {
            throw new CHttpException(404,'此页面不存在');
        }
    }

    protected function confirmOrderCacnel($order) {
        switch ($order->status) {
            case Order::ORDER_READY :
                return '未报单';
                break;
            case Order::ORDER_COMPLATE :
                return '已报单';
                break;
            case Order::ORDER_COMFIRM :

                return CHtml::encode('待审核');
                break;
            case Order::ORDER_CANCEL :
                return CHtml::encode('已销单');
                break;
            case Order::ORDER_NOT_COMFIRM :
                return CHtml::encode('拒绝销单');
                break;
        }
    }

    public function orderCancel($data){
        $cencal = '';
        $cencal .= self::orderCacnelType($data);
        $cencal .= '<br/>'.CHtml::encode($data->cancel_desc);
        return $cencal;
    }

    protected function orderStatus($row, $order) {
        $css_class = 'wait';
        switch ($order->status) {
            case Order::ORDER_READY :
                $css_class = 'ready';
                break;
            case Order::ORDER_COMPLATE :
                $css_class = 'complate';
                break;
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

    public function orderTime($data){
        $created = $data->source == "1" ? date("m-d H:i",$data->created) : "";
        $time = '';
        $time .= '呼叫:'.date("m-d H:i",$data->call_time);
        $time .= '<br/>预约:'.date("m-d H:i",$data->booking_time);
        if($created != ''){
            $time .= '<br/>派单:'.$created;
        }
        return $time;
    }

    public function OrderAddr($data){
        $addr = '';
        if($data->status == 1 || $data->status == 4){
            $addr .= '开始：'.$data->location_start;
            $addr .= '<br/>结束：'.$data->location_end;
        } else {
        }
        return $addr;
    }

    public function orderFee($data){
        $fee = '';
        if($data->status == 1 || $data->status == 4){
            $fee .= '总费用：'. $data->income;
            /*
            if($data->price != 0)
                $fee .= '<br/>实收：'. $data->price;
            $fee .= '<br/>信息费：'. $data->cast;
            */
        }else{
            $fee = 0;
        }
        return $fee;
    }


    protected function orderCacnelType($data){
        $cancel_type = $data->cancel_type;
        return $cancel_type == 0 ? '' : Dict::item('cancel_type', $data->cancel_type);
    }

    protected function queueStatus($row, $data) {
		switch ($data->flag) {
			case 0 :
				$css_class = 'alert';
				break;
			case 3 :
				$css_class = 'alert-error';
				break;
			default :
				$css_class = 'alert-success';
				break;
		}
        if(time() - strtotime($data->booking_time) > 1 && $data->flag ==1){
            $css_class = 'alert-info';
        }

		return $css_class;
	}

    protected function queueDispatchStatus($data) {
        switch ($data->flag) {
            case 0 :
                $driver_state='等待派单';
                break;
            case 1 :
                $driver_state='已发调度';
                break;
            case 2 :
                $driver_state='调度接单';
                break;
            case 3 :
                $driver_state='取消';
                break;
            case 4 :
                $driver_state='已派单';
                break;
        }

        return $driver_state;
    }

    public function getOrderInfoLink($data) {
        echo CHtml::link($data->order_id, Yii::app()->createUrl('business/partnerOrder/view', array('id'=>$data->order_id)), array('target'=>'_blank'));
    }

}
?>
