<?php
/**
 * This is the model class for table "{{bonus_library}}".
 *
 * The followings are the available columns in table '{{bonus_library}}':
 * @property string $id
 * @property string $bonus_sn
 * @property string $password
 * @property string $money
 * @property integer $bonus_id
 * @property integer $status
 * @property integer $sn_type
 * @property string $effective_date
 * @property string $binding_deadline
 * @property string $end_date
 * @property string $create_by
 * @property string $created
 * @property int $city_id
 */
Yii::import("application.models.customer.*");

class BonusLibrary extends FinanceActiveRecord
{
    //未使用
    const STATUS_NO_USE = 0;

    //已使用
    const STATUS_USE = 1;
    /** 实体卡税率 为6个点 */
    const RATE_COUNPON = 6;

    public $bonusNames = array();

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return BonusLibrary the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{bonus_library}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('effective_date, binding_deadline, create_by, created', 'required'),
            array('bonus_id, status, sn_type', 'numerical', 'integerOnly' => true),
            array('bonus_sn, money', 'length', 'max' => 30),
            array('password, city_id', 'length', 'max' => 10),
            array('update_by, owner, number, channel', 'length', 'max' => 20),
            array('create_by', 'length', 'max' => 32),
            array('distri_by', 'length', 'max' => 50),
            array('end_date', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, bonus_sn, password, money, channel, bonus_id, status, sn_type, effective_date, binding_deadline, end_date, create_by, created, owner, city_id, number', 'safe', 'on' => 'search'),
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
            'bonus_sn' => '优惠券号码',
            'number' => '优惠码编号',
            'password' => '优惠券密码',
            'money' => '优惠券金额',
            'channel' => '渠道类型',
            'bonus_id' => '优惠券ID',
            'status' => '是否已经绑定',
            'sn_type' => '优惠券类型',
            'effective_date' => '生效日期',
            'binding_deadline' => '绑定截止日期',
            'end_date' => '使用截止时间',
            'create_by' => '创建人',
            'created' => '创建时间',
            'update_by' => '修改人',
            'update' => '修改时间',
            'owner' => '所有者',
            'city_id' => '城市',
            'datestart' => '开始时间',
            'channel_num' => '渠道数量',
            'num_all' => '实体卷数量',
            'distried' => '总分配数量',
            'distring' => '未分配数量',
            'used' => '已使用数量',
            'distri_by' => '分配人',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($pageSize = NULL)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('bonus_sn', $this->bonus_sn, true);
        $criteria->compare('number', $this->number, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('money', $this->money, true);
        $criteria->compare('channel', $this->channel);
        $criteria->compare('bonus_id', $this->bonus_id);
        $criteria->compare('status', $this->status);
        $criteria->compare('sn_type', $this->sn_type);
        $criteria->compare('effective_date', $this->effective_date, true);
        $criteria->compare('binding_deadline', $this->binding_deadline, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('create_by', $this->create_by, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('owner', $this->owner, true);
        $criteria->compare('city_id', $this->city_id);
        $criteria->order = 'id asc';

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $pageSize !== NULL ? $pageSize : 10,
            ),
        ));
    }


    public function searchDistri($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '')
    {
        $where = "sn_type = 2 and city_id!=0 and CASE WHEN :city_id=0 THEN 1=1 ELSE city_id=:city_id END AND CASE WHEN :dateStart!='' AND :dateEnd!=''
         THEN created BETWEEN :dateStart AND :dateEnd ELSE 1=1 END";
        $params = array(':city_id' => $city_id, ':dateStart' => $dateStart, 'dateEnd' => $dateEnd);


        $count = Yii::app()->db_finance->createCommand()
            ->select('id,city_id,count(id) as num_all,SUM(case WHEN  `channel`=0 AND is_use=1 THEN 1 ELSE 0 END) as distring,
SUM(case WHEN `channel`>0 THEN 1 ELSE 0 END) as distried,
SUM(case WHEN `channel`=0 and is_use=1 THEN 1 ELSE 0 END) as unusual,
SUM(case WHEN `channel`>0 and is_use=1 THEN 1 ELSE 0 END) as used')
            ->from('{{bonus_library}}')
            ->where($where, $params)
            ->group('city_id')
            ->order('used DESC')
            ->queryAll();

        return new CArrayDataProvider($count, array(
            'id' => 'id',
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
    }


    public function searchBonusAll($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '')
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'city_id,channel,COUNT(*) as bonus_sn';
        if ($channel != 0) {
            $criteria->group = 'channel';
        } else {
            $criteria->group = 'city_id';
        }

        if ($dateStart != '' && $dateEnd != '') {
            $criteria->addBetweenCondition("created", $dateStart, $dateEnd);
        }

        self::$db = Yii::app()->db_finance;
        $count = self::model()->findAll($criteria);

        $result = array();
        if ($channel == 0) {
            foreach ($count as $value) {
                $result[$value['city_id']] = $value['bonus_sn'];
            }
        } else {
            foreach ($count as $value) {
                $result[$value['channel']] = $value['bonus_sn'];
            }
        }
        return $result;
    }


    /**统计已分配实体卷数量
     * @param int $city_id
     * @param int $channel
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function searchBonusDistriedCount($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '')
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'city_id,channel,COUNT(*) as channel';
        //$channel>0表示按渠道查找,else 按城市查找
        if ($channel != 0) {
            $criteria->group = 'channel';
        } else {
            $criteria->group = 'city_id';
        }

        if ($dateStart != '' && $dateEnd != '') {
            $criteria->addBetweenCondition("distri_date", $dateStart, $dateEnd);
            $criteria->addCondition("created<=:dateEnd");
            $criteria->params[":dateEnd"] = $dateEnd;
        }

        //对总部来说，只要分配到城市就算已分配
        if ($channel != 0) {
            $criteria->addCondition("channel!=0");
        } else {
            $criteria->addCondition("city_id!=0");
        }


        self::$db = Yii::app()->db_finance;
        $count = self::model()->findAll($criteria);

        $result = array();
        if ($channel == 0) {
            foreach ($count as $value) {
                $result[$value['city_id']] = $value['channel'];
            }
        } else {
            foreach ($count as $value) {
                $result[$value['channel']] = $value['channel'];
            }
        }
        return $result;
    }


    /**统计未分配(库存)实体卷数量列表
     * @param int $city_id
     * @param int $channel
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function searchBonusList($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '', $Column
        , $arr = array())
    {
        $criteria = new CDbCriteria;

        $criteria->select = 'id,city_id,channel,COUNT(*) as bonus_sn';


        //$channel>0表示按渠道查找,else 按城市查找
        if ($channel != 0) {
            $criteria->group = 'channel';
            if (count($arr) > 0) {
                $criteria->addInCondition("channel", $arr);
            }
        } else {
            //if ($city_id == 0) {
            $criteria->group = 'city_id';
            if ($city_id != 0) {
                $criteria->addCondition("city_id=:city_id");
                $criteria->params[":city_id"] = $city_id;
            }
        }
        $criteria->addCondition("sn_type=2 and city_id!=0");

        switch ($Column) {
            //查询实体卷总数
            case 'bonus_sn': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addBetweenCondition("distri_city_date", $dateStart, $dateEnd);
                }

            }
                break;
            //查询已分配实体卷总数
            case 'channel': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addBetweenCondition("distri_date", $dateStart, $dateEnd);
                    $criteria->addCondition("created<=:dateEnd");
                    $criteria->params[":dateEnd"] = $dateEnd;
                }
                $criteria->addCondition("channel!=0 and status!=2");
            }
                break;
            //查询未分配实体卷总数
            case 'money': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addCondition('distri_date>:dateEnd or distri_date is null');
                    $criteria->addCondition('distri_city_date<=:dateEnd');
                    $criteria->params[":dateEnd"] = $dateEnd;
                }

                $criteria->addCondition("channel=0 and status!=2 and is_use=0");
            }
                break;
            //查询已使用实体卷总数
            case 'number': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addBetweenCondition("use_date", $dateStart, $dateEnd);
                }

                $criteria->addCondition("city_id!=0 and channel!=0 and status!=2 and is_use=1");
                $criteria->order = 'bonus_sn desc';
            }
                break;
            //查询未使用实体卷总数
            case 'password': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addCondition('use_date>:dateEnd or is_use=0');
                    if ($city_id == 0) {
                        $criteria->addBetweenCondition("distri_city_date", $dateStart, $dateEnd);
                    } else {
                        $criteria->addBetweenCondition("distri_date", $dateStart, $dateEnd);
                    }
                    $criteria->params[":dateEnd"] = $dateEnd;
                }
                $criteria->addCondition("channel!=0 and status!=2 and is_use=0");
            }
                break;
            //查询坏卡实体卷总数
            case 'bonus_id': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addBetweenCondition("error_date", $dateStart, $dateEnd);
                }

                $criteria->addCondition("status=2");
                $criteria->addCondition("is_use!=0");
            }
                break;

            //查询异常卡实体卷总数
            case 'owner': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addBetweenCondition("use_date", $dateStart, $dateEnd);
                }

                $criteria->addCondition("(city_id=0 or channel=0 ) and is_use=1");
            }
                break;
        }

        self::$db = Yii::app()->db_finance;
        $count = self::model()->findAll($criteria);

        $result = array();


        if ($channel == 0) {
            foreach ($count as $value) {
                $result[$value['city_id']]['id'] = $value['id'];
                $result[$value['city_id']]['bonus_sn'] = $value['bonus_sn'];
                $result[$value['city_id']]['city_id'] = $value['city_id'];
            }
        } else {
            foreach ($count as $value) {
                $result[$value['channel']]['id'] = $value['id'];
                $result[$value['channel']]['bonus_sn'] = $value['bonus_sn'];
                $result[$value['channel']]['channel'] = $value['channel'];
                $result[$value['channel']]['city_id'] = $value['city_id'];
                //$result[$value['channel']]['distri_by'] = $value['distri_by'];
            }
        }
        return $result;
    }

    /**统计已使用实体卷数量
     * @param int $city_id
     * @param int $channel
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function searchBonusUsedCount($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '')
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'city_id,channel,COUNT(*) as number';
        //$channel>0表示按渠道查找,else 按城市查找
        if ($channel != 0) {
            $criteria->group = 'channel';
        } else {
            $criteria->group = 'city_id';
        }

        if ($dateStart != '' && $dateEnd != '') {
            $criteria->addBetweenCondition("use_date", $dateStart, $dateEnd);
            $criteria->addCondition("created<=:dateEnd");
            $criteria->params[":dateEnd"] = $dateEnd;
        }

        //对总部来说，只要分配到城市就算已分配,库房包括未分配和坏卡，分公司没有坏卡
        if ($channel != 0) {
            $criteria->addCondition("chanel=0 and status!=2 and is_use=0");
        } else {
            $criteria->addCondition("city_id=0 and is_use=0");
        }


        self::$db = Yii::app()->db_finance;
        $count = self::model()->findAll($criteria);

        $result = array();
        if ($channel == 0) {
            foreach ($count as $value) {
                $result[$value['city_id']] = $value['money'];
            }
        } else {
            foreach ($count as $value) {
                $result[$value['channel']] = $value['money'];
            }
        }
        return $result;
    }

    public function searchChannelBonus($city_id = 0, $channel = '', $disTri_by = '', $dateStart, $dateEnd)
    {
        $channel = BonusChannel::model()->getChannelInfoList($city_id, $channel, $disTri_by);
        $arr_channel = array();

        foreach ($channel as $value) {
            $arr_channel[$value['id']] = $value;
        }
        $arr_channel_ids = array_keys($arr_channel);

        $criteria = new CDbCriteria;
        $params = array();
        if ($city_id > 0) {
            $criteria->addCondition("city_id=:city_id");
            $criteria->params[":city_id"] = $city_id;
        }

        if ($dateStart != '' && $dateEnd != '') {
            $criteria->addBetweenCondition("created", $dateStart, $dateEnd);
        }

        $criteria->addCondition("sn_type=2");
        $criteria->addCondition('channel!=0');

        if ($channel != '') {
            $criteria->addInCondition('channel', $arr_channel_ids);
        }
        $criteria->select = 'channel,
SUM(case WHEN `channel`!=0 THEN 1 ELSE 0 END) as number,
SUM(case WHEN `channel`!=0 and `is_use`=1 THEN 1 ELSE 0 END) as password';
        $criteria->group = 'channel';
        $criteria->order = 'number DESC';
        self::$db = Yii::app()->db_finance;
        $count = self::model()->findAll($criteria);

        $array_id = array();
        $data = array();
        foreach ($count as $value) {
            array_push($array_id, $value['channel']);
            $arr = array();
            foreach ($value as $key => $v) {
                $arr[$key] = $v;
            }
            if (array_key_exists($value['channel'], $arr_channel)) {
                $arr["channel_name"] = $arr_channel[$value['channel']]["channel"];
                $arr["contact"] = $arr_channel[$value['channel']]['contact'];
                $arr["tel"] = $arr_channel[$value['channel']]["tel"];
                $arr['dis_count'] = $arr_channel[$value['channel']]['dis_count'];
                $arr['city_id'] = $arr_channel[$value['channel']]['area_id'];
                $arr['distri_by'] = $arr_channel[$value['channel']]['distri_by'];
                $arr['id'] = $arr_channel[$value['channel']]['id'];
            } else {
                $arr["channel_name"] = '';
                $arr["contact"] = '';
                $arr["tel"] = '';
                $arr['dis_count'] = 0;
                $arr['city_id'] = 0;
                $arr['distri_by'] = '';
                $arr['id'] = 0;
            }
            $arr['dateStart'] = $dateStart;
            $arr['dateEnd'] = $dateEnd;
            array_push($data, $arr);
        }

        foreach ($channel as $value) {
            if (!array_key_exists($value['id'], $array_id)) {
                $arr = array();
                $arr["channel_name"] = $value["channel"];
                $arr["contact"] = $value['contact'];
                $arr["tel"] = $value["tel"];
                $arr['dis_count'] = $value['dis_count'];
                $arr['city_id'] = $value['area_id'];
                $arr['distri_by'] = $value['distri_by'];
                $arr['id'] = $value['id'];
                $arr['number'] = 0;
                $arr['password'] = 0;
                $arr['dateStart'] = $dateStart;
                $arr['dateEnd'] = $dateEnd;
                array_push($data, $arr);
            }
        }

        return new CArrayDataProvider($data, array(
            'id' => 'id',
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
    }


    public function searchBonusDisCount($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '', $arr_bonus_id = array())
    {
        $data = array();
        //总数
        $data['bonus_sn'] = $this->searchBonusCount($city_id, $channel, $dateStart, $dateEnd, 'bonus_sn');
        //已分配
        $data['channel'] = $this->searchBonusCount($city_id, $channel, $dateStart, $dateEnd, 'channel');
        //未分配
        $data['money'] = $this->searchBonusCount($city_id, $channel, $dateStart, $dateEnd, 'money');
        //已使用
        $data['number'] = $this->searchBonusCount($city_id, $channel, $dateStart, $dateEnd, 'number');
        //未使用
        $data['password'] = $this->searchBonusCount($city_id, $channel, $dateStart, $dateEnd, 'password');
        //坏卡
        $data['bonus_id'] = $this->searchBonusCount($city_id, $channel, $dateStart, $dateEnd, 'bonus_id');
        //异常卡
        $data['owner'] = $this->searchBonusCount($city_id, $channel, $dateStart, $dateEnd, 'owner');

        $data['update_by'] = BonusChannel::model()->getChannelCountAll($city_id, $dateStart, $dateEnd);
        $data['id'] = 1;

        if (count($arr_bonus_id) > 0) {
            $data['contact'] = $arr_bonus_id['contact'];
            $data['tel'] = $arr_bonus_id['tel'];
            $data['distri_by'] = $arr_bonus_id['distri_by'];
        } else {
            $data['contact'] = '';
            $data['tel'] = '';
            $data['distri_by'] = '';
        }

        $result = array();
        array_push($result, $data);
        return new CArrayDataProvider($result, array(
            'id' => 'id',
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
    }


    /**统计未分配(库存)实体卷数量
     * @param int $city_id
     * @param int $channel
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function searchBonusCount($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '', $Column, $arr = array())
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'count(*)';
        //$channel>0表示按渠道查找,else 按城市查找
        if ($city_id != 0) {
            $criteria->addCondition("city_id=:city_id");
            $criteria->params[":city_id"] = $city_id;
            if ($channel != 0) {
                $criteria->addCondition("channel=:channel");
                $criteria->params[":channel"] = $channel;
            }
        }

        $criteria->addCondition("sn_type=2");

        switch ($Column) {
            //查询实体卷总数
            case 'bonus_sn': {
                if ($channel != 0) {
                    if ($dateStart != '' && $dateEnd != '') {
                        $criteria->addBetweenCondition("distri_date", $dateStart, $dateEnd);
                    }
                } elseif ($city_id != 0) {
                    if ($dateStart != '' && $dateEnd != '') {
                        $criteria->addBetweenCondition("distri_city_date", $dateStart, $dateEnd);
                    }
                } else {
                    if ($dateStart != '' && $dateEnd != '') {
                        $criteria->addBetweenCondition("created", $dateStart, $dateEnd);
                    }
                }
            }
                break;
            //查询已分配实体卷总数
            case 'channel': {
                if ($dateStart != '' && $dateEnd != '') {
                    if ($city_id == 0) {
                        $criteria->addBetweenCondition("distri_city_date", $dateStart, $dateEnd);
                    } else {
                        $criteria->addBetweenCondition("distri_date", $dateStart, $dateEnd);
                    }
                }
                if ($city_id == 0) {
                    $criteria->addCondition("city_id!=0 and status!=2");
                } else {
                    $criteria->addCondition("channel!=0 and status!=2");
                }
            }
                break;
            //查询未分配实体卷总数
            case 'money': {
                if ($dateStart != '' && $dateEnd != '') {
                    if ($city_id == 0) {
                        $criteria->addCondition('distri_city_date>:dateEnd or distri_city_date is null');
                        $criteria->addCondition("created<=:dateEnd");
                    } else {
                        $criteria->addCondition('distri_date>:dateEnd or distri_date is null');
                        $criteria->addCondition("distri_city_date<=:dateEnd");
                    }
                    $criteria->params[":dateEnd"] = $dateEnd;
                }
                if ($city_id == 0) {
                    $criteria->addCondition("city_id=0 and channel=0 and status!=2 and is_use=0");
                } else {
                    $criteria->addCondition("channel=0 and status!=2 and is_use=0");
                }
            }
                break;
            //查询已使用实体卷总数
            case 'number': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addBetweenCondition("use_date", $dateStart, $dateEnd);
                    if ($city_id == 0) {
                        $criteria->addBetweenCondition("distri_city_date", $dateStart, $dateEnd);
                    } else {
                        $criteria->addBetweenCondition("distri_date", $dateStart, $dateEnd);
                    }
                }
                $criteria->addCondition("city_id!=0 and channel!=0 and status!=2 and is_use=1");
                $criteria->order = 'bonus_sn desc';
            }
                break;
            //查询未使用实体卷总数
            case 'password': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addCondition('use_date>:dateEnd or is_use=0');
                    $criteria->params[':dateEnd'] = $dateEnd;
                    if ($city_id == 0) {
                        $criteria->addBetweenCondition("distri_city_date", $dateStart, $dateEnd);
                    } else {
                        $criteria->addBetweenCondition("distri_date", $dateStart, $dateEnd);
                    }
                }
                if ($city_id == 0) {
                    $criteria->addCondition("city_id!=0 and status!=2 and is_use=0");
                } else {
                    $criteria->addCondition("city_id!=0 and channel!=0 and status!=2 and is_use=0");
                }
            }
                break;
            //查询坏卡实体卷总数
            case 'bonus_id': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addBetweenCondition("error_date", $dateStart, $dateEnd);
                }
                $criteria->addCondition("city_id=0");
                $criteria->addCondition("status=2 and is_use!=1");
            }
                break;

            //查询异常卡实体卷总数
            case 'owner': {
                if ($dateStart != '' && $dateEnd != '') {
                    $criteria->addBetweenCondition("use_date", $dateStart, $dateEnd);
                }
                $criteria->addCondition("channel=0 and is_use=1");
            }
                break;
        }

        $count = self::model()->count($criteria);
        return $count;
    }

    public function searchBonusDistried($city_id = 0, $channel = 0, $type = 0, $arr, $arr_bonus_id = array(), $arr_channel_id = array())
    {
        $params = array();
        $criteria = new CDbCriteria;
        $criteria->addCondition("sn_type=2");
        if (array_key_exists('city_id', $arr)) {
            if ($arr['city_id'] != 0) {
                $criteria->addCondition("city_id=:city_id");
                $criteria->params[':city_id'] = $arr['city_id'];
            }
        } elseif ($city_id != 0) {
            $criteria->addCondition("city_id=:city_id");
            $criteria->params[':city_id'] = $city_id;
        }

        if (count($arr_bonus_id) > 0) {
            $criteria->addInCondition("bonus_id", $arr_bonus_id);
        }

        if (count($arr_channel_id) > 0) {
            $criteria->addInCondition("channel", $arr_channel_id);
        }

        if ($channel != 0) {
            $criteria->addCondition("channel=:channel");
            $criteria->params[':channel'] = $channel;
        }

        if (array_key_exists('dateStart', $arr) && array_key_exists('dateEnd', $arr) && $arr['dateStart'] != '' && $arr['dateEnd'] != '') {
            if ($type == 3) {
                $criteria->addBetweenCondition('use_date', $arr['dateStart'], $arr['dateEnd']);
            } else {
                $criteria->addBetweenCondition('distri_date', $arr['dateStart'], $arr['dateEnd']);
            }

        }

        if (array_key_exists('bonus_sn', $arr) && $arr['bonus_sn']) {
            $criteria->addCondition('number=:bonus_sn');
            $criteria->params[':bonus_sn'] = $arr['bonus_sn'];
        }
        if (array_key_exists('password', $arr) && $arr['password']) {
            $criteria->addCondition('bonus_sn=:password');
            $criteria->params[':password'] = $arr['password'];
        }


        if (array_key_exists('distri_type', $arr) && ($arr['distri_type'] == 1 || $arr['distri_type'] == 2)) {
            $criteria->addCondition('distri_type=:distri_type');
            $criteria->params[':distri_type'] = $arr['distri_type'] - 1;
        }

        if (array_key_exists('distri_by', $arr) && $arr['distri_by'] != '') {
            $criteria->addCondition('distri_by=:distri_by');
            $criteria->params[':distri_by'] = $arr['distri_by'];
        }

        if (array_key_exists('sn_type', $arr) && ($arr['sn_type'] == 1 || $arr['sn_type'] == 2)) {
            $criteria->addCondition('is_use=:is_use');
            $criteria->params[':is_use'] = $arr['sn_type'] - 1;
        }
        if ($type == 3) {
            $criteria->addCondition("channel=0");
            $criteria->addCondition("is_use=1");

            if (array_key_exists('operat_by', $arr)) {
                if ($arr['operat_by'] == 1) {
                    $criteria->addCondition("status=2");
                } elseif ($arr['operat_by'] == 2) {
                    $criteria->addCondition("status!=2");
                }
            }
        } else {
            $criteria->addCondition("status!=2");
            if ($city_id == 0) {
                $criteria->addCondition("city_id!=0");
            } else {
                $criteria->addCondition("channel!=0");
            }
        }

        $criteria->order = 'use_date,distri_date desc';

        $criteria->limit = 10000;
        $criteria->select = 'id,bonus_id,status,number,bonus_sn,created,effective_date,binding_deadline,end_date,money,distri_by,is_use,city_id,channel,distri_type,use_date';
        $result = self::model()->findAll($criteria);
        return $result;
    }


    public function searchBonusDistring($city_id = 0, $channel = 0, $type = 0, $arr)
    {
        $criteria = new CDbCriteria;

        if ($city_id != 0) {
            $criteria->addCondition("city_id=:city_id");
            $criteria->params[':city_id'] = $city_id;
        } else {
            $criteria->addCondition("city_id=0");
        }

        if (array_key_exists('bonus_sn', $arr) && $arr['bonus_sn'] != '') {
            $criteria->addCondition("number=:bonus_sn");
            $criteria->params[':bonus_sn'] = $arr['bonus_sn'];
        }

        if (array_key_exists('password', $arr) && $arr['password'] != '') {
            $criteria->addCondition("bonus_sn=:password");
            $criteria->params[':password'] = $arr['password'];
        }

        if (array_key_exists('bonus_id', $arr) && $arr['bonus_id'] != '') {
            $arr_id = BonusCode::getBonusIDByName($arr['bonus_id']);

            if (count($arr_id) == 0) {
                return $arr_id;
            } else {
                $criteria->addInCondition("bonus_id", $arr_id);
            }
        }
        if (array_key_exists('dateStart', $arr) && array_key_exists('dateStart', $arr) && $arr['dateStart'] != '' && $arr['dateEnd'] != '') {
            if ($type == 3) {
                $criteria->addBetweenCondition('error_date', $arr['dateStart'], $arr['dateEnd']);
            } else {
                if ($city_id != 0) {
                    $criteria->addCondition('distri_date>:endDate or distri_date is null');
                    $criteria->addCondition("distri_city_date<=:endDate");
                    $criteria->params[':endDate'] = $arr['dateEnd'];
                    //$criteria->addBetweenCondition('created', $arr['dateStart'], $arr['dateEnd']);
                } else {
                    $criteria->addCondition('distri_city_date>:endDate or distri_city_date is null');
                    $criteria->addCondition("created<=:endDate");
                    $criteria->params[':endDate'] = $arr['dateEnd'];
                }
            }
        }

        $criteria->addCondition("is_use=0");
        $criteria->addCondition("sn_type=2");
        if ($type == 3) {
            $criteria->addCondition("status=2");
        } else {
            $criteria->addCondition("status!=2");
            $criteria->addCondition("channel=0");
        }

        $criteria->select = 'id,bonus_id,number as bonus_sn,created,effective_date,binding_deadline,end_date,money';
        $criteria->limit = 10000;
        if ($city_id != 0) {
            $criteria->order = 'distri_city_date desc';
        } else {
            if ($type == 3) {
                $criteria->order = 'error_date desc';
            } else {
                $criteria->order = 'id asc';
            }
        }
        $result = self::model()->findAll($criteria);
        return $result;
    }


    public function searchSelectBonusCount($start, $end, $city_id)
    {
        $criteria = new CDbCriteria;
        if ($city_id != 0) {
            $criteria->addCondition("city_id=:city_id");
            $criteria->params[':city_id'] = $city_id;
        }
        $criteria->addCondition("sn_type=2");
        $criteria->addBetweenCondition("number", $start, $end);
        if ($city_id == 0) {
            $criteria->select = 'COUNT(*) as bonus_sn,SUM(case WHEN  city_id!=0 or channel!=0  or status=2 or is_use=1 THEN 1  ELSE 0 END) as bonus_id';
        } else {
            $criteria->select = 'COUNT(*) as bonus_sn,SUM(case WHEN  channel!=0 or status=2 or is_use=1 THEN 1  ELSE 0 END) as bonus_id';
        }

        $result = self::model()->find($criteria);
        return $result;
    }


    public function selectCityList()
    {
        $criteria = new CDbCriteria;
        $criteria->addCondition("sn_type=2");
        $criteria->select = 'DISTINCT(city_id)';
        self::$db = Yii::app()->db_readonly;
        $result = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;
        return $result;
    }

    /**
     * 保存之前要更新的字段
     * @return bool
     * author mengtianxue
     */
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->created = date("Y-m-d H:i:s");
            }
            return true;
        }
        return parent::beforeSave();
    }

    /**
     * 检查优惠码是否可用
     * @param $bonus_sn
     * @param int $password
     * @param null $status 为null查询全部，0为未绑定的优惠券，1为已经绑定的优惠券
     * @return int
     * author mengtianxue
     */
    public function checkBonusUse($bonus_sn, $password = 0, $status = null)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('bonus_sn', $bonus_sn);
        $criteria->addCondition('sn_type != :sn_type');
        $criteria->params[':sn_type'] = BonusCode::SN_TYPE_COUNPON;
        if (!is_null($status)) {
            $criteria->compare('status', $status);
        }
        $bonusCode = self::model()->find($criteria);
        $bonusInfo = array();
        if (!$bonusCode) {
            $bonusInvite = Yii::app()->db_readonly->createCommand()
                ->select("*")
                ->from('{{customer_invite}}')
                ->where('bonus_sn = :bonus_sn', array(':bonus_sn' => $bonus_sn))
                ->queryRow();
            if ($bonusInvite) {
                $bonusInfo['bonus_sn'] = $bonus_sn;
                $bonusInfo['bonus_id'] = 16;
                $bonusInfo['status'] = 0;
            }
        } else {
            $bonusInfo = $bonusCode;
            if (empty($password) && $bonusCode['password'] != $password) {
                $bonusInfo['status'] = 2;
            }
        }
        return $bonusInfo;
    }

    /**
     * 修改优惠码当前状态
     * 注意：如果是固定码，优惠码状态不变
     * @param $bonus_sn
     * @param $status 0.未绑定 1.绑定
     * @param int $end_date
     * @return bool
     * author mengtianxue
     */
    public function updateStatus($bonus_sn, $status, $end_date = 0)
    {
        $ret = false;
        $bonus = $this->find('bonus_sn = :bonus_sn', array(':bonus_sn' => $bonus_sn));

        if ($bonus) {
            $bonusCode = BonusCode::model()->find('id = :id', array(':id' => $bonus['bonus_id']));

            //如果是固定吗  $status = 0
            if ($bonus['sn_type'] == BonusCode::SN_TYPE_FIXED_CODE) {
                $status = 0;
            } else {
                //如果优惠码可重复使用  $status = 0
                if (!empty($bonusCode) && $bonusCode['repeat_limited'] == 1) {
                    $status = 0;
                }
            }

            if ($end_date != 0) {
                $bonus->end_date = $end_date;
            }

            $bonus->status = $status;
            $bonus->is_use = 1;
            $bonus->use_date = date('Y-m-d H:i:s');
            $ret = $bonus->update($bonus);
        }
        return $ret;
    }

    /**
     * 优惠券绑定
     * @param $bonus_sn
     * @param $phone
     * @param $password
     * @return bool|void
     * 0：不存在，1：成功，2：还未生效，不能绑定 3：优惠码失效
     * author mengtianxue
     */

    public function BonusBinding($bonus_sn, $phone, $password = 0, $channel = 0, $app_ver = 1, $send_sms = 1, $repeat_bind = 0)
    {
        $ret = array(
            'code' => 2,
            'message' => '抱歉，此优惠券号码无效');

        if (empty($bonus_sn) && empty($phone)) {
            $ret = array(
                'code' => 2,
                'message' => '优惠券和密码都不能为空');
        }

        //vip用户限制
        $vip_phone = VipPhone::model()->getPrimary($phone);

        if (!empty($vip_phone)) {
            $ret = array(
                'code' => 2,
                'message' => '这个用户是我们的VIP用户，不能绑定优惠券');
            return $ret;
        }

        //只能绑定一个优惠码
//        $customerBonus = CustomerBonus::model()->customerPhoneExists($phone);
//        if ($customerBonus) {
//            $ret = array(
//                'code' => 2,
//                'message' => '用户已经绑定过优惠券，还未使用');
//            return $ret;
//        }

        $checkBonusUse = $this->checkBonusUse($bonus_sn, $password);
        if ($checkBonusUse) {

            //商家优惠券不给普通用户绑定
            if (isset($checkBonusUse['ismerchants']) && $checkBonusUse['ismerchants'] == 1) {
                $ret = array(
                    'code' => 2,
                    'message' => '抱歉，此优惠券号码无效');
                return $ret;
            }

            if ($checkBonusUse['status'] == 2) {
                $ret = array(
                    'code' => 2,
                    'message' => '优惠券密码错误');
                return $ret;
            }

            //新客邀请码 当月优惠券帮定数 必须小于400
            if ($checkBonusUse['bonus_id'] == 8) {

                //一人只能绑定一次 新客邀请码
                $newInviteBonus = CustomerBonus::model()->getNewInviteBonus($phone);
                if ($newInviteBonus) {
                    $ret = array(
                        'code' => 2,
                        'message' => '您已经绑定过此优惠券，无法再次绑定');
                    return $ret;
                }

                $bonusCount = CustomerBonus::model()->getCustomerBounsCount($bonus_sn);
                if ($bonusCount > 400) {
                    $ret = array(
                        'code' => 2,
                        'message' => '师傅，辛苦了！您已绑定了400位用户，本月绑定次数已用完，下月继续哟~');
                    return $ret;
                }
            }

            $bonusCode = BonusCode::model()->getBonusCodeById($checkBonusUse['bonus_id'], BonusCode::STATUS_APPROVED, 2, 'not');

            //优惠券类型 必须是已审核
            if (!$bonusCode) {
                $ret = array(
                    'code' => 2,
                    'message' => '此类优惠券不能使用');
                return $ret;
            }

            //优惠券必须是未绑定
            if ($checkBonusUse['status'] != self::STATUS_NO_USE) {
                $ret = array(
                    'code' => 2,
                    'message' => '优惠券已经绑定过');
                return $ret;
            }

            $date_now = date('Y-m-d H:i:s');
            //绑定日期必须在生效时间之后
            if ($bonusCode['effective_date'] > $date_now) {
                $ret = array(
                    'code' => 2,
                    'message' => '优惠券尚未生效');
                return $ret;
            }

            //绑定日期必须在绑定时间之内
            if (strtotime($bonusCode['binding_deadline']) < strtotime($date_now)) {
                $ret = array(
                    'code' => 2,
                    'message' => '优惠券已过有效绑定期限');
                return $ret;
            }

            //符合的用户
            $user_limited = BonusCode::model()->is_user_limited($bonusCode['user_limited'], $phone);
            if (!$user_limited) {
                $ret = array(
                    'code' => 2,
                    'message' => '对不起，您无法使用此类优惠券');
                return $ret;
            }

            //符合的重复使用
            $repeat_limited = BonusCode::model()->is_repeat_limited($bonusCode['repeat_limited'], $phone, $bonus_sn, $repeat_bind);
            if (!$repeat_limited) {
                $ret = array(
                    'code' => 2,
                    'message' => '您已经绑定过此优惠券，无法再次绑定');
                return $ret;
            }
            $ret = $this->Binding($bonusCode, $bonus_sn, $phone, $send_sms, $channel);
            return $ret;
//                $params = array();
//                $params['bonusCode'] = $bonusCode;
//                $params['bonus_sn'] = $bonus_sn;
//                $params['phone'] = $phone;
//
//                //绑定添加队列中执行
//                $task = array(
//                    'method' => 'BonusBinding',
//                    'params' => $params
//                );
//                Queue::model()->putin($task, 'task');
        }

        return $ret;
    }


    /**
     * 优惠券绑定 客服赠送优惠券调用此方法
     * @param $bonus_sn
     * @param $phone
     * @param $password
     * @return bool|void
     * 0：不存在，1：成功，2：还未生效，不能绑定 3：优惠码失效
     * author mengtianxue
     */

    public function backBonusBinding($bonus_sn, $phone, $password = 0, $channel = 0, $app_ver = 1, $send_sms = 1, $repeat_bind = 0)
    {
        $ret = array(
            'code' => 2,
            'message' => '抱歉，此优惠券号码无效');

        if (empty($bonus_sn) && empty($phone)) {
            $ret = array(
                'code' => 2,
                'message' => '优惠券和密码都不能为空');
        }

        //vip用户限制
        $vip_phone = VipPhone::model()->getPrimary($phone);

        if (!empty($vip_phone)) {
            $ret = array(
                'code' => 2,
                'message' => '这个用户是我们的VIP用户，不能绑定优惠券');
            return $ret;
        }

        //只能绑定一个优惠码
//        $customerBonus = CustomerBonus::model()->customerPhoneExists($phone);
//        if ($customerBonus) {
//            $ret = array(
//                'code' => 2,
//                'message' => '用户已经绑定过优惠券，还未使用');
//            return $ret;
//        }

        $checkBonusUse = $this->checkBonusUse($bonus_sn, $password);
        if ($checkBonusUse) {

            //商家优惠券不给普通用户绑定
            if (isset($checkBonusUse['ismerchants']) && $checkBonusUse['ismerchants'] == 1) {
                $ret = array(
                    'code' => 2,
                    'message' => '抱歉，此优惠券号码无效');
                return $ret;
            }

            if ($checkBonusUse['status'] == 2) {
                $ret = array(
                    'code' => 2,
                    'message' => '优惠券密码错误');
                return $ret;
            }

            //新客邀请码 当月优惠券帮定数 必须小于400
            if ($checkBonusUse['bonus_id'] == 8) {

                //一人只能绑定一次 新客邀请码
                $newInviteBonus = CustomerBonus::model()->getNewInviteBonus($phone);
                if ($newInviteBonus) {
                    $ret = array(
                        'code' => 2,
                        'message' => '您已经绑定过此优惠券，无法再次绑定');
                    return $ret;
                }

                $bonusCount = CustomerBonus::model()->getCustomerBounsCount($bonus_sn);
                if ($bonusCount > 400) {
                    $ret = array(
                        'code' => 2,
                        'message' => '师傅，辛苦了！您已绑定了400位用户，本月绑定次数已用完，下月继续哟~');
                    return $ret;
                }
            }

            $bonusCode = BonusCode::model()->getBonusCodeById($checkBonusUse['bonus_id'], BonusCode::STATUS_APPROVED, 2, 'not');

            //优惠券类型 必须是已审核
            if (!$bonusCode) {
                $ret = array(
                    'code' => 2,
                    'message' => '此类优惠券不能使用');
                return $ret;
            }

            //优惠券必须是未绑定
            if ($checkBonusUse['status'] != self::STATUS_NO_USE) {
                $ret = array(
                    'code' => 2,
                    'message' => '优惠券已经绑定过');
                return $ret;
            }

            $date_now = date('Y-m-d H:i:s');
            //绑定日期必须在生效时间之后
            if ($bonusCode['effective_date'] > $date_now) {
                $ret = array(
                    'code' => 2,
                    'message' => '优惠券尚未生效');
                return $ret;
            }

            //绑定日期必须在绑定时间之内
            if (strtotime($bonusCode['binding_deadline']) < strtotime($date_now)) {
                $ret = array(
                    'code' => 2,
                    'message' => '优惠券已过有效绑定期限');
                return $ret;
            }

            //符合的用户
            $user_limited = BonusCode::model()->is_user_limited($bonusCode['user_limited'], $phone);
            if (!$user_limited) {
                $ret = array(
                    'code' => 2,
                    'message' => '对不起，您无法使用此类优惠券');
                return $ret;
            }

            //符合的重复使用
            $repeat_limited = BonusCode::model()->is_repeat_limited($bonusCode['repeat_limited'], $phone, $bonus_sn, $repeat_bind);
            if (!$repeat_limited) {
                $ret = array(
                    'code' => 2,
                    'message' => '您已经绑定过此优惠券，无法再次绑定');
                return $ret;
            }
            $ret = $this->backBinding($bonusCode, $bonus_sn, $phone, $send_sms, $channel);
            return $ret;
//                $params = array();
//                $params['bonusCode'] = $bonusCode;
//                $params['bonus_sn'] = $bonus_sn;
//                $params['phone'] = $phone;
//
//                //绑定添加队列中执行
//                $task = array(
//                    'method' => 'BonusBinding',
//                    'params' => $params
//                );
//                Queue::model()->putin($task, 'task');
        }

        return $ret;
    }


    /**
     * 商家优惠券绑定
     * @param $bonus_sn
     * @param $phone
     * @return bool
     * author mengtianxue
     */
    public function merchantsBind($bonus_sn, $phone)
    {
        $bonus = $this->find('bonus_sn = :bonus_sn', array(':bonus_sn' => $bonus_sn));

        if ($bonus) {
            $bonusCode = BonusCode::model()->getBonusCodeById($bonus['bonus_id'], BonusCode::STATUS_APPROVED);
            $bonusCode = $bonusCode->getAttributes();
            if ($bonusCode) {
                $bonusCode['merchants_created'] = time() - 60;
                $ret = $this->Binding($bonusCode, $bonus_sn, $phone);
                return $ret;
            }
        }
        return false;
    }


    /**
     * 优惠券绑定
     * @param $bonusCode
     * @param $bonus_sn
     * @param $phone
     * @param $is_send 默认是1 发送短信
     * @return array
     * author mengtianxue
     */
    public function Binding($bonusCode, $bonus_sn, $phone, $is_send = 1, $channel = 0, $amount = 0, $ms = '')
    {
        $bonus = array();
        $bonus['id'] = $bonusCode['id'];
        $bonus['channel'] = $bonusCode['channel'];
        $bonus['sn_type'] = $bonusCode['sn_type'];
        $bonus['bonus_sn'] = trim($bonus_sn);
        if ($bonusCode['sn_type'] == 0) {
            $bonus['parityBit'] = substr($bonus_sn, -1, 1);
        } else {
            $bonus['parityBit'] = 0;
        }
        $bonus['money'] = $amount == 0 ? $bonusCode['money'] : $amount;
        $end_date = 0;
        if ($bonusCode['end_day'] != 0) {
            $end_date = date('Y-m-d H:i:s', strtotime($bonusCode['end_day'] . " day"));
            $bonus['end_date'] = $end_date;
        } else {
            $bonus['end_date'] = $bonusCode['end_date'];
        }
        $bonus['user_limited'] = $bonusCode['user_limited'];
        $bonus['channel_limited'] = $bonusCode['channel_limited'];
        $bonus['back_type'] = $bonusCode['back_type'];

        //方便第三方查询, 增加 channel, 统计是从哪个渠道绑定的
        if (!empty($channel)) {
            $bonus['channel'] = $channel;
        }

        //商家绑定时间
        if (isset($bonusCode['merchants_created'])) {
            $bonus['created'] = $bonusCode['merchants_created'];
        }
        $bonus['bonus_use_limit'] = isset($bonusCode['bonus_use_limit']) ? $bonusCode['bonus_use_limit'] : 0;
        $customerBonus = CustomerBonus::model()->AddCustomerBonus($bonus, $phone);
        if ($customerBonus) {
            //修改优惠码状态
            $this->updateStatus($bonus_sn, self::STATUS_USE, $end_date);
            $app_ver = CustomerMain::model()->getAppversion($phone);
            if (!empty($app_ver) && strcmp($app_ver, '5.1.0') >= 0) {
                $content = trim($bonusCode['name']) . '绑定成功,点击查看';
                CustomerMessage::model()->addCouponMsg($phone, $content);
            }
            if ($is_send == 1) {
                //发送绑定成功短信
                $message_sms = $ms == '' ? $bonusCode['sms'] : $ms;
                Sms::SendSMS($phone, $message_sms, Sms::CHANNEL_SOAP);
            }

            $ret = array(
                'code' => 0,
                'message' => '成功',
                'bind_id' => $customerBonus
            );
            return $ret;
        } else {
            $ret = array(
                'code' => 2,
                'message' => '绑定失败，请重新尝试');
            return $ret;
        }
    }


    /**
     * 优惠券绑定  后台客服赠送优惠券调用此方法
     * @param $bonusCode
     * @param $bonus_sn
     * @param $phone
     * @param $is_send 默认是1 发送短信
     * @return array
     * author mengtianxue
     */
    public function backBinding($bonusCode, $bonus_sn, $phone, $is_send = 1, $channel = 0)
    {
        $bonus = array();
        $bonus['id'] = $bonusCode['id'];
        $bonus['channel'] = $bonusCode['channel'];
        $bonus['sn_type'] = $bonusCode['sn_type'];
        $bonus['bonus_sn'] = trim($bonus_sn);
        if ($bonusCode['sn_type'] == 0) {
            $bonus['parityBit'] = substr($bonus_sn, -1, 1);
        } else {
            $bonus['parityBit'] = 0;
        }
        $bonus['money'] = $bonusCode['money'];
        $end_date = 0;
        if ($bonusCode['end_day'] != 0) {
            $end_date = date('Y-m-d H:i:s', strtotime($bonusCode['end_day'] . " day"));
            $bonus['end_date'] = $end_date;
        } else {
            $bonus['end_date'] = $bonusCode['end_date'];
        }
        $bonus['user_limited'] = $bonusCode['user_limited'];
        $bonus['channel_limited'] = $bonusCode['channel_limited'];
        $bonus['back_type'] = $bonusCode['back_type'];

        //方便第三方查询, 增加 channel, 统计是从哪个渠道绑定的
        if (!empty($channel)) {
            $bonus['channel'] = $channel;
        }

        //商家绑定时间
        if (isset($bonusCode['merchants_created'])) {
            $bonus['created'] = $bonusCode['merchants_created'];
        }
        $bonus['bonus_use_limit'] = isset($bonusCode['bonus_use_limit']) ? $bonusCode['bonus_use_limit'] : 0;
        $customerBonus = CustomerBonus::model()->AddCustomerBonus2($bonus, $phone);
        if ($customerBonus) {
            //修改优惠码状态
            $this->updateStatus($bonus_sn, self::STATUS_USE, $end_date);
            $app_ver = CustomerMain::model()->getAppversion($phone);
            if (!empty($app_ver) && strcmp($app_ver, '5.1.0') >= 0) {
                $content = trim($bonusCode['name']) . '绑定成功,点击查看';
                CustomerMessage::model()->addCouponMsg($phone, $content);
            }
            if ($is_send == 1) {
                //发送绑定成功短信
                $message_sms = $bonusCode['sms'];
                Sms::SendSMS($phone, $message_sms, Sms::CHANNEL_SOAP);
            }

            $ret = array(
                'code' => 0,
                'message' => '成功',
                'bind_id' => $customerBonus
            );
            return $ret;
        } else {
            $ret = array(
                'code' => 2,
                'message' => '绑定失败，请重新尝试');
            return $ret;
        }
    }

    /**
     * 获取一张要使用优惠券
     * @param $phone
     * @param $status
     * @return array|bool
     * @auther mengtianxue
     */
    public function getBonus_sn($phone, $status, $bonus_use_limit = 0, $app_ver = 0)
    {
        $bonus_arr = array();
        if (isset($phone) && empty($phone)) {
            return $bonus_arr;
        }
        $arr = array();
        $arr['phone'] = $phone;
        $arr['type'] = $status;
        $arr['pageSize'] = 1;
        $arr['sort'] = 2;
        $arr['bonus_use_limit'] = $bonus_use_limit;
        $arr['app_ver'] = $app_ver;
        //$customerBonus = CustomerBonus::model()->CheckCustomerBonusUsed($phone, $status);
        $customerBonus = BBonus::model()->getCustomerBonus($arr);
        if (!empty($customerBonus)) {
            foreach ($customerBonus as $bonus) {
                $bonus_arr['card'] = $bonus['sn'];
                $bonus_arr['money'] = $bonus['money'];
//                $bonus_arr['card'] = $bonus->bonus_sn;
//                $bonus_arr['money'] = $bonus->balance;
                break;
            }
        }
        return $bonus_arr;
    }


    /**
     * 优惠券占用
     * @param $phone 手机号
     * @param $status 呼叫类型
     * @param $order_id 订单id
     * @param $is_sms 是否发送短信
     * @return bool
     * author mengtianxue
     */
    public function BonusOccupancy($phone, $order_id, $status, $is_sms = 0, $bonus_use_limit = 0, $app_ver = 0, $driver_phone = 0)
    {
        $time = date('Y-m-d H:i:s');

        //vip用户限制
        $vip_phone = VipPhone::model()->getPrimary($phone);
        if (!empty($vip_phone)) {
            return false;
        }
        $bonus = $this->getBonus_sn($phone, $status, $bonus_use_limit, $app_ver);
        if (empty($bonus)) {
            return false;
        }

        $bonus_sn = $bonus['card'];

//      bonus_sn 优惠券码必传   phone 客户手机号必传 order_id 订单id
        $params = array('bonus_sn' => $bonus_sn, 'phone' => $phone, 'order_id' => $order_id);

        $bonus_occupancy = CustomerBonus::model()->CustomerBonusOccupancy($params);
        if ($bonus_occupancy) {
            if ($is_sms == 0) {
                $orderInfo = Order::model()->getOrderById($order_id);
                if ($orderInfo) {
                    //$message = sprintf('刚才呼入的客户(%s)为优惠券客户,账户余额:%s元，不足部分请收取现金。报单时，系统将自动从客户账户划转%s元到您的信息费账户。', $this->phone, $bonus['money'], $bonus['money']);
                    $message = MessageText::getFormatContent(MessageText::DRIVER_CUSTOMER_BONUS, $phone, $bonus_occupancy['balance']);
                    Sms::SendSMS($driver_phone, $message, Sms::CHANNEL_SOAP);
                }
                $message = sprintf('您的本次代驾服务可享受%s元优惠,感谢您选择e代驾,祝您愉快！', $bonus_occupancy['balance']);
                Sms::SendSMS($phone, $message, Sms::CHANNEL_SOAP);
            }
            return $bonus_occupancy;
        } else {
            return false;
        }
    }


    public function getBonusName($id)
    {
        if (!isset($this->bonusNames[$id])) {
            $name = BonusCode::model()->findByPk($id)->name;
            $this->bonusNames[$id] = $name;
        }
        return $this->bonusNames[$id];
    }


    /**
     * 优惠券占用
     * @param $phone 手机号
     * @param $bonus_sn 呼叫类型
     * @param $order_id 订单id  传订单号是占用，传0取消占用
     * @param $is_sms 是否发送短信
     * @return bool
     * author mengtianxue
     */
    public function BonusOccupancyBySn($phone, $bonus_sn, $order_id, $is_sms = 0, $driver_phone = 0)
    {
//      bonus_sn 优惠券码必传   phone 客户手机号必传 order_id 订单id
        $params = array('bonus_sn' => $bonus_sn, 'phone' => $phone, 'order_id' => $order_id);

        $bonus_occupancy = CustomerBonus::model()->CustomerBonusOccupancy($params);
        if ($bonus_occupancy) {
            if ($is_sms == 0) {
                $orderInfo = Order::model()->getOrderById($order_id);
                if ($orderInfo) {
                    //$message = sprintf('刚才呼入的客户(%s)为优惠券客户,账户余额:%s元，不足部分请收取现金。报单时，系统将自动从客户账户划转%s元到您的信息费账户。', $this->phone, $bonus['money'], $bonus['money']);
                    $message = MessageText::getFormatContent(MessageText::DRIVER_CUSTOMER_BONUS, $phone, $bonus_occupancy['balance']);
                    Sms::SendSMS($driver_phone, $message, Sms::CHANNEL_SOAP);
                }
                $message = sprintf('您的本次代驾服务可享受%s元优惠,感谢您选择e代驾,祝您愉快！', $bonus_occupancy['balance']);
                Sms::SendSMS($phone, $message, Sms::CHANNEL_SOAP);
            }
            return $bonus_occupancy;
        } else {
            return false;
        }
    }

    /**
     * 优惠券的使用
     * @param $phone
     * @param $order_id
     * @param $money
     * @param $type 1为使用，2为取消使用
     * @return bool
     * author mengtianxue
     */
    public function BonusUsed($phone, $order_id, $money = 0, $type = 1)
    {
        $customerBonus = CustomerBonus::model()->getBonusUsed($phone, $order_id);
        if (empty($customerBonus)) {
            return false;
        }
        $params = array();
        $params_library = array();
        if ($type == 1) {
            $params['used'] = time();
            $params_library['is_use'] = 1;
            $params_library['use_date'] = date('Y-m-d H:i:s');
        } else {
            $params['order_id'] = 0;
            $params_library['is_use'] = 0;
        }

        if ($money != 0) {
            if ($money > $customerBonus['balance']) {
                $params['use_money'] = $customerBonus['balance'];
            } else {
                $params['use_money'] = $money;
            }
        } else {
            $params['use_money'] = 0;
        }

        $params['money'] = $customerBonus['money'] - $params['use_money'];
        EdjLog::info('test:');
        $ret = CustomerBonus::model()->updateAll($params,
            'customer_phone = :customer_phone and order_id = :order_id',
            array(':customer_phone' => $phone, ':order_id' => $order_id));
        try {
            $this->useUpdate($params_library, $customerBonus['bonus_sn']);
        } catch (Exception $e) {
            EdjLog::info('$ret_library:' . json_encode($params_library));
        }

        if ($ret) {
            return $params['use_money'];
        } else {
            return false;
        }

    }

    /**
     * 商家客户取消当前订单时取消优惠券绑定
     * @param $bonus_sn
     * @param $phone
     * @param $order_id
     * @return bool
     * author mengtianxue
     */
    public function cancelBonus($bonus_sn, $phone, $order_id = 0)
    {
        $where = 'bonus_sn = :bonus_sn and customer_phone = :phone and used = 0 and order_id = :order_id';
        $params = array(':bonus_sn' => $bonus_sn,
            ':phone' => $phone,
            ':order_id' => $order_id);
        $cancel_bonus = CustomerBonus::model()->deleteAll($where, $params);
        if ($cancel_bonus) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 固定优惠码
     * @param array $fixedArr
     * @return bool
     * @author daiyihui
     */
    public function doFixedCouponInsert($fixedArr = array())
    {
        $result = false;
        if (!empty($fixedArr)) {
            try {
                $c = new BonusLibrary();
                $c->setIsNewRecord(true);
                $fixedArr['created'] = date('Y-m-d H:i:s');
                $fixedArr['create_by'] = '系统生成';
                $c->attributes = $fixedArr;
                $result = $c->save();
            } catch (Exception $e) {
                $result = false;
                FinanceUtils::sendFinanceAlarm('create bonus error', $e);
            }
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * 获取最大的number   由于number是字符串类型，用复制方法不行，只能拼接查询
     * @param $prefix
     * @param $len
     * @return int
     * @auther mengtianxue
     */
    public function getMaxNumber($prefix, $len)
    {
        $min = intval(str_pad($prefix, $len, '0'));
        $max = intval(str_pad($prefix, $len, '9'));
        $library = Yii::app()->db_finance->createCommand()
            ->select('max(number) as number')
            ->from("{{bonus_library}}")
            ->where("number between $min and $max")
            ->queryRow();
        if ($library && !empty($library['number'])) {
            return $library['number'] - $min;
        } else {
            return 1;
        }
    }

    /**
     * 批量生成区域码
     * @param array $areaArr
     * @param array $codeRules
     * @return bool
     * @author daiyihui
     */
    public function doAreaCouponInsert($areaArr = array(), $codeRules = array())
    {
        if (!empty($areaArr) && !empty($codeRules)) {
            $insertLibArr = array(); //批量插入返回id的数组
            $bonusTotal = $codeRules['issued']; //批量生成优惠码总数

            $area_id = $codeRules['area_id'];
            $num_prifix = $codeRules['num_prdfix'];
            $len = $codeRules['num'];
            $max_number = $this->getMaxNumber($num_prifix, $len);
            $len_s = $len - strlen($num_prifix);
            for ($i = 1; $i <= $bonusTotal; $i++) {
                $number = $max_number + $i;

                //过滤含有4的优惠码
//                if (strpos($number, '4') === false) {
                $areaArr['number'] = $num_prifix . str_pad($number, $len_s, '0', STR_PAD_LEFT);
                $areaArr['create_by'] = '系统生成';
                $areaArr['created'] = date("Y-m-d H:i:s");

                $couponCode = $this->getCouponNum($codeRules, $area_id);
                if (empty($couponCode)) {
                    $bonusTotal += 1;
                    continue;
                }
                $areaArr['bonus_sn'] = $couponCode;
                $this->setIsNewRecord(true);
                $this->attributes = $areaArr;
                $this->id = null;
                $this->save();
                if ($this->save()) {
                    $insertLibArr[] = $this->id;
                }
//                } else {
//                    $bonusTotal += 1;
//                    continue;
//                }
            }

            if (count($insertLibArr) == $bonusTotal) {
                return true;
            } else {
                return false;
            }
        }

    }

    /**
     * 获取优惠码
     * @param array $rules
     * @param string $areaId
     * @return bool|string
     * @auther mengtianxue
     */
    public function getCouponNum($rules = array(), $areaId = '')
    {
        if (!empty($rules) && !empty($areaId)) {
            $randNum = $this->randBonusCode($rules);
            $couponNum = $areaId . $randNum;
            $couponCode = BonusLibrary::model()->find('bonus_sn=:bonus_sn', array(':bonus_sn' => $couponNum));
            if (!empty($couponCode)) {
                $this->getCouponNum($rules, $areaId);
            } else {
                return $couponNum;
            }
        } else {
            return false;
        }
    }

    /**
     * 随机生成一个优惠码
     * @param $params
     * @return int
     * @auther mengtianxue
     */
    public function randBonusCode($params)
    {
        $couponDigits = $params['couponDigits'];
        $str_num = '012356789';
        $num = '';
        for ($i = 0; $i < $couponDigits; $i++) {
            $dit = rand(0, 8);
            $num .= $str_num[$dit];
        }

        if (empty($num)) {
            $this->randBonusCode($params);
        } else {
            return $num;
        }
    }

    /**
     * 检测该优惠码是否存在
     * @param $bonusCode
     * @return bool
     */
    public function checkIsBonus($bonusCode)
    {
        $returnBonus = self::model()->find('bonus_sn=:bonus_sn', array(':bonus_sn' => $bonusCode));
        if (!empty($returnBonus)) {
            return false;
        } else {
            return true;
        }
    }

    //检测指定优惠劵是否含有优惠码
    public function checkBonus($bounsId, $bonusCode)
    {
        $returnBonus = self::model()->find('bonus_id=:bonus_id and  bonus_sn=:bonus_sn', array(':bonus_id' => $bounsId, ':bonus_sn' => $bonusCode));
        if (empty($returnBonus)) {
            return false;
        }
        return true;
    }


    /**
     *
     * @param $bonus_sn
     * @return mixed
     * author mengtianxue
     */
    public function getBonusByBonus_sn($bonus_sn, $sn_type = 2)
    {
        $bonusInfo = Yii::app()->db_finance->createCommand()
            ->select("*")
            ->from('{{bonus_library}}')
            ->where('bonus_sn = :bonus_sn and sn_type = :sn_type',
                array(':bonus_sn' => $bonus_sn, ':sn_type' => $sn_type))
            ->queryRow();
        return $bonusInfo;
    }

    /**
     * 获取优惠码信息
     * @param $bonus_sn
     * @return mixed
     * author mengtianxue
     */
    public function getBonusInfo($bonus_sn, $phone = '')
    {
        $params = array(':bonus_sn' => $bonus_sn);
        $where = 'bonus_sn = :bonus_sn';
        if (!empty($phone)) {
            $params[':customer_phone'] = $phone;
            $where = $where . ' and customer_phone=:customer_phone';
        }

        $bonusInfo = Yii::app()->db_finance->createCommand()
            ->select("*")
            ->from('{{customer_bonus}}')
            ->where($where, $params)
            ->queryRow();
        return $bonusInfo;
    }

    /**
     * 获取最大的区域码id号
     * @return mixed
     * author mengtianxue
     */
    public function getMaxAreaId()
    {
        return BonusCode::model()->getMaxAreaID();
    }


    /**
     * 添加优惠券
     * @param $driver_id
     * @return bool
     * author mengtianxue
     */
    public function addBonusLibrary($driver_id)
    {
        $code = substr($driver_id, 0, 2);
        $code_num = substr($driver_id, 2);

        $bonus_city = Dict::items('bonus_city');
        $bonus_code = array_flip($bonus_city);

        $city = $bonus_code[$code];
        if ($city) {
            $bonus_sn = $city . $code_num;
            $checkedBonus = $this->getBonusInfo($bonus_sn);
            if (!$checkedBonus) {
                $bonus = array();
                $bonus['bonus_sn'] = $bonus_sn;
                $bonus['money'] = 10;
                $bonus['bonus_id'] = 8;
                $bonus['sn_type'] = 0;
                $bonus['effective_date'] = '2012-06-30 23:59:00';
                $bonus['binding_deadline'] = '2014-06-30 23:59:00';
                $bonus['end_date'] = '2014-12-30 23:59:00';
                $bonus['create_by'] = '孟天学';
                $bonus['created'] = date('Y-m-d H:i:s');

                $bonus['city_id'] = $city;
                $add_bonus = Yii::app()->db_finance->createCommand()->insert('t_bonus_library', $bonus);
                if ($add_bonus) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * bonusLibrary 列表展示
     * @param $data
     * @auther mengtianxue
     */
    public function ownerShow($data)
    {
        $owner = $data->owner;
        if ($owner == "") {
            echo "未分配";
        } else {
            $bonus_code = BonusChannel::model()->getInfoById($owner);
            if ($bonus_code) {
                $city_name = Dict::item('city', $bonus_code['area_id']);
                echo $city_name . $bonus_code['channel'];
            } else {
                echo "已分配";
            }
        }
    }

    /**
     * 获取渠道分配和使用统计
     * @param $owner
     * @param int $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusByOwner($owner = '', $bonus_id = 0, $fileName = '')
    {
        $where = 'id > 0';
        $params = array();
        if ($owner) {
            $where .= " and owner = :owner";
            $params[':owner'] = $owner;
        }

        if ($bonus_id !== 0) {
            $where .= " and bonus_id = :bonus_id";
            $params[':bonus_id'] = $bonus_id;
        }
        $bonusCount = Yii::app()->db_finance->createCommand()
            ->select("SUM( IF( (STATUS =1), 1, 0 ) ) AS usedCount, count( 1 ) AS count")
            ->from('{{bonus_library}}')
            ->where($where, $params)
            ->queryRow();
        if ($fileName) {
            return $bonusCount[$fileName];
        } else {
            return $bonusCount;
        }
    }

    /**
     * 根据渠道和规则，绑定一张优惠券
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getOneBonus($bonus_id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('id > 0');
        $criteria->addCondition('status = 0');
        $criteria->addInCondition('bonus_id', $bonus_id);
        $criteria->order = 'id desc';
        $code = BonusLibrary::model()->find($criteria);
        if ($code) {
            $code = $code->attributes;
        }

        return $code;
    }


    public function channelBind($channel, $money, $phone)
    {
        $ret = array(
            'code' => 2,
            'message' => '绑定失败'
        );

        $bonus_id = BonusCode::model()->getBonusIDByChannle($channel, $money);
        if (empty($bonus_id)) {
            $ret['message'] = '没有给商家分配该类型的优惠券';
            return $ret;
        }
        //0310-3512863

        $checked_bonus = CustomerBonus::model()->checkedBonusByPhone($phone, $bonus_id);
        if ($checked_bonus) {
            $ret['message'] = '一分钟内只可以绑定一次';
            return $ret;
        }
        $code = $this->getOneBonus($bonus_id);

        if (empty($code)) {
            $ret['message'] = '此类优惠券已经用完。';
            return $ret;
        }

        $bonus_code_id = $code['bonus_id'];
        $bonus_sn = $code['bonus_sn'];
        $bonus_code = BonusCode::model()->getBonusCodeById($bonus_code_id);
        if ($bonus_code) {
            $ret = $this->Binding($bonus_code, $bonus_sn, $phone, 2);
            return $ret;
        }
        return $ret;
    }

    /**
     * 市场活动优惠券绑定
     * @param $phone
     * @param array $bonus_id
     * @param int $is_sms
     * @return array
     * @auther mengtianxue
     */
    public function ActivitiesBind($phone, $bonus_id = array(), $is_sms = 1)
    {
        $ret = array(
            'code' => 2,
            'message' => '绑定失败'
        );

        if (!is_array($bonus_id)) {
            return $ret;
        }

        //0310-3512863
        $checked_bonus = CustomerBonus::model()->checkedBonusByPhone($phone, $bonus_id);
        if ($checked_bonus) {
            $ret['message'] = '一分钟内只可以绑定一次';
            return $ret;
        }
        $code = $this->getOneBonus($bonus_id);

        if (empty($code)) {
            $ret['message'] = '此类优惠券已经用完。';
            return $ret;
        }

        $bonus_code_id = $code['bonus_id'];
        $bonus_sn = $code['bonus_sn'];
        $bonus_code = BonusCode::model()->getBonusCodeById($bonus_code_id);
        if ($bonus_code) {
            $ret = $this->Binding($bonus_code, $bonus_sn, $phone, $is_sms);
            return $ret;
        }
        return $ret;
    }

    /**
     * 根据渠道和规则，绑定一张优惠券
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusCount($bonus_sn)
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('bonus_sn', $bonus_sn);
        $criteria->select = 'SUM(case WHEN `channel`=0 and is_use=0 and status!=2 THEN 1 ELSE 0 END) as bonus_sn,SUM(case WHEN `status`=2 THEN 1 WHEN `channel`=0 and is_use=1 THEN 1 ELSE 0 END) as bonus_id';
        $result = self::model()->findAll($criteria);
        $code = BonusLibrary::model()->find($criteria);
        if ($code) {
            $code = $code->attributes;
        }

        return $code;
    }

    /**
     * 分配实体卷
     * @param $bonus_sns
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function disTriByBonusIDs($bonus, $dis_city)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('channel = 0');
        $criteria->addCondition('is_use = 0');
        $criteria->addCondition('status != 2');
        $criteria->addInCondition('number', explode(',', $bonus['bonus_sn']));
        $code = BonusLibrary::model()->findAll($criteria);

        $result = 0;
        if (count($code) > 0) {
            foreach ($code as $co) {
                $co->channel = $bonus['channel'];
                $co->city_id = $bonus['city_id'];
                $co->update = $bonus['update'];
                $co->distri_type = $bonus['distri_type'];
                $co->distri_by = $bonus['distri_by'];
                EdjLog::info('XX' . json_encode($co->attributes));
                if ($dis_city == 0) {
                    $co->distri_date = $bonus['distri_date'];
                }

                if (isset($bonus['log_id'])) {
                    $co->log_id = $bonus['log_id'];
                }
                if (isset($bonus['distri_city_date'])) {
                    $co->distri_city_date = $bonus['distri_city_date'];
                }

                if (!$co->save()) {
                    $result = 1;
                    EdjLog::info(json_encode($co->getErrors()));
                    break;
                }
            }
        }


        return $result;
    }

    /**
     * 分配实体卷
     * @param $bonus_sns
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function disTriByBonusBetweenIDs($bonus, $dis_city)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('channel = 0');
        $criteria->addCondition('is_use = 0');
        $criteria->addCondition('status != 2');
        $criteria->addBetweenCondition("number", $bonus['start'], $bonus['end']);
        $code = BonusLibrary::model()->findAll($criteria);
        $result = 0;
        if (count($code) > 0) {
            foreach ($code as $co) {
                $co->channel = $bonus['channel'];
                $co->city_id = $bonus['city_id'];
                $co->update = $bonus['update'];
                $co->distri_by = $bonus['distri_by'];
                $co->distri_type = $bonus['distri_type'];

                if ($dis_city == 0) {
                    $co->distri_date = $bonus['distri_date'];
                }
                if (isset($bonus['log_id'])) {
                    $co->log_id = $bonus['log_id'];
                }
                if (isset($bonus['distri_city_date'])) {
                    $co->distri_city_date = $bonus['distri_city_date'];
                }
                if (!$co->save()) {
                    $result = 1;
                    var_dump($co->getErrors());
                    break;
                }
            }
        }
        return $result;
    }


    /**
     * 获取该城市分配人列表
     * @param $city_id
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function disTriList($city_id = 1)
    {
        $criteria = new CDbCriteria();
        $criteria->select = ('DISTINCT(distri_by)');
        $params = array();
        if ($city_id > 1) {
            $criteria->addCondition('city_id=:city_id');
            $params[':city_id'] = $city_id;
        }
        $criteria->params = $params;
        $code = BonusLibrary::model()->findAll($criteria);

        $arr = array();
        foreach ($code as $value) {
            $arr[$value['distri_by']] = $value['distri_by'];
            //array_push($arr,$value['distri_by']);
        }

        return $arr;
    }

    /**
     * 标记坏卡
     * @param $bonus_sn
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function erroCard($bonus_sn)
    {
        $criteria = new CDbCriteria();
        $criteria->addInCondition('number', $bonus_sn);
        $criteria->addCondition('channel=0 and is_use=0');
        $code = BonusLibrary::model()->findAll($criteria);
        $result = 0;
        if (count($code) > 0) {
            foreach ($code as $co) {
                $co->status = 2;
                $co->update_by = Yii::app()->user->name;
                $co->error_date = date('Y-m-d H:i:s');
                if (!$co->save()) {
                    $result = 1;
                    break;
                }
            }
        }
        return $result;
    }


    /**
     * 统计渠道分配列表数量
     * @param $channel_id
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function channelBonusCount($channel, $dateStart = '', $dateEnd = '')
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('channel=:channel');
        $criteria->addCondition('sn_type=2 and status!=2');
        $criteria->params[':channel'] = $channel;
        $criteria->addCondition('is_use=1');
        if ($dateStart != '' && $dateEnd != '') {
            $criteria->addBetweenCondition('use_date', $dateStart, $dateEnd);
        }
        $criteria->select = 'log_id,COUNT(*) as channel';
        $criteria->order = 'channel DESC';
        $criteria->group = 'log_id';

        $result = self::model()->findAll($criteria);

        return $result;
    }


    /**
     * 根据获取坏卡
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusErrorCard($bonus_sn, $type = 0, $start, $end, $dis_city, $city_id = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('sn_type=2');
        if ($type == 0) {
            $bonus = trim($bonus_sn, ',');
            $criteria->addInCondition('number', explode(',', $bonus));
        } else {
            $criteria->addBetweenCondition('number', $start, $end);
        }

        if ($city_id != 0) {
            $criteria->addCondition('city_id=:city_id');
            $criteria->params[':city_id'] = $city_id;
        }


        if ($dis_city == 0) {
            $criteria->addCondition('channel!=0 or is_use=1 or status=2');
            $criteria->select = "number,(CASE WHEN channel=0 AND status!=2 AND is_use=1 THEN '未分配已使用' WHEN `status`=2 AND is_use=0 THEN '坏卡' WHEN `status`=2 AND is_use=1 THEN '坏卡已使用' WHEN channel!=0 THEN '已分配' ELSE 0 END) AS bonus_id";
        } else {
            $criteria->addCondition('city_id!=0 or channel!=0 or is_use=1 or status=2');
            $criteria->select = "number,(CASE WHEN city_id=0 AND status!=2 AND is_use=1 THEN '未分配已使用' WHEN `status`=2 AND is_use=0 THEN '坏卡' WHEN `status`=2 AND is_use=1 THEN '坏卡已使用' WHEN city_id!=0 or channel!=0 THEN '已分配' ELSE 0 END) AS bonus_id";
        }


        $result = self::model()->findAll($criteria);

        return $result;
    }

    /**
     * 根据获取坏卡
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusErrorCard1($bonus_sn, $type = 0, $start, $end)
    {
        $criteria = new CDbCriteria();

        if ($type == 0) {
            $criteria->addInCondition('bonus_sn', $bonus_sn);
        } else {
            $criteria->addBetweenCondition('bonus_sn', $start, $end);
        }
        $criteria->addCondition('channel!=0');
        $criteria->addCondition('is_use!=0');
        $criteria->addCondition('status=2');
        $criteria->select = 'bonus_sn';

        $result = self::model()->findAll($criteria);
        return $result;
    }

    /**
     * 根据获取坏卡
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusNumByBonusSn($bonus_sn)
    {
        $criteria = new CDbCriteria();


        $criteria->addCondition('bonus_sn=:bonus_sn');
        $criteria->params[':bonus_sn'] = $bonus_sn;
        $criteria->select = 'number';
        $result = self::model()->find($criteria);

        return $result['number'];
    }


    /**
     * 标记坏卡
     * @param $bonus_sn
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function updateUseInfo($bonus)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('bonus_sn=:bonus_sn');
        $criteria->params[':bonus_sn'] = $bonus['bonus_sn'];
        $code = BonusLibrary::model()->find($criteria);

        $result = 0;
        $code->is_use = 1;
        $code->use_date = date('Y-m-d H:i:s');
        if (!$code->save()) {
            $result = 1;
            EdjLog::info(json_encode($code->getErrors()));
        }

        return $result;
    }

    public function useUpdate($params, $bonus_sn)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('bonus_sn=:bonus_sn');
        $criteria->params[':bonus_sn'] = $bonus_sn;
        $criteria->addCondition('sn_type=2');
        $code = BonusLibrary::model()->find($criteria);

        if ($code) {
            $code->is_use = $params['is_use'];
            $code->use_date = $params['use_date'];
            if (!$code->save()) {
                EdjLog::info('$ret_library:' . json_encode($code->getErrors()));
            }
        }
    }

    /**
     * 根据bonus_sn 查询 bonus_id
     * @param $bonus_sn
     * @return mixed
     * @auther zhangxiaoyin
     */
    public static function getBonusIdBySn($bonus_sns)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'bonus_id,bonus_sn';
        $criteria->addInCondition('bonus_sn', $bonus_sns);
        $code = self::model()->findAll($criteria);
        $result = array();
        if ($code) {
            foreach ($code as $bonus) {
                $result[$bonus->bonus_sn] = $bonus->bonus_id;
            }
        }

        return $result;
    }

    /**
     * 客户补贴评价返优惠券给客户优惠券绑定
     * @param $bonus_sn v2后台建立的固定码
     * @param $phone  客户手机号
     * @param $password
     * @return bool|void
     * 0：不存在，1：成功，2：还未生效，不能绑定 3：优惠码失效
     * author mengtianxue
     */

    public function bonusBindingSubsidy($bonus_sn, $phone, $password = 0, $channel = 0)
    {
        $ret = array(
            'code' => 2,
            'message' => '抱歉，此优惠券号码无效');

        if (empty($bonus_sn) && empty($phone)) {
            $ret = array(
                'code' => 2,
                'message' => '优惠券码和客户电话不能为空');
        }
        //vip用户限制
        $vip_phone = VipPhone::model()->getPrimary($phone);

        if (!empty($vip_phone)) {
            $ret = array(
                'code' => 2,
                'message' => '这个用户是我们的VIP用户，不能绑定优惠券');
            return $ret;
        }

        $checkBonusUse = $this->checkBonusUse($bonus_sn, $password);
        if ($checkBonusUse) {
            $bonusCode = BonusCode::model()->getBonusCodeById($checkBonusUse['bonus_id'], BonusCode::STATUS_APPROVED, 2, 'not');
            //优惠券类型 必须是已审核
            if (!$bonusCode) {
                $ret = array(
                    'code' => 2,
                    'message' => '此类优惠券不能使用');
                return $ret;
            }
            $ret = $this->Binding($bonusCode, $bonus_sn, $phone, 1, $channel);

            return $ret;
        }
        return $ret;
    }

    /**
     * 绑定优惠券-面额自定义
     * @param $phone
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function bindBonusActive($phone, $acount, $code, $ms)
    {
        $result = array();
        if ($code == 0) {
            $result['code'] = 1;
            $result['message'] = '参数错误';
            return $result;
        }


        $bonus_code = $this->gitBonusCodeInfo($code);
        if (empty($bonus_code)) {
            $result['code'] = 1;
            $result['message'] = '参数错误';
            return $result;
        }

        $bonus_code_role = $this->gitBonusCodeRoleInfo($bonus_code);
        $bonus_sn = BonusLibrary::model()->doAreaCouponActivity($bonus_code, $bonus_code_role, $acount);
        if (!empty($bonus_sn)) {
            $result = $this->Binding($bonus_code, $bonus_sn, $phone, $ms == '' ? 0 : 1, 0, $acount, $ms);
        }else{
            $result['code'] = 1;
            $result['message'] = '生成bonus_sn error';
            EdjLog::info(json_encode($result).'phone:'.$phone.'acount:'.$acount.'code:'.$code);
            return $result;
        }

        return $result;
    }


    /**
     * 批量生成区域码
     * @param array $areaArr
     * @param array $codeRules
     * @return bool
     * @author daiyihui
     */
    public function doAreaCouponActivity($areaArr = array(), $codeRules = array(), $acount)
    {
        if (!empty($areaArr) && !empty($codeRules)) {
            $area_id = $areaArr['area_id'];
            $num_prifix = $codeRules['num_prdfix'];
            $len = $codeRules['num'];
            $max_number = $this->getMaxNumber($num_prifix, $len);
            $len_s = $len - strlen($num_prifix);

            $number = $max_number + 1;
            $areaArr['number'] = $num_prifix . str_pad($number, $len_s, '0', STR_PAD_LEFT);
            $areaArr['create_by'] = '系统生成';
            $areaArr['created'] = date("Y-m-d H:i:s");
            $couponCode = $this->getCouponNum($codeRules, $area_id);
            $areaArr['bonus_sn'] = $couponCode;
            $areaArr['money'] = $acount;
            $this->setIsNewRecord(true);
            $this->attributes = $areaArr;
            $this->id = null;

            if ($this->save()) {
                return $couponCode;
            } else {
                return '';
            }
        }
    }


    public function gitBonusCode($type)
    {
        $bonus_code_id = 0;
        switch ($type) {
            case 1:
                $bonus_code_id = self::active_bonus_id;
        }
        return $bonus_code_id;
    }

    public function gitBonusCodeInfo($bonus_code_id)
    {
        $code = BonusCode::model()->getBonusCodeById($bonus_code_id);
        $couponArr = array();
        $couponArr['id'] = $bonus_code_id;
        $couponArr['bonus_id'] = $bonus_code_id;
        $couponArr['sn_type'] = $code['sn_type']; //类型
        $couponArr['name'] = $code['name']; //渠道
        $couponArr['effective_date'] = $code['effective_date']; //生效日期
        $couponArr['binding_deadline'] = $code['binding_deadline']; //绑定截止日期
        $couponArr['end_date'] = $code['end_date'];
        $couponArr['end_day'] = $code['end_day'];
        $couponArr['coupon_rules'] = $code['coupon_rules'];
        $couponArr['area_id'] = $code['area_id']; //区域码前三位
        $couponArr['issued'] = $code['issued']; //生成优惠码数量
        $couponArr['channel'] = 0;
        $couponArr['user_limited'] = $code['user_limited']; //生成优惠码数量
        $couponArr['channel_limited'] = $code['channel_limited']; //生成优惠码数量
        $couponArr['back_type'] = $code['back_type']; //生成优惠码数量
        $couponArr['sms'] = $code['sms']; //生成优惠码数量
        return $couponArr;
    }


    public function gitBonusCodeRoleInfo($code)
    {
        $couponRules = CJSON::decode($code['coupon_rules']);
        $area_id = 0;
        if ($code['area_id'] != 0) {
            $area_id = str_pad($code['area_id'], 3, '0', STR_PAD_LEFT); //拼装区域优惠码前三位
        }else{
            EdjLog::info('bonus area_id = 0,'.json_encode($code));
        }
        $codeRules['area_id'] = $area_id; //区域码前三位
        $codeRules['issued'] = $code['issued']; //生成优惠码数量
        $codeRules['couponDigits'] = $couponRules['code_num'] - 3; //优惠码位数
        $codeRules['num'] = $couponRules['num']; //优惠码位数
        $codeRules['num_prdfix'] = $couponRules['num_prdfix']; //优惠码位数
        return $codeRules;
    }
}

