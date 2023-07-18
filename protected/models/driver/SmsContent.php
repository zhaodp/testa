<?php

/**
 * This is the model class for table "{{sms_content}}".
 *
 * The followings are the available columns in table '{{sms_content}}':
 * @property integer $id
 * @property string $phone
 * @property string $content
 * @property integer $comments_id
 * @property string $create_time
 */
class SmsContent extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SmsContent the static model class
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
		return '{{sms_content}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('comments_id,content,phone', 'required'),
			array('comments_id', 'numerical', 'integerOnly'=>true),
			array('phone', 'length', 'max'=>30),
			//array('content', 'length', 'max'=>70),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, phone, content, comments_id, create_time', 'safe', 'on'=>'search'),
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
			'comment'=>array(self::BELONGS_TO,'Comments', 'comments_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'phone' => '接收号码',
			'content' => '短信内容',
			'comments_id' => '评价详情',
			'create_time' => '回复时间',
		);
	}
	public function beforeSave(){
		if(parent::beforeSave()){
			if($this->isNewRecord){
				$this->create_time = date('Y-m-d H:i:s',time());
			}
			return true;
		}else{
			return false;
		}
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
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('comments_id',$this->comments_id);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
				'pagination'=>array (
						'pageSize'=>50)
		));
	}
}