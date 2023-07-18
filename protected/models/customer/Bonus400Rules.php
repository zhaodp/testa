<?php

/**
 * This is the model class for table "{{bonus_rules}}".
 *
 * The followings are the available columns in table '{{bonus_rules}}':
 * @property integer $id
 * @property integer $bonus_code_id
 * @property string $bonus_name
 * @property string $remark
 * @property string $sms
 * @property integer $status
 * @property integer $multi
 * @property string $created
 * @property string $update_by
 * @property string $update
 */
class Bonus400Rules extends FinanceActiveRecord
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
        return '{{bonus_400_rules}}';
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => '序号',
            'bonus_sn' => '优惠码',
            'remark' => '备注',
            'sms' => '短信内容',
            'phone' => '手机号',
            'status' => '是否启用',
            'multi' => '是否可以多次绑定',
            'type' => '类型',
            'sms' => '',
            'created' => '创建时间',
            'update_by' => '生成人',
            'update' => '生成时间',
	    'use_range'=>'使用限制',	
        );
    }

    /**
     * 保存之前要更新的字段
     * @return bool
     * author mengtianxue
     */
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->created = date("Y-m-d H:i:s");
            } else {
                $this->update_by = Yii::app()->user->getId();
                $this->update = date("Y-m-d H:i:s");
            }
            return true;
        }
        return parent::beforeSave();
    }

    public function getBonusRules($id)
    {
        $bonus_400_rules = self::model()->findByPk($id);
        return $bonus_400_rules;
    }

}
