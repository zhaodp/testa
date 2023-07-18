<?php

/**
 * This is the model class for table "{{new_notice_post}}".
 *
 * The followings are the available columns in table '{{new_notice_post}}':
 * @property string $id
 * @property string $title
 * @property string $content
 * @property string $created
 * @property string $opt_user_id
 * @property string $opt_user_name
 * @property integer $is_delete
 */
class NewNoticePost extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{new_notice_post}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created, title, content, opt_user_id, opt_user_name', 'required'),
			array('is_delete', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>100),
			array('opt_user_id', 'length', 'max'=>11),
			array('opt_user_name', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, content, created, opt_user_id, opt_user_name, is_delete', 'safe', 'on'=>'search'),
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
			'title' => '标题',
			'content' => '内容',
			'created' => '创建时间',
			'opt_user_id' => 'Opt User',
			'opt_user_name' => '创建者',
			'is_delete' => 'Is Delete',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('opt_user_id',$this->opt_user_id,true);
		$criteria->compare('opt_user_name',$this->opt_user_name,true);
		$criteria->compare('is_delete',$this->is_delete);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NewNoticePost the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
