<?php
/**
 * This is the model class for table "{{customer_main}}".
 *
 * The followings are the available columns in table '{{customer_main}}':
 * @property integer $id
 * @property string $name
 * @property integer $gender
 * @property string $birthday
 * @property string $phone
 * @property string $email
 * @property integer $city_id
 * @property string $credit
 * @property string $activity
 * @property integer $type
 * @property integer $account_type
 * @property string $vip_card
 * @property integer $bill_receive_mode
 * @property integer $status
 * @property string $source
 * @property string $remark
 * @property string $operator
 * @property string $create_time
 * @property string $update_time
 */
class CarCustomerMain extends CActiveRecord
{
    //性别
    const CUSTOMER_DEFAULT=0;
    const CUSTOMER_MALE=1;
    const CUSTOMER_FEMALE=2;

    static $customer_gender=array(
        self::CUSTOMER_DEFAULT => '未知',
        self::CUSTOMER_MALE=>'男',
        self::CUSTOMER_FEMALE=>'女',
    );

    //用户类型 1.个人、2.企业
    const CUSTOMER_TYPE_D=0;
    const CUSTOMER_TYPE_P=1;
    const CUSTOMER_TYPE_E=2;

    static $customer_type=array(
        self::CUSTOMER_TYPE_D=>'普通',
        self::CUSTOMER_TYPE_P=>'个人',
        self::CUSTOMER_TYPE_E=>'企业',
    );

    //用户账户类型 1.普通 2.主卡 3.副卡
    const CUSTOMER_ACCOUNT_TYPE_D=0;
    const CUSTOMER_ACCOUNT_TYPE_F=1;
    const CUSTOMER_ACCOUNT_TYPE_S=2;
    const CUSTOMER_ACCOUNT_TYPE_T=3;

    static $customer_account_type=array(
        self::CUSTOMER_ACCOUNT_TYPE_D=>'普通',
        self::CUSTOMER_ACCOUNT_TYPE_F=>'普通',
        self::CUSTOMER_ACCOUNT_TYPE_S=>'主卡',
        self::CUSTOMER_ACCOUNT_TYPE_T=>'副卡',
    );

    //账单接受方式 0.默认   1.短信 2.邮件 3.短信邮件 bill_receive_mode
    const BILL_RECEIVE_SMS=1;
    const BILL_RECEIVE_EMAIL=2;
    const BILL_RECEIVE_SMS_EMAIL=3;

    static $bill_receive_mode=array(
        self::BILL_RECEIVE_SMS=>'短信',
        self::BILL_RECEIVE_EMAIL=>'邮件',
        self::BILL_RECEIVE_SMS_EMAIL=>'短信&邮件',
    );

    //用户状态 0.默认 1.正常 2.屏蔽 3.删除
    const CUSTOMER_STATUS_D=0;
    const CUSTOMER_STATUS_F=1;
    const CUSTOMER_STATUS_S=2;
    const CUSTOMER_STATUS_T=3;

    static $customer_status=array(
        self::CUSTOMER_STATUS_D=>'正常',
        self::CUSTOMER_STATUS_F=>'正常',
        self::CUSTOMER_STATUS_S=>'屏蔽',
        self::CUSTOMER_STATUS_T=>'删除',
    );

    //用户来源 1.APP
    const CUSTOMER_SOURCE_F=1;
    static $customer_source=array(
        self::CUSTOMER_SOURCE_F=>'APP',
    );



    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_main}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('phone', 'required'),
            array('gender, city_id, type, account_type, bill_receive_mode, status', 'numerical', 'integerOnly'=>true),
            array('name, email, vip_card, operator', 'length', 'max'=>50),
            array('phone', 'length', 'max'=>32),
            array('activity', 'length', 'max'=>10),
            array('source', 'length', 'max'=>30),
            array('remark', 'length', 'max'=>100),
            array('birthday, update_time, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, gender, birthday, phone, email, city_id, activity, type, account_type, vip_card, bill_receive_mode, status, source, remark, operator, create_time, update_time', 'safe', 'on'=>'search'),
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
            'name' => '姓名',
            'gender' => '性别',
            'birthday' => '生日',
            'phone' => '手机',
            'email' => 'Email',
            'city_id' => '城市',
            'activity' => '活跃度',
            'type' => '类型',
            'account_type' => '账户类型',
            'vip_card' => 'VIP卡',
            'bill_receive_mode' => '账单接收方式',
            'status' => '状态',
            'source' => '来源',
            'remark' => '备注',
            'operator' => '操作人',
            'create_time' => '创建时间',
            'update_time' => '操作时间',
             'amount'=>'账户余额',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('gender',$this->gender);
        $criteria->compare('birthday',$this->birthday,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('activity',$this->activity,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('account_type',$this->account_type);
        $criteria->compare('vip_card',$this->vip_card,true);
        $criteria->compare('bill_receive_mode',$this->bill_receive_mode);
        $criteria->compare('status',$this->status);
        $criteria->compare('source',$this->source,true);
        $criteria->compare('remark',$this->remark,true);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CustomerMain the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
