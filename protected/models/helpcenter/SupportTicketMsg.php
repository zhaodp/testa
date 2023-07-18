<?php

/**
 * This is the model class for table "{{support_ticket_msg}}".
 *
 * The followings are the available columns in table '{{support_ticket_msg}}':
 * @property integer $id
 * @property integer $support_ticket_id
 * @property string $message
 * @property string $create_time
 * @property string $reply_user
 * @property integer $type
 */
class SupportTicketMsg extends CActiveRecord
{
    const REPLY_TYPE_INSIDE = 0; //内部沟通
    const REPLY_TYPE_TO_DRIVER = 1; //to 司机
    const REPLY_TYPE_FROM_DRIVER = 2; //司机回复

    public static  $replyTypeList = array(
        self::REPLY_TYPE_INSIDE => '内部沟通',
        self::REPLY_TYPE_TO_DRIVER => '回复给司机',
        self::REPLY_TYPE_FROM_DRIVER => '司机说',
    );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{support_ticket_msg}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('support_ticket_id,', 'numerical', 'integerOnly'=>true),
			array('message', 'length', 'max'=>3000),
			array('reply_user', 'length', 'max'=>50),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, support_ticket_id, message, create_time, reply_user', 'safe', 'on'=>'search'),
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
			'support_ticket_id' => 'Support Ticket',
			'message' => 'Message',
			'create_time' => 'Create Time',
			'reply_user' => 'Reply User',

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
		$criteria->compare('support_ticket_id',$this->support_ticket_id);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('reply_user',$this->reply_user,true);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SupportTicketMsg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * 获取工单回复数
     */
    public function getCountByTicketId($ticket_id)
    {
        $sql = "select count(`id`) count from t_support_ticket_msg where support_ticket_id=:support_ticket_id and `reply_type` in ('1','2')";
        return Yii::app()->db_readonly->createCommand($sql)->queryScalar(array('support_ticket_id'=>$ticket_id));
    }

    /**
     * 保存回复
     * @params $params array,$user_type int
     */
    public function createSupportTicketMsg($params, $user_type, $reply_type)
    {
        $date = date('Y-m-d H:i:s',time());
        $msg_model = new SupportTicketMsg();
        $msg_model->support_ticket_id = $params['ticket_id'];
        $msg_model->message = $params['message'];
        $msg_model->create_time  = $date;
        $msg_model->reply_user = $params['reply_user'];
        $msg_model->reply_user_type = $user_type;//司机,
        $msg_model->reply_type = $reply_type;
        return $msg_model->save();
    }
}
