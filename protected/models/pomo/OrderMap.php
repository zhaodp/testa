<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 2015/4/8
 * Time: 15:33
 */

class OrderMap extends PomoActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{order_map}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id,city_id', 'numerical', 'integerOnly' => true),
            array('day', 'length', 'max' => 20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('order_id,city_id,lng,lat,day', 'safe', 'on' => 'search'),
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
            'order_id' => '订单ID',
            'city_id' => 'city_id',
            'lng' => '纬度',
            'lat' => '经度',
            'day' => '日期',
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
        $criteria->compare('order_id', $this->order_id, true);

        return new CActiveDataProvider('OrderMap', array(
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

}