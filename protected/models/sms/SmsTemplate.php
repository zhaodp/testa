<?php

/**
 * This is the model class for table "{{sms_template}}".
 *
 * The followings are the available columns in table '{{sms_template}}':
 * @property integer $id
 * @property string $name
 * @property string $subject
 * @property integer $receive
 * @property string $channel
 * @property integer $type
 * @property string $content
 * @property string $create_time
 * @property string $created
 * @property string $operator
 * @property string $update_time
 */
class SmsTemplate extends CActiveRecord
{
    const ANNOUNCE=1;     //公告
    const BUSINESS=2;     //业务
    const CALLCENTER=3;   //话务中心

    public static $types= array(
        '1'=>'公告',
        '2'=>'业务',
        '3'=>'话务中心');

    const ALL=1;//全部
    const USER=2;//用户
    const DRIVER=3;//司机

    public static $recerves=array(
          '1'=>'全部',
          '2'=>'用户',
          '3'=>'司机',
          '4'=>'员工',
    );

    const CHANNEL_SOAP =Sms::CHANNEL_SOAP;//E达信
    const CHANNEL_GSMS =Sms::CHANNEL_GSMS;//33易9
    const CHANNEL_ZLZX =Sms::CHANNEL_ZLZX; //指联在线

    public static $channels=array(
        Sms::CHANNEL_SOAP=>'E达信',
        Sms::CHANNEL_GSMS=>'33易9',
        Sms::CHANNEL_ZLZX=>'指联在线',
    );
    
    private $_subjects = array();       //有对应模版的模版标识符
    private $_m = array();              //临时存储的模型，以subject为键值

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TSmsTemplate the static model class
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
		return '{{sms_template}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, subject, receive, channel, type, content', 'required'),
			array('receive, type', 'numerical', 'integerOnly'=>true),
			array('name, subject', 'length', 'max'=>50),
			array('channel', 'length', 'max'=>10),
			array('content', 'length', 'max'=>100),
			array('created, operator', 'length', 'max'=>20),
            array('subject','unique', 'message'=>'subject已存在'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, subject, receive, channel, type, content, create_time, created, operator, update_time', 'safe', 'on'=>'search'),
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
			'name' => '模板名称（中文）',
			'subject' => 'Subject',
			'receive' => '接收方',
			'channel' => '短信通道',
			'type' => '短信类型',
			'content' => '短信内容',
			'create_time' => '创建时间',
			'created' => '创建者',
			'operator' => '操作者',
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
		$criteria->compare('name',$this->name);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('receive',$this->receive);
		$criteria->compare('channel',$this->channel,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('operator',$this->operator,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 根据模板名称和变量返回替换后的模板内容
     * @param $subject
     * @param array $param
     * @return array
     */
    public function getContentBySubject($subject,$param=array()){

        $ret=array('content'=>'','channel'=>'');

        if(isset($this->_m[$subject]) && $this->_m[$subject]['attributes']['status'] == 0){
            $smsTmpModel = $this->_m[$subject];
        }else{
            $criteria=new CDbCriteria();
            $criteria->condition = 'subject=:subject and status=:status';
            $criteria->params=array(':subject'=>$subject,':status'=>0);
            $smsTmpModel= self::model()->find($criteria);
            if($smsTmpModel){
                $this->_m[$subject] = $smsTmpModel;
            }
        }
        if(!empty($smsTmpModel)){
            $ret['content']=$smsTmpModel->content;
            $ret['channel']=$smsTmpModel->channel;
        }
        if($param && is_array($param)){
            foreach($param as $k=>$v){
                $ret['content']=str_replace($k,$v,$ret['content']);
            }
        }

        return  $ret;

    }

    /**
     * 根据模板类别返回模板列表
     * @param $type
     * @return array
     */
    public function getListByType($type){

        $ret=array();
        if(!empty($type)) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'type=:type and status=:status';
            $criteria->params = array(':type'=>$type,':status'=>0);
            $ret = self::model()->findall($criteria);
        }
        return  $ret;
    }

    /**
     * 根据模板名称和变量返回替换后的模板内容 (增加订单渠道判断)
     * @param <string> $subject          模版标识符
     * @param <array> $param
     * @param <string> $orderChannel    订单渠道标识符
     * @return <array>
     */
    public function getContentBySubjectExtOrder($subject, $param = array(), $orderChannel = ''){
        $subject = $this->getSubjectByOrderChannel($subject, $orderChannel);
        return $this->getContentBySubject($subject, $param);
    }

    /**
     * 判断渠道短信模版是否存在，并返回subject
     * @param <string> $subject         模版标识符
     * @param <string> $orderChannel    订单渠道标识符
     * @return <string> $subject        对应的模版标识符
     */
    public function getSubjectByOrderChannel($subject, $orderChannel = ''){
        $channelSubject = trim($subject).'_orderchannel'.trim($orderChannel);
        if(isset($this->_subjects[$channelSubject])){
            return $channelSubject;
        }
        $hasChannelTmp = self::model()->exists('subject = :subject', array(':subject'=>$channelSubject));
        if($hasChannelTmp){
            $this->_subjects[$channelSubject] = $channelSubject;
        }
        
        return $hasChannelTmp ? $channelSubject : $subject;
    }

    /**
     * 替换模板变量
     * @param $content
     * @param array $param
     * @return mixed
     */
    public function replaceContent($content,$param=array()){
        if($param && is_array($param)){
            foreach($param as $k=>$v){
                $content=str_replace($k,$v,$content);
            }
        }
        return  $content;
    }



}
