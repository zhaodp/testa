<?php

/**
 * This is the model class for table "{{bonus_merchants}}".
 */
class BonusMerchants extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return BonusCode the static model class
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
        return '{{bonus_merchants}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, contacts, phone,email', 'required'),
            array('name, contacts', 'length', 'max' => 20),
            array('phone', 'length', 'max' => 15),
            array('email', 'length', 'max' => 50),
            array('shop_type', 'numerical', 'integerOnly' => true),
            array('created, updated', 'length', 'max' => 20),
            array('name,shop_type', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name' => '商家名称',
            'contacts' => '商家联系人',
            'phone' => '联系人电话',
            'email' => '邮箱',
            'shop_type' => '商家类型'
        );
    }

    public function getBonusMerchantsByName($name, $shop_type)
    {
        $criteria = new CDbCriteria;
        if (isset($name) && !empty($name)) {
            $criteria->compare('name', $name, true);
        }
        EdjLog::info('shop-type:'.$shop_type.';'.serialize(Dict::items('bonus_shop_type')));
        if (array_key_exists($shop_type, Dict::items('bonus_shop_type'))) {
            $criteria->addCondition('shop_type=' . $shop_type);
        }

        $criteria->order = 'create_time desc';
        return new CActiveDataProvider($this, array(
            'pagination' => array('pageSize' => 50),
            'criteria' => $criteria,
        ));
    }


    /**
     * 保存之前要更新的字段
     * @return bool
     * author cuiluzhe
     */
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->created = Yii::app()->user->getId();
                $this->create_time = date("Y-m-d H:i:s");
            }
            $this->updated = Yii::app()->user->getId();
            $this->update_time = date("Y-m-d H:i:s");
            return true;
        }
        return parent::beforeSave();
    }

    /**
     * 商家信息
     */
    public function findInfo($bonusMerchants_name, $bonusMerchants_id = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('name=:name');
        $criteria->params[':name'] = $bonusMerchants_name;
        if ($bonusMerchants_id != 0) {
            $criteria->addCondition('id!=' . $bonusMerchants_id);
        }
        $bonus = self::model()->find($criteria);
        EdjLog::info(serialize($bonus));
        return $bonus;
    }
}
