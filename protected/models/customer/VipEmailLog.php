<?php

/**
 * This is the model class for table "{{vip_email_log}}".
 *
 * The followings are the available columns in table '{{vip_email_log}}':
 * @property integer $id
 * @property string $email
 * @property string $vipcard
 * @property integer $type
 * @property string $remarks
 * @property string $created
 */
class VipEmailLog extends FinanceActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return VipEmailLog the static model class
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
        return '{{vip_email_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('email, vipcard, type, vip_bill_time, created, send_time,', 'required'),
            array('type, status', 'numerical', 'integerOnly' => true),
            array('email', 'length', 'max' => 50),
            array('vipcard, create_by', 'length', 'max' => 20),
            array('remarks', 'length', 'max' => 30),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, email, vipcard, type, remarks, vip_bill_time, send_time, status, create_by, created', 'safe', 'on' => 'search'),
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
            'email' => '邮箱',
            'vipcard' => 'VIP卡号',
            'type' => '发送类型',
            'remarks' => '备注',
            'send_time' => '发送时间',
            'create_by' => '创建人',
            'vip_bill_time' => '账单时间',
            'status' => '发送状态',
            'created' => '创建时间',
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
        $criteria->compare('email', $this->email, true);
        $criteria->compare('vipcard', $this->vipcard, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('remarks', $this->remarks, true);
        $criteria->compare('send_time', $this->send_time, true);
        $criteria->compare('create_by', $this->create_by, true);
        $criteria->compare('vip_bill_time', $this->vip_bill_time, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('status', $this->status, true);
        $criteria->order = "status asc, id desc";

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 30,
            ),

        ));
    }

    /**
     * 添加email发送日志
     * @param $params
     * @return bool
     * author mengtianxue
     */
    public function addLog($params)
    {
        $emailLog = new VipEmailLog();
        $emailLog->attributes = $params;
        $emailLog->insert();
        return true;
    }


    public function updateLog($id)
    {
        $params = array('status' => 1);
        $update = self::model()->updateAll($params, 'id = :id', array(':id' => $id));
        if ($update) {
            return true;
        } else {
            return false;
        }
    }

    //查询往前7天，往后7天以内要发的邮件
    public function getSendList()
    {
	$nowUnixTime=time();
        $start_time = date('Y-m-d H:i:s',$nowUnixTime-3600*24*7);
        $end_time = date('Y-m-d H:i:s', $nowUnixTime+3600*24*7);

        $all_log = Yii::app()->db_finance->createCommand()
            ->select("*")
            ->from("t_vip_email_log")
            ->where("send_time >= :start_time and send_time < :end_time and status = 0",
                array(':start_time' => $start_time, ':end_time' => $end_time))
            ->queryAll();
        return $all_log;
    }

}
