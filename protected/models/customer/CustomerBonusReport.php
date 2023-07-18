<?php

/**
 * This is the model class for table "{{customer_bonus_report}}".
 *
 * The followings are the available columns in table '{{customer_bonus_report}}':
 * @property integer $id
 * @property string $name
 * @property string $driver_id
 * @property string $bonus_sn
 * @property integer $bonus_count
 * @property integer $used_count
 * @property integer $amount
 * @property integer $report_time
 * @property string $created
 */
class CustomerBonusReport extends ReportActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerBonusReport the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_bonus_report}}';
    }

    /**
     * @return CDbConnection database connection
     */
//    public function getDbConnection()
//    {
//        return Yii::app()->dbreport;
//    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('bonus_count, used_count, amount, report_time, city_id', 'numerical', 'integerOnly' => true),
            array('name, bonus_sn', 'length', 'max' => 20),
            array('driver_id', 'length', 'max' => 10),
            array('created', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, driver_id, city_id, bonus_sn, bonus_count, used_count, amount, report_time, created', 'safe', 'on' => 'search'),
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
            'name' => '司机名字',
            'driver_id' => '司机工号',
            'city_id' => '城市',
            'bonus_sn' => '新客邀请码',
            'bonus_count' => '绑定',
            'used_count' => '使用',
            'amount' => '收入金额',
            'report_time' => '统计日期',
            'created' => 'Created',
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

        $criteria = new CDbCriteria();

        if ($this->city_id != 0) {
            $criteria->compare('city_id', $this->city_id);
        }

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('driver_id', $this->driver_id, true);
        $criteria->compare('bonus_sn', $this->bonus_sn, true);
        $criteria->compare('bonus_count', $this->bonus_count);
        $criteria->compare('used_count', $this->used_count);
        $criteria->compare('amount', $this->amount);
        $criteria->addBetweenCondition('report_time', $this->report_time, $this->created);
        $criteria->select = 'name,driver_id,bonus_sn,sum(bonus_count) as bonus_count,sum(used_count) as used_count,sum(amount) as amount,report_time';
        $criteria->group = 'driver_id';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 30,
            ),
            'sort' => array(
                'defaultOrder' => 'bonus_count DESC',
                'attributes' => array(
                    'bonus_count' => array(
                        'asc' => 'bonus_count',
                        'desc' => 'bonus_count DESC',
                        'default' => 'desc',
                    ),
                    'used_count' => array(
                        'asc' => 'used_count',
                        'desc' => 'used_count DESC',
                        'default' => 'desc',
                    ),
                    'amount' => array(
                        'asc' => 'amount',
                        'desc' => 'amount DESC',
                        'default' => 'desc',
                    ),
                ),

            ),
        ));
    }


    /**
     *
     * 获取各城市指定时间段的订单状态统计（按天)
     */
    public function getGroupByCity($begin_time, $end_time, $city_id)
    {
        $where = 'report_time BETWEEN :begin_time AND :end_time';
        $params = array(
            ':begin_time' => $begin_time,
            ':end_time' => $end_time
        );
        if ($city_id > 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        return Yii::app()->dbreport->createCommand()
            ->select("sum(bonus_count) as bonus_count,sum(used_count) as used_count, report_time")
            ->from("t_customer_bonus_report")
            ->where($where, $params)
            ->group("report_time")
            ->queryAll();
    }

    /**
     * 获取各城市指定时间段的订单状态统计（按条件)
     * @param $begin_time
     * @param $end_time
     * @param $city_id
     * @return mixed
     * author mengtianxue
     */
    public function getBonusReport($begin_time, $end_time, $city_id)
    {
        $where = 'report_time BETWEEN :begin_time AND :end_time';
        $params = array(
            ':begin_time' => $begin_time,
            ':end_time' => $end_time
        );
        if ($city_id > 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        return Yii::app()->dbreport->createCommand()
            ->select("sum(bonus_count) as bonus_count,sum(used_count) as used_count,sum(amount) as amount")
            ->from("t_customer_bonus_report")
            ->where($where, $params)
            ->queryRow();
    }


}