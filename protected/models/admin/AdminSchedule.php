<?php

/**
 * This is the model class for table "{{admin_schedule}}".
 *
 * The followings are the available columns in table '{{admin_schedule}}':
 * @property integer $id
 * @property integer $sender
 * @property integer $to_user
 * @property string $title
 * @property string $msg
 * @property string $begin_date
 * @property string $end_date
 * @property string $begin_time
 * @property string $end_time
 * @property integer $is_all_day
 * @property string $repeat_type
 * @property string $status
 * @property string $type
 * @property integer $create_user
 * @property string $update_time
 * @property string $created
 */
class AdminSchedule extends CActiveRecord
{
    //类型
    const TYPE1=1;
    const TYPE2=2;
    const TYPE3=3;

    public static $types=array(
        1=>'我的任务',
        2=>'给别人的',
        3=>'公共的',
    );

    //事项状态
    const STATUS1=1;
    const STATUS2=2;
    const STATUS3=3;
    const STATUS4=4;
    const STATUS5=5;
    const STATUS6=6;
    const STATUS7=7;

    public static $statuses=array(
        1=>'刚新建的任务',
        2=>'已经收的任务',
        3=>'进行中的任务',
        4=>'完成的任务',
        5=>'未完成的任务',
        6=>'未接收的任务',
        7=>'拒绝的任务',
    );

    //展示
    const ISALLDAY0=0;
    const ISALLDAY1=1;

    public static $is_all_days=array(
        0=>'展示',
        1=>'隐藏',
    );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{admin_schedule}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, begin_date, end_date, begin_time, end_time, repeat_type, create_user, update_time, created', 'required'),
			array('sender, to_user, is_all_day, create_user', 'numerical', 'integerOnly'=>true),
			array('title, msg', 'length', 'max'=>255),
			array('repeat_type', 'length', 'max'=>10),
			array('status', 'length', 'max'=>1),
			array('type', 'length', 'max'=>2),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sender, to_user, title, msg, begin_date, end_date, begin_time, end_time, is_all_day, repeat_type, status, type, create_user, update_time, created', 'safe', 'on'=>'search'),
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
			'sender' => 'Sender',
			'to_user' => 'To User',
			'title' => 'Title',
			'msg' => 'Msg',
			'begin_date' => 'Begin Date',
			'end_date' => 'End Date',
			'begin_time' => 'Begin Time',
			'end_time' => 'End Time',
			'is_all_day' => 'Is All Day',
			'repeat_type' => 'Repeat Type',
			'status' => 'Status',
			'type' => 'Type',
			'create_user' => 'Create User',
			'update_time' => 'Update Time',
			'created' => 'Created',
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
		$criteria->compare('sender',$this->sender);
		$criteria->compare('to_user',$this->to_user);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('msg',$this->msg,true);
		$criteria->compare('begin_date',$this->begin_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('begin_time',$this->begin_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('is_all_day',$this->is_all_day);
		$criteria->compare('repeat_type',$this->repeat_type,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('create_user',$this->create_user);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('created',$this->created,true);

        $criteria->order='created desc';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AdminSchedule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
