<?php

/**
 * This is the model class for table "t_user_notify_banner".
 *
 * The followings are the available columns in table 't_user_notify_banner':
 * @property integer $Id
 * @property integer $t_user_notify_id
 * @property string $word
 * @property integer $word_order_status
 * @property string $banner_picture_url
 * @property string $banner_jump_url
 * @property integer $banner_order_status
 */
class UserNotifyBanner extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{user_notify_banner}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('t_user_notify_id', 'unique'),
			array('t_user_notify_id', 'numerical', 'integerOnly'=>true),
			array('word, banner_picture_url, banner_jump_url, word_order_status, banner_order_status', 'length', 'max'=>255),
            array('banner_picture_url,banner_jump_url', 'url'),
            array('word, banner_picture_url, word_order_status, banner_order_status', 'validateRequired'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('Id, t_user_notify_id, word, word_order_status, banner_picture_url, banner_jump_url, banner_order_status', 'safe', 'on'=>'search'),
		);
	}

    /**
     *
     */
public function validateRequired(){
    if(!$this->hasErrors())
    {
        if(empty($this->word) && empty($this->banner_picture_url)) {
            $this->addError("total",'文字或banner图片(至少配置一项)');
        }
        if(!empty($this->word) && empty($this->word_order_status)) {
            $this->addError("word_order_status",'文字显示状态不可为空');
        }
        if(!empty($this->banner_picture_url) && empty($this->banner_order_status)) {
            $this->addError("banner_order_status",'banner显示状态不可为空');
        }

    }

}
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
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
			'word' => '显示文案',
			'word_order_status' => '请选择显示文字通知的订单状态(多选)',
			'banner_picture_url' => 'banner图片地址',
			'banner_jump_url' => 'banner跳转地址',
			'banner_order_status' => '请选择显示banner的订单状态(多选)',
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
		$criteria->compare('word_order_status',$this->word_order_status);
		$criteria->compare('banner_picture_url',$this->banner_picture_url,true);
		$criteria->compare('banner_jump_url',$this->banner_jump_url,true);
		$criteria->compare('banner_order_status',$this->banner_order_status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserNotifyBanner the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * @param $userNotifyId
     * word,word_order_status
     * 返回数组
     */
	public  function itemsWord($userNotifyId)
	{
        if(empty($userNotifyId)){
            return;
        }
		//TO_DO 根据$userNotifyId，返回表中所有数据，并按照 word_order_status asc,id desc排序
        $criteria=new CDbCriteria;
        $params = array();
        $criteria->addCondition('t_user_notify_id=:t_user_notify_id');
        $params[':t_user_notify_id'] = $userNotifyId;


        $criteria->addCondition('word is not null');
        $criteria->params=$params;
        $userNotifyBanner=$this->find($criteria);
        if (empty($userNotifyBanner)){
            return;
        }

        $word_order_status = isset($userNotifyBanner->word_order_status)?$userNotifyBanner->word_order_status:"";
        $word = isset($userNotifyBanner->word)?$userNotifyBanner->word:"";
        if (empty($word_order_status)){
            return;
        }
        $word_order_status_list=explode(',',$word_order_status);
        if($word_order_status_list){
            $retParam = array();
            foreach($word_order_status_list as $word_order_status){
                if(empty($word_order_status)){
                    continue;
                }
                $retParam[]=array('word'=>$word,
                    'word_order_status'=>$word_order_status);
            }
            return $retParam;
        }
	}
    /**
     *返回数组
* @property string $banner_picture_url
* @property string $banner_jump_url
* @property integer $banner_order_status
     *
     **/
	public  function itemsBanner($userNotifyId)
	{
        if(empty($userNotifyId)){
            return;
        }
        //TO_DO 根据$userNotifyId，返回表中所有数据，并按照 word_order_status asc,id desc排序
        $criteria=new CDbCriteria;
        $params = array();
        $criteria->addCondition('t_user_notify_id=:t_user_notify_id');
        $params[':t_user_notify_id'] = $userNotifyId;
        $criteria->addCondition('banner_picture_url is not null');
        $criteria->params=$params;
        $userNotifyBanner=$this->find($criteria);
        if (empty($userNotifyBanner)){
            return;
        }

        $banner_order_status = isset($userNotifyBanner->banner_order_status)?$userNotifyBanner->banner_order_status:"";
        $banner_picture_url = isset($userNotifyBanner->banner_picture_url)?$userNotifyBanner->banner_picture_url:"";
        $banner_jump_url = isset($userNotifyBanner->banner_jump_url)?$userNotifyBanner->banner_jump_url:"";
        if (empty($banner_order_status)){
            return;
        }

        $banner_order_status_list=explode(',',$userNotifyBanner->banner_order_status);
        if($banner_order_status_list){
            $retParam = array();
            foreach($banner_order_status_list as $banner_order_status){
                if(empty($banner_order_status)){
                    continue;
                }
                $retParam[]=array('banner_picture_url'=>$banner_picture_url,
                    'banner_jump_url'=>$banner_jump_url,
                    'banner_order_status'=>$banner_order_status);
            }
            return $retParam;
        }
	}

	
}
