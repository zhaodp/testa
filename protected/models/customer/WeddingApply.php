<?php

/**
 * This is the model class for table "{{wedding_apply}}".
 *
 * The followings are the available columns in table '{{wedding_apply}}':
 * @property string $id
 * @property string $name
 * @property string $phone
 * @property integer $wedding_type
 * @property string $run_time
 * @property string $city_id
 * @property string $hotels
 * @property string $detail_site
 * @property integer $number
 * @property string $create_time
 * @property string $mark
 */
class WeddingApply extends CActiveRecord
{

    const WEDDING_TYPE0=0;//婚宴
    const WEDDING_TYPE1=1;//酒会
    const WEDDING_TYPE2=2;//其他

    public static $wedding_types=array(
        '0'=>'婚宴',
        '1'=>'酒会',
        '2'=>'其他',
    );

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{wedding_apply}}';
    }

    public $verifyCode; //验证码

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, phone, wedding_type, detail_site, number, mark, run_time, city_id', 'required'),
            array('wedding_type, number', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>40),
            array('phone', 'length', 'max'=>32),
            array('city_id', 'length', 'max'=>4),
            array('hotels', 'length', 'max'=>50),
            array('detail_site', 'length', 'max'=>200),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, phone, wedding_type, run_time, city_id, hotels, detail_site, number, create_time, mark', 'safe', 'on'=>'search'),
            array (
                'verifyCode',
                'captcha',
                'on'=>'insert',
                'allowEmpty'=>!CCaptcha::checkRequirements(),
                'message'=>'请输入正确的验证码')
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
            'name' => '申请人姓名',
            'phone' => '联系电话',
            'wedding_type' => '宴会类型',
            'run_time' => '宴会举办日期',
            'city_id' => '城市',
            'hotels'=>'举办酒店',
            'detail_site' => '宴会详细地点',
            'number' => '参加人数',
            'create_time' => '申请时间',
            'mark' => '宴会流程、主要内容',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('wedding_type',$this->wedding_type);
        $criteria->compare('run_time',$this->run_time,true);
        $criteria->compare('city_id',$this->city_id,true);
        $criteria->compare('hotels',$this->hotels,true);
        $criteria->compare('detail_site',$this->detail_site,true);
        $criteria->compare('number',$this->number);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('mark',$this->mark,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TWeddingApply the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


}
