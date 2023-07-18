<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-4
 * Time: 下午4:30
 * auther mengtianxue
 */
class ReportFsAccountRp extends ReportActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DailyAccountReport the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return CDbConnection database connection
     */
    public function getDbConnection()
    {
        return Yii::app()->dbreport;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{fs_account_rp}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id, channel, money, account_date, bill_type, created', 'required'),
            array('city_id, channel, bill_type', 'numerical', 'integerOnly' => true),
            array('money, account_date, operator', 'length', 'max' => 20),
            array('remark', 'length', 'max' => 100),
            array('status', 'length', 'max' => 2),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, city_id, channel, status, money, account_date, bill_type, remark, operator, created, updated', 'safe', 'on' => 'search'),
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
            'city_id' => '城市',
            'channel' => '账单渠道',
            'money' => '金额',
            'account_date' => '结账时间',
            'bill_type' => '账单类型',
            'status' => '审核状态',
            'remark' => '备注',
            'operator' => '操作人',
            'created' => '统计时间',
            'updated' => '审核时间',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('city_id', $this->city_id);
        $criteria->compare('channel', $this->channel);
        $criteria->compare('money', $this->money, true);
        $criteria->compare('account_date', $this->account_date, true);
        $criteria->compare('bill_type', $this->bill_type);
        $criteria->compare('status', $this->status);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('operator', $this->operator, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('updated', $this->updated, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}