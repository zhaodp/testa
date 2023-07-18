<?php

/**
 * This is the model class for table "{{sms_log}}".
 *
 * The followings are the available columns in table '{{sms_log}}':
 * @property integer $id
 * @property string $receiver
 * @property string $message
 * @property string $result
 * @property string $created
 */
class CarSmsLog extends ReportActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{sms_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('receiver, message, result, created', 'required'),
            array('receiver', 'length', 'max'=>20),
            array('message, result', 'length', 'max'=>512),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, receiver, message, result, created', 'safe', 'on'=>'search'),
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
            'id' => '序号',
            'receiver' => '电话号码',
            'message' => '短信内容',
            'result' => '发送状态',
            'created' => '发送时间',
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
        $criteria->compare('receiver',$this->receiver,true);
        $criteria->compare('message',$this->message,true);
        $criteria->compare('result',$this->result,true);
        $criteria->compare('created',$this->created,true);
        $criteria->order = "created desc";

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SmsLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
