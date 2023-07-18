<?php

/**
 * This is the model class for table "{{driver_bank_sign}}".
 *
 * The followings are the available columns in table '{{driver_bank_sign}}':
 * @property integer $id
 * @property string $sign_no
 * @property string $pay_no
 * @property string $pay_name
 * @property string $fees_name
 * @property string $driver_id
 * @property integer $amount
 * @property string $remark
 * @property string $operator
 * @property string $created
 */
class DriverBankSign extends FinanceActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DriverBankSign the static model class
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
        return '{{driver_bank_sign}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('created', 'required'),
            array('amount', 'numerical', 'integerOnly' => true),
            array('sign_no, pay_no', 'length', 'max' => 30),
            array('pay_name, fees_name, operator', 'length', 'max' => 20),
            array('driver_id', 'length', 'max' => 10),
            array('remark', 'length', 'max' => 100),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, sign_no, pay_no, pay_name, fees_name, driver_id, amount, remark, operator, created', 'safe', 'on' => 'search'),
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
            'id' => '序号',
            'sign_no' => '付款签约编号',
            'pay_no' => '付款账号',
            'pay_name' => '付款户名',
            'fees_name' => '缴费户名',
            'driver_id' => '工号',
            'amount' => '金额',
            'remark' => '附言',
            'operator' => '操作人',
            'created' => '创建/修改时间',
        );
    }

    /**
     * 根据sign_no 签约号码获取当前信息
     * Enter description here ...
     * @param unknown_type $sign_no
     */
    public function getPrimaryBysignNO($sign_no)
    {
        return self::find('sign_no = :sign_no', array(':sign_no' => $sign_no));
    }

    /**
     * 根据Driver_id获取 司机签约信息
     * Enter description here ...
     * @param unknown_type $driver_id
     */
    public function getPrimaryByDriverId($driver_id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('driver_id = :driver_id');
        $criteria->order = "id desc";
        $criteria->params = array(':driver_id' => $driver_id);
        return self::find($criteria);
    }

	public function driverBankSave($data)
	{
		if (!empty($data)) {
			$driver = $this->getPrimaryBysignNO($data['0']);
			if ($driver) {
				return 1;
			}
			if (!isset($driver->driver_id)) {
				$driver = new DriverBankSign();
				$driverbank = $driver->attributes;
				$driverbank['sign_no'] = empty($data['0']) ? '' : $data['0'];
				$driverbank['pay_no'] = empty($data['1']) ? '' : $data['1'];
				$driverbank['pay_name'] = empty($data['2']) ? '' : $data['2'];
				$driverbank['fees_name'] = empty($data['3']) ? '' : $data['3'];
				$driverbank['driver_id'] = empty($data['5']) ? '' : $data['5'];
				$driverbank['amount'] = empty($data['6']) ? '' : $data['6'];
				$driverbank['remark'] = empty($data['7']) ? '' : $data['7'];
				$driverbank['operator'] = '系统';
				$driverbank['created'] = date('Y-m-d H:i:s');
				$driver->attributes = $driverbank;
				$driver->insert();
			} elseif ($driver->driver_id != $data['4']) {
				$driverbank = $driver->attributes;
				$driverbank['sign_no'] = empty($data['0']) ? '' : $data['0'];
				$driverbank['pay_no'] = empty($data['1']) ? '' : $data['1'];
				$driverbank['pay_name'] = empty($data['2']) ? '' : $data['2'];
				$driverbank['fees_name'] = empty($data['3']) ? '' : $data['3'];
				$driverbank['driver_id'] = empty($data['5']) ? '' : $data['5'];
				$driverbank['amount'] = empty($data['6']) ? '' : $data['6'];
				$driverbank['remark'] = empty($data['7']) ? '' : $data['7'];
				$driverbank['operator'] = '系统';
				$driverbank['created'] = date('Y-m-d H:i:s');
				$driver->attributes = $driverbank;
				$driver->updateByPk($driver->id, $driver->attributes);
			}
			return 2;
		} else {
			return -1;
		}
	}

    /**
     * 通过sign_no获取driver_id
     * @param $sign_no
     * @return mixed
     * author mengtianxue
     */
    public function getDriverIDBySing_no($sign_no)
    {
		$criteria = new CDbCriteria();
		$criteria->compare('sign_no', $sign_no);
		$model = self::find($criteria);
		return $model->driver_id;
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
        $criteria->compare('sign_no', $this->sign_no, true);
        $criteria->compare('pay_no', $this->pay_no, true);
        $criteria->compare('pay_name', $this->pay_name, true);
        $criteria->compare('fees_name', $this->fees_name, true);
        $criteria->compare('driver_id', $this->driver_id, true);
        $criteria->compare('amount', $this->amount);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('operator', $this->operator, true);
        $criteria->compare('created', $this->created, true);

        return new CActiveDataProvider($this, array(
            'pagination' => array(
                'pageSize' => 50
            ),
            'criteria' => $criteria,
        ));
    }
}
