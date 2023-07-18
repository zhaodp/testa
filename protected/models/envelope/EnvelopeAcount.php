<?php

/**
 * This is the model class for table "t_envelope_acount".
 *
 * The followings are the available columns in table 't_envelope_acount':
 * @property string $id
 * @property string $envelope_id
 * @property integer $amount
 * @property integer $num
 * @property string $create_date
 * @property string $last_changed_date
 */
class EnvelopeAcount extends FinanceActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{envelope_acount}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id, acount,last_changed_date', 'required'),
            array('order_id, acount,city_id', 'numerical', 'integerOnly' => true),
            array('day', 'length', 'max' => 20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, order_id,city_id, acount, create_date, last_changed_date, day,driver_id,', 'safe', 'on' => 'search'),
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
            'id' => 'Id',
            'order_id' => '订单ID',
            'driver_id' => '司机ID',
            'city_id' => '城市ID',
            'acount' => '单日司机订单数量',
            'day' => '日期',
            'create_date' => 'Create Date',
            'last_changed_date' => 'Last Changed Date',
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
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);

        $criteria->compare('envelope_id', $this->envelope_id, true);

        $criteria->compare('amount', $this->amount);

        $criteria->compare('num', $this->num);

        $criteria->compare('create_date', $this->create_date, true);

        $criteria->compare('last_changed_date', $this->last_changed_date, true);

        return new CActiveDataProvider('EnvelopeAcount', array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return EnvelopeAcount the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**
     * save 信息
     */
    public function saveInfo($orderId, $driverId, $day, $city_id)
    {
        $result = false;
        $criteria = new CDbCriteria;
        $criteria->addCondition('driver_id=:driver_id');
        $criteria->params[':driver_id'] = $driverId;

        $criteria->addCondition('order_id=:order_id');
        $criteria->params[':order_id'] = $orderId;

        $data = self::model()->count($criteria);
        if ($data==0) {
            $data = new EnvelopeAcount();
            $data->acount = 1;
            $data->create_date = date('Y-m-d H:i:s');
            $data->day = $day;
            $data->driver_id = $driverId;
            $data->city_id = $city_id;
            $data->order_id=$orderId;
            $data->last_changed_date = date('Y-m-d H:i:s');
            $re = $data->save();
            if ($re) {
                $result = true;
            } else {
                EdjLog::info(serialize($data->getErrors()));
            }
        }


        return $result;
    }


    /**
     * 获取该司机当天报单数量
     */
    public function driverOrderNum($driverId, $date_start, $date_end)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'sum(acount) as acount';
        $criteria->addCondition('driver_id=:driver_id');
        $criteria->params[':driver_id'] = $driverId;
        $criteria->addBetweenCondition('day', $date_start, $date_end);
        $data = self::model()->find($criteria);

        return empty($data) ? 0 : $data['acount'];
    }

    /**获取满足条件的司机列表
     * @param array $arr
     * @return mixed
     */
    public function getOrderList($num, $arr, $city_id, $date_start, $date_end)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'driver_id,sum(acount) as acount,city_id,order_id';
        $criteria->addCondition('city_id=:city_id');
        $criteria->params[':city_id'] = $city_id;
        $criteria->addBetweenCondition('day', $date_start, $date_end);
        if (!empty($arr)) {
            $criteria->addNotInCondition('driver_id', $arr);
        }

        $criteria->group = 'driver_id';
        $criteria->having = 'sum(acount)>' . $num;
        $data = self::model()->findAll($criteria);
        return $data;
    }
}