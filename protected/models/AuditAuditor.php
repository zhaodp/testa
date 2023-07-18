<?php

/**
 * This is the model class for table "{{audit_auditor}}".
 *
 * The followings are the available columns in table '{{audit_auditor}}':
 * @property integer $id
 * @property integer $audit_id
 * @property string $auditor
 */
class AuditAuditor extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{audit_auditor}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('audit_id', 'numerical', 'integerOnly'=>true),
            array('auditor', 'length', 'max'=>32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, audit_id, auditor', 'safe', 'on'=>'search'),
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
            'id' => 'id',
            'audit_id' => '审核id，t_audit_action主键',
            'auditor' => '审核员姓名',
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
        $criteria->compare('audit_id',$this->audit_id);
        $criteria->compare('auditor',$this->auditor,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->dbadmin;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AuditAuditor the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}