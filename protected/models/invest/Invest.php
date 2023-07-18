<?php

/**
 * This is the model class for table "{{invest}}".
 *
 * The followings are the available columns in table '{{invest}}':
 * @property integer $id
 * @property string $title
 * @property string $des
 * @property integer $status
 * @property integer $coupon
 * @property integer $coupon_code
 * @property integer $reward_coupon
 * @property integer $send_num
 * @property integer $reply_num
 * @property string $create_time
 * @property string $start_time
 * @property string $end_time
 * @property string $update_time
 * @property string $operator
 */
class Invest extends CActiveRecord
{
    // 问卷激活禁用标示常亮
    const INVEST_ACTIVE = 1;
    const INVEST_FORBIDEN = 0;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{invest}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('update_time', 'required'),
            array('status, coupon, coupon_code, reward_coupon, send_num, reply_num', 'numerical', 'integerOnly' => true),
            array('title, des', 'length', 'max' => 255),
            array('operator', 'length', 'max' => 50),
            array('create_time, start_time, end_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, title, des, status, coupon, coupon_code, reward_coupon, send_num, reply_num, create_time, start_time, end_time, update_time, operator', 'safe', 'on' => 'search'),
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
            'id' => '问卷标识',
            'title' => '标题',
            'des' => '问卷描述',
            'status' => '问卷状态',
            'coupon' => '优惠券金额',
            'coupon_code' => '优惠券码',
            'reward_coupon' => 'Reward Coupon',
            'send_num' => '发送次数',
            'reply_num' => '收回次数',
            'create_time' => '创建时间',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'update_time' => 'Update Time',
            'operator' => '创建者',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('des', $this->des, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('coupon', $this->coupon);
        $criteria->compare('coupon_code', $this->coupon_code);
        $criteria->compare('reward_coupon', $this->reward_coupon);
        $criteria->compare('send_num', $this->send_num);
        $criteria->compare('reply_num', $this->reply_num);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('start_time', $this->start_time, true);
        $criteria->compare('end_time', $this->end_time, true);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('operator', $this->operator, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
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
     * 更新活动状态
     * @param $investId
     * @param $status
     */
    public function updateInvestStatus($investId, $status)
    {
        $sql = "UPDATE `t_invest` SET `status` = :status WHERE id = :investId";
        return Yii::app()->db_activity->createCommand($sql)->execute(array(
            ':investId' => $investId,
            ':status' => $status,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Invest the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
