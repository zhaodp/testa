<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/1/16
 * Time: 11:37
 */

class VipYearCommand extends  LoggerExtCommand{

    public function actionRun($dateStart, $dateEnd, $vipCard = 0){
        $dateStart = $dateStart.' 0:0:0';
        $dateEnd   = $dateEnd.' 23:59:59';
        //1.get all vip info
        $vipList = $this->getVipList($vipCard);
        $vipMap  = $this->listToMap('id', $vipList);

        //2.to map
        $format = '|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|';
        foreach($vipMap as $k => $v){
            //get total charge money, total count;
            $vip = $v;
            $chargeInfo = $this->getChargeInfo($dateStart, $dateEnd, $k);
            $consumeInfo = $this->getConsumeInfo($dateStart, $dateEnd,$k);
//            1,3,2,5,4,8,9,10,11,7
            $log = sprintf($format, $vip['id'], $vip['company'], $vip['name'],
                $vip['city_id'],$vip['created'],
                                $vip['balance'], $vip['remarks'], $chargeInfo['amount'], $chargeInfo['id'], $consumeInfo['amount'], $consumeInfo['order_id']);
            //get total used, total number
            echo $log."\n";
            EdjLog::info('vip ----'.$log);
        }
        //get trade info
    }


    private function getChargeInfo($dateStart, $dateEnd, $vipCard){
        $criteria = new CDbCriteria();
        $criteria->compare('vipcard', $vipCard);
        $typeArr = array(
            VipTrade::TYPE_INCOME,
            VipTrade::TYPE_CARD_INCOME,
        );
        $criteria->addInCondition('type', $typeArr);
        $criteria->addInCondition('source', array(0));
        $criteria->addBetweenCondition('created', strtotime($dateStart), strtotime($dateEnd));
        $criteria->select = 'sum(amount) as amount, count(id) as id';
        return VipTrade::model()->find($criteria);
    }

    private function getConsumeInfo($dateStart, $dateEnd, $vipCard){
        $criteria = new CDbCriteria();
        $criteria->compare('vipcard', $vipCard);
        $typeArr = array(
            VipTrade::TYPE_ORDER,
        );
        $criteria->addInCondition('type', $typeArr);
        $criteria->addBetweenCondition('created', strtotime($dateStart), strtotime($dateEnd));
        $criteria->select = 'sum(amount) as amount, count(distinct order_id) as order_id';
        return VipTrade::model()->find($criteria);
    }

    private function getVipList($vipCard){
        $criteria = new CDbCriteria();
        if(!empty($vipCard)){
            $criteria->compare('id', $vipCard);
        }
        $criteria->select = 'id, company, name,city_id,from_unixtime(created) as created,balance,remarks';
        return Vip::model()->findAll($criteria);
    }


    /**
     * 把一个二维数组,转为一个指定字段为索引的map
     *
     * @param $column
     * @param array $list
     * @return array 可能抛出undefined index异常
     */
    private  function listToMap($column, $list = array()){
        if(empty($list)){
            return array();
        }
        $ret = array();
        foreach($list as $item){
            $index = $item[$column];
            $ret[$index]  = $item;
        }
        return $ret;
    }
}