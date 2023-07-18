 <?php

/**
 * This is the model class for table "{{apple_message}}".
 *
 * The followings are the available columns in table '{{apple_message}}':
 * @property integer $id
 * @property integer $status
 * @property string $phone
 * @property string $token
 * @property string $message_json
 * @property string $create_time
 * @property string $update_time
 */
class AppleMessage extends CActiveRecord
{   
    const DEFAULT_STATUS=0;
    const CONSUMED=1;
    const SUCCESS_TO_APNS=2;
    const FAILED=3;
    const SUCCESS_TO_CLIENT=4;

    // 被Apple Push Server拒绝的message，这种message不应该再尝试push——曾坤 2015/4/3
    const UNINTELLIGIBLE_MESSAGE = 5;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{apple_message}}';
    }

     /* add will be sent apple message
     * @author aiguoxin 2014-05-02
     * @param array $message
     * @return bool
     */
    public function addAppleMessage($phone,$token,$message,$rank=3)
    {
        $apple_message = new AppleMessage();
        $apple_message_attr = $apple_message->attributes;
        $apple_message_attr['phone'] = $phone;
        $apple_message_attr['create_time'] = date("Y-m-d H:i:s");
        $apple_message_attr['token'] = $token;
        $apple_message_attr['message_json'] = $message;
        $apple_message_attr['rank']=$rank;
        $apple_message->attributes = $apple_message_attr;
        if ($apple_message->insert()) {
            return true;
        }
        return false;
    }

    public function updateStatusById($id,$status,$reason=''){
        $message = self::getMessageById($id);
        if(empty($message)){
            return false;
        }

        /* 先把这个逻辑去掉，防止因为APNS导致的重试让重试次数过多——曾坤 2015/4/9
        //send_num auto inc 1
        if($status != self::CONSUMED){
            $send_num = $message->send_num +1 ;
            $message->send_num = $send_num;
        }
        */

        $message->status = $status;
        $message->reason = $reason;
        if ($message->update()){
            return true;
        } else {
            return false;
        }
    }

    public function updateStatusByToken($token){
        $sql = "UPDATE `t_apple_message` SET `status` = :status WHERE token = :token";
        return Yii::app()->db->createCommand($sql)->execute(array(
            ':token' => $token,
            ':status' => self::FAILED,
        ));
    }

    public function getMessageById($id) {
        $message = $this->find('id=:id', array(':id'=>$id));
        return $message;
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('message_json, update_time', 'required'),
            array('status, rank, send_num', 'numerical', 'integerOnly'=>true),
            array('phone', 'length', 'max'=>20),
            array('token', 'length', 'max'=>100),
            array('reason', 'length', 'max'=>255),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, status, phone, token, message_json, reason, create_time, update_time, rank, send_num', 'safe', 'on'=>'search'),
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
            'status' => '0:未发送;1：已消费;2：发送成功;3：发送失败',
            'phone' => '接收通知的手机号',
            'token' => '发送给苹果APNS的手机对应token',
            'message_json' => '消息的json格式',
            'reason' => '失败原因',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'rank' => '默认推送等级',
            'send_num' => '已推送次数',
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
        $criteria->compare('status',$this->status);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('token',$this->token,true);
        $criteria->compare('message_json',$this->message_json,true);
        $criteria->compare('reason',$this->reason,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('rank',$this->rank);
        $criteria->compare('send_num',$this->send_num);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AppleMessage the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
