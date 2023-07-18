<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-4
 * Time: 下午4:38
 * auther mengtianxue
 */
class ReportFsVipTag extends ReportActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DailyAccountTag the static model class
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
        return '{{fs_vip_tag}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id, daily_date, status, created', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('daily_date, start_balance, add_balance, minus_balance, end_balance', 'length', 'max' => 20),
            array('month_date', 'length', 'max' => 10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, city_id, daily_date, month_date, start_balance,add_balance, minus_balance, end_balance, status, created', 'safe', 'on' => 'search'),
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
            'daily_date' => '时间',
            'month_date' => '月',
            'start_balance' => '期初余额',
            'add_balance' => '本期增加',
            'minus_balance' => '本期减少',
            'end_balance' => '期末余额',
            'status' => '状态',
            'created' => '统计时间',
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
        $criteria->compare('daily_date', $this->daily_date, true);
        $criteria->compare('start_balance', $this->start_balance, true);
        $criteria->compare('add_balance', $this->add_balance, true);
        $criteria->compare('minus_balance', $this->minus_balance, true);
        $criteria->compare('end_balance', $this->end_balance, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('created', $this->created, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}