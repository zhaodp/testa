<?php

/**
 * This is the model class for table "t_envelope_info".
 *
 * The followings are the available columns in table 't_envelope_info':
 * @property string $id
 * @property string $envelope_name
 * @property integer $envelope_type
 * @property string $envelope_role
 * @property integer $status
 * @property string $start_date
 * @property string $end_date
 * @property string $create_date
 * @property string $last_changed_date
 */
class EnvelopeInfo extends FinanceActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{envelope_info}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('envelope_name', 'required'),
            array('envelope_type, status', 'numerical', 'integerOnly' => true),
            array('envelope_name', 'length', 'max' => 100),
            array('envelope_role', 'length', 'max' => 300),
            array('start_date, end_date', 'length', 'max' => 300),
            array('create_date, last_changed_date', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, envelope_name, envelope_type, envelope_role, status, start_date, end_date, create_date, last_changed_date', 'safe', 'on' => 'search'),
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
            'id' => 'Id',
            'envelope_name' => '红包名称',
            'envelope_type' => '发放方式',
            'envelope_role' => '发放规则',
            'status' => '状态',
            'start_date' => '开始时间',
            'end_date' => '结束时间',
            'create_date' => '创建时间',
            'last_changed_date' => '最后修改时间',
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

        $criteria->compare('envelope_name', $this->envelope_name, true);

        $criteria->compare('envelope_type', $this->envelope_type);

        $criteria->compare('envelope_role', $this->envelope_role, true);

        $criteria->compare('status', $this->status);

        $criteria->compare('start_date', $this->start_date, true);

        $criteria->compare('end_date', $this->end_date, true);

        $criteria->compare('create_date', $this->create_date, true);

        $criteria->compare('last_changed_date', $this->last_changed_date, true);

        return new CActiveDataProvider('EnvelopeInfo', array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return EnvelopeInfo the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**获取进行中红包列表
     * @param array $arr
     * @return mixed
     */
    public function getList($arr = array(), $ids = array())
    {
        $criteria = new CDbCriteria;

        $criteria->select = 'id,envelope_type,envelope_role,start_date,end_date,status';

        if (!empty($ids)) {
            $criteria->addInCondition('id', $ids);
        }

        if (isset($arr['start_date']) && $arr['start_date'] != '') {
            $criteria->addCondition('start_date<=:start_date and end_date>=:start_date');
            $criteria->params[':start_date'] = $arr['start_date'];
        }

        if (isset($arr['envelope_type']) && $arr['envelope_type'] != 0) {
            $criteria->addCondition('envelope_type='.$arr['envelope_type']);
        }

        $criteria->addCondition('status>0');
        $criteria->order = 'last_changed_date desc';
        $criteria->limit = 10000;
        return self::model()->findAll($criteria);
//        return new CActiveDataProvider('EnvelopeInfo', array(
//            'criteria'=>$criteria,
//            'pagination' => array(
//                'pageSize' => 10,
//            ),
//        ));
    }

    /**生成随机红包金额
     * @param $params
     * @return mixed
     */
    public static function  getEvenlopeNum($params)
    {
        $arr = array(100);
        $num = 0;
        $num1 = 0;
        foreach (explode('-', $params) as $role) {
            $pub_role = explode(':', $role);
            if (count($pub_role) == 2) {
                $num1 = $num + $pub_role[1];
                for ($num; $num < $num1; $num++) {
                    $arr[$num] = $pub_role[0];
                }
            }
        }
        return $arr[rand(0, 99)];
    }

    /**获取报单奖励红包列表
     * @return mixed
     */
    public static function  getEvenlopeList($date)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'id,envelope_type,envelope_role,start_date,end_date';
        $criteria->addCondition('status=1 AND envelope_type<100');
        $criteria->addCondition('start_date<=:date');
        $criteria->addCondition('end_date>=:date');
        $criteria->params[':date']=$date;
        $data = self::model()->findAll($criteria);
        return $data;
    }

    /**获取在线时长红包列表
     * @return mixed
     */
    public static function  getEvenlopeHoteTimeList()
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'id,envelope_type,envelope_role';
        $criteria->addCondition('status=1 AND start_date<=NOW() AND end_date>=NOW() and envelope_type>=100');
        $data = self::model()->findAll($criteria);
        return $data;
    }
}