<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2014/11/16
 * Time: 14:46
 */

class CtrafficAccident extends CActiveRecord{


    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{ctraffic_accident}}';
    }


    public function attributeLabels()
    {
        return array(
            'id' => 'id',
            'customer_id' => 'customer_id',
            'accidentTime' => '出险时间',
            'sign_order' => '是否签署服务确认单',
            'accident_site' => '事故地点',
            'take_photos' => '是否拍照',
            'calll_police' => '是否报警',
            'deaths' => '是否涉及人伤',
            'material' => '是否涉及物损',
            'accident_duty' => '单双方事故',
            'single_carmodel' => '车型',
            'single_damagepart' => '受损部位',
            'both_carmodel' => '主车车型',
            'both_damagepart' => '主车受损部位',
            'third_carmodel1' => '第三方车型①',
            'third_carmodel2' => '第三方车型②',
            'third_carmodel3' => '第三方车型③',
            'third_carmodel4' => '第三方车型④',
            'third_carmodel5' => '第三方车型⑤',
            'third_damagepart1' => '第三方受损部位①',
            'third_damagepart2' => '第三方受损部位②',
            'third_damagepart3'=>'第三方受损部位③',
            'third_damagepart4' => '第三方受损部位④',
            'third_damagepart5' => '第三方受损部位⑤',
        );
    }

} 