<?php

/**
 * This is the model class for table "{{driver_bank_result}}".
 *
 * The followings are the available columns in table '{{driver_bank_result}}':
 * @property integer $id
 * @property string $sign_no
 * @property string $pay_no
 * @property string $pay_name
 * @property string $fees_name
 * @property string $driver_id
 * @property integer $amount
 * @property string $remark
 * @property string $result
 * @property string $error_reason
 * @property string $operator
 * @property string $created
 */
class DriverBankResult extends FinanceActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DriverBankResult the static model class
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
        return '{{driver_bank_result}}';
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
            array('remark, result, error_reason', 'length', 'max' => 100),
            array('static', 'length', 'max' => 1),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, sign_no, pay_no, pay_name, fees_name, driver_id, amount, remark, result, status, error_reason, operator, created', 'safe', 'on' => 'search'),
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
            'result' => '返回结果',
            'error_reason' => '错误日志',
            'status' => '状态',
            'operator' => '操作人',
            'created' => '创建/修改时间',
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
        $criteria->compare('sign_no', $this->sign_no, true);
        $criteria->compare('pay_no', $this->pay_no, true);
        $criteria->compare('pay_name', $this->pay_name, true);
        $criteria->compare('fees_name', $this->fees_name, true);
        $criteria->compare('driver_id', $this->driver_id, true);
        $criteria->compare('amount', $this->amount);
        $criteria->compare('remark', $this->remark, true);
        $criteria->compare('result', $this->result, true);
        $criteria->compare('error_reason', $this->error_reason, true);
        $criteria->compare('operator', $this->operator, true);
        $criteria->compare('status', $this->status, true);
        if ($this->created) {
            $start_time = $this->created;
            $end_time = $this->created . ' 23:59:59';
            $criteria->addBetweenCondition('created', $start_time, $end_time);
        }

//		$criteria->compare('created',$this->created,true);
        $criteria->order = 'result desc,status asc';
        return new CActiveDataProvider($this, array(
            'pagination' => array(
                'pageSize' => 50
            ),
            'criteria' => $criteria,
        ));
    }

    /**
     * 根据sign_no 签约号码获取当前信息
     * Enter description here ...
     * @param unknown_type $sign_no
     */
    public function getPrimaryBysignNO($sign_no)
    {
        $created = date('Y-m-d H:i:s', time() - 7200);
        return self::find('sign_no = :sign_no and created > :created', array(':sign_no' => $sign_no, ':created' => $created));
    }

    public function driverBankSave($data)
    {
        $driver = $this->getPrimaryBysignNO($data['2']);
        if (empty($driver)) {
            $driver = new DriverBankResult();
            $driverbank = $driver->attributes;
            $driverbank['sign_no'] = $data['2'];
            $driverbank['pay_no'] = $data['3'];
            $driverbank['pay_name'] = $data['4'];
            $driverbank['fees_name'] = $data['5'];
            $driverbank['driver_id'] = $data['9'];
            $driverbank['amount'] = $data['7'];
            $driverbank['remark'] = $data['10'];
            $driverbank['result'] = $data['11'];
            $driverbank['error_reason'] = $data['12'];
            $driverbank['operator'] = Yii::app()->user->getId();
            $driverbank['created'] = date('Y-m-d H:i:s');
            $driver->attributes = $driverbank;
            $driver->insert();
        }
    }

    /**
     * 充值
     * @param $id
     * @return bool
     * author mengtianxue
     */
    public function Recharges($id)
    {
        $driverBankResult = new DriverBankResult();

        $data = array();
        //检查id是否为空
        if (empty($id)) {
            return false;
        }

        //1.检查是否是 已入账成功 但未充值的
        $driverBankResultReturn = $driverBankResult->find('id = :id and result = :result and status = :status',
            array(':id' => $id, ':result' => '入账成功', ':status' => 0));
        if (empty($driverBankResultReturn)) {
            return false;
        }

        //划款信息
        $data['id'] = $id;
        $data['user'] = strtoupper($driverBankResultReturn->driver_id);
        $data['cast'] = $driverBankResultReturn->amount;
        $data['comment'] = "交行划款";
        $data['channel'] = 11;
        $data['city_id'] = DriverStatus::model()->getItem($data['user'],'city_id');


        //检查1小时内是否充过信息费（防止重复充值记录)
        $findEmployeeaccount = EmployeeAccount::model()->getDriverRecharge($data);
        if (!empty($findEmployeeaccount)) {
            return false;
        }
        //充值
//        $employee = EmployeeAccount::model()->driverRecharge($data);
        $employee = OrderSettlement::model()->driverRecharge($data);
        if ($employee) {
            //修改 driverBankResult  划款状态
            $driverBankResult->updateByPk($id, array('status' => 1));

            //如果是屏蔽状态 把用户状态改成正常状态
            $this->activationDriver($data);

           return true;
        }
    }


    /**
     * 激活司机
     * @param $data  必须包扣 user、comment
     * @return bool
     * author mengtianxue
     */
    public function activationDriver($data){
        //检查用户状态 如果是屏蔽状态 把用户状态改成正常状态
        $driver = Driver::getProfile($data['user']);
        if ($driver->mark != Driver::MARK_LEAVE && $driver->mark != Driver::MARK_ENABLE) {
            //把司机状态置为正常状态
            Driver::model()->block($data['user'], Driver::MARK_ENABLE, DriverLog::LOG_MARK_ENABLE, $data['comment'], true);
            //更新redis
            DriverAccountService::reloadRedisDriverBalance($data['user']);
            return true;
        }
        return false;
    }
}
