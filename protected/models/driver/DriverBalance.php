<?php

/**
 * This is the model class for table "{{driver_balance}}".
 *
 * The followings are the available columns in table '{{driver_balance}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $name
 * @property integer $city_id
 * @property string $balance
 */
class DriverBalance extends FinanceActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DriverBalance the static model class
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
        return '{{driver_balance}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver_id, name, city_id, balance', 'required'),
            array('city_id', 'numerical', 'integerOnly' => true),
            array('driver_id', 'length', 'max' => 10),
            array('name', 'length', 'max' => 20),
            array('balance', 'length', 'max' => 8),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, driver_id, name, city_id, balance', 'safe', 'on' => 'search'),
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
            'driver_id' => '司机工号',
            'name' => '司机姓名',
            'city_id' => '城市',
            'balance' => '余额',
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
        $criteria->compare('driver_id', $this->driver_id);
        $criteria->compare('name', $this->name);
        if ($this->city_id != 0) {
            $criteria->compare('city_id', $this->city_id);
        }
        $criteria->compare('balance', $this->balance);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 30
            ),
        ));
    }

    /**
     * 修改师傅信息费  $type为0   是司机收入，不需要更改师傅的余额
     * @param $data array(balance,driver_id,order_id,type)
     * @param int $type
     * @return bool|string
     * author mengtianxue
     */
    public function updateBalance($data, $type = 0)
    {
		EdjLog::info(' driver balance updateBalance call at'.time().' and input args is'.serialize($data));
        if (empty($data)) {
            return false;
        }


        if ($type != 0) {
            $driver_attr = array();
            $attr = array('balance' => $data['cast']);
            $driverBalance = $this->find('driver_id = :driver_id', array(':driver_id' => $data['user']));

			EdjLog::info(' driver balance --- update '.serialize($driverBalance));
            //用户存在 修改记录; 用户不存在 保存数据
            if ($driverBalance) {

                //修改余额
                $num = $this->updateCounters($attr, 'driver_id = :driver_id', array(':driver_id' => $data['user']));
				EdjLog::info('have driver balance and update counter return num is '.$num);
                // 添加log记录
                $driver_attr = $driverBalance->attributes;
                $driver_attr['b_id'] = $driver_attr['id'];
                unset($driver_attr['id']);
                $driver_attr['balance'] = $driver_attr['balance'] + $data['cast'];

            } else {

                $driver = Driver::model()->find('user = :user', array(':user' => $data['user']));
                if ($driver) {
                    $driver_attr = array(
                        'driver_id' => $data['user'],
                        'name' => $driver->name,
                        'city_id' => $driver->city_id,
                        'balance' => $data['cast'],
                    );
                    DriverBalanceLog::$table_name = date('Ym');
                    $driverBalance = new DriverBalance();
                    $driverBalance->refreshMetaData();
                    $driverBalance->setAttributes($driver_attr);
                    $driverBalance->insert();
                    $driver_attr['id'] = $driverBalance->id;
                }

            }

            if ($driver_attr) {
                $driver_attr['order_id'] = isset($data['order_id']) ? $data['order_id'] : '';
                $driver_attr['type'] = $data['type'];
                $driver_attr['operation'] = isset($data['comment']) ? $data['comment'] :'系统扣费';
                $driver_attr['created'] = date('Y-m-d H:i:s');

                DriverBalanceLog::$table_name = date('Ym');
                $driverBalanceLog = new DriverBalanceLog();
                $driverBalance->refreshMetaData();
                $driverBalanceLog->setAttributes($driver_attr);
                $driverBalanceLog->insert();
                return $driver_attr['balance'];
            }
        } else {
            $driverBalance = $this->find('driver_id = :driver_id', array(':driver_id' => $data['user']));
            if ($driverBalance) {
                return $driverBalance->balance;
            }
        }
        return false;
    }


    /**
     * 获取司机资料
     * @param $driver_id
     * @return array|CActiveRecord|mixed|null
     * author mengtianxue
     */
    public function getDriverBalance($driver_id)
    {
        return $this->find('driver_id = :driver_id',
            array(':driver_id' => $driver_id));
    }


    /**
     * 新司机签约的时候初始化信息
     * @param $user
     * @return bool
     * author mengtianxue
     */
    public function AddDriverBalance($user)
    {
        $driverBalance = array();
        $driverBalance['user'] = $user;
        $driverBalance['cast'] = $this->getDriverBalance($user);
        $driverBalance['order_id'] = 0;
        $driverBalance['type'] = EmployeeAccount::TYPE_INFOMATION;
        $back = Driverbalance::model()->updateBalance($driverBalance, $driverBalance['type']);
        if ($back) {
            return true;
        } else {
            return false;
        }

    }


    public function getBalance($city_id = 0, $driver_id = '')
    {
        $where = "1 = 1 ";
        $params = array();
        if($city_id != 0){
            $where .= " and city_id = :city_id ";
            $params[':city_id'] = $city_id;
        }

        if($driver_id != ''){
            $where .= " and driver_id = :driver_id";
            $params[':driver_id'] = $driver_id;
        }else{
            $where .= " and driver_id not in (" . Common::getTestDriverID() . ") ";
        }

        return Yii::app()->db_finance->createCommand()
            ->select("sum(balance) as balance")
            ->from("t_driver_balance")
            ->where($where, $params)
            ->queryScalar();
    }
    
    public function dealDriverBalance($driver_id,$rate){
        $num = $this->updateCounters(array('balance'=>-$rate), 'driver_id = :driver_id', array(':driver_id' => $driver_id));
        EdjLog::info('have driver balance and update counter return num is '.$num.' driver_id '.$driver_id.' rate '.$rate);
   }

}
