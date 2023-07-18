<?php

/**
 * This is the model class for table "{{call_phone_report}}".
 *
 * The followings are the available columns in table '{{call_phone_report}}':
 * @property integer $id
 * @property string $user_id
 * @property string $name
 * @property integer $call_count
 * @property integer $order_count
 * @property integer $dispatch_count
 * @property integer $report_time
 * @property integer $created
 */
class CallPhoneReport extends ReportActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverBonusRankReport the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return CDbConnection database connection
	 */
//	public function getDbConnection()
//	{
//		return Yii::app()->dbreport;
//	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{call_phone_report}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('user_id, call_count, order_count, dispatch_count, report_time', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>20),
			array('bonus_code', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, user_id, call_count, order_count, dispatch_count, report_time, created', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'user_id' => 'User ID',
			'call_count' => 'Call Count',
			'order_count' => 'Order Count',
			'dispatch_count' => 'Dispatch Count',
			'report_time' => 'Report Time',
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
		$criteria->compare('name',$this->name);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('call_count',$this->call_count);
		$criteria->compare('order_count',$this->order_count);
		$criteria->compare('dispatch_count',$this->dispatch_count);

        //如果不选择开始时间  默认向前推一天
        if(empty($this->report_time)){
            $this->report_time = date('Y-m-d H:i:s',strtotime("-1 day"));
        }

        //借用一下created字段 作为搜索时候的结束时间  修改的时候请注意
        if(empty($this->created)){
            $this->created = date('Y-m-d H:i:s');
        }



		$criteria->addBetweenCondition('report_time', $this->report_time, $this->created);
		return new CActiveDataProvider($this, array(
            'pagination'=>array (
                'pageSize'=>30
            ),
            'criteria'=>$criteria,
		));
	}

    /**
     * 统计条件查询的总和
     * @author mengtianxue 2013-06-01
     * @param $data 参数有   name 开始时间（start_time） 结束时间(end_time)
     * @return array
     */
    public function getCallPhoneReportByName($data){

        $params = array();
        $where =  'id > 1';
        if(!empty($data['name'])){
            $where  .= ' and name = :name';
            $params[':name'] = $data['name'];
        }

        //默认向前推一天
        $where .= ' and report_time >= :start_time';
        $params[':start_time'] = date('Y-m-d H:i:s', strtotime("-1 day"));
        if(!empty($data['start_time'])){
            $params[':start_time'] = $data['start_time'];
        }

        $where .= ' and report_time < :end_time';
        $params[':end_time'] = date('Y-m-d H:i:s');

        if(!empty($data['end_time'])){
            $params[':end_time'] = $data['end_time'];
        }

        $call_count = array('name' => 0, 'call_count' => 0, 'order_count' => 0, 'dispatch_count' => 0);

        $call = Yii::app()->dbreport->createCommand()
                        ->select("name, sum(call_count) as call_count, sum(order_count) as order_count, sum(dispatch_count) as dispatch_count")
                        ->from($this->tableName())
                        ->where($where, $params)
                        ->queryRow();
        if(!empty($call)){
            $call_count = $call;
        }

        return $call_count;
    }
}