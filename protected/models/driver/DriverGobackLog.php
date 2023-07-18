<?php
/**
 * 结伴返城Log
 * User: zhanglimin
 * Date: 13-7-30
 * Time: 下午2:15

 * This is the model class for table "{{driver_goback_log}}".
 *
 * The followings are the available columns in table '{{driver_goback_log}}':
 * @property string $id
 * @property string $driver_id
 * @property integer $goback
 * @property string $lng
 * @property string $lat
 * @property integer $status
 * @property string $created
 */
class DriverGobackLog extends CActiveRecord
{
    //是否结伴返城
    public static $goback = array(
        0 => '否',
        1 => '是',
    );

    //返城状态
    public static $goback_stauts = array(
        0 => '空闲状态',
        2 => '下班状态',
    );


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_goback_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('status, created', 'required'),
            array('goback, status', 'numerical', 'integerOnly'=>true),
            array('driver_id, lng, lat', 'length', 'max'=>10),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, driver_id, goback, lng, lat, status, created', 'safe', 'on'=>'search'),
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
            'driver_id' => '司机工号',
            'goback' => '是否返程',
            'lng' => '经度',
            'lat' => '纬度',
            'status' => '状态',
            'created' => '创建时间',
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
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('goback',$this->goback);
        $criteria->compare('lng',$this->lng,true);
        $criteria->compare('lat',$this->lat,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverGobackLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    public function insertInfo($params = array()){
        if(empty($params)){
            return false;
        }
        $params['created'] = date("Y-m-d H:i:s");
        return Yii::app()->db->createCommand()->insert('{{driver_goback_log}}',$params);
    }

    /**
     * 获取当前地址
     * @param string $lng
     * @param string $lat
     * @return string
     */
    public function getAddress($lng = "" , $lat = ""){
        if(empty($lng) || empty($lat)){
            return "";
        }
        return GPS::model()->getStreetByBaiduGPS($lng, $lat);
    }
}