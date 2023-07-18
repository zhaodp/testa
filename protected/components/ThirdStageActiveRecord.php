<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/3/30
 * Time: 17:11
 */

class ThirdStageActiveRecord extends CActiveRecord {
    public function getDbConnection() {
        return Yii::app()->db_third;
    }
    protected function convertBillToArrayDateProvider($list, $channel){
        $arr = array();
        foreach($list as $item){
            $month = $item['month'];
            $status  = ThirdBillStatus::model()->getStatus($channel, $month);
            $tmp = isset($arr[$month]) ? $arr[$month] : array(
                'month'  => $month,
                'type_1' => 0,
                'type_2' => 0,
                'type_3' => 0,
                'type_4' => 0,
                'cast'   => 0,
                'channel' => $channel,
                'status'  => $status,
            );
            $type = $item['type'];
            $cast = $item['cast'];
            $arr[$month] = $this->buildTmp($tmp, $month, $type, $cast);

        }
        $tmpArr = array();
        foreach($arr as $item){
            $tmpArr[] = $item;
        }
        return new CArrayDataProvider($tmpArr, array(
            'keyField' => 'month',
            'id' => 'month',

        ));
    }

    private function buildTmp($tmp, $month, $type, $cast){
        $index = 'type_'.$type;
        $tmpCast = isset($tmp[$index]) ? $tmp[$index] : 0.00;
        $tmp[$index] = $tmpCast + $cast;
        $tmp['month'] = $month;
        $tmp['cast'] += $cast;
        return $tmp;
    }
}