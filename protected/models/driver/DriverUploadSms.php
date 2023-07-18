<?php

/**
 * This is the model class for table "{{driver_upload_sms}}".
 *
 * The followings are the available columns in table '{{driver_upload_sms}}':
 * @property string $id
 * @property string $phone
 * @property string $content
 * @property string $driver_id
 * @property integer $user_id
 * @property integer $status
 * @property string $created
 * @property string $update_time
 */
class DriverUploadSms extends CActiveRecord
{
    public static $status = array(
        '' => '',
        0 => '未处理',
        1 => '己处理',
    );


    public static  $type = array(
        '' => '',
        'sms' => '短信',
        'call' => '呼叫',
    );

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverUploadSms the static model class
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
		return '{{driver_upload_sms}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('phone, content, driver_id, user_id, status, created, update_time,type', 'required'),
			array('user_id, status', 'numerical', 'integerOnly'=>true),
			array('phone', 'length', 'max'=>20),
			array('content', 'length', 'max'=>1000),
			array('driver_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, phone, content, driver_id, user_id, status, created, update_time,type', 'safe', 'on'=>'search'),
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
			'phone' => '电话',
			'content' => '内容',
			'driver_id' => '司机工号',
			'user_id' => '操作人',
			'status' => '状态',
            'type' => '类型',
			'created' => '上报时间',
			'update_time' => '更新时间',
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
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('content',$this->content,true);
        $this->driver_id = isset($_GET['driver_id']) ? $_GET['driver_id'] : $this->driver_id;
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('user_id',$this->user_id);
        $this->status = isset($_GET['status']) ? trim($_GET['status']) : $this->status;
		$criteria->compare('status',$this->status);
        $this->type = isset($_GET['type']) ? trim($_GET['type']) : $this->type;
        $criteria->compare('type',$this->type);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('update_time',$this->update_time,true);

        if(!isset($_GET['DriverUploadSms_sort']['created'])){
            $criteria->order = "  id desc";
        }
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>30
            )
		));
	}

    public function getHref(){
        if($this->status == 0 ){
            return CHtml::link("加入黑名单",array("driver/uploadSms","id"=>$this->id),array("onclick"=>"return confirm(\"确定要加入黑名单吗?\")"));
        }else{
            return "加入黑名单";
        }
    }

    public function targetPosition($driver_id=null,$refer_time=null){
    	
    	$driver = empty($driver_id) ? $this->driver_id : $driver_id;
    	$created = empty($refer_time) ? $this->created : $refer_time;
    	
        return CHtml::link($driver,
            array(
                "driver/position",
                "driver_id"=>$driver,
                'endDate'=> date("Y-m-d H:i:s",strtotime($created)+1800),
                'startDate'=> date("Y-m-d H:i:s",strtotime($created)-1800),
            ),array('target'=>"_blank"));
    }

    /**
     * 插入信息
     * @param array $params
     * @return array|bool
     */
    public function insertInfo($params = array()){
        if(empty($params)){
            return false;
        }
        $params['status'] = isset($params['status']) ? $params['status'] : 0;
        $params['type'] = isset($params['type']) ? strtolower($params['type']) : 'sms';
        return Yii::app()->db->createCommand()->insert('t_driver_upload_sms',$params);
    }
}