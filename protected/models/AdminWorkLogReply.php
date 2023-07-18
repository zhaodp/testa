<?php

class AdminWorkLogReply extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{admin_work_log_reply}}';
	}

	public function rules()
	{
		return array(
			array('log_id', 'numerical', 'integerOnly'=>true),
			array('author', 'length', 'max'=>32),
			array('content, create_time, update_time', 'safe'),
			array('id, log_id, author, content, create_time, update_time', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'log_id' => 'Log',
			'author' => 'Author',
			'content' => '回复内容',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('log_id',$this->log_id);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('update_time',$this->update_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    
    public function beforeSave() {
        $time = date('Y-m-d H:i:s',time());
        if(parent::beforeSave()){
            if($this->isNewRecord){
                $this->author = Yii::app()->user->id;
                $this->create_time = $time;
            }
            $this->update_time = $time;
            return true;
        }
        return false;
    }
    
    public function getReplyByLogId($id){
        $criteria = new CDbCriteria;
        $criteria->order = 'update_time';
        return self::model()->findAllByAttributes(array('log_id'=>$id), $criteria);
    }
}