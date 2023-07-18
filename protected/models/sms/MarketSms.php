<?php

/**
 * This is the model class for table "{{market_sms}}".
 *
 * The followings are the available columns in table '{{market_sms}}':
 * @property integer $id
 * @property string $phone
 * @property string $content
 * @property integer $status
 * @property string $pre_send_time
 * @property integer $user_id
 * @property string $created
 */
class MarketSms extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MarketSms the static model class
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
		return '{{market_sms}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('phone, content', 'required'),
			array('status, user_id', 'numerical', 'integerOnly'=>true),
			array('phone', 'length', 'max'=>15),
			array('content', 'length', 'max'=>255),
			array('pre_send_time, created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, phone, content, status, pre_send_time, user_id, created', 'safe', 'on'=>'search'),
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
			'content' => 'Content',
			'status' => 'Status',
			'pre_send_time' => 'Pre Send Time',
			'user_id' => 'User',
			'created' => 'Created',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('phone',$this->phone);
		$criteria->compare('content',$this->content);
		$criteria->compare('status',$this->status);
		$criteria->compare('pre_send_time',$this->pre_send_time);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 发送短信列表
	 * @param array $condition
	 * @return object $dataProvider
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-07-02
	 */
	public function getSmsList($condition = array()) {
		$sql = "SELECT * FROM t_market_sms WHERE 1=1";
		$str_condition = '1=1';
		if ($condition['status'] !== '') {
			$sql .= ' AND status='.$condition['status'];
			$str_condition .= ' AND status='.$condition['status'];
		}
		$sql .= ' ORDER BY id DESC';
		$count = Yii::app()->db_readonly->createCommand()
		            ->select('*')
		            ->from('t_market_sms')
		            ->where($str_condition)
		            ->query()
		            ->count();
		//sql数据转化成Provider格式 源自：http://blog.yiibook.com/?p=420   Yii手册CSqlDataProvider
		$dataProvider = new CSqlDataProvider($sql, array(
	            'keyField'=>'phone',   //必须指定一个作为主键
	            'totalItemCount'=>$count,    //分页必须指定总记录数
	            'db'=>Yii::app()->db_readonly,
	            'pagination'=>array(
			        'pageSize'=>50,
			    ),
	    ));
	    //sql数据转化成Provider格式 END
	    return $dataProvider;
	}
	
	/**
	 * 插入营销短信
	 * @param array $data
	 */
	public function insertMarketSms($data = array()) {
		$sql = "INSERT INTO t_market_sms(`pre_send_time` , `phone` , `content` , `user_id` , `created`) VALUES(:pre_send_time , :phone , :content , :user_id , :created)";
		if (is_array($data['sms_phones'])) {
			$phone_arr = $data['sms_phones'];
		} else {
			$phone_str = $data['sms_phones'];
		    $phone_arr = preg_split('/[\r\n]+/', $phone_str);
		}
		$time = date("Y-m-d H:i:s" , time());
		foreach ($phone_arr as $phone) {
			if (!empty($phone)) {
				$command = Yii::app()->db->createCommand($sql);
				$command->bindParam(":pre_send_time" , $data['pre_send_time']);
				$command->bindParam(":phone" , $phone);
				$command->bindParam(":content" , $data['content']);
				$command->bindParam(":user_id" , $data['user_id']);
				$command->bindParam(":created" , $time);
				$command->execute();
				$command->reset();
			}
		}
		return true;
	}
}