<?php

/**
 * This is the model class for table "{{order_comment_log}}".
 *
 * The followings are the available columns in table '{{order_comment_log}}':
 * @property integer $id
 * @property integer $order_id
 * @property string $order_date
 * @property integer $comment_status
 * @property integer $notice_status
 * @property string $notice_date
 * @property string $create_time
 * @property string $update_time
 */
class OrderCommentLog extends CActiveRecord
{
    //order is commented
    const ORDER_COMMENTED = 1;


    //order notice is sent ok
    const ORDER_NOTICE_HAS_SNET_OK = 1;

    //order notice is sent fail
    const ORDER_NOTICE_HAS_SNET_FAIL = 2;

    //order notice is consumed
    const ORDER_NOTICE_HAS_CONSUMED = 3;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{order_comment_log}}';
    }

    /* 记录日志
     * @author aiguoxin 2014-04-09
     * @param array $order
     * @return bool
     */
    public function addOrderCommentLog($order)
    {
        $log = new OrderCommentLog();
        $log_attr = $log->attributes;
        $log_attr['order_id'] = $order['order_id'];
        $log_attr['order_date'] = $order['order_date'];
        $log_attr['create_time'] = date("Y-m-d H:i:s");
        $log_attr['notice_phone'] = $order['phone'];
        $log_attr['comment_status'] = $order['comment_status'];
        $log->attributes = $log_attr;
        if ($log->insert()) {
            return true;
        }
        return false;
    }

    /* update comment status success 1
     * @author aiguoxin 2014-04-09
     * @param array $params
     * @return bool
     */
    public function updateCommentStatusOk($order_id){
        $log = self::getOrderCommentLog($order_id);
        if(empty($log)){
            return false;
        }
        $log->comment_status = OrderCommentLog::ORDER_COMMENTED;
        if ($log->update()){
            return true;
        } else {
            return false;
        }
    }

    /* update comment status consumed 3
     * @author aiguoxin 2014-04-29
     * @param array $params
     * @return bool
     */
    public function updateNoticeStatusConsumed($id){
        $log = self::getOrderCommentLogById($id);
        if(empty($log)){
            return false;
        }
        $log->notice_status = OrderCommentLog::ORDER_NOTICE_HAS_CONSUMED;
        if ($log->update()){
            return true;
        } else {
            return false;
        }
    }

    /* update notice sent status success 1
     * @author aiguoxin 2014-04-09
     * @param int order_id
     * @return bool
     */
    public function updateNoticeStatusOk($order_id,$phone){
        $log = self::getOrderCommentLog($order_id);
        if(empty($log)){
            return false;
        }
        $log->notice_status = OrderCommentLog::ORDER_NOTICE_HAS_SNET_OK;
        $log->notice_phone = $phone;
        $log->notice_date = date("Y-m-d H:i:s");
        if ($log->update()){
            return true;
        } else {
            return false;
        }
    }

    /* update notice sent status fail 2
     * @author aiguoxin 2014-04-10
     * @param int order_id
     * @return bool
     */
    public function updateNoticeStatusFail($order_id,$reason){
        $log = self::getOrderCommentLog($order_id);
        if(empty($log)){
            return false;
        }
        $log->notice_status = OrderCommentLog::ORDER_NOTICE_HAS_SNET_FAIL;
        $log->notice_fail_reason = $reason;
        $log->notice_date = date("Y-m-d H:i:s");
        if ($log->update()){
            return true;
        } else {
            return false;
        }
    }

    /* judge phone has sent one message
     * @author aiguoxin 2014-04-10
     * @param string phone, date order_date
     * @return bool
     */
    public function canNotice($phone, $create_time,$id){
        $count = self::getNoticeOkLogByPhoneAndDate($phone,$create_time,$id);
        return $count == 0;
    }


    /**
     * notice log info
     * @param int $order_id
     */
    public function getOrderCommentLog($order_id) {
        $orderCommentLog = $this->find('order_id=:order_id', array(':order_id'=>$order_id));
        return $orderCommentLog;
    }

     /**
     * notice log info
     * @param int $order_id
     */
    public function getOrderCommentLogById($id) {
        $orderCommentLog = $this->find('id=:id', array(':id'=>$id));
        return $orderCommentLog;
    }

    /**
     * get unCommented order num by phone and date
     * @param int $order_id
     */
    public function getUncommentedOrderNum($phone,$order_date){
        $today = date("Y-m-d 00:00:00");
        $command = Yii::app()->db_readonly->createCommand()
            ->select('count(1)')
            ->from(OrderCommentLog::model()->tableName())
            ->where('notice_phone = :phone AND comment_status = 0 
                AND order_date>:order_date
                AND order_date<:today');
        $query = $command->queryScalar(array(':phone'=>$phone,
                                             ':order_date'=>$order_date,
                                             ':today'=>$today));
        return $query;
    }
     /**
     * find the log by phone and order_day
     * @param var phone, order_date
     */
    public function getNoticeOkLogByPhoneAndDate($phone,$create_time,$id) {
        $notice_status=OrderCommentLog::ORDER_NOTICE_HAS_SNET_OK;
        $command = Yii::app()->db_readonly->createCommand()
            ->select('count(1)')
            ->from(OrderCommentLog::model()->tableName())
            ->where('id !=:id AND notice_phone = :phone AND notice_status = :notice_status AND create_time>=:create_time');
        $query = $command->queryScalar(array(':id'=>$id,':phone'=>$phone,':create_time'=>$create_time,':notice_status'=>$notice_status));
        return $query;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id, update_time', 'required'),
            array('order_id, comment_status, notice_status', 'numerical', 'integerOnly'=>true),
            array('notice_fail_reason', 'length', 'max'=>255),
            array('notice_phone', 'length', 'max'=>20),
            array('id,order_date, notice_date, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, order_id, order_date, comment_status, notice_status, notice_date, notice_fail_reason, notice_phone, create_time, update_time', 'safe', 'on'=>'search'),
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
            'order_id' => 'Order',
            'order_date' => '订单创建时间',
            'comment_status' => '0:未评价，默认；1：已评价',
            'notice_status' => '0:未发送，默认；1：发送成功；2：发送失败',
            'notice_date' => '通知发送时间',
            'notice_fail_reason' => '发送失败原因',
            'notice_phone' => '接收通知的手机号',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
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
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('order_date',$this->order_date,true);
        $criteria->compare('comment_status',$this->comment_status);
        $criteria->compare('notice_status',$this->notice_status);
        $criteria->compare('notice_date',$this->notice_date,true);
        $criteria->compare('notice_fail_reason',$this->notice_fail_reason,true);
        $criteria->compare('notice_phone',$this->notice_phone,true);
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
     * @return OrderCommentLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
