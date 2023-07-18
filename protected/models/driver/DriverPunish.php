<?php

/**
 * This is the model class for table "{{driver_punish}}".
 *
 * The followings are the available columns in table '{{driver_punish}}':
 * @property integer $id
 * @property string $driver_id
 * @property integer $complain_id
 * @property integer $result
 * @property integer $reason
 * @property integer $status
 * @property string $limit_time
 * @property string $un_punish_time
 * @property string $mark
 * @property string $operator
 * @property string $create_time
 * @property string $update_time
 */
class DriverPunish extends CActiveRecord
{
    const STATUS_ENABLE  = 0; //未屏蔽状态
    CONST STATUS_DISABLE = 1; // 屏蔽状态
    CONST STATUS_OVER    = 2; // 屏蔽已经结束状态
    const STATUS_LEAVE   = 3 ; // 解约司机
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DriverPunish the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_punish}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver_id, reason, operator, create_time', 'required'),
            array('reason, status, limit_time', 'numerical', 'integerOnly'=>true),
            array('driver_id', 'length', 'max'=>10),
            array('mark', 'length', 'max'=>200),
            array('operator,unpunish_operator', 'length', 'max'=>20),
            array('un_punish_time, update_time', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, driver_id, reason, status, limit_time, un_punish_time, mark, operator, create_time, update_time', 'safe', 'on'=>'search'),
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
            'driver_id' => 'Driver',
            'reason' => 'Reason',
            'status' => 'Status',
            'limit_time' => 'Limit Time',
            'un_punish_time' => 'Un Punish Time',
            'mark' => 'Mark',
            'operator' => 'Operator',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('reason',$this->reason);
        $criteria->compare('status',$this->status);
        $criteria->compare('limit_time',$this->limit_time);
        $criteria->compare('un_punish_time',$this->un_punish_time,true);
        $criteria->compare('mark',$this->mark,true);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * 屏蔽处罚是否过期
     * @param $driver_id
     * @author bidong
     */
    public function is_expire($driver_id){
        $flag=true;     //过期

        $condition='driver_id=:did and status =:s';
        $params[':s']=Driver::MARK_DISNABLE;
        $params[':did']=$driver_id;
        $params[':t']=date('Y-m-d',time());
        $drivers=self::model()->find($condition,$params);
        if(!empty($drivers) && strtotime($drivers->un_punish_time)>time()){
            $flag=false;
        }

        return $flag;
    }

    /***
     * 司机受处罚次数
     * @param $driver_id
     */
    public function getPunishCount($driver_id) {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('count(*)');
        $command->from('t_driver_punish');
        $command->where('driver_id=:driver_id', array(':driver_id'=>$driver_id));
        return $command->queryScalar();
    }

    /**
    *   add by aiguoxin
    *   get status = 1
    */
    public function getHandledPunish($driver_id){
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver_punish');
        $command->where('driver_id=:driver_id and status=1', array(':driver_id'=>$driver_id));
        $command->order('id desc');
        $command->limit('1');
        return $command->queryRow();
    }

    /**
     * 司机处罚记录
     * @param $driver_id
     * @param int $page_size
     * @return CActiveDataProvider
     */
    public function getProviderByDriver($driver_id, $page_size=20) {
        $criteria=new CDbCriteria;
        $criteria->compare('driver_id',$driver_id,true);
        $criteria->order = 'id DESC';
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>$page_size,
            ),
        ));
    }


    /**
     * 屏蔽司机 （系统，手动）
     * @param $driver_id
     * @param $type
     * @param $reason
     * @param int $days
     * @return int
     */
    public function disable_driver($driver_id, $type, $reason, $days = 0,$operator='system')
    {
        $driver_mark = Driver::MARK_DISNABLE;
        //add by aiguoxin
        $is_system=false;
        if($operator == 'system'){
            $is_system = true;
        }
        $criteria = new CDbCriteria() ;
        $criteria -> select = array('*');
        $criteria -> condition = 'driver_id = :driver_id and status = :status';
        $criteria -> order = 'id desc';
        $criteria -> limit = 1;
        $criteria -> params = array (':driver_id'=> $driver_id,':status' => self::STATUS_DISABLE) ;
        $driverPunish = $this->find($criteria);
        $block = true; //默认是需屏蔽 如果days 是负数 且计算后没有接除屏蔽则不需屏蔽（block）
        if($driverPunish){
            if((strtotime($driverPunish->un_punish_time) < time()) && ($days > 0) ){
                $driverPunish->status = self::STATUS_OVER;
                $driverPunish->save(); // 如果接触屏蔽的理论时间小于当前时间则 当前记录更改为已经出发完毕状态，新建一条屏蔽记录

                $data = array(  'driver_id'     => $driver_id,
                                'reason'        => $type,
                                'status'        => self::STATUS_DISABLE,
                                'limit_time'    => $days,
                                'mark'          => $reason,
                                'operator'      => $operator,
                                'create_time'   => date('Y-m-d H:i:s'),
                                'un_punish_time'=> date('Y-m-d H:i:s', strtotime("+$days day"))
                );

                $model = new DriverPunish;
                $model->setAttributes($data, false);
                $model->insert();
            }
            else {
                $driverPunish->limit_time = (int)$driverPunish->limit_time + $days;
                $newTime = strtotime($driverPunish->un_punish_time) + $days * 24 * 3600;
                if($newTime < time()){ //如果days 是负数或者计算完时间后小于当前时间  如果大于则直接街屏蔽
                    $driverPunish->status = self::STATUS_ENABLE;
                    $driver_mark = Driver::MARK_ENABLE;
                } 
                $driverPunish->un_punish_time = date('Y-m-d H:i:s', $newTime);
                $driverPunish->operator = $operator;
                $driverPunish->save();
            }
        }else {
            $operator_new = isset(Yii::app()->user) ? Yii::app()->user->id : $operator;
            if($days > 0){
                $data = array(
                    'driver_id'     => $driver_id,
                    'reason'        => $type,
                    'status'        => self::STATUS_DISABLE,
                    'limit_time'    => $days,
                    'mark'          => $reason,
                    'operator'      => $operator_new,
                    'create_time'   => date('Y-m-d H:i:s'),
                    'un_punish_time'=> date('Y-m-d H:i:s', strtotime("+$days day"))
                );

                //print_r($data);die;

                $model = new DriverPunish;
                $model->setAttributes($data, false);
                $model->insert();
                //$res = $this->insert($data);
                //var_dump($res);die;
            }else {
                $block = false;
            }
        }
        $driver_mod = Driver::model();
        if($block){
            return $driver_mod->block($driver_id, $driver_mark, $type, $reason, false, $is_system);
        }
        if($days > 0 )return 2; //如果是累加屏蔽则需要补发短信
        return 1; //不是累加屏蔽则
    }


    /**
    *   @param type 处罚类型
    *   @param reason 解除屏蔽原因
    *   @param operator 解除屏蔽人
    *   @param enable_auto 是否脚本自动解除屏蔽
    */
    public function enable_driver($driver_id, $type, $reason, $operator='system',$enable_auto=false)
    {
        $driver_mark = Driver::MARK_ENABLE;
         //add by aiguoxin
        $is_system=false;
        if($operator == 'system'){
            $is_system = true;
        }
        $criteria = new CDbCriteria() ;
        $criteria -> select = array('*');
        $criteria -> condition = 'driver_id = :driver_id ';
        $criteria -> order = 'id desc';
        $criteria -> limit = 1;
        $criteria -> params = array (':driver_id'=> $driver_id) ;
        $driverPunish = $this->find($criteria);
        if($driverPunish){
            if($driverPunish->status == self::STATUS_LEAVE){
                return false;
            }else

            if($driverPunish->status == self::STATUS_DISABLE){
                $driverPunish->status = self::STATUS_OVER;
                $driverPunish->mark = $driverPunish->mark.'-'.$reason;
                $driverPunish->unpunish_operator = $operator;
                $driverPunish->update_time = date('Y-m-d H:i:s');

                $driverPunish->save();
            }
        }
        $driver_mod = Driver::model();
        return $driver_mod->block($driver_id, $driver_mark, $type, $reason,false,$is_system,$enable_auto);
    }


    public function leave_driver($driver_id, $type, $reason,$operator='system'){
        $criteria = new CDbCriteria() ;
        $criteria -> select = array('*');
        $criteria -> condition = 'driver_id = :driver_id ';
        $criteria -> order = 'id desc';
        $criteria -> limit = 1;
        $criteria -> params = array (':driver_id'=> $driver_id) ;
        $driverPunish = DriverPunish::model()->find($criteria);
        if($driverPunish && $driverPunish->status == self::STATUS_DISABLE){
            $driverPunish->status = self::STATUS_OVER;
            $driverPunish->unpunish_operator = $operator;
            $driverPunish->update_time = date('Y-m-d H:i:s');
            $driverPunish->mark = $driverPunish->mark.'-'.$reason;
            $driverPunish->save();
        }
        //解约 更新屏蔽记录，新建一个解约记录
        $days = 3600;
        $driverPunishl = new DriverPunish();
        $driverPunishl->driver_id = $driver_id;
        $driverPunishl->reason = $type;
        $driverPunishl->status = DriverPunish::STATUS_LEAVE;
        $driverPunishl->limit_time = $days;
        $driverPunishl->mark = $reason;
        $driverPunishl->create_time = date('Y-m-d H:i:s', time());
        $driverPunishl->un_punish_time = date('Y-m-d H:i:s', strtotime("+$days days"));
        $driverPunishl->operator = $operator;
        $driverPunishl->insert();

        $driver_mod = Driver::model();
        $res = $driver_mod->block($driver_id, Driver::MARK_LEAVE, $type, $reason);
        if($res){
            //离职成功，装备押金返还，开通城市，并且有扣除押金订单的
            $driver = Driver::getProfile($driver_id);
            EdjLog::info('driver_id' . $driver_id . '离职成功....');
            if($driver) {
                $order_num = DriverOrder::model()->restoreOrder($driver_id);
                EdjLog::info('driver_id' . $driver_id . '离职成功....order_num='.$order_num);
                if($order_num){
                    EdjLog::info('driver_id' . $driver_id . '离职成功....返还押金...');
                    $money = -DriverOrder::DEPOSIT_MONEY;
                    $ret = FinanceWrapper::settleDriver($driver_id, $driver->city_id, $money,
                        EmployeeAccount::CHANNEL_DEVICE_FEE, DriverOrder::DEPOSIT_TYPE);
                    $success = FinanceConstants::isSuccess($ret);
                    if ($success) {
                        EdjLog::info('driver_id' . $driver_id . '离职成功，返回装备押金成功');
                        return true;
                    }
                }
            }else{
                EdjLog::info('driver_id'.$driver_id.'没有找到司机信息');
            }
        }else{
            EdjLog::info('driver_id'.$driver_id.'离职操作失败');
        }
        return false;
    }

    public function getDriverPunishInfo($driver_id){

        $criteria = new CDbCriteria() ;
        $criteria -> select = array('*');
        $criteria -> condition = 'driver_id = :driver_id and status = :status';
        $criteria -> order = 'id desc';
        $criteria -> limit = 1;
        $criteria -> params = array (':driver_id'=> $driver_id,':status' => self::STATUS_DISABLE) ;
        $driverPunish = $this->find($criteria);
        if($driverPunish){
            $driverPinish_info = $driverPunish->attributes;
            return $driverPinish_info ;
        }return array('limit_time'=>0,'un_punish_time'=>0);
    }


    /**
     * 获取本日待激活司机
     * 如果有城市id 统计会不准确，只能截取工号来统计，没有city_id字段
     * @param int $city_id
     * @author duke
     */
    public function getToUnPunish($city_id = 0){
        //本日待激活司机
        if ($city_id>0) {
            $user_city_prefix = Dict::items('city_prefix');
            $city_prefix = $user_city_prefix[$city_id];
            $activation = Yii::app()->db_readonly->createCommand()->select('count(1) as cnt')->from('t_driver_punish')
                ->where("un_punish_time <= :t  and status != :s and driver_id like '{$city_prefix}%'",array(':t'=>date('Y-m-d',time()),':s'=>'2'))
                ->queryScalar();
        } else {
            $activation = Yii::app()->db_readonly->createCommand()->select('count(1) as cnt')->from('t_driver_punish')
                ->where("un_punish_time <= :t  and status !=:s",array(':t'=>date('Y-m-d',time()),':s'=>'2'))
                ->queryScalar();

        }
        return $activation;
    }



} 