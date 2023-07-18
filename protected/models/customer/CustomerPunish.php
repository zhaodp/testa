<?php

/**
 * This is the model class for table "{{customer_punish}}".
 *
 * The followings are the available columns in table '{{customer_punish}}':
 * @property string $id
 * @property string $phone
 * @property string $reason
 * @property integer $statue
 * @property integer $limit_time
 * @property string $un_punish_time
 * @property string $mark
 * @property string $operator
 * @property integer $create_time
 * @property integer $update_time
 */
class CustomerPunish extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_punish}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('phone, reason, statue, operator, create_time', 'required'),
            array('statue, limit_time, create_time, update_time', 'numerical', 'integerOnly'=>true),
            array('phone', 'length', 'max'=>32),
            array('reason', 'length', 'max'=>10),
            array('mark', 'length', 'max'=>200),
            array('operator', 'length', 'max'=>20),
            array('un_punish_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, phone, reason, statue, limit_time, un_punish_time, mark, operator, create_time, update_time', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'phone' => 'Phone',
            'reason' => 'Reason',
            'statue' => 'Statue',
            'limit_time' => 'Limit Time',
            'un_punish_time' => 'Un Punish Time',
            'mark' => 'Mark',
            'operator' => 'Operator',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('reason',$this->reason,true);
        $criteria->compare('statue',$this->statue);
        $criteria->compare('limit_time',$this->limit_time);
        $criteria->compare('un_punish_time',$this->un_punish_time,true);
        $criteria->compare('mark',$this->mark,true);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('create_time',$this->create_time);
        $criteria->compare('update_time',$this->update_time);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TCustomerPunish the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
