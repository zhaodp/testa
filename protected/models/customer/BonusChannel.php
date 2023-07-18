<?php

/**
 * This is the model class for table "{{bonus_channel}}".
 *
 * The followings are the available columns in table '{{bonus_channel}}':
 * @property integer $id
 * @property string $area_id
 * @property string $channel
 * @property string $created
 * @property integer $dis_count
 * @property string $contact
 * @property string $tel
 * @property string $creat_by
 */
class BonusChannel extends FinanceActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{bonus_channel}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('area_id, channel, created', 'required'),
            array('dis_count', 'numerical', 'integerOnly' => true),
            array('area_id, tel, creat_by', 'length', 'max' => 20),
            array('channel, contact', 'length', 'max' => 50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, area_id, channel, created, dis_count, contact, tel, creat_by', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'area_id' => '地区编号',
            'channel' => '发放渠道',
            'created' => '创建时间',
            'dis_count' => '被分配次数',
            'contact' => '联系人',
            'tel' => '联系电话',
            'creat_by' => '渠道创建人',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('area_id', $this->area_id, true);
        $criteria->compare('channel', $this->channel, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('dis_count', $this->dis_count);
        $criteria->compare('contact', $this->contact, true);
        $criteria->compare('tel', $this->tel, true);
        $criteria->compare('creat_by', $this->creat_by, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BonusChannel the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**
     * 获取城市
     * @return array
     * @auther mengtianxue
     */
    public function getArea()
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'area_id';
        $criteria->group = 'area_id';
        $criteria->order = 'id desc';
        $area = self::model()->findAll($criteria);
        $area_arr = array();
        foreach ($area as $v) {
            $area_id = $v->area_id;
            $area_arr[$area_id] = Dict::item('city', $area_id);
        }
        return $area_arr;
    }

    /**
     * 获取渠道
     * @param int $area_id
     * @return array
     * @auther mengtianxue
     */
    public function getChannel($area_id = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = '*';
        $criteria->compare('area_id', $area_id);
        $criteria->order = 'id desc';
        $channel = self::model()->findAll($criteria);
        $channel_arr = array();
        if ($channel) {
            foreach ($channel as $v) {
                $id = $v->id;
                $channel_name = $v->channel;
                $channel_arr[$id] = $channel_name;
            }
        }
        return $channel_arr;
    }


    /**
     * 获取渠道
     * @param int $area_id
     * @return array
     * @auther mengtianxue
     */
    public function getChannelInfoList($area_id = 0, $channel = '', $arr = array())
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'id,area_id,channel,dis_count,contact,tel,distri_by,created,creat_by';
        if ($area_id > 0) {
            $criteria->compare('area_id', $area_id);
        }
        if ($channel != '') {
            $criteria->compare('channel', $channel, true);
        }

        if (count($arr) > 0) {
            $criteria->addInCondition('id', $arr);
        }
        $criteria->addCondition('area_id!=0');
        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;
        return $channel;
    }

    /**
     * 获取渠道数量
     * @param int $area_id
     * @return int
     * @auther mengtianxue
     */
    public static function getChannelCount($area_id = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'count(area_id)';
        $criteria->compare('area_id', $area_id);

        self::$db = Yii::app()->db_readonly;

        $channel = self::model()->count($criteria);

        return $channel;
    }

    /**
     * 获取渠道数量
     * @param array $area_id
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelCountByIds($area_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'area_id,count(*) as channel';
        $criteria->addInCondition("area_id", $area_id);
        $criteria->group = 'area_id';
        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;
        $result = array();
        foreach ($channel as $value) {
            $result[$value['area_id']] = $value['channel'];
        }
        return $result;
    }

    /**
     * 获取详情
     * @param int $id
     * @return array
     * @auther mengtianxue
     */
    public function getInfoById($id = 0)
    {
		$info = self::model()->findByPk($id);
        return $info;
    }

    /**
     * 获取渠道名称,被分配次数
     * @param int $id
     * @return string
     * @auther mengtianxue
     */
    public static function getChannelNameById($id)
    {
        $criteria = new CDbCriteria;
        $criteria->addInCondition('id', $id);//这个id的是一个数组，如果非要传那个串，需要使用implode或者explode
        $criteria->select = 'id,channel,contact,tel,dis_count,area_id,distri_by';
        self::$db = Yii::app()->db_readonly;
        $result = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;
        return $result;
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function actionGetInfo($id)
    {
        $model = ChannelBonus::model()->findByPk($id);
        return $model;
    }


    /**
     * 统计渠道数量
     * @param int $area_id
     * @return int
     * @auther mengtianxue
     */
    public static function getChannelCountAll($city_id = 0,$dateStart='', $dateEnd = '')
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'count(id)';
        $arr = array();
        if ($city_id != 0) {
            $criteria->addCondition("area_id=:area_id");
            $criteria->params[":area_id"] = $city_id;
        }

        if ($dateEnd != ''&&$dateStart!='') {
            $criteria->addBetweenCondition("created", $dateStart, $dateEnd);
        }

        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->count($criteria);
        self::$db = Yii::app()->db;
        return $channel;
    }


    /**
     * 获取渠道数量
     * @param int $channel
     * @return array
     * @auther mengtianxue
     */
    public static function getChannelIdByName($channel)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'id';
        $criteria->compare('channel', $channel, true);

        self::$db = Yii::app()->db_readonly;
        $channels = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;
        $arr_ids = array();
        foreach ($channels as $value) {
            array_push($arr_ids, $value['id']);
        }
        return $arr_ids;
    }


    /**
     * 分配实体卷
     * @param $bonus_sns
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function disUpdateDistriBy($bonus)
    {
        $channel = self::model()->findByPk($bonus['id']);
        $channel->distri_by = $bonus['distri_by'];
        $channel->dis_count = $channel['dis_count']+1;
        $result=1;
        if (!$channel->save()) {
            $result=0;
            EdjLog::info(json_encode($channel->getErrors()));
        }
        return $result;
    }


    /**
     * 获取渠道列表
     * @param int $channel
     * @return array
     * @auther mengtianxue
     */
    public static function getChannelListIdByName($channel,$city)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'id,channel';
        $criteria->compare('channel', $channel, true);

        $criteria->addCondition("area_id<=:area_id");
        $criteria->params[":area_id"] = $city;
        self::$db = Yii::app()->db_readonly;
        $channels = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;
        return $channels;
    }

    /**
     * 获取渠道基本信息
     * @param int $id
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelInfo($id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'contact,tel,distri_by';

        $criteria->addCondition("id=:id");
        $criteria->params[":id"] = $id;
        self::$db = Yii::app()->db_readonly;
        $channels = self::model()->find($criteria);
        self::$db = Yii::app()->db;
        $result=array();
        if($channels){
            $result['contact']=$channels['contact'];
            $result['tel']=$channels['tel'];
            $result['distri_by']=$channels['distri_by'];
        }
        return $result;
    }
}
