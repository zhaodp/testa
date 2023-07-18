<?php

/**
 * This is the model class for table "sys_qname_manager".
 *
 * The followings are the available columns in table 'sys_qname_manager':
 * @property integer $id
 * @property string $base_qname
 * @property string $hash_qname
 * @property integer $level
 * @property integer $max
 * @property string $owner
 */
class QNameManager extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'sys_qname_manager';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('base_qname, hash_qname, level, owner', 'required'),
            array('level, max', 'numerical', 'integerOnly'=>true),
            array('base_qname, hash_qname', 'length', 'max'=>128),
            array('owner', 'length', 'max'=>64),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, base_qname, hash_qname, level, max, owner', 'safe', 'on'=>'search'),
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
            'base_qname' => '队列名称',
            'hash_qname' => 'Hash队列名称',
            'level' => '报警级别',
            'max' => '报警阈值',
            'owner' => '责任人',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('base_qname',$this->base_qname,true);
        $criteria->compare('hash_qname',$this->hash_qname,true);
        $criteria->compare('level',$this->level);
        $criteria->compare('max',$this->max);
        $criteria->compare('owner',$this->owner,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return QnameManager the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getDbConnection()
    {
        return Yii::app()->dbsys;
    }
}
