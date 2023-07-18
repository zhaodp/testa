<?php

/**
 * This is the model class for table "{{invest_rules}}".
 *
 * The followings are the available columns in table '{{invest_rules}}':
 * @property integer $id
 * @property integer $invest_id
 * @property integer $rule_type
 * @property integer $reply_limit
 * @property integer $send_vip
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $city_id
 * @property integer $send_per_time
 */
class InvestRules extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{invest_rules}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('invest_id, rule_type, reply_limit, send_vip, status, send_per_time', 'numerical', 'integerOnly' => true),
            array('city_id', 'length', 'max' => 1024),
            array('create_time, update_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, invest_id, rule_type, reply_limit, send_vip, status, create_time, update_time, city_id, send_per_time', 'safe', 'on' => 'search'),
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
            'id' => 'ID',
            'invest_id' => '问卷编号',
            'rule_type' => '规则类型',
            'reply_limit' => '回收上限',
            'send_vip' => '是否发送给vip',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'city_id' => '城市id',
            'send_per_time' => '每次发送条数',
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
        $criteria->compare('invest_id', $this->invest_id);
        $criteria->compare('rule_type', $this->rule_type);
        $criteria->compare('reply_limit', $this->reply_limit);
        $criteria->compare('send_vip', $this->send_vip);
        $criteria->compare('status', $this->status);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('city_id', $this->city_id, true);
        $criteria->compare('send_per_time', $this->send_per_time);

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
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return InvestRules the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
