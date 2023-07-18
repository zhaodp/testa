<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/4/29
 * Time: 17:31
 */

class DriverAccount extends EmployeeAccount{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 司机的款项
     */
    public function chargeDriver($employeeAccountAttributes){
        try {
            $driverPriceRet = OrderSettlement::model()->insertAccount($employeeAccountAttributes);
            if (!$driverPriceRet) {
                EdjLog::info('update driver account fail ' . serialize($employeeAccountAttributes));
                return false;
            } else {
                $this->ReloadDriverAmountRedis($employeeAccountAttributes['user']);
                return true;
            }
        } catch (Exception $e) {
            EdjLog::error($e->getMessage());
        }
    }
}