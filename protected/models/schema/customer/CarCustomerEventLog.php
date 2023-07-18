<?php

/**
 * This is the model class for table "{{customer_event_log}}".
 *
 * The followings are the available columns in table '{{customer_event_log}}':
 * @property integer $id
 * @property string $event_id
 * @property string $phone
 * @property string $imei
 * @property string $model
 * @property integer $create_time
 */
class CarCustomerEventLog extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{customer_event_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id', 'required'),
            array('create_time', 'numerical', 'integerOnly' => true),
            array('event_id, phone,model, imei', 'length', 'max' => 32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, phone, imei, model, create_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'event_id' => '事件唯一标识',
            'phone' => '参加此事件的手机号',
            'imei' => '参加此事件的手机IMEI号',
            'model' => '手机类型',
            'create_time' => '创建时间',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('event_id', $this->event_id, true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('imei', $this->imei, true);
        $criteria->compare('model',$this->model,true);
        $criteria->compare('create_time', $this->create_time);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CarCustomerEventLog the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
