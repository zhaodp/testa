<?php

/**
 * This is the model class for table "{{red_packet}}".
 *
 * The followings are the available columns in table '{{red_packet}}':
 * @property integer $id
 * @property string $token
 * @property string $phone
 * @property integer $order_id
 * @property integer $type
 * @property integer $share_times
 * @property string $update_time
 * @property string $create_time
 */
class RedPacket extends CActiveRecord
{

    CONST TYPE_ORDER = 1;
    CONST TYPE_TOKEN = 0;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{red_packet}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type,create_time', 'required'),
            array('order_id, type, share_times', 'numerical', 'integerOnly'=>true),
            array('token', 'length', 'max'=>64),
            array('phone', 'length', 'max'=>15),
            array('qr_code', 'length', 'max'=>255),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('token, phone, order_id, type, share_times', 'safe', 'on'=>'search'),
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
            'token' => 'Token',
            'phone' => 'Phone',
            'order_id' => 'Order',
            'type' => 'Type',
            'qr_code'=> 'qr_code',
            'share_times' => 'Share Times',
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
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;


        $criteria->compare('token',$this->token,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('type',$this->type);
        $criteria->compare('share_times',$this->share_times);


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
     * @return RedPacket the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}