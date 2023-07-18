<?php

/**
 * This is the model class for table "t_user_notify_msg".
 *
 * The followings are the available columns in table 't_user_notify_msg':
 * @property integer $Id
 * @property integer $t_user_notify_id
 * @property integer $trigger_condition
 * @property string $word
 * @property string $title
 * @property string $content
 * @property string $button_text
 * @property string $button_url
 * client_page
 */
class UserNotifyMsg extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_notify_msg}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('t_user_notify_id, word,title,content,button_text', 'required'),
//            array('t_user_notify_id', 'unique'),
            array('button_url', 'url'),
            array('trigger_condition', 'triggerValidate'),
			array('t_user_notify_id,,client_page', 'numerical', 'integerOnly'=>true),
			array('word, title, content, button_text, button_url,trigger_condition', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Id, t_user_notify_id, word, title, content, button_text, button_url', 'safe', 'on'=>'search'),
		);
	}
    public function triggerValidate(){
        if(!$this->hasErrors())
        {
            EdjLog::info('come in'.$this->trigger_condition,'console');
            if (empty($this->trigger_condition)) {
                $this->addError('trigger_condition','触发条件不可空');
                EdjLog::info('error','console');
            }

        }
    }
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        return array(
            'UserNotify'=>array(self::BELONGS_TO, 'UserNotify', 't_user_notify_id'),
        );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			't_user_notify_id' => '父表ID',
			'trigger_condition' => '请选择触发通知的事件（多选）',
			'word' => '填写push系统通知文案',
			'title' => '弹屏标题',
			'content' => '弹屏提示正文（支持换行）',
			'button_text' => 'button文案',
            'client_page' => '指定客户端页面（可不填，默认仅打开APP）',
            'button_url' => '跳转链接（可不填，默认点击收起弹屏）',
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

		$criteria->compare('Id',$this->Id);
		$criteria->compare('t_user_notify_id',$this->t_user_notify_id);

		$criteria->compare('word',$this->word,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('button_text',$this->button_text,true);
		$criteria->compare('button_url',$this->button_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserNotifyMsg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @param $userNotifyId
     * @param $trigger_condition
     * 单条数据，对象
     */
    public function itemsMsg($userNotifyId,$trigger_condition){
        if(empty($trigger_condition)|| empty($userNotifyId)){
            return;
        }
        $criteria=new CDbCriteria;
        $criteria->compare('t_user_notify_id',$userNotifyId);
        $criteria->addSearchCondition('trigger_condition',','.$trigger_condition);
        $userNotifyMsg=$this->find($criteria);
        return $userNotifyMsg;
    }

}
