<?php

/**
 * This is the model class for table "{{driver_examnew_log}}".
 *
 * The followings are the available columns in table '{{driver_examnew_log}}':
 * @property integer $id
 * @property string $open_id
 * @property integer $question_id
 * @property string $answer
 * @property string $user_answer
 * @property integer $result
 * @property integer $type
 * @property string $update_time
 * @property string $create_time
 */
class DriverExamnewLog extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_examnew_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('question_id,open_id,answer', 'required'),
            array('question_id, result, type', 'numerical', 'integerOnly'=>true),
            array('open_id', 'length', 'max'=>64),
            array('answer, user_answer', 'length', 'max'=>16),
            array('create_time', 'safe'),
            // The following rule is used by search().
            array('open_id, question_id, answer, user_answer, result, type', 'safe', 'on'=>'search'),
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
            'open_id' => '微信open_id',
            'question_id' => 'Question',
            'answer' => '标准答案',
            'user_answer' => '司机的答案',
            'result' => '结果',
            'type' => '类型',
            'update_time' => 'Update Time',
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

        $criteria=new CDbCriteria;

        $criteria->compare('open_id',$this->open_id,true);
        $criteria->compare('question_id',$this->question_id);
        $criteria->compare('answer',$this->answer,true);
        $criteria->compare('user_answer',$this->user_answer,true);
        $criteria->compare('result',$this->result);
        $criteria->compare('type',$this->type);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverExamnewLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}