<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2014/11/16
 * Time: 15:20
 */

class CaccidentCost extends CActiveRecord{

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{caccident_cost}}';
    }


    public function attributeLabels()
    {
        return array(
            'id' => 'id',
            'customer_id' => 'customer_id',
            'maintain_cost' => '赔偿维修费',
            'realcarcost' => '实际本车维修费',
            'realthirdcost' => '实际第三方维修费',
            'premium_up' => '保费上浮',
            'extal_cost' => '额外补偿',
            'towing_fee' => '拖车费',
            'traffic_compensation' => '交通补偿',
            'car_workcost' => '运营车辆务工',
            'damage_cost' => '物损',
            'hurt_cost' => '人伤',
            'other_cost' => '其他',
        );
    }

} 