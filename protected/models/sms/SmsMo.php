<?php

/**
 * This is the model class for table "{{sms_mo}}".
 *
 * The followings are the available columns in table '{{sms_mo}}':
 * @property string $id
 * @property string $recvtel
 * @property string $sender
 * @property string $content
 * @property string $recdate
 * @property string $channel
 * @property string $created
 * @property integer $status
 * @property string $update_time
 * @property string $subcode
 */
class SmsMo extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SmsMo the static model class
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
		return '{{sms_mo}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status', 'numerical', 'integerOnly'=>true),
			array('recvtel', 'length', 'max'=>30),
			array('sender', 'length', 'max'=>15),
			array('content', 'length', 'max'=>500),
			array('channel, subcode', 'length', 'max'=>10),
			array('recdate, created, update_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, recvtel, sender, content, recdate, channel, created, status, update_time, subcode', 'safe', 'on'=>'search'),
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
			'recvtel' => 'Recvtel',
			'sender' => 'Sender',
			'content' => 'Content',
			'recdate' => 'Recdate',
			'channel' => 'Channel',
			'created' => 'Created',
			'status' => 'Status',
			'update_time' => 'Update Time',
			'subcode' => 'Subcode',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('recvtel',$this->recvtel);
		$criteria->compare('sender',$this->sender);
		$criteria->compare('content',$this->content);
		$criteria->compare('recdate',$this->recdate);
		$criteria->compare('channel',$this->channel);
		$criteria->compare('created',$this->created);
		$criteria->compare('status',$this->status);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('subcode',$this->subcode);
        if (!isset($_GET['SmsMo'])) {
            $criteria->addCondition('unix_timestamp(created)+604800>=unix_timestamp()');
        }
        if(!empty($_GET['start_time'])&&!empty($_GET['end_time'])){
            $criteria->addBetweenCondition('date(created)', $_GET['start_time'], $_GET['end_time']);
        }

        $criteria->order='created desc';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>50,
            ),
		));
	}


    /**
     * 根据短信通道和状态获取数据
     * @param $channel
     * @param $status
     * @param <int> $lastDays             筛选最后多少天的数据
     * @author bidong 2013-08-08
     */
    public function getSmsData($channel,$status=0,$lastDays=NULL){

        $ret=array();
        echo "创建command对象\n";
        $sms_mo_command=Yii::app()->db_readonly->createCommand();
        $limit=200;
        $where = ' channel = :channel and status=:status and subcode!=\'\' ';
        $param=array(':channel'=>$channel,':status'=>$status);
        if($lastDays != NULL){
            $where .= ' AND created > :created ';
            $param[':created'] = date('Y-m-d H:i:s', strtotime('-'.$lastDays.' day'));
        }
        echo "开始查询数据库\n";
        $sms_mo_data = $sms_mo_command->select('*')
                                      ->from('{{sms_mo}}')
                                      ->where($where)
                                      ->limit($limit)->queryAll(true,$param);
        echo "查询sql:" . $sms_mo_command->text."\n";
        
        if(!empty($sms_mo_data)){
        	echo "开始更新状态\n";
            //更新状态
            foreach($sms_mo_data as $sms){
                Yii::app()->db->createCommand()->update('t_sms_mo', array('status' => 1,'update_time'=>date('Y-m-d H:i:s')), 'id=:id',array(':id'=>$sms['id']));
            }
            $ret=$sms_mo_data;
        }
		echo "返回数据\n";
        return $ret;

    }

    /**
     * 获取重置密码短信
     * @auhtor bidong 2013-09-13
     */
    public function getResetPwdSMS($channel,$status=0){
        $sms_mo_data=array();

        $sms_mo_command=Yii::app()->db->createCommand();
        $limit=50;
        $param=array(':channel'=>$channel,':status'=>$status,':content'=>'忘记密码');
        $sms_mo_data = $sms_mo_command->select('*')
            ->from('{{sms_mo}}')
            ->where('channel = :channel and status=:status and content=:content and subcode=\'\'')
            ->limit($limit)->queryAll(true,$param);

        if(!empty($sms_mo_data)){
            //更新状态
            foreach($sms_mo_data as $sms){
                $sms_mo_model=self::model()->findByPk($sms['id']);
                if($sms_mo_model){
                    $sms_mo_model->status=1;
                    $sms_mo_model->update_time=date('Y-m-d H:i:s');
                    $sms_mo_model->update();
                }
            }
        }

        return $sms_mo_data;
    }


    /**
     * 获取subcode 为空的短信
     * @return mixed
     * @author bidong 2013-09-16
     */
    public function getAutoNaviSMS($limit,$offset){
        $sms_mo_data=array();
        $channel=Sms::CHANNEL_SOAP;
        $status=0;
        $sms_mo_command=Yii::app()->db_readonly->createCommand();
        $param=array(':channel'=>$channel,':status'=>$status,':content'=>'拒绝酒驾');
        $sms_mo_data = $sms_mo_command->select('*')
            ->from('{{sms_mo}}')
            ->where('channel = :channel and status=:status and content=:content and subcode=\'\'')
            ->limit($limit)->offset($offset)->queryAll(true,$param);

        return $sms_mo_data;
    }



}