<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/5/5
 * Time: 14:09
 */

class CityConfigLogic extends  CityConfig{

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CityConfig the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}