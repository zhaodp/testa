<?php

/**
 * 客户司机补偿扣款流水表
 * This is the model class for table "{{customer_complain_recoup}}".
 *
 * The followings are the available columns in table '{{customer_complain_recoup}}':
 * @property integer $id
 * @property integer $complain_id
 * @property integer $recoup_type
 * @property string $customer
 * @property string $driver_id
 * @property string $recoup_customer
 * @property integer $amount_customer
* @property string $recoup_driver
 * @property integer $amount_driver
 * @property integer $process_type
 * @property integer $status
 * @property string $mark
 * @property string $created
 * @property string $operator
 * @property string $create_time
 * @property string $update_time
 */
class CustomerComplainRecoup extends CActiveRecord
{

    //status 0:未处理 1:已处理 2:已驳回
    const STATUS_UNHANDLED='0';
    const STATUS_FINISH='1';
    const STATUS_REJECT='2';

    const RECOUP_TYPE1=1;   //现金
    const RECOUP_TYPE2=2;   //优惠券

    const PROCESS_TYPE1=0;    //默认
    const PROCESS_TYPE2=1;    //补偿用户
    const PROCESS_TYPE3=2;    //用户扣款

    const PROCESS_TYPE4=3;    //司机补偿
    const PROCESS_TYPE5=4;    //司机扣款

    const PROCESS_TYPE1AND3=5; //补偿用户,司机补偿
    const PROCESS_TYPE1AND4=6; //补偿用户,司机扣款
    const PROCESS_TYPE2AND3=7; //用户扣款,司机补偿
    const PROCESS_TYPE2AND4=8; //用户扣款,司机扣款

    public static $process_type=array(
                                '0'=>'默认',
                                '1'=>'用户补偿',
                                '2'=>'用户扣款',
                                '3'=>'司机补偿',
                                '4'=>'司机扣款',
                                '5'=>'补偿用户 司机补偿',
                                '6'=>'补偿用户 司机扣款',
                                '7'=>'用户扣款 司机补偿',
                                '8'=>'用户扣款 司机扣款',
                            );




    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerComplainRecoup the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_complain_recoup}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('complain_id, process_type, status,mark, created, operator, create_time', 'required'),
            array('complain_id, recoup_type, amount_customer, amount_driver, process_type, status', 'numerical', 'integerOnly'=>true),
            array('customer, recoup_customer, created, operator', 'length', 'max'=>20),
            array('driver_id, recoup_driver', 'length', 'max'=>10),
            array('mark', 'length', 'max'=>500),
            array('update_time', 'safe'),

            // Please remove those attributes that should not be searched.
            array('id, complain_id, recoup_type, customer, driver_id, recoup_customer, amount_customer, recoup_driver, amount_driver, process_type, status, mark, created, operator, create_time, update_time', 'safe', 'on'=>'search'),
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
            'complain_id' => '投诉ID',
            'recoup_type' => '补偿方式',
            'customer' => '客户',
            'driver_id' => '司机',
            'recoup_customer' => '客户',
            'amount_customer' => '补偿客户金额',
            'recoup_driver' => '司机',
            'amount_driver' => '补偿司机金额',
            'process_type' => '处理类型',
            'status' => '处理状态',
            'mark' => 'Mark',
            'created' => '创建人',
            'operator' => '操作人',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($criteria=null)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        if(!$criteria){
            $criteria=new CDbCriteria;
        }
        $criteria->compare('id',$this->id);
        $criteria->compare('complain_id',$this->complain_id);
        if(!empty($this->recoup_type)){
            $criteria->compare('recoup_type',$this->recoup_type);
        }
        $criteria->compare('customer',$this->customer,true);
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('recoup_customer',$this->recoup_customer,true);
        $criteria->compare('amount_customer',$this->amount_customer);
        $criteria->compare('recoup_driver',$this->recoup_driver,true);
        $criteria->compare('amount_driver',$this->amount_driver);
        $criteria->compare('process_type',$this->process_type);
        $criteria->compare('status',$this->status);
        $criteria->compare('mark',$this->mark,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>20,
            ),
        ));
    }


    //添加补偿流水
    public function addComplainRecoup($data){
        self::$db=Yii::app()->db;
        $model=new CustomerComplainRecoup();
        $model->driver_id=$data['driver_id'];
        $model->complain_id=$data['complain_id'];
        $model->recoup_type=$data['recoup_type'];   //补偿扣款类型，现金or优惠券
        $model->customer=$data['customer'];     //用户手机号、VIP卡
        $model->recoup_customer=$data['recoup_customer'];
        $model->amount_customer=$data['amount_customer'];
        $model->recoup_driver=$data['recoup_driver'];
        $model->amount_driver=$data['amount_driver'];
        $model->process_type=$data['process_type'];
        $model->status=0;
        $model->mark=$data['mark'];;
        $model->created=$model->operator=$data['user'];
        $model->create_time=$data['create_time'];

        $ret=  $model->insert();
        return $ret;

    }

    //find recoup
    public function findComplainRecoup($complainId){
        return  $this->find('complain_id=:complain_id', array(':complain_id'=>$complainId));
    }


    /**
    *  获取驳回的最后补偿信息
    *   
    */
    public function getComplainRecoup($complain_id){
        $sql = "SELECT * FROM t_customer_complain_recoup WHERE complain_id=:complain_id order by create_time desc LIMIT 1";
        return $this->findBySql($sql,array(':complain_id'=>$complain_id));
    }
} 