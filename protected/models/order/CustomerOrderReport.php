<?php

/**
 * This is the model class for table "{{customer_order_report}}".
 *
 * The followings are the available columns in table '{{customer_order_report}}':
 * @property integer $id
 * @property integer $user_id
 * @property string $phone
 * @property integer $ready
 * @property integer $complate
 * @property integer $cancel
 * @property integer $comfirm
 * @property integer $driver_reject_cancel
 * @property integer $customer_cancel
 * @property integer $driver_reject_no_dispatch
 */
class CustomerOrderReport extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_order_report}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, phone', 'required'),
            array('user_id, complate', 'numerical', 'integerOnly' => true),
            array('phone', 'length', 'max' => 20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, phone, ready, complate, cancel, comfirm, driver_reject_cancel, customer_cancel, driver_reject_no_dispatch', 'safe', 'on' => 'search'),
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
            'user_id' => 'User',
            'phone' => 'Phone',
            'ready' => 'Ready',
            'complate' => 'Complate',
            'cancel' => 'Cancel',
            'comfirm' => 'Comfirm',
            'driver_reject_cancel' => 'Driver Reject Cancel',
            'customer_cancel' => 'Customer Cancel',
            'driver_reject_no_dispatch' => 'Driver Reject No Dispatch',
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
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('ready', $this->ready);
        $criteria->compare('complate', $this->complate);
        $criteria->compare('cancel', $this->cancel);
        $criteria->compare('comfirm', $this->comfirm);
        $criteria->compare('driver_reject_cancel', $this->driver_reject_cancel);
        $criteria->compare('customer_cancel', $this->customer_cancel);
        $criteria->compare('driver_reject_no_dispatch', $this->driver_reject_no_dispatch);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CustomerOrderReport the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 获取用户订单情况
     * @param array $params
     * @return mixed
     * @auther mengtianxue
     */
    public function getCustomerOrder($params = array())
    {
        $where = 'id > 0';
        $where_params = array();
        if (isset($params['user_id'])) {
            $where .= ' and user_id = :user_id';
            $where_params[':user_id'] = $params['user_id'];
        }

        if (isset($params['phone'])) {
            $where .= ' and phone = :phone';
            $where_params[':phone'] = $params['phone'];
        }

        $ret = Yii::app()->db_readonly->createCommand()
            ->select("*")
            ->from("{{customer_order_report}}")
            ->where($where, $where_params)
            ->queryRow();
        return $ret;

    }

    /**
    *   判断客户是否新客
    *
    */
    public function isNewCustomer($phone){
        $params=array(
            'phone'=>$phone,
            );
        $res = $this->getCustomerOrder($params);
        return empty($res);
    }

    /**
     *
     * @param array $params
     * @return mixed
     * @auther mengtianxue
     */
    public function addCustomerOrder($params = array())
    {
        if (isset($params['phone']) && isset($params['user_id'])) {
            $phone = trim($params['phone']);
            $user_id = trim($params['user_id']);
        } else {
            return false;
        }

        $arr = array();
        if ($params)
            $arr['phone'] = $phone;
        $arr['user_id'] = $user_id;
        if (isset($params['complate'])) {
            $complate = $params['complate'];
        } else {
            $complate = 0;
        }
        if (isset($params['app_num'])) {
            $app_num = $params['app_num'];
        } else {
            $app_num = 0;
        }
        if (isset($params['call_num'])) {
            $call_num = $params['call_num'];
        } else {
            $call_num = 0;
        }
        if (isset($params['other_num'])) {
            $other_num = $params['other_num'];
        } else {
            $other_num = 0;
        }
        if (isset($params['first_order_time'])) {
            $first_order_time = $params['first_order_time'];
        } else {
            $first_order_time = 0;
        }
        $arr['complate'] = $complate;
        $customer_order_report = new CustomerOrderReport();
        $customer_order_report['app_num'] = $app_num;
        $customer_order_report['call_num'] = $call_num;
        $customer_order_report['other_num'] = $other_num;
        $customer_order_report['first_order_time'] = $first_order_time;
        $customer_order_report->attributes = $arr;
        if ($customer_order_report->insert()) {
            return true;
        } else {
            return false;
        }

    }


    /**
     * 修改订单计数
     * @param array $update_arr
     * @param $phone
     * @return bool
     * @auther mengtianxue
     */
    public function updateCustomerOrder($update_arr = array(), $phone)
    {
        if (empty($update_arr) && empty($phone)) {
            return false;
        }

        $ret_num = self::model()->updateCounters($update_arr, 'phone = :phone', array(':phone' => $phone));
        if ($ret_num) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 判断客户是否为app新客
     * @param $phone
     */
    public function isNewAppCustomer($phone){
        $command = Yii::app()->db->createCommand();
        $rows = $command
            ->select("phone,app_num")
            ->from("t_customer_order_report")
            ->where("phone =:phone",
                array(':phone'=>$phone))
            ->queryAll();
        if(count($rows) == 0){
            return true;
        }elseif($rows[0]['app_num'] == 0){
            return true;
        }
        return false;
    }

}
