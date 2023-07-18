<?php

/**
 * This is the model class for table "{{restaurant_token}}".
 *
 * The followings are the available columns in table '{{restaurant_token}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $authtoken
 * @property string $created
 */
class RestaurantToken extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{restaurant_token}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, authtoken, created', 'required'),
            array('user_id', 'numerical', 'integerOnly'=>true),
            array('authtoken', 'length', 'max'=>32),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, authtoken, created', 'safe', 'on'=>'search'),
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
            'user_id' => 'User',
            'authtoken' => 'Authtoken',
            'created' => 'Created',
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
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('authtoken',$this->authtoken,true);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RestaurantToken the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 验证手机号
     * @param string $phone
     * @return mixed
     */
    public function checkPhone($phone = ""){
        $user = Yii::app()->db_readonly->createCommand()
            ->select("user_id,name,phone,status")
            ->from("t_admin_user")
            ->where("phone = :phone",
                array(':phone' => trim($phone)))
            ->queryRow();
        return $user;
    }


    /**
     * 验证TOKEN
     * @param $token
     */
    public function validateToken($token){
        $ret = $this->find('authtoken = :token',array('token'=>$token));
        return $ret;
    }


}