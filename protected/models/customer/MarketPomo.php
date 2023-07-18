<?php

/**
 * This is the model class for table "t_market_pomo".
 *
 * The followings are the available columns in table 't_bank_customer_bonus':
 * @property integer $id
 * @property string $open_id
 * @property integer $subscribe_type
 * @property integer $event_key
 * @property integer $created
 */
class MarketPomo extends FinanceActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{market_pomo}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('subscribe_type, event_key,created', 'numerical', 'integerOnly' => true),
            array('open_id', 'length', 'max' => 100),
            array('day', 'length', 'max' => 15),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id,open_id, subscribe_type,event_key, created,day', 'safe', 'on' => 'search'),
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
            'id' => '自增主键',
            'open_id' => 'open_id',
            'subscribe_type' => '是否关注',
            'event_key' => '渠道',
            'created' => '关注时间',
            'day' => '日期',
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
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);
        $criteria->compare('open_id', $this->open_id, true);

        $criteria->compare('subscribe_type', $this->subscribe_type);

        $criteria->compare('event_key', $this->event_key);

        $criteria->compare('created', $this->created);

        return new CActiveDataProvider('MarketPomo', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return EnvelopeAcount the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function getList($dateStart, $dateEnd)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'day,count(*) as subscribe_type,SUM(case WHEN event_key=0 THEN 0 ELSE 1 END)  as event_key';
        $criteria->addCondition('subscribe_type=1');

        if ($dateStart != '') {
            $criteria->addCondition('day>=:dateStart');
            $criteria->params[':dateStart'] = $dateStart;
        }
        if ($dateEnd != '') {
            $criteria->addCondition('day<=:dateEnd');
            $criteria->params[':dateEnd'] = $dateEnd;
        }

        EdjLog::info('day start:' . $dateStart . ';day end:' . $dateEnd);
        $criteria->group = 'day';
        $criteria->order = 'day desc';

        return new CActiveDataProvider('MarketPomo', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
    }


    public static  function getAcount($day)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'count(*) as subscribe_type';
        $criteria->addCondition('subscribe_type=1');

        $criteria->addCondition('day=:day');
        $criteria->params[':day'] = $day;

        return self::model()->find($criteria);
    }

    public static  function getOpenIdList($day)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'open_id';
        $criteria->addCondition('subscribe_type=1');

        $criteria->addCondition('day=:day');
        $criteria->params[':day'] = $day;

        $criteria->addCondition('event_key!=0');


        return self::model()->findAll($criteria);
    }

    public static  function getOpenIdListByOpenIds($openIds,$day,$type)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'open_id';
        $criteria->addCondition('subscribe_type=1');
        $criteria->addInCondition('open_id',$openIds);
        $criteria->addCondition('event_key!=0');

        if($type==1){
            $criteria->addCondition('day<=:day');
            $criteria->params[':day'] = $day;
        }else{
            $criteria->addCondition('day=:day');
            $criteria->params[':day'] = $day;
        }

        return self::model()->findAll($criteria);
    }
}