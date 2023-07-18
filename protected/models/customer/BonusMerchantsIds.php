<?php

/**
 * This is the model class for table "{{bonus_merchants_ids}}".
 */
class BonusMerchantsIds extends CActiveRecord
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
        return '{{bonus_merchants_ids}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array();
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
        return array();
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
            return true;
        }
        return parent::beforeSave();
    }

    /**
     *查询某一商家绑定的优惠劵种类
     **/
    public function getBonusNumByMerchantsId($merchantsId)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('bonus_merchants_id=:bonus_merchants_id');
        $criteria->params[':bonus_merchants_id'] = $merchantsId;
        $count = BonusMerchantsIds::model()->count($criteria);
        return $count;

    }

    /**
     *返回该商家绑定的优惠劵id
     **/
    public function getBonusByMerchantsId($merchantsId)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'bonus_id';
        $criteria->addCondition('bonus_merchants_id=:bonus_merchants_id');
        $criteria->params[':bonus_merchants_id'] = $merchantsId;
        $criteria->order = 'create_time desc';
        self::$db = Yii::app()->db_readonly;
        $result = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;
        return $result;
    }

    public function getRelatedTimeByBonusId($bonusId)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('bonus_id=:bonus_id');
        $criteria->params[':bonus_id'] = $bonusId;
        $model = BonusMerchantsIds::model()->find($criteria);
        return $model->create_time;
    }

    public function deleteRelation($id)
    {
        if (!isset($id) || empty($id)) {
            return false;
        }
        $sql = "delete from t_bonus_merchants_ids where bonus_id=:bonus_id";
        return Yii::app()->db->createCommand($sql)->execute(array('bonus_id' => $id));
    }

    public function checkBind($bonusId)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('bonus_id=:bonus_id');
        $criteria->params[':bonus_id'] = $bonusId;
        $model = BonusMerchantsIds::model()->find($criteria);
        return $model;
    }

}
