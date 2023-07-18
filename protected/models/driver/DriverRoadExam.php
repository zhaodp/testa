<?php

/**
 * This is the model class for table "{{driver_road_exam}}".
 *
 * The followings are the available columns in table '{{driver_road_exam}}':
 * @property string $id
 * @property string $exam_date
 * @property string $serial_number
 * @property integer $city_id
 * @property string $automatic
 * @property string $manuax
 * @property string $a_examiner
 * @property string $m_examiner
 */
class DriverRoadExam extends CActiveRecord
{

    CONST REDIS_KEY_PREFIX = 'driver_road_exam_';

    CONST TYPE_AUTO = 1; //手动档

    CONST TYPE_MAN = 2; //自动档

    CONST EXAM_FAILED=1; //考试未通过

    CONST EXAM_PASSED='A';//考试通过

    public static $cached = null;

    public static $rank = array('A','B','C');

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DriverRoadExam the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function initRedis() {
        if (!self::$cached)
            self::$cached = new DriverInterviewCache();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_road_exam}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('exam_date, serial_number, city_id', 'required'),
            array('city_id', 'numerical', 'integerOnly'=>true),
            array('serial_number, a_examiner, m_examiner', 'length', 'max'=>10),
            array('automatic, manuax', 'length', 'max'=>2),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, exam_date, serial_number, city_id, automatic, manuax, a_examiner, m_examiner', 'safe', 'on'=>'search'),
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
            'exam_date' => 'Exam Date',
            'serial_number' => 'Serial Number',
            'city_id' => 'City',
            'automatic' => 'Automatic',
            'manuax' => 'Manuax',
            'a_examiner' => 'A Examiner',
            'm_examiner' => 'M Examiner',
            'id_card'=>'司机身份证号',
            'a_road_time'=>'手动档打分时间',
            'm_road_time'=>'自动挡打分时间',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('exam_date',$this->exam_date,true);
        $criteria->compare('serial_number',$this->serial_number,true);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('automatic',$this->automatic,true);
        $criteria->compare('manuax',$this->manuax,true);
        $criteria->compare('a_examiner',$this->a_examiner,true);
        $criteria->compare('m_examiner',$this->m_examiner,true);
        $criteria->compare('id_card',$this->id_card,true);
        $criteria->compare('a_road_time',$this->a_road_time,true);
        $criteria->compare('m_road_time',$this->m_road_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @param $id
     * @return bool
     * 是否通过路考
     */
    public function isRoadPass($road){
        $pass = false;
        if(in_array($road,array(DriverRecruitment::STATUS_ROAD_FIELD_PASS))){
            $pass = true;
        }
        return $pass;
    }

    /**
     * @param $serialNumber
     * @return mixed
     * 获取最近的一条记录
     */
    public function getPassInfo($serialNumber){
        $command = Yii::app()->db_readonly->createCommand();
        $data = $command->select('*')->from('t_driver_road_exam')
            ->where("serial_number=:serial_number order by id desc",
                array(':serial_number'=>$serialNumber))
            ->queryRow();
        return $data;
    }

    /**
     * 以下为自己封装
     */

    /**
     * 增加路考考官
     * @param $date  日期
     * @param $city_id 城市
     * @param $driver_id 司机工号
     * @param $type 考试类型
     */
    public function addDriverExaminer($date, $city_id, $driver_id,$type) {
        $driver_id = strtoupper($driver_id);
        $this->initRedis();
        $key = self::REDIS_KEY_PREFIX.$date.'_'.$type;
        $driver_array = unserialize(self::$cached->redis->hGet($key, $city_id));
        if (!is_array($driver_array))
            $driver_array = array();
        array_push($driver_array, $driver_id);
        $driver_str = serialize($driver_array);
        return self::$cached->redis->hSet($key, $city_id, $driver_str);
    }

    /**
     * 增加路考考官
     * @param $date  日期
     * @param $city_id 城市
     * @param $driver_id 司机工号
     * @param $type 考试类型
     */
    public function delDriverExaminer($date, $city_id, $driver_id,$type) {
        $driver_id = strtoupper($driver_id);
        $this->initRedis();
        $key = self::REDIS_KEY_PREFIX.$date.'_'.$type;
        $driver_array = unserialize(self::$cached->redis->hGet($key, $city_id));
        if (!is_array($driver_array)) {
            return false;
        } else {
            foreach($driver_array as $k=>$v) {
                if ($v==$driver_id) {
                    unset($driver_array[$k]);
                }
            }
            $driver_str = serialize($driver_array);
            return self::$cached->redis->hSet($key, $city_id, $driver_str);
        }
    }

    /**
     * 获得司机
     * @param $date
     * @param $city_id
     * @return array
     */
    public function getDriverExaminer($date, $city_id) {
        $this->initRedis();
        $key_auto = self::REDIS_KEY_PREFIX.$date.'_'.self::TYPE_AUTO;
        $auto_driver_str = self::$cached->redis->hGet($key_auto, $city_id);
        $auto_driver_arr = $auto_driver_str ? unserialize($auto_driver_str) : array();
        $key_man = self::REDIS_KEY_PREFIX.$date.'_'.self::TYPE_MAN;
        $man_driver_str = self::$cached->redis->hGet($key_man, $city_id);
        $man_driver_arr = $man_driver_str ? unserialize($man_driver_str) : array();
        return array(
            'automatic' => $auto_driver_arr,
            'manuax' => $man_driver_arr
        );
    }

    /**
     * 判断司机是否是某日的考官
     * @param $date
     * @param $driver_id
     */
    public function checkDriverIsExaminer($driver_id, $date=null) {
        if (is_null($date)) {
            $date = date('Y-m-d', time());
        }
        $driver_id = strtoupper($driver_id);
        $driver_model = Driver::getProfile($driver_id);
        $city_id = $driver_model->city_id;
        $examiner_info = $this->getDriverExaminer($date, $city_id);
        if (in_array($driver_id, $examiner_info['automatic']) )
        {
            return 'automatic';
        } elseif (in_array($driver_id, $examiner_info['manuax'])) {
            return 'manuax';
        } else {
            return false;
        }
    }

    /**
     * 获得路考信息
     * @param $date 路考日期
     * @param $serialNum 司机报名流水号，不包括城市前缀
     * @return mixed
     */
    public function getRoadExamInfo($date, $serialNum) {
        $data = DriverRoadExam::model()->findByAttributes(array('exam_date'=>$date,'serial_number'=>$serialNum));
        return $data;
    }

    /**
     * 增加考试记录
     */
    public function addRoadInfo($params){
        $res = false;
        $idCard=isset($params['idCard'])?$params['idCard']:0;
        $cityId=isset($params['cityId'])?$params['cityId']:0;
        $serialNum=isset($params['serialNum'])?$params['serialNum']:0;
        $pass=isset($params['pass'])?$params['pass']:0;
        $operator=isset($params['operator'])?$params['operator']:'system';
        $autoRoadExam=isset($params['autoRoadExam'])?$params['autoRoadExam']:0;
        $manualRoadExam=isset($params['manualRoadExam'])?$params['manualRoadExam']:0;
        $reason = isset($params['reason'])?$params['reason']:'';//不通过原因
        $examDate = date("Y-m-d");
        $currentTime= date("Y-m-d H:i:s");
        $exam = self::getRoadExamInfo($examDate,$serialNum);
        if($autoRoadExam && $manualRoadExam){//手动自动同时打分
            //今天已经打过分，不能再插入记录
            if($exam){
                return $res;
            }else {
                //1.记录数据到t_driver_road_exam
                if ($pass) {
                    $autoScore = self::EXAM_PASSED;
                    $manualScore = self::EXAM_PASSED;
                    DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_PASS, $operator);
                    DriverRecruitment::model()->updateByPk($serialNum, array('recycle' => '0'));
                    EdjLog::info('id=' . $serialNum . '路考手动、自动同时打分通过，operator=' . $operator);
                } else {
                    $autoScore = self::EXAM_FAILED;
                    $manualScore = self::EXAM_FAILED;
                    DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_FAILED, $operator);
                    DriverRecruitment::model()->updateByPk($serialNum, array('recycle_reason' => $reason, 'recycle' => '1', 'discard_time' => time()));
                    EdjLog::info('id=' . $serialNum . '路考手动、自动同时打分不通过，operator=' . $operator);
                }
                $driverRoadExam = new DriverRoadExam();
                $driverRoadExam->exam_date = $examDate;
                $driverRoadExam->serial_number = $serialNum;
                $driverRoadExam->city_id = $cityId;
                $driverRoadExam->automatic = $autoScore;
                $driverRoadExam->manuax = $manualScore;
                $driverRoadExam->a_examiner = $operator;
                $driverRoadExam->m_examiner = $operator;
                $driverRoadExam->id_card = $idCard;
                $driverRoadExam->a_road_time = $currentTime;
                $driverRoadExam->m_road_time = $currentTime;
                $driverRoadExam->save();
            }
        }elseif($autoRoadExam){//只打自动
            EdjLog::info('id='.$serialNum.'路考--自动打分，operator='.$operator);
            $autoScore = $pass ? self::EXAM_PASSED:self::EXAM_FAILED;
            if($exam){
                if($exam['a_road_time'] == '0000-00-00 00:00:00') {
                    $exam->updateByPk($exam['id'], array(
                            'a_examiner' => $operator,
                            'a_road_time' => $currentTime,
                            'automatic' => $autoScore,
                        )
                    );
                }
                if($pass){
                    if(in_array($exam['manuax'],self::$rank)){
                        DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_PASS, $operator);
                        DriverRecruitment::model()->updateByPk($serialNum, array('recycle' => '0'));
                        EdjLog::info('operator='.$operator.'更新id='.$serialNum.'路考成功');
                    }
                }else{
                    DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_FAILED, $operator);
                    DriverRecruitment::model()->updateByPk($serialNum,array('recycle_reason'=>$reason,'recycle'=>'1','discard_time'=>time()));
                    EdjLog::info('id='.$serialNum.'路考--自动打分--更新状态为不通过成功，operator='.$operator);
                }
            }else {
                //新增
                $driverRoadExam = new DriverRoadExam();
                $driverRoadExam->exam_date = $examDate;
                $driverRoadExam->serial_number = $serialNum;
                $driverRoadExam->city_id = $cityId;
                $driverRoadExam->automatic = $autoScore;
                $driverRoadExam->a_examiner = $operator;
                $driverRoadExam->id_card = $idCard;
                $driverRoadExam->a_road_time = $currentTime;
                $driverRoadExam->save();
                if (empty($pass)) {
                    DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_FAILED, $operator);
                    DriverRecruitment::model()->updateByPk($serialNum, array('recycle_reason' => $reason, 'recycle' => '1', 'discard_time' => time()));
                    EdjLog::info('id=' . $serialNum . '路考--自动打分--更新状态为不通过成功，operator=' . $operator);
                } else {//进行中
                    DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_PROCESS, $operator);
                    EdjLog::info('id=' . $serialNum . '路考--自动打分--更新状态为进行中成功，operator=' . $operator);
                }
                }
        }elseif($manualRoadExam) {//只打手动
            EdjLog::info('id=' . $serialNum . '路考--手动打分，operator=' . $operator);
            //判断今天是否有路考记录，有记录，则更新，没有则插入
            $manualScore = $pass ? self::EXAM_PASSED : self::EXAM_FAILED;
            if ($exam) {
                if ($exam['m_road_time'] == '0000-00-00 00:00:00') {
                    $exam->updateByPk($exam['id'], array(
                            'm_examiner' => $operator,
                            'm_road_time' => $currentTime,
                            'manuax' => $manualScore,
                        )
                    );
                }
                if ($pass) {
                    if (in_array($exam['automatic'], self::$rank)) {
                        DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_PASS, $operator);
                        DriverRecruitment::model()->updateByPk($serialNum, array('recycle' => '0'));
                        EdjLog::info('operator=' . $operator . '更新id=' . $serialNum . '路考成功');
                    }
                } else {
                    DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_FAILED, $operator);
                    DriverRecruitment::model()->updateByPk($serialNum, array('recycle_reason' => $reason, 'recycle' => '1', 'discard_time' => time()));
                    EdjLog::info('operator=' . $operator . '更新id=' . $serialNum . '路考成功');
                }
            } else {
                if (empty($driverRoadExam)) {//新增
                    $driverRoadExam = new DriverRoadExam();
                    $driverRoadExam->exam_date = $examDate;
                    $driverRoadExam->serial_number = $serialNum;
                    $driverRoadExam->city_id = $cityId;
                    $driverRoadExam->manuax = $manualScore;
                    $driverRoadExam->m_examiner = $operator;
                    $driverRoadExam->id_card = $idCard;
                    $driverRoadExam->m_road_time = $currentTime;
                    $driverRoadExam->save();
                    if (empty($pass)) {
                        DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_FAILED, $operator);
                        DriverRecruitment::model()->updateByPk($serialNum, array('recycle_reason' => $reason, 'recycle' => '1', 'discard_time' => time()));
                        EdjLog::info('operator=' . $operator . '更新id=' . $serialNum . '路考成功');
                    } else {//进行中
                        DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_PROCESS, $operator);
                    }
                }
            }
        }
        return true;
    }


    /**
     * 保存路考信息
     * @param $data
     * @return bool
     */
    public function saveRecord($data) {
        $currentTime= date("Y-m-d H:i:s");
        $criteria = new CDbCriteria;
        $criteria->addCondition("serial_number=:serial_number");
        $criteria->addCondition("exam_date=:exam_date");
        $criteria->params[':serial_number']=$data['serial_number'];
        $criteria->params[':exam_date']=$data['exam_date'];
        $model_recruitment = DriverRecruitment::model()->findByPk($data['serial_number']);
        $idCard = $model_recruitment->id_card;
        $operator= Yii::app()->user->id;
        $model = self::model()->find($criteria);
        if (!$model) { //进行中状态
            $model = new DriverRoadExam();
            if (isset($data['a_examiner']) && in_array($data['automatic'], self::$rank)) {//自动通过，改成进行中
                DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_PROCESS, $operator);
                EdjLog::info('报名序号:' . $data['serial_number'] . '手动档通过，更新状态成功');
            }
            if (isset($data['m_examiner']) && in_array($data['manuax'], self::$rank)) {//手动通过，改成进行中
                DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_PROCESS, $operator);
                EdjLog::info('报名序号:' . $data['serial_number'] . '自动档通过，更新状态成功');
            }

        }
        $model->attributes = $data;
        //增加保存司机工号、操作时间
        $model->id_card = $model_recruitment->id_card;
        if(isset($data['a_examiner'])){
            $model->a_road_time= $currentTime;
        }
        if(isset($data['m_examiner'])){
            $model->m_road_time= $currentTime;
        }
        $result=$model->save(false);
        //手动、自动都通过，更新入职状态
        if (in_array($model->automatic, self::$rank) && in_array($model->manuax, self::$rank)) {
            $road = min($model->automatic, $model->manuax);
            if($model_recruitment){
                $model_recruitment->road = $road;
                $model_recruitment->rank = min($road, $model_recruitment->interview);
                $open = DriverOrder::model()->checkOpenCity($model_recruitment->city_id);
                if(empty($open)){
                    $model_recruitment->status = DriverRecruitment::STATUS_ROAD_PASS;
                }
                $model_recruitment->save(false);
                DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_PASS, $operator);
                EdjLog::info('报名序号:' . $data['serial_number'] . '参加完路考，t_driver_recruitment表status已经更改成路考通过3...');
            }
        }
        //路考不通过
        if ($result) {
            if ((isset($data['automatic']) && $data['automatic']==self::EXAM_FAILED) || (isset($data['manuax']) && $data['manuax']==self::EXAM_FAILED)) {
                $reason = isset($data['automatic']) ? '自动档路考未通过' : '手动档路考未通过';
                if($model_recruitment) {
                    $model_recruitment->recycle = 1;
                    $model_recruitment->discard_time = time();
                    $model_recruitment->recycle_reason = $reason;
                    $model_recruitment->save();
                    DriverRecruitment::model()->updateRoadStatus($idCard, DriverRecruitment::STATUS_ROAD_FIELD_FAILED, $operator);
                }
            }
        }
        return $result;
    }

    /**
     * 根据路考成绩更新司机报名表中的相关信息
     * @param $serial_number
     * @param $automatic
     * @param $manuax
     * @return bool
     */
    private function updateRecruitmentRecord($serial_number, $automatic, $manuax) {
        if (in_array($automatic, self::$rank) && in_array($manuax, self::$rank)) {
            $model = DriverRecruitment::model()->findByPk($serial_number);
            $road = min($automatic, $manuax);
            $model->road = $road;
            $model->rank = min($road, $model->interview);
            $model->status = 3;
            $model->save(false);
        } else {
            return false;
        }
    }

    public function getEntryDriverRoadExamData($date_start, $date_end, $city_id, $offset, $limit) {
        $sql = "select * from t_driver_road_exam as e left join t_driver_recruitment as r on e.serial_number=r.id where r.status=4 and exam_date>='{$date_start}' and exam_date<='{$date_end}'";
        if ($city_id>0) {
            $sql .= " and e.city_id={$city_id}";
        }
        $sql .= " limit {$offset}, {$limit}";
        $command = Yii::app()->db_readonly->createCommand($sql);
        return $command->queryAll();
    }

    public function getTotal($date_start, $date_end, $city_id) {
        $sql = "select count(*) from t_driver_road_exam as e left join t_driver_recruitment as r on e.serial_number=r.id where r.status=4 and exam_date>='{$date_start}' and exam_date<='{$date_end}'";
        if ($city_id>0) {
            $sql .= " and e.city_id={$city_id}";
        }
        $command = Yii::app()->db_readonly->createCommand($sql);
        return $command->queryScalar();
    }

    public function getEntryDriverRoadExamByDriverId($driver_id) {
        $recruitment_model = DriverRecruitment::model()->find("driver_id='{$driver_id}'");
        if ($recruitment_model) {
            $serial_number = $recruitment_model->id;
            $model = self::model()->findByAttributes(array('serial_number'=>$serial_number));
            if ($model) {
                $data = $model->getAttributes();
                $data['driver_id'] = $driver_id;
                $data['name'] = $recruitment_model->name;
                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getRoadTimes($id_card){
        $driver_info = DriverRecruitment::model()->getDriverByIDCard($id_card);
        if($driver_info) {
            $examDate = date("Y-m-d");
            $total = $this->count('id_card = :id_card', array(':id_card' => $id_card));
            $today = $this->count('id_card = :id_card and exam_date=:exam_date', array(':id_card' => $id_card, 'exam_date' => $examDate));
            return array('total'=>$total,'today'=>$today);
        }
        return false;
    }
}