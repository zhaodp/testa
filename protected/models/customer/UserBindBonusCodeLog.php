<?php

/**
 * This is the model class for table "{{bonus_rules}}".
 *
 * The followings are the available columns in table '{{bonus_rules}}':
 * @property integer $id
 * @property integer $bonus_code_id
 * @property string $bonus_sn
 * @property string $phone
 */
class UserBindBonusCodeLog extends FinanceActiveRecord
{


    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return BonusRules the static model class
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
        return '{{user_bind_bonus_code_log}}';
    }


}