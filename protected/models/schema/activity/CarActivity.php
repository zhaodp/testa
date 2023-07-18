<?php

/**
 * This is the model class for table "{{activity}}".
 *
 * The followings are the available columns in table '{{activity}}':
 * @property integer $id
 * @property string $title
 * @property integer $begin_time
 * @property integer $end_time
 * @property string $start_person
 * @property string $activity_key
 * @property integer $status
 * @property string $extra
 * @property string $remark
 * @property integer $create_time
 * @property string $activity_url
 * @property integer $modify_time
 */
class CarActivity extends FinanceActiveRecord {

    public $bonusSn;    //相关优惠券
    public $bonusWorkTime;    //自领取之日起有效时间（天）

    /**
     * @return string the associated database table name
     */

    public function tableName() {
        return '{{activity}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, begin_time, create_time, modify_time', 'required'),
            array('activity_key', 'required', 'on' => array('insert')),
            array('begin_time, end_time, status, create_time, modify_time', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 60),
            array('start_person', 'length', 'max' => 100),
            array('activity_key', 'length', 'max' => 30),
            array('extra, remark', 'length', 'max' => 1000),
            array('activity_url', 'length', 'max' => 300),
            array('bonusSn', 'length', 'max' => 64),
            array('bonusSn', 'required', 'on' => array('createActivityBonus')),
            array('bonusSn, bonusWorkTime', 'safe'),
            array('bonusWorkTime', 'required', 'on' => array('createActivityBonus')),
            array('bonusWorkTime', 'numerical', 'integerOnly' => true, 'on' => array('createActivityBonus')),
            array('activity_key', 'unique', 'on' => array('createActivityBonus')),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, title, begin_time, end_time, start_person, activity_key, status, extra, remark, create_time, activity_url, modify_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'title' => '活动名称',
            'begin_time' => '活动开始时间',
            'end_time' => '结束时间',
            'start_person' => '活动发起人',
            'activity_key' => '活动标识',
            'status' => '活动有效状态',
            'extra' => '用于存储活动信息json字符串',
            'remark' => '备注',
            'create_time' => '添加时间',
            'activity_url' => '活动页面地址',
            'modify_time' => '最后修改时间',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('begin_time', $this->begin_time);
        $criteria->compare('end_time', $this->end_time);
        $criteria->compare('start_person', $this->start_person, true);
        $criteria->compare('activity_key', $this->activity_key, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('extra', $this->extra, true);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('create_time', $this->create_time);
        $criteria->compare('activity_url', $this->activity_url, true);
        $criteria->compare('modify_time', $this->modify_time);

        $criteria->order = 'id DESC';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CarActivity the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
