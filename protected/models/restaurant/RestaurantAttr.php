<?php

/**
 * This is the model class for table "{{restaurant_attr}}".
 *
 * The followings are the available columns in table '{{restaurant_attr}}':
 * @property integer $id
 * @property integer $restaurant_id
 * @property string $keyword
 * @property string $value
 * @property integer $created
 */
class RestaurantAttr extends CActiveRecord
{

    //渠道类型
    public static $channel_type = array(
        'self' => 0, //自有
        'vintners' => 1, //酒商
    );

    public static $channel_type_name = array(
        '0'=>'自有',
        '1'=>'酒商',
    );

    //是否有竞品物料(竞品概况)
    public static $has_competition = array(
        '0'=> '无',
        '1'=> '有',
    );

    //竞品物料是否已清除
    public static  $has_competition_wiped = array(
        '0'=> '尚未清除',
        '1'=> '全部清除',
    );

    //是否已进店(物料概况)
    public static $has_materials = array(
        '0' =>  '未进店',
        '1' =>  '己进店',
    );


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{restaurant_attr}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('restaurant_id, keyword, value, created', 'required'),
            array('restaurant_id, created', 'numerical', 'integerOnly'=>true),
            array('keyword', 'length', 'max'=>20),
            array('value', 'length', 'max'=>200),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, restaurant_id, keyword, value, created', 'safe', 'on'=>'search'),
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
            'restaurant_id' => 'Restaurant',
            'keyword' => 'Keyword',
            'value' => 'Value',
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
        $criteria->compare('restaurant_id',$this->restaurant_id);
        $criteria->compare('keyword',$this->keyword,true);
        $criteria->compare('value',$this->value,true);
        $criteria->compare('created',$this->created);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RestaurantAttr the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 保存属性
     */
    public function insertAttInfo($params , $condition){
        if(empty($params)){
            return array();
        }
        $value = "";
        foreach($params as $key=>$val){
            if(!empty($val)){
                if($key == 'restaurant_info'){
                    $channel_type = isset(self::$channel_type[$val['channel_type']]) ? self::$channel_type[$val['channel_type']] : "self";
                    $value .= $channel_type.",".$val['has_competition'].",".$val['has_competition_wiped'].",".$val['has_materials'];
                }else{
                    //物料详情
                    $value = implode(",",$val);
                }
                $data = array(
                    'restaurant_id' => $condition['restaurant_id'],
                    'keyword' => $key,
                    'value' => $value,
                    'created' => $condition['created'],
                );
                Yii::app()->db->createCommand()->insert('{{restaurant_attr}}',$data);
                $value = "";
            }
        }
        return true;

    }
}