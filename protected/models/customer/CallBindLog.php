<?php

/**
 * This is the model class for table "{{call_bind_log}}".
 *
 * The followings are the available columns in table '{{call_config}}':
 * @property string $id
 * @property string $phone
 * @property integer $bonus_code_id
 * @property string $created
 */
class CallBindLog extends FinanceActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CallConfig the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{call_bind_log}}';
    }


    /**
     * 保存之前要更新的字段
     * @return bool
     * author mengtianxue
     */
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->created = date("Y-m-d H:i:s");
            return true;
        }
        return parent::beforeSave();
    }

//    在07：00到第二天07：00是否绑定过优惠券
    public function isBind($phone)
    {
        $start_time = date('Y-m-d H:i:s', mktime(07, 0, 0, date('m'), date('d'), date('Y')));
        $end_time = date('Y-m-d H:i:s', mktime(07, 0, 0, date('m')+1, date('d'), date('Y')));
        $log = self::model()->find("phone=:phone and created between :start and :end", array("phone" => $phone, 'start' => $start_time, 'end' => $end_time));
        if ($log)
            return true;
        else
            return false;

    }

    //    用户是否曾经绑定过
    public function isBind2($phone)
    {
        $log = self::model()->find("phone=:phone", array("phone" => $phone));
        if ($log)
            return true;
        else
            return false;

    }


}