<?php

/**
 * This is the model class for table "{{demo}}".
 *
 * The followings are the available columns in table '{{demo}}':
 * @property integer $id
 * @property string $name
 * @property string $age
 * @property string $phone
 * @property string $email
 */
class CarDemo extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{demo}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            
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
            'user_id' => 'User',
            'name' => 'Name',
            'pass' => 'Pass',
            'phone' => 'Phone',
            'email' => 'Email',
        );
    }

}