<?php
/**
 * 后台派单页活动配置
 */
class DispatchOrderAct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{dispatch_order_act}}';
	}

	
        /**
         * Returns the static model of the specified AR class.
         * Please note that you should have this exact method in all your CActiveRecord descendants!
         * @param string $className active record class name.
         * @return TicketUser the static model class
         */
        public static function model($className=__CLASS__)
        {
                return parent::model($className);
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			 array('begintime, endtime, title, act_url, pic_url, city_ids, created','required'),
                    array('title', 'length', 'max'=>50),
                    array('act_url', 'length', 'max'=>255),
                    array('pic_url', 'length', 'max'=>255),
                    array('city_ids', 'length', 'max'=>500),
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
			'title' => '活动标题',
			'act_url' => '页面地址',
		);
	}



	public function search()
	{
		$criteria=new CDbCriteria;
		$criteria->order = " id desc ";
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                		'pageSize'=>30,
            ),
		));
	}

    /**
     * 获取本城市期限内活动
     * @param $city_id
     * @return array|bool
     */
    public function getDispatchOrderAct($city_id){
        EdjLog::info('begin to get dispatch order act from '.$city_id);
        $atcs = self::model()->findAll();
        if(!$atcs){
            return false;
        }
        $now_time = date('Y-m-d H:i:s',time());
        $act_array = array();
        foreach($atcs as $act){
            if($act->begintime>=$now_time || $act->endtime<=$now_time || $act->status == 1){
                EdjLog::info('活动未开始或过期');
                continue;
            }
            $city_ids = $act->city_ids;
            if($city_ids != '0'){//city_id=0为全国
                $city_array = explode(',', $city_ids);
                if(empty($city_array)){
                    EdjLog::info('该活动城市为空');
                    continue;
                }
                if(!in_array($city_id, $city_array)){
                    EdjLog::info('该活动不适用此城市:'.$city_id);
                    continue;
                }
            }
            $act_array['pic_url'] = $act->pic_url;
            $act_array['act_url'] = $act->act_url;
        }
        if(empty($act_array)){
            EdjLog::info('该城市:'.$city_id.'不存在合适的活动');
            return false;
        }
        return $act_array;
    }

}
