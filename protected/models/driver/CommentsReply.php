<?php

/**
 * This is the model class for table "{{comments_reply}}".
 *
 * The followings are the available columns in table '{{comments_reply}}':
 * @property integer $id
 * @property integer $comment_id
 * @property string $description
 * @property string $operator
 * @property integer $created
 */
class CommentsReply extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CommentsReply the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{comments_reply}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'comment_id', 
				'required'), 
			array (
				'comment_id', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'description', 
				'length', 
				'max'=>1024), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'comment_id, description', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array (
			'comments'=>array (
				self::HAS_ONE, 
				'Comments', 
				'comment_id'));
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'comment_id'=>'Comment', 
			'description'=>'处理意见', 
			'operator'=>'操作人', 
			'created'=>'修改时间');
	}
	
	public function beforeSave() {
		if (parent::beforeSave()) {
			$this->operator = Yii::app()->user->getId();
			$this->created = time();
			return true;
		}
	}
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('comment_id', $this->comment_id);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('operator', $this->operator, true);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}