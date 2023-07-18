<?php

/**
 * This is the model class for table "t_envelope_extend".
 *
 * The followings are the available columns in table 't_envelope_extend':
 * @property string $id
 * @property string $drive_id
 * @property string $drive_code
 * @property string $envelope_id
 * @property integer $amount
 * @property integer $status
 * @property string $create_date
 * @property string $last_changed_date
 */
class EnvelopeExtend extends FinanceActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{envelope_extend}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('envelope_id, create_date, last_changed_date', 'required'),
            array('amount, status,city_id,envelope_type,is_use,order_id', 'numerical', 'integerOnly' => true),
            array('envelope_id,day', 'length', 'max' => 20),
            array('drive_id', 'length', 'max' => 10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id,is_use, drive_id,city_id, envelope_type,envelope_id, amount, status, create_date, last_changed_date,order_id,day', 'safe', 'on' => 'search'),
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
            'drive_id' => '司机ID',
            'envelope_id' => 'Envelope',
            'amount' => 'Amount',
            'status' => 'Status',
            'create_date' => 'Create Date',
            'last_changed_date' => 'Last Changed Date',
            'city_id' => '城市ID',
            'envelope_type' => '红包类型',
            'is_use'=>'领取状态'
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

        $criteria->compare('drive_id', $this->drive_id, true);
        $criteria->compare('city_id', $this->city_id, true);
        $criteria->compare('envelope_id', $this->envelope_id, true);

        $criteria->compare('amount', $this->amount);

        $criteria->compare('status', $this->status);

        $criteria->compare('create_date', $this->create_date, true);

        $criteria->compare('last_changed_date', $this->last_changed_date, true);

        return new CActiveDataProvider('EnvelopeExtend', array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return EnvelopeExtend the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**获取城市红包列表
     * @param array $arr
     * @return mixed
     */
    public function getDriveList($envelope_id,$date_start,$date_end)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'drive_id';
        $criteria->addBetweenCondition('create_date',$date_start,$date_end);
        $criteria->addCondition('envelope_id=:envelope_id');
        $criteria->params[':envelope_id'] = $envelope_id;

        $data = self::model()->findAll($criteria);
        $result = array();
        foreach ($data as $da) {
            $result[] = $da->drive_id;
        }
        return $result;
    }


    /**获取城市红包列表
     * @param array $arr
     * @return mixed
     */
    public function envelopeInsert($arr)
    {
        $result = true;

        $model = new EnvelopeExtend();
        $model->attributes = $arr;
        try {
            $result = $model->insert();
        } catch (Exception $e) {
            $result = false;
            EdjLog::info($e->getMessage());
        }
        return $result;
    }


    /**获取司机红包列表
     * @param array $arr
     * @return mixed
     */
    public function driveEnvelopeList($drive_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'envelope_id,amount';
        $criteria->addCondition('drive_id=:drive_id');
        $criteria->params[':drive_id'] = $drive_id;
        $criteria->addCondition('status=0');
        $data = self::model()->findAll($criteria);
        $result = array();
        foreach ($data as $da) {
            $arr = array();
            $arr[] = $da->envelope_id;
            $arr[] = $da->amount;
            $result[] = $arr;
        }
        return $result;
    }


    /**
     *
     * 更新红包推送状态
     *
     * @param array $arr
     * @return mixed
     */
    public function updateEnvelopeStatus($id,$driver_id,$status=0)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria;

            $criteria->addCondition('id=:id');
            $criteria->params[':id'] = $id;
            $criteria->addCondition('drive_id=:drive_id');
            $criteria->params[':drive_id'] = $driver_id;
            $data = self::model()->find($criteria);
            if ($data) {
                $result['status']=$data->status;
                $data->status = 1;
                if($status==1){
                    $data->is_use=$status;
                }
                $data->last_changed_date = date('Y-m-d H:i:s');
                if ($data->save()) {
                    $result['code'] = FinanceConstants::CODE_SUCCESS;
                    $result['amount'] = $data->amount;
                    $result['msg'] = 'success';
                } else {
                    $result['code'] = FinanceConstants::CODE_FAIL;
                    $result['amount'] = 0;
                    $result['msg'] = $data->getErrors();
                    EdjLog::error($data->getErrors());
                }
            }else{
                $result['code'] = FinanceConstants::CODE_NO_RECORD;
                $result['amount'] = 0;
                $result['msg'] = '无记录';
            }
        } catch (Exception $e) {
            $result['code'] = FinanceConstants::CODE_NO_RECORD;
            $result['amount'] = 0;
            $result['msg'] = $e->getMessage();
            EdjLog::info($e->getMessage());
        }
        return $result;
    }

    /**更新红包领取状态
     * @param array $arr
     * @return mixed
     */
    public function updateEnvelopeReceiveStatus($id,$driver_id,$status=2)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria;

            $criteria->addCondition('id=:id');
            $criteria->params[':id'] = $id;
            $criteria->addCondition('drive_id=:drive_id');
            $criteria->params[':drive_id'] = $driver_id;
            $data = self::model()->find($criteria);
            if ($data) {
                if($data->is_use == 1){
                    $result['code'] = 102;
                    $result['amount'] = $data->amount;
                    $result['msg'] = '你已经领取了该红包,获取信息费'.$data->amount.'元';
                }else{
                    $data->status=1;
                    $data->is_use = $status;
                    $data->last_changed_date = date('Y-m-d H:i:s');
                    if ($data->save()) {
                        $result['code'] = FinanceConstants::CODE_SUCCESS;
                        $result['amount'] = $data->amount;
                        $result['msg'] = 'success';
                    } else {
                        $result['code'] = FinanceConstants::CODE_FAIL;
                        $result['amount'] = 0;
                        $result['msg'] = $data->getErrors();
                        EdjLog::info(json_encode($data->getErrors()));
                    }
                }
            }else{
                $result['code'] = 102;
                $result['amount'] = 0;
                $result['msg'] = '无可领取的红包';
            }
        } catch (Exception $e) {
            $result['code'] = 101;
            $result['amount'] = 0;
            $result['msg'] = '系统忙碌,请稍候重试';
            EdjLog::info($e->getMessage());
        }
        return $result;
    }

    /**获取司机红包列表
     * @param array $arr
     * @return mixed
     */
    public function extendList($params)
    {
        $criteria = new CDbCriteria;
        $criteria->order = 'id desc';

        if (isset($params['city_id']) && $params['city_id'] != 0) {
            $criteria->addCondition('city_id=:city_id');
            $criteria->params[':city_id'] = $params['city_id'];
        }

        if (isset($params['start_date']) && $params['start_date'] != '') {
            $criteria->addCondition('create_date>=:create_date');
            $criteria->params[':create_date'] = $params['start_date'];
        }

        if (isset($params['end_date']) && $params['end_date'] != '') {
            $criteria->addCondition('create_date<=:end_date');
            $criteria->params[':end_date'] = $params['end_date'] . ' 23:59:59';
        }

        if (isset($params['drive_id']) && trim($params['drive_id']) != '') {
            $criteria->compare('drive_id', trim($params['drive_id']), true);
        }

        if (isset($params['amount']) && $params['amount'] != 0) {
            $criteria->addCondition('amount=:amount');
            $criteria->params[':amount'] = $params['amount'];
        }

        return new CActiveDataProvider('EnvelopeExtend', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
    }

    /**获取司机红包列表
     * @param array $arr
     * @return mixed
     */
    public function extendAcountList()
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'DISTINCT(amount)';
        $criteria->order = 'amount asc';
        $data = self::model()->findAll($criteria);
        $result = array('0' => '全部');
        if ($data) {
            foreach ($data as $da) {
                $result[$da['amount']] = $da['amount'];
            }
        }
        return $result;
    }


    /**获取司机红包列表
     * @param array $arr
     * @return mixed
     */
    public function extendAcount()
    {
        $result = 0;
        try {
            $criteria = new CDbCriteria;
            $criteria->select = 'sum(amount) as amount';
            $criteria->addCondition('create_date>:create_date');
            $criteria->params[':create_date'] = date('Y-m-d');
            $criteria->addCondition('status=1');
            $data = self::model()->find($criteria);
            if ($data && $data->amount) {
                $result = $data->amount;
            }
        } catch (Exception $e) {
            EdjLog::info($e->getMessage());
        }

        return $result;
    }


    /**获取司机红包城市列表
     * @param array $arr
     * @return mixed
     */
    public function cityList()
    {
        $result = array();
        try {
            $criteria = new CDbCriteria;
            $criteria->select = 'DISTINCT(city_id)';
            $criteria->addCondition('status=1');
            $criteria->order = 'city_id asc';
            $data = self::model()->findAll($criteria);
            if ($data) {
                foreach ($data as $da) {
                    $result[] = $da->city_id;
                }
            }
        } catch (Exception $e) {
            EdjLog::info($e->getMessage());
        }
        return $result;
    }


    /**获取城市红包发放情况
     * @param array $arr
     * @return mixed
     */
    public function cityEnvelopeList($city_id)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria;
            $criteria->select = 'SUM(amount) as amount,DATE_FORMAT(last_changed_date,"%Y-%m-%d") as last_changed_date,COUNT(*) as envelope_id,COUNT(DISTINCT(drive_id)) as id';
            $criteria->addCondition('status=1');
            if ($city_id != 0) {
                $criteria->addCondition('city_id=:city_id');
                $criteria->params[':city_id'] = $city_id;
            }
            $criteria->group = 'DATE_FORMAT(last_changed_date,"%Y-%m-%d")';
            $criteria->order = 'last_changed_date desc';
            $criteria->limit = 30;
            $result = self::model()->findAll($criteria);
        } catch (Exception $e) {
            EdjLog::info($e->getMessage());
        }

        return $result;
    }


    /**获取待push数据列表
     * @param array $arr
     * @return mixed
     */
    public function getPushList($status, $offset = 0, $limit = 5000)
    {
        $result = array();
        try {
            $arr_type = Dict::items('envelope_type') + Dict::items('envelope_time_type');

            $criteria = new CDbCriteria;
            $criteria->addCondition('status=:status');
            $criteria->params[':status'] = $status;
            $criteria->limit = $limit;
            $criteria->offset = $offset;
            $criteria->order = 'id desc';
            $data = self::model()->findAll($criteria);
            if ($data) {
                foreach ($data as $da) {
                    $arr_pub = array();
                    $arr_pub['id'] = $da->id;
                    $arr_pub['drive_id'] = $da->drive_id;
                    $arr_pub['envelope_id'] = $da->envelope_id;
                    $arr_pub['envelope_type'] = $da->envelope_type < 100 ? 1 : 2;
                    $arr_pub['name'] = EnvelopeInfo::model()->findbypk($da->envelope_id)->envelope_name;
                    $arr_pub['amount'] = $da->amount;
                    $arr_pub['city_id'] = $da->city_id;
                    $arr_pub['order_id'] = $da->order_id;
                    $arr_pub['status'] = $da->status;
                    $arr_pub['is_use'] = $da->is_use;
                    $result[] = $arr_pub;
                }
            }
        } catch (Exception $e) {
            EdjLog::info($e->getMessage());
        }
        return $result;
    }

    /**获取待push数据列表
     * @param array $arr
     * @return mixed
     */
    public function getPushSecondList($status=0, $offset = 0, $limit = 5000)
    {
        $result = array();
        try {
            $arr_type = Dict::items('envelope_type') + Dict::items('envelope_time_type');

            $criteria = new CDbCriteria;
            $criteria->addCondition('status=1');
            $criteria->addCondition('is_use=:is_use');
            $criteria->params[':is_use'] = $status;
            $criteria->addCondition('last_changed_date<=date_add(now(),interval -60 minute)');
            $criteria->limit = $limit;
            $criteria->offset = $offset;
            $criteria->order = 'id desc';
            $data = self::model()->findAll($criteria);
            if ($data) {
                foreach ($data as $da) {
                    $arr_pub = array();
                    $arr_pub['id'] = $da->id;
                    $arr_pub['drive_id'] = $da->drive_id;
                    $arr_pub['envelope_id'] = $da->envelope_id;
                    $arr_pub['envelope_type'] = $da->envelope_type < 100 ? 1 : 2;
                    $arr_pub['envelope_name'] = $arr_type[$da->envelope_type];
                    $arr_pub['amount'] = $da->amount;
                    $arr_pub['city_id'] = $da->city_id;
                    $arr_pub['order_id'] = $da->order_id;
                    $result[] = $arr_pub;
                }
            }
        } catch (Exception $e) {
            EdjLog::info($e->getMessage());
        }
        return $result;
    }

    /**获取待push数据列表
     * @param array $arr
     * @return mixed
     */
    public function getDriveEnvelopeNum($drive_id, $envelope_id)
    {
        $result = 0;
        try {
            $criteria = new CDbCriteria;
            $criteria->addCondition('drive_id=:drive_id');
            $criteria->params[':drive_id'] = $drive_id;
            $criteria->addCondition('envelope_id=:envelope_id');
            $criteria->params[':envelope_id'] = $envelope_id;
            $criteria->addCondition('create_date>:create_date');
            $criteria->params[':create_date'] = date('Y-m-d');
            $data = self::model()->findAll($criteria);
            if ($data) {
                $result = count($data);
            }
        } catch (Exception $e) {
            EdjLog::info($e->getMessage());
            $result = -1;
        }
        return $result;
    }



    /**查看司机是否已经获取红包
     * @param array $arr
     * @return mixed
     */
    public function driveEnvelope($drive_id,$envelope_id)
    {
        $criteria = new CDbCriteria;
        $criteria->addCondition('drive_id=:drive_id');
        $criteria->params[':drive_id'] = $drive_id;
        $criteria->addCondition('envelope_id=:envelope_id');
        $criteria->params[':envelope_id']=$envelope_id;
        $data = self::model()->count($criteria);
        return $data;
    }


    /**重新发放红包
     * @param array $arr
     * @return mixed
     */
    public function rePush($id)
    {
        $data=self::model()->findbypk($id);
        if($data){
            if($data->is_use==0){
                $data->status=0;
                $data->is_use=0;
                $data->save();
            }
            return true;
        }
        return false;
    }
}