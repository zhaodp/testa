<?php

/**
 * created by PhpStorm.
 * User: zhangxiaoyin
 * Date: 2014/10/15
 * Time: 16:18
 */
class BonusLibrarySearch extends CFormModel
{
    public $bonus_sn_start;
    public $bonus_sn_end;
    public $channel;
    public $id;
    public $select_bonus;
    public $tel;
    public $contact;
    public $selectNum;
    public $problemNum=0;
    public $city_id;
    public $bonus_sn;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        $array = array(
            array('bonus_select_num', 'required'),
        );
        return $array;
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'bonus_sn_start' => '起始号码',
            'bonus_sn_end' => '结束号码',
            'contact' => '渠道联系人',
            'tel' => '联系人电话',
            'creat_by' => '渠道创建人',
            'selectNum' => '选择卡数量',
            'problemNum' => '问题卡数量',
            'channel' => '渠道名称',
            'bonus_sn' => '优惠券ID',
        );
    }


}
