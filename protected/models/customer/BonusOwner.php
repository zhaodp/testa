<?php

/**
 * This is the model class for table "{{bonus_owner}}".
 *
 * The followings are the available columns in table '{{bonus_owner}}':
 * @property integer $id
 * @property integer $bonus_id
 * @property string $owner_id
 * @property string $area
 * @property string $created
 */
class BonusOwner extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return BonusOwner the static model class
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
        return '{{bonus_owner}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('bonus_id, created', 'required'),
            array('bonus_id', 'numerical', 'integerOnly' => true),
            array('owner_id, area', 'length', 'max' => 20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, bonus_id, owner_id, area, created', 'safe', 'on' => 'search'),
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
            'id' => 'ID',
            'bonus_id' => '优惠码ID',
            'owner_id' => '商家编号',
            'area' => '区域编号',
            'created' => '创建时间',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('bonus_id', $this->bonus_id);
        $criteria->compare('owner_id', $this->owner_id, true);
        $criteria->compare('area', $this->area, true);
        $criteria->compare('created', $this->created, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @param $bonus_id
     * @param $owner
     * @return bool
     * @auther mengtianxue
     */
    public function addOwner($bonus_id, $owner)
    {
        $params = array();
        $params['bonus_id'] = $bonus_id;
        $params['owner'] = $owner;


        return false;
    }

    /**
     * 检查 Owner 是否存在
     * @param $bonus_id
     * @param $owner_id
     * @param $area
     * @return CActiveRecord
     * @auther mengtianxue
     */
    public function checkedOwner($bonus_id, $owner_id, $area)
    {
       return $this->find('bonus_id = :bonus_id and owner_id = :owner_id and area = :area',
            array(':bonus_id' => $bonus_id, ':owner_id' => $owner_id, ':area' => $area));
    }


}