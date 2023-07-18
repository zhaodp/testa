<?php
/**
 * vip 导数据
 * User: mengxiangan
 */
Yii::import('application.models.pay.activitySettlementImpl.*');
Yii::import('application.models.pay.orderSettlementImpl.*');
Yii::import('application.models.pay.calculator.*');
Yii::import('application.models.pay.settlement.*');
Yii::import('application.models.schema.customer.*');
Yii::import('application.models.pay.param_settle.*');
Yii::import('application.models.pay.subsidy.*');
class VipCheckCommand extends LoggerExtCommand{

    //测试阶段可以先指定一个手机号$phone导数据
    public function actionCheckVip($checkcount=0,$phone=''){
        //获取vip
        $vipList = $this->getVip($checkcount,$phone);
        foreach($vipList as $vip){
            $vipId = $vip['id'];
            $phone=$vip['phone'];
            $data = array();
            $data['phone'] = $data['send_phone'] = $phone;
            $data['id'] = $vipId;
            $data['balance'] =  $vip['balance'];;
            $data['operator'] = '系统操作';
            //只有正常的才转
            if($vip['status'] == Vip::STATUS_NORMAL){
                $this->save($data);
            }
            //删除原有数据和缓存
            $extra='d';
            Vip::model()->updatePhoneNum($vipId,$phone.$extra);
            VipPhone::model()->updatePhoneNum($vipId,$extra);
            RCustomerInfo:: model()->deleteCustomerMain($phone);
        }
    }
    private function save($data=array()){
        $customer = CustomerMain::model()->initCustomer( $data['phone']);
        $customerIncomeParams = array(
            'trans_type' => CarCustomerTrans::TRANS_TYPE_CARD,
            'source' => CarCustomerTrans::TRANS_SOURCE_CARD_PAY,
            'operator'=>$data['operator'],
            'remark'=>'充值卡转账:'.$data['id'],
        );
        $ret =  BCustomers::model()->income($customer->id,$data['balance'],$customerIncomeParams);
        if( $ret['code']===0){
            return true;
        }else{
            return false;
        }
    }
 /*   private function save($vipphone, $tradeList)
    {
        $customer = CustomerMain::model()->initCustomer($vipphone);
        foreach($tradeList as $viptrade){
            $cModel = new CarCustomerTrans();
            $c = $cModel->attributes;
            $c['user_id'] =$customer->id;
            $c['trans_order_id']=$viptrade['order_id'];
            $c['trans_type']=$viptrade['type'];
            $c['trans_card']=$viptrade['vipcard'];
            $c['amount']=$viptrade['amount'];
            $c['balance']=$viptrade['balance'];
            $c['source']=$viptrade['source'];
            $c['invoiced']=$viptrade['invoiced'];
            $c['invoice_id']=$viptrade['invoice_id'];
            $c['remark']=$viptrade['comment'];
            $params['create_time'] = date('Y-m-d H:i:s',$viptrade['created']);
            $c['operator']='系统拆分';
            $cModel->attributes = $c;
            $cModel->insert();
        }

    }*/

    private function getVip($checkcount=0,$phone=''){
        $criteria = new CDbCriteria();
        $criteria->compare('type', Vip::TYPE_FIXED);
        if($phone!=''){
            $criteria->compare('phone', $phone);
        }
        if($checkcount > 0) {
            $criteria->limit = $checkcount;
        }
        return Vip::model()->findAll($criteria);
    }

    private function getVipPhone($vipId){
        $criteria = new CDbCriteria();
        $criteria->compare('vipid', $vipId);
        return VipPhone::model()->findAll($criteria);
    }

    private function getTrade($vipId){
        $criteria = new CDbCriteria();
        $criteria->compare('vipcard', $vipId);
        return VipTrade::model()->findAll($criteria);
    }
} 