<?php

/**
 * This is the model class for table "{{driver_complaint_log}}".
 *
 * The followings are the available columns in table '{{driver_complaint_log}}':
 * @property string $id
 * @property integer $complain_id
 * @property string $driver_id
 * @property string $phone
 * @property string $mark
 * @property string $type
 * @property integer $status
 * @property string $content
 * @property string $operator
 * @property integer $create_time
 */
class DriverComplaintLog extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_complaint_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver_id, phone, operator, create_time', 'required'),
            array('complain_id, status, create_time', 'numerical', 'integerOnly'=>true),
            array('driver_id', 'length', 'max'=>10),
            array('phone', 'length', 'max'=>32),
            array('mark, content', 'length', 'max'=>200),
            array('type', 'length', 'max'=>12),
            array('operator', 'length', 'max'=>20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, complain_id, driver_id, phone, mark, type, status, content, operator, create_time', 'safe', 'on'=>'search'),
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
            'complain_id' => 'Complain',
            'driver_id' => 'Driver',
            'phone' => 'Phone',
            'mark' => 'Mark',
            'type' => 'Type',
            'status' => 'Status',
            'content' => 'Content',
            'operator' => 'Operator',
            'create_time' => 'Create Time',
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
        $criteria->compare('complain_id',$this->complain_id);
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('mark',$this->mark,true);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('create_time',$this->create_time);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TDriverComplaintLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
