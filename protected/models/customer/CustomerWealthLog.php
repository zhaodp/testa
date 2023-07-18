 <?php

/**
 * This is the model class for table "{{customer_wealth_log}}".
 *
 * The followings are the available columns in table '{{customer_wealth_log}}':
 * @property integer $id
 * @property string $phone
 * @property integer $type
 * @property integer $wealth
 * @property string $create_time
 * @property string $expire_time
 * @property string $update_time
 */
class CustomerWealthLog extends CActiveRecord
{
    const CASH_TYPE=1;//现金支付
    const RECHARGE_TYPE=2;//账户充值
    const BILL_TYPE=3;//抵扣发票快递费

     public static $typeName= array(
        self::CASH_TYPE => '现金支付',
        self::RECHARGE_TYPE => '账户充值',
        self::BILL_TYPE => '抵扣发票快递费',
        );

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_wealth_log}}';
    }

    /**
     * @return CDbConnection database connection
     */
    public function getDbConnection()
    {
        return Yii::app()->dbreport;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('update_time', 'required'),
            array('type', 'numerical', 'integerOnly'=>true),
            array('phone', 'length', 'max'=>16),
            array('wealth', 'length', 'max'=>8),
            array('id,create_time, expire_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, phone, type, wealth, create_time, expire_time, update_time', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'phone' => 'Phone',
            'type' => 'Type',
            'wealth' => 'Wealth',
            'create_time' => 'Create Time',
            'expire_time' => 'Expire Time',
            'update_time' => 'Update Time',
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

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('wealth',$this->wealth);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('expire_time',$this->expire_time,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CustomerWealthLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
    *   添加消息
    *
    **/
    public function addWealthLog($phone,$type,$wealth){
        $year = date('Y')+1;
        $expire_time = $year.'-12-31 23:59:59'; //过期时间到第二年年底

        $customerWealthLog = new CustomerWealthLog();
        $customerWealthLog_attr = $customerWealthLog->attributes;
        $customerWealthLog_attr['phone'] = $phone;
        $customerWealthLog_attr['type'] = $type;
        $customerWealthLog_attr['wealth'] = $wealth;
        $customerWealthLog_attr['create_time']=date("Y-m-d H:i:s");
        $customerWealthLog_attr['expire_time']=$expire_time;
        $customerWealthLog->attributes = $customerWealthLog_attr;
        return $customerWealthLog->insert();
    }

    /**
    *   获取用户e币列表
    */
    public function getListByphone($phone,$pageNo,$pageSize){
        $criteria = new CDbCriteria();
        $criteria->select = "phone, type, wealth, create_time";
        $criteria->compare('phone', $phone);
        $count = self::model()->count($criteria);
        $criteria->offset = $pageNo * $pageSize;
        $criteria->limit = $pageSize;
        $criteria->order = 'id desc';
        $logs = self::model()->findAll($criteria);

        $ret = array();
        if ($logs) {
            foreach ($logs as $log) {
                $ret[] = array(
                    'money' => $log->wealth > 0 ? '+'.$log->wealth : $log->wealth,
                    'description' => self::$typeName[$log->type],
                    'create_time' => date("Y-m-d H:i",strtotime($log->create_time)));
            }
        }

        return $ret;
    }

}