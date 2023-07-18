<?php

/**
 * This is the model class for table "t_bank_customer_bonus".
 *
 * The followings are the available columns in table 't_bank_customer_bonus':
 * @property string $card_id
 * @property string $phone
 * @property integer $card_number
 * @property integer $club_number
 * @property integer $is_vip
 * @property string $create_date
 * @property string $last_changed_date
 */
class BankCustomerBonus extends FinanceActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{bank_customer_bonus}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('card_id, phone,create_date,last_changed_date', 'required'),
            array('card_number, club_number,is_vip,status', 'numerical', 'integerOnly' => true),
            array('phone', 'length', 'max' => 20),
            array('name', 'length', 'max' => 100),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id,card_id, phone,card_number, club_number, create_date, last_changed_date, is_vip,status,name', 'safe', 'on' => 'search'),
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
            'name' => '用户名',
            'card_id' => 'card_id',
            'phone' => '客户手机号',
            'card_number' => '卡优惠数',
            'club_number' => '俱乐部优惠券数',
            'create_date' => '创建时间',
            'last_changed_date' => '最后修改时间',
            'is_vip' => '是否vip',
            'status' => '是否已经绑定优惠券'
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
        $criteria->compare('card_id', $this->card_id, true);

        $criteria->compare('phone', $this->phone, true);

        $criteria->compare('card_number', $this->card_number);

        $criteria->compare('club_number', $this->club_number);

        $criteria->compare('create_date', $this->create_date, true);

        $criteria->compare('last_changed_date', $this->last_changed_date, true);

        return new CActiveDataProvider('BankCustomerBonus', array(
            'criteria' => $criteria,
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


    /**获取满足条件的司机列表
     * @param array $arr
     * @return mixed
     */
    public function getList($flag = 0,$thread, $limit = 1000)
    {
        try {
            $criteria = new CDbCriteria;
            $criteria->addCondition('status=0');
            $criteria->addCondition('id%'.$thread.'='.$flag);
            $criteria->limit = $limit;
            $criteria->order = 'id asc';
            $data = self::model()->findAll($criteria);
        } catch (Exception $e) {
            $data = array();
        }
        return $data;
    }


    /**获取满足条件的司机列表
     * @param array $arr
     * @return mixed
     */
    public function getYesterdayList($offset, $limit = 1000)
    {
        try {
            $criteria = new CDbCriteria;
            $date = date('Y-m-d 15:00:00', strtotime("-1 day"));
            $today = date('Y-m-d 15:00:00');
            $criteria->addBetweenCondition('last_changed_date', $date, $today);
            $criteria->addCondition('status=1');
            $criteria->offset = $offset;
            $criteria->limit = $limit;
            $criteria->order = 'last_changed_date desc';
            $data = self::model()->findAll($criteria);
        } catch (Exception $e) {
            $data = array();
        }
        return $data;
    }


    /**获取满足条件的司机列表
     * @param array $arr
     * @return mixed
     */
    public function checkCardIsExit($card_id,$name,$phone)
    {
        $result = 0;
        try {
            $criteria = new CDbCriteria;
            $criteria->addCondition('card_id=:card_id');
            $criteria->params[':card_id'] = $card_id;

            $criteria->addCondition('name=:name');
            $criteria->params[':name'] = $name;

            $criteria->addCondition('phone=:phone');
            $criteria->params[':phone'] = $phone;
            $data = self::model()->find($criteria);
            if ($data) {
                $result = 1;
            }
        } catch (Exception $e) {
            $result = -1;
            EdjLog::error($e->getMessage());
        }
        return $result;
    }
}