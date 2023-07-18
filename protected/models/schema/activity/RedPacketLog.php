<?php

/**
 * This is the model class for table "{{red_packet_log}}".
 *
 * The followings are the available columns in table '{{red_packet_log}}':
 * @property integer $id
 * @property string $phone
 * @property integer $rp_id
 * @property integer $is_new_customer
 * @property integer $share_type
 * @property integer $money
 * @property integer $status
 * @property string $create_time
 */
class RedPacketLog extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{red_packet_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('phone,rp_id,share_type', 'required'),
            array('rp_id, is_new_customer, share_type, money, status', 'numerical', 'integerOnly'=>true),
            array('phone', 'length', 'max'=>15),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, phone, rp_id, is_new_customer, share_type, money, status, create_time', 'safe', 'on'=>'search'),
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
            'rp_id' => 'Rp',
            'is_new_customer' => 'Is New Customer',
            'share_type' => 'Share Type',
            'money' => 'Money',
            'status' => 'Status',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('rp_id',$this->rp_id);
        $criteria->compare('is_new_customer',$this->is_new_customer);
        $criteria->compare('share_type',$this->share_type);
        $criteria->compare('money',$this->money);
        $criteria->compare('status',$this->status);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db_activity;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RedPacketLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}