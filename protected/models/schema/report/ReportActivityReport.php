<?php

/**
 * This is the model class for table "{{activity_report}}".
 *
 * The followings are the available columns in table '{{activity_report}}':
 * @property integer $id
 * @property string $city_id
 * @property integer $total_order
 * @property integer $complate_count
 * @property integer $complate_p
 * @property integer $complate_driver_b
 * @property integer $complate_customer_b
 * @property string $order_account
 * @property string $driver_account
 * @property string $company_subsidy
 * @property string $driver_subsidy
 * @property string $customer_subsidy
 * @property integer $day_date
 * @property string $create_date
 */
class ReportActivityReport extends ReportActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{activity_report}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id', 'required'),
            array('total_order, complate_count, complate_p, complate_driver_b, complate_customer_b, day_date', 'numerical', 'integerOnly' => true),
            array('city_id', 'length', 'max' => 10),
            array('driver_name, order_account, driver_account, company_subsidy, driver_subsidy, customer_subsidy', 'length', 'max' => 20),
            array('create_date', 'length', 'max' => 11),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, city_id, total_order, complate_count, complate_p, complate_driver_b, complate_customer_b, order_account, driver_account, company_subsidy, driver_subsidy, customer_subsidy, day_date, create_date', 'safe', 'on' => 'search'),
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
            'city_id' => '城市ID',
            'total_order' => '总订单数',
            'complate_count' => '报单数',
            'complate_p' => '普通报单数',
            'complate_driver_b' => '司机补贴单数',
            'complate_customer_b' => '用户补贴单数',
            'order_account' => '总订单金额',
            'driver_account' => '司机收入',
            'company_subsidy' => '公司补贴',
            'driver_subsidy' => '司机补贴',
            'customer_subsidy' => '用户补贴',
            'day_date' => '时间',
            'create_date' => '创建时间',
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
        if (!empty($this->city_id)) {
            $criteria->compare('city_id', $this->city_id, true);
        }
        $criteria->compare('total_order', $this->total_order);
        $criteria->compare('complate_count', $this->complate_count);
        $criteria->compare('complate_p', $this->complate_p);
        $criteria->compare('complate_driver_b', $this->complate_driver_b);
        $criteria->compare('complate_customer_b', $this->complate_customer_b);
        $criteria->compare('order_account', $this->order_account, true);
        $criteria->compare('driver_account', $this->driver_account, true);
        $criteria->compare('company_subsidy', $this->company_subsidy, true);
        $criteria->compare('driver_subsidy', $this->driver_subsidy, true);
        $criteria->compare('customer_subsidy', $this->customer_subsidy, true);
        $criteria->compare('day_date', $this->day_date);
        $criteria->compare('create_date', $this->create_date, true);
        $criteria->order = "day_date desc";

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 30,
            ),
        ));
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
     * @return ActivityReport the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
