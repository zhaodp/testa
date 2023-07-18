<?php

/**
 * This is the model class for table "{{driver_balance_log}}".
 *
 * The followings are the available columns in table '{{driver_balance_log}}':
 * @property integer $id
 * @property integer $driver_balance_id
 * @property string $driver_id
 * @property string $name
 * @property integer $city_id
 * @property string $balance
 * @property integer $order_id
 * @property integer $type
 * @property string $operation
 * @property string $created
 */
class DriverBalanceLog extends FinanceActiveRecord
{
    public static $table_name = '';

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DriverBalanceLog the static model class
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
        if (self::$table_name != '') {
            return '{{driver_balance_log_' . self::$table_name . '}}';
        } else {
            return '{{driver_balance_log}}';
        }
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('b_id, driver_id, name, city_id, balance, order_id, type, operation, created', 'required'),
            array('b_id, city_id, order_id, type', 'numerical', 'integerOnly' => true),
            array('driver_id', 'length', 'max' => 10),
            array('name, operation', 'length', 'max' => 20),
            array('balance', 'length', 'max' => 8),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, b_id, driver_id, name, city_id, balance, order_id, type, operation, created', 'safe', 'on' => 'search'),
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
            'b_id' => 'b_id',
            'driver_id' => '司机工号',
            'name' => '司机姓名',
            'city_id' => '城市',
            'balance' => '余额',
            'order_id' => '订单号',
            'type' => '订单类型',
            'operation' => '操作人',
            'created' => '操作时间',
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
        $criteria->compare('b_id', $this->b_id);
        $criteria->compare('driver_id', $this->driver_id);
        $criteria->compare('name', $this->name);
        $criteria->compare('city_id', $this->city_id);
        $criteria->compare('balance', $this->balance);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('type', $this->type);
        $criteria->compare('operation', $this->operation);
        $criteria->compare('created', $this->created);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => '30',
            ),
            'sort' => array(
                'defaultOrder' => array(
                    'id' => CSort::SORT_DESC,
                ),
            ),
        ));
    }

    public function addLog($data)
    {

    }


}
