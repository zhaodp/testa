<?php

/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 2015/4/13
 * Time: 16:54
 */
class WxPhone extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{weixin_phone}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('phone', 'length', 'max' => 20),
            array('open_id', 'length', 'max' => 100),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('phone,open_id', 'safe', 'on' => 'search'),
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
            'open_id' => 'open_id',
            'phone' => 'phone',
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
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('phone', $this->phone, true);

        return new CActiveDataProvider('WxPhone', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return EnvelopeAcount the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public static function getOpenIdList($phones)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'open_id,phone';
        $criteria->addInCondition('phone', $phones);
        $result= self::model()->findAll($criteria);
        $data=array();
        if(!empty($result)){
            foreach($result as $re){
                $data[$re->open_id]=$re->phone;
            }
        }
        return $data;
    }
}