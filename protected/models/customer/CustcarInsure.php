<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2014/11/16
 * Time: 15:10
 */

class CustcarInsure extends CActiveRecord{

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{custcar_insure}}';
    }


    public function attributeLabels()
    {
        return array(
            'id' => 'id',
            'customer_id' => 'customer_id',
            'car_number' => '车牌号',
            'insure_company' => '所属保险公司',
            'insurer_name' => '被保险人姓名',
            'insurer_cardid' => '被保险人身份证号码',
            'car_salino' => '车辆交强险保单号',
            'car_businessno' => '车辆商业险保单号',
        );
    }

} 