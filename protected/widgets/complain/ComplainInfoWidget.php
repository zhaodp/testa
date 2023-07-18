<?php
/**
 * 投诉信息（可根据订单id 或 客户手机 搜索）
 * @author  liuxiaobo
 */

class ComplainInfoWidget extends CWidget
{
    public $phone;          //客户手机
    public $orderId;          //订单id


    public function run()
    {
        $phone = $this->phone;
        $orderId = $this->orderId;
        
        $order = CustomerComplain::model();
        $criteria = new CDbCriteria();
        if($phone){
            $criteria->addCondition('phone = :phone OR customer_phone = :phone');
            $criteria->params[':phone'] = $phone;
        }
        if($orderId){
            $criteria->addCondition('order_id = :order_id');
            $criteria->params[':order_id'] = $orderId;
        }
        $orders = new CActiveDataProvider($order, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 5
            )
        ));
//        print_r($orders);
        $this->render('info', array(
            'data' => $orders,
        ));
    }

    //list 页面 format
    protected function getType($data){
        $name='';
        if($data->complain_type){
            $type= CustomerComplainType::model()->getComplainType($data->complain_type);
            if($type){
                $name= $type[0]->name;
            }
        }
        return $name;
    }
    protected function complainUser($data){
        $User_news='';
//        if($data->source==2){
//            $User_news.='短信评价<br/>';
//        }
        if($data->source == 8){
            $User_news .= $data->name.'<br>';
        }
        $User_news.=Common::parseCustomerPhone($data->phone);
        return $User_news;
    }
    /**
     * 
     * by 曾志海
     * @param obj $data
     */
    protected function customer_phone($data){
    	if (empty($data->customer_phone)) return $data->customer_phone;
		$sql = "SELECT count(*) as c FROM {{customer_complain}} WHERE (status=1 or status=2) and customer_phone=:customer_phone";
		$command = Yii::app ()->db_readonly->createCommand($sql)->bindValue(':customer_phone',$data->customer_phone);
		$customer_phoneCounts = $command->queryScalar();
		$phoneCounts=$customer_phoneCounts;
		if ($phoneCounts>1){
			return Common::parseCustomerPhone($data->customer_phone)." <span style='color:red;'>(".$phoneCounts.')</span>';
		}
    	return Common::parseCustomerPhone($data->customer_phone);
    }
    //司机工号、订单
    protected function driverInfo($data){
        $driverStr='';
        $driverId = '';
        if(is_array($data) && isset($data['driver_id'])){
            $driverId = $data['driver_id'];
        }
        if(is_object($data) && !empty($data->driver_id)){
            $driverId = $data['driver_id'];
        }
        $driverCount = $driverId && CustomerComplain::getDriverCount($driverId) > 0 ? '('.CustomerComplain::getDriverCount($driverId).')' : '';
        if(is_array($data)){
            if(isset($data['driver_id']))
            $driverStr.='';
            //获取司机姓名 by 曾志海
            $sql1 = "select name from {{driver}} where user=:user";
            $command = Yii::app()
                    ->db_readonly
                    ->createCommand($sql1)
                    ->bindValue(':user',$data['driver_id']);
            $driverName = $command->queryScalar();
            //存在司机返回<a>链接
            if($driverName)
                $driverStr.= '<a target="_blank" href="'.Yii::app()->createUrl('driver/archives',array('id'=>$data['driver_id'])).'" style="display:block;cursor:pointer;"  >'.$data['driver_id'].'<br/>'.$driverName.'</a>'/*.$driverCount*/;
        }
        if(is_object($data)){
            if(!empty($data->driver_id))
            $driverStr.='';
            //获取司机姓名  by 曾志海
            $sql1 = "select name from {{driver}} where user=:user";
            $command = Yii::app()
                ->db_readonly
                ->createCommand($sql1)
                ->bindValue(':user',$data->driver_id);
            $driverName = $command->queryScalar();
            //存在司机返回<a>链接
            if($driverName)
                $driverStr.= '<a target="_blank" href="'.Yii::app()->createUrl('driver/archives',array('id'=>$data->driver_id)).'" style="display:block;cursor:pointer;"  >'.$data->driver_id.'<br/>'.$driverName.'</a>'.$driverCount;
        }

        return $driverStr;
    }
    //list
    //查看订单
    public function orderIdAndNumber($data){
        $str = '';
        if(is_object($data)){
            if($data->order_id){
                $url=Yii::app()->createUrl('/order/view',array('id'=>$data->order_id));
                $str .= '<a type="button" target="_blank"  href="'.$url.'" style="display:block;cursor:pointer;" >'.$data->order_id.'</a>'.'<br/>';
            }else{
                $str='未定位订单';
            }
        }
        if(is_array($data)){
            if($data['order_id']){
                $url=Yii::app()->createUrl('/order/view',array('id'=>$data['order_id']));
                $str .= '<a type="button" target="_blank"  href="'.$url.'" style="display:block;cursor:pointer;" >'.$data['order_id'].'</a>'.'<br/>';
            }else{
                $str='未定位订单';
            }
        }
        return $str;
    }

    protected function processStatus($data){
        $statusStr=$status=$cid='';
        if(is_object($data)){
            $status=$data->status;
            $cid=$data->id;
        }
        if(is_array($data)){
            $status=$data['status'];
            $cid=$data['id'];
        }
        $p_url=Yii::app()->createUrl('complain/status',array('cid'=>$cid));
        //add by aiguoxin 2014-07-08 兼容旧状态
        if($status == 2 || $status == 3 || $status == 4){
            $status=8;
        }        
        $statusStr.='<a data-toggle="modal" data-target=""  url="'.$p_url.'" style="display:inline-block;cursor:pointer;" >'.CustomerComplain::$newStatus[$status].'</a>';
        return $statusStr;
    }
    protected function opt($data){
        $opt_str='';
        if($data->driver_id)
            $p_param['driver_id']=$data->driver_id;
        if($data->customer_phone)
            $p_param['phone']=$data->customer_phone;

        $currentUrl=Yii::app()->request->getUrl();
        $p_param['re']=$currentUrl;
        $p_param['cid']=$data->id;


        if ($data->status == 1) { //品监还未处理
            $search_order_url = Yii::app()->createUrl('complain/order', $p_param);
            $opt_str .= '<a style="display:inline-block;cursor:pointer;" url="' . $search_order_url . '" mewidth="900px" data-target="" data-toggle="modal">定位订单</a>&nbsp;';

            $p_param['oid'] = $data->order_id;
            $p_url = Yii::app()->createUrl('complain/confirm', $p_param);
            $opt_str .= '<a class="btn" type="button"   url="' . $p_url . '"  href="' . $p_url . '">处理</a>' . '<br/>';

        } else {
            $opt_str .= '已处理 ';
        }
        $remark_url = Yii::app()->createUrl('complain/remark', $p_param);
        $opt_str .= '<a data-toggle="modal" data-target="" mewidth="400px"  url="' . $remark_url . '" style="display:inline-block;cursor:pointer;">跟进备注</a> &nbsp;';
        if($data->attention<1){
            $opt_str .= '<span id="ajaxLink_'.$data->id.'">'.CHtml::link('加关注',array('complain/list','attention_id'=>$data->id,'attention_status'=>1),array('class'=>'attentionLink','onclick'=>'addAttention(this);return false')).'</span>';
        }else{
            $opt_str .= '<span id="ajaxLink_'.$data->id.'">'.CHtml::link('取消关注',array('complain/list','attention_id'=>$data->id,'attention_status'=>0),array('class'=>'attentionLink','onclick'=>'addAttention(this);return false')).'</span>';
        }


        return $opt_str;
    }
}