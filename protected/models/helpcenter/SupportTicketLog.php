<?php

/**
 * This is the model class for table "{{support_ticket_log}}".
 *
 * The followings are the available columns in table '{{support_ticket_log}}':
 * @property integer $id
 * @property integer $support_ticket_id
 * @property string $op_content
 * @property string $create_time
 * @property integer $action
 * @property string $operater
 */
class SupportTicketLog extends CActiveRecord
{
    //操作类型
    const LOG_ACTION_TOOP = 1;     //转处理人
    const LOG_ACTION_CLOSE = 2;    //关闭
    const LOG_ACTION_REPLY = 3;    //回复
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{support_ticket_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('support_ticket_id, action', 'numerical', 'integerOnly'=>true),
			array('operater', 'length', 'max'=>20),
            array('op_content','length', 'max'=>3000),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, support_ticket_id, create_time, action, operater', 'safe', 'on'=>'search'),
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
			'op_content' => 'Op Content',
			'create_time' => 'Create Time',
			'action' => 'Action',
			'operater' => 'Operater',
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
		$criteria->compare('op_content',$this->op_content,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('action',$this->action);
		$criteria->compare('operater',$this->operater,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SupportTicketLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * 添加日志
     * wanglonghuan 2014/1/2
     * @params $ticket_id,$driver_id,$action,$content
     */
    public function SupportTicketAddLog($ticket_id, $operater, $action, $content="")
    {
        $contentList = array(
            self::LOG_ACTION_CLOSE => " 关闭了 工单：",
            self::LOG_ACTION_REPLY => " 回复了工单：",
            self::LOG_ACTION_TOOP => " 转了 负责人：",   //1期暂无操作 预留
        );
        $log_model = new SupportTicketLog();
        $log_model->support_ticket_id = $ticket_id;
        $log_model->op_content = $operater .$contentList[$action] . $ticket_id . $content;
        $log_model->create_time = date("Y-m-d H:i:s",time());
        $log_model->action = $action;
        $log_model->operater = $operater;
        $log_model->create_time = date('Y-m-d H:i:s',time());

        return $log_model->save();
    }
}
