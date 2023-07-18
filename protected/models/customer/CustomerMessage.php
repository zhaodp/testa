 <?php

/**
 * This is the model class for table "{{customer_message}}".
 *
 * The followings are the available columns in table '{{customer_message}}':
 * @property integer $id
 * @property string $phone
 * @property integer $type
 * @property integer $state
 * @property string $title
 * @property string $content
 * @property string $action_url
 * @property string $create_time
 * @property string $update_time
 */
class CustomerMessage extends CActiveRecord
{
    const UNREAD_STATE=0;//未读
    const READED_STATE=1;//已读
    const DEL_STATE=2;//已删除

    const TYPE_ORDER=1;// 1.订单评价 2. 优惠券（绑定成功或者到期提醒） 3.活动 
    const TYPE_COUPON=2;
    const TYPE_ACT=3;
    const TYPE_BILL=4;//发票
    const TYPE_FEEDBACK=5; //反馈
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_message}}';
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
            array('type, state,content_id', 'numerical', 'integerOnly'=>true),
            array('phone', 'length', 'max'=>16),
            array('title, action_url', 'length', 'max'=>255),
            array('content', 'length', 'max'=>1000),
            array('create_time,content_id', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id,content_id, phone, type, state, title, content, action_url, create_time, update_time', 'safe', 'on'=>'search'),
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
            'state' => 'State',
            'title' => 'Title',
            'content' => 'Content',
            'action_url' => 'Action Url',
            'create_time' => 'Create Time',
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
        $criteria->compare('state',$this->state);
        $criteria->compare('title',$this->title,true);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('action_url',$this->action_url,true);
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
     * @return CustomerMessage the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
    *   添加消息
    *
    **/
    public function addMsg($phone,$type,$title,$content,$action_url='',$content_id=0){

        $customerMessage = new CustomerMessage();
        $customerMessage_attr = $customerMessage->attributes;
        $customerMessage_attr['phone'] = $phone;
        $customerMessage_attr['type'] = $type;
        $customerMessage_attr['title'] = $title;
        $customerMessage_attr['content'] = $content;
        $customerMessage_attr['action_url']=$action_url;
        $customerMessage_attr['content_id']=$content_id;
        $customerMessage_attr['create_time']=date("Y-m-d H:i:s");
        $customerMessage->attributes = $customerMessage_attr;
        $customerMessage->save(false);
        return $customerMessage->attributes['id'];
    }

    /**
    *   获取用户没有被删除的信息
    *
    */
    public function getMsgList($phone,$version='5.1.0'){
        //默认5.2.0之前版本不展示反馈、发票
        $sql="select id,type,state,title,content,content_id,create_time,action_url from t_customer_message
                        where phone=:phone and state !=2 and type in(1,2,3) order by create_time desc";
        if($version && $version > '5.1.0'){
            $sql="select id,type,state,title,content,content_id,create_time,action_url from t_customer_message
                        where phone=:phone and state !=2 order by create_time desc";
        }
        $command = Yii::app()->dbreport->createCommand($sql);
        $command->bindParam(":phone", $phone);
        $msg_list = $command->queryAll();
        return $msg_list;
    }

    /**
    *   更新状态
    *
    */
    public function updateState($phone,$id,$state){
        //更新非删除状态的消息
        $sql = "UPDATE `t_customer_message` SET `state` = :state WHERE id = :id and state!=2";
        $res =  Yii::app()->dbreport->createCommand($sql)->execute(array(
            ':id' => $id,
            ':state' => $state,
        ));
        //如果类型是订单或优惠券，合成的一条消息，更新其状态
        $message = $this->findByPk($id);
        if($message){
            if($message->type == self::TYPE_ORDER || $message->type == self::TYPE_COUPON){
                //全部更新为删除
                $sql = "UPDATE `t_customer_message` SET `state` = 2 WHERE phone=:phone and type=:type and state !=2";
                $res =  Yii::app()->dbreport->createCommand($sql)->execute(array(
                    ':phone' => $phone,
                    ':type' => $message->type,
                ));

                //更新最后一条为已读
                $sql = "UPDATE `t_customer_message` SET `state` = :state WHERE phone=:phone and type=:type order by create_time desc limit 1";
                $res =  Yii::app()->dbreport->createCommand($sql)->execute(array(
                    ':phone' => $phone,
                    ':state' => $state,
                    ':type' => $message->type,
                ));
            }

        }
        return $res;
    }

    /**
    *  更新优惠券和未评价订单状态，先删除，再新增一条
    */
    public function updateStateByType($phone,$type,$content='',$suggestion_id=0){
        $del_sql = "update `t_customer_message` set  `state`=:state where phone=:phone and type=:type and state!=2";
        $res = Yii::app()->dbreport->createCommand($del_sql)->execute(array(
            ':phone' => $phone,
            ':type' => $type,
            ':state' => self::DEL_STATE
        ));
        //新增一条
        $title='';
        switch ($type) {
            case self::TYPE_ORDER:
                $title='订单';
                break;
            case self::TYPE_COUPON:
                $title='优惠券';
                break;
            case self::TYPE_FEEDBACK;
                $title='反馈';
                break;
            default:
                # code...
                break;
        }
        return $this->addMsg($phone,$type,$title,$content,'',$suggestion_id);
        
    }

    /**
    *
    *   删除过期活动消息
    */
    public function delActMsg($action_url){
        $del_sql = "update `t_customer_message` set `state`=:state where action_url=:action_url and type=:type and state!=2";
        $res = Yii::app()->dbreport->createCommand($del_sql)->execute(array(
            ':action_url' => $action_url,
            ':type' => self::TYPE_ACT,
            ':state' => self::DEL_STATE,
        ));
    }

    /**
    *   优惠券消息   
    *   @param $phone：客户电话,$content:消息内容
    */
    public function addCouponMsg($phone,$content){
        $flag = false;
        $messageid = $this->updateStateByType($phone,self::TYPE_COUPON,$content);
        if($messageid > 0){
            //推送
            $flag = ClientPush::model()->pushMsg($phone,$content,$messageid,AppleMsgFactory::TYPE_MSG_COUPON);
        }
        return $flag;
    }

    /**
    *   回复反馈消息
    *   @param $suggestion_id:反馈主题id,$content:针对该条反馈主题的回复内容
    */
    public function addFeedBackMsg($suggestion_id, $content){
        $flag = false;
        //通过反馈id,找到用户电话
        $customerFeedback = CustomerSuggestion::model()->findByPk($suggestion_id);
        if(empty($customerFeedback)){
            EdjLog::info('反馈id='.$suggestion_id.'找不到，不发送');
            return $flag;
        }
        $phone = $customerFeedback['phone'];
        $messageid = $this->updateStateByType($phone,self::TYPE_FEEDBACK,$content,$suggestion_id);
        if($messageid > 0){
            //推送
            $flag = ClientPush::model()->pushMsg($phone,$content,$messageid,AppleMsgFactory::TYPE_MSG_FEEDBACK,$suggestion_id);
        }
        return $flag;
    }


    /**
    *   发票申请,多条消息展示，不覆盖,与活动一样
    *
    */
    public function addBillMsg($model){
        $flag = false;
        $total_amount = $model->total_amount;
        $floor_amount = floor($total_amount);
        if($model->type == CustomerInvoice::TYPE_UNDETERMIND){
            $content = '您申请的'.$floor_amount.'元发票已寄出';
        }else{
            $type = CustomerInvoice::$type[$model->type];
            $content = '您申请的'.$floor_amount.'元'.$type.'发票已寄出';
        }
        $messageid = $this->addMsg($model->customer_phone,self::TYPE_BILL,'发票',$content);
        if($messageid >0){
            //推送消息
            $flag = ClientPush::model()->pushMsg($model->customer_phone, $content, $messageid,AppleMsgFactory::TYPE_MSG_BILL);
        }
        return $flag;
    }

    /**
     *  获取用户未读消息数
     */
    public function getUnReadMsgNum($phone,$version='5.1.0'){
        $sql = 'select count(*) as unread_msg_num from t_customer_message where phone=:phone and state =0';
        if($version <= '5.1.0'){//默认5.2.0之前版本不展示反馈、发票
            $sql .= ' and type in(1,2,3)';
        }
        $command = Yii::app()->dbreport->createCommand($sql);
        $unread_msg_num = $command->queryScalar(array(':phone'=>$phone));
        return $unread_msg_num;
    }

}
