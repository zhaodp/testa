<?php

/**
 * This is the model class for table "{{daily_order_perhour}}".
 *
 * The followings are the available columns in table '{{daily_order_perhour}}':
 * @property integer $id
 * @property integer $day
 * @property integer $city_id
 * @property integer $one
 * @property integer $two
 * @property integer $three
 * @property integer $four
 * @property integer $five
 * @property integer $six
 * @property integer $seven
 * @property integer $eight
 * @property integer $nine
 * @property integer $ten
 * @property integer $eleven
 * @property integer $twelve
 * @property integer $thirteen
 * @property integer $fourteen
 * @property integer $fifteen
 * @property integer $sixteen
 * @property integer $seventeen
 * @property integer $eighteen
 * @property integer $nineteen
 * @property integer $twenty
 * @property integer $twenty_one
 * @property integer $twenty_two
 * @property integer $twenty_three
 * @property integer $twenty_four
 * @property string $update_time
 */
class ReportDailyOrderPerHour extends ReportActiveRecord
{
    public $otherDay = null;                //对比的其他时间
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{daily_order_perhour}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('day, city_id, one, two, three, four, five, six, seven, eight, nine, ten, eleven, twelve, thirteen, fourteen, fifteen, sixteen, seventeen, eighteen, nineteen, twenty, twenty_one, twenty_two, twenty_three, twenty_four', 'numerical', 'integerOnly'=>true),
            array('update_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, day, otherDay, city_id, one, two, three, four, five, six, seven, eight, nine, ten, eleven, twelve, thirteen, fourteen, fifteen, sixteen, seventeen, eighteen, nineteen, twenty, twenty_one, twenty_two, twenty_three, twenty_four, update_time', 'safe', 'on'=>'search'),
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
            'day' => '日期',
            'city_id' => '城市',
            'one' => '1',
            'two' => '2',
            'three' => '3',
            'four' => '4',
            'five' => '5',
            'six' => '6',
            'seven' => '7',
            'eight' => '8',
            'nine' => '9',
            'ten' => '10',
            'eleven' => '11',
            'twelve' => '12',
            'thirteen' => '13',
            'fourteen' => '14',
            'fifteen' => '15',
            'sixteen' => '16',
            'seventeen' => '17',
            'eighteen' => '18',
            'nineteen' => '19',
            'twenty' => '20',
            'twenty_one' => '21',
            'twenty_two' => '22',
            'twenty_three' => '23',
            'twenty_four' => '24',
            'update_time' => 'Update Time',
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
    public function search($extCriteria=null, $pageSize=null)
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('one',$this->one);
        $criteria->compare('two',$this->two);
        $criteria->compare('three',$this->three);
        $criteria->compare('four',$this->four);
        $criteria->compare('five',$this->five);
        $criteria->compare('six',$this->six);
        $criteria->compare('seven',$this->seven);
        $criteria->compare('eight',$this->eight);
        $criteria->compare('nine',$this->nine);
        $criteria->compare('ten',$this->ten);
        $criteria->compare('eleven',$this->eleven);
        $criteria->compare('twelve',$this->twelve);
        $criteria->compare('thirteen',$this->thirteen);
        $criteria->compare('fourteen',$this->fourteen);
        $criteria->compare('fifteen',$this->fifteen);
        $criteria->compare('sixteen',$this->sixteen);
        $criteria->compare('seventeen',$this->seventeen);
        $criteria->compare('eighteen',$this->eighteen);
        $criteria->compare('nineteen',$this->nineteen);
        $criteria->compare('twenty',$this->twenty);
        $criteria->compare('twenty_one',$this->twenty_one);
        $criteria->compare('twenty_two',$this->twenty_two);
        $criteria->compare('twenty_three',$this->twenty_three);
        $criteria->compare('twenty_four',$this->twenty_four);
        $criteria->compare('update_time',$this->update_time,true);
        
        if($this->otherDay !== null){
            //todo对比时间参数
            $this->day = array($this->day, $this->otherDay);
        }
        $criteria->compare('day',$this->day);
                
        if ($extCriteria !== null) {
            $criteria->mergeWith($extCriteria);
        }

        $pagination = array(
            'pageSize' => 10,
        );

        if ($pageSize === 0) {
            $pagination = FALSE;
        }

        $params = array(
            'criteria' => $criteria,
            'pagination' => $pagination,
        );

        return new CActiveDataProvider($this, $params);
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->dbreport;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DailyOrderPerhour the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}