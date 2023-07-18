<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/4/16
 * Time: 11:39
 */

class BillProcess {

    private $billInstance = array();

    private $dateSummary  = array();

    function __construct($billInstance, $dateSummary)
    {
        $this->billInstance = $billInstance;
        $this->dateSummary = $dateSummary;
    }

    public function calculator($month){
        $billInstance = json_decode($this->billInstance['meta'], true);
        foreach($this->dateSummary as $item){
            if(empty($billInstance)){
                $this->defaultForm($item, 0, $month);
                continue;
            }
            if(isset($billInstance['billForm'])){
                $this->billForm($billInstance['billForm'], $item, $month);
            }

            if(isset($billInstance['newUserForm'])){
                $this->newUserForm($billInstance['newUserForm'], $item, $month);
            }

            if(isset($billInstance['oldUserForm'])){
                $this->oldUserForm($billInstance['oldUserForm'], $item, $month);
            }
        }
    }

    private function billForm($billInstance, $dataSummary, $month){
        $orderCount = $dataSummary['orderCount'];
        $stage = $billInstance['children'];
        $cast   =  $this->rangePrice($orderCount, $stage);
        if(-1 == $cast){
            $cast = $orderCount * $billInstance['base'];
        }
        $userId = ThirdUser::model()->getUserIdByChannel($dataSummary['channel']);
        ThirdMonthBill::model()->createInstance($userId, ThirdMonthBill::BILL_TYPE_ORDER_SHARE, $cast, $month);

        return 0;
    }


    private function newUserForm($billInstance, $dataSummary, $month){
        $orderCount = $dataSummary['inviteCount'];
        $stage = $billInstance['children'];
        $cast   =  $this->rangePrice($orderCount, $stage);
        if(-1 == $cast){
            $cast = $orderCount * $billInstance['base'];
        }
        $userId = ThirdUser::model()->getUserIdByChannel($dataSummary['channel']);
        ThirdMonthBill::model()->createInstance($userId, ThirdMonthBill::BILL_TYPE_INVITE_ORDER, $cast, $month);

        return 0;
    }



    private function oldUserForm($billInstance, $dataSummary, $month){
        $orderCount =  $dataSummary['customerCount'] - $dataSummary['inviteCount'];
        $stage = $billInstance['children'];
        $cast   =  $this->rangePrice($orderCount, $stage);
        if(-1 == $cast){
            $cast = $orderCount * $billInstance['base'];
        }
        $userId = ThirdUser::model()->getUserIdByChannel($dataSummary['channel']);
        ThirdMonthBill::model()->createInstance($userId, ThirdMonthBill::BILL_TYPE_OLD_ORDER, $cast, $month);

        return 0;
    }

    private function defaultForm($dataSummary, $cast, $month){
        $userId = ThirdUser::model()->getUserIdByChannel($dataSummary['channel']);
        ThirdMonthBill::model()->createInstance($userId, ThirdMonthBill::BILL_TYPE_FRIEND, $cast, $month);
    }

    private function rangePrice($count, $stage){
        foreach(array_reverse($stage) as $item){
            $low = $item['lowPrice'];
            $high = $item['highPrice'];
            $price = $item['perPrice'];
            if( $count > $low && $count < $high){
                return $price * $count;
            }
        }
        return -1;
    }






}