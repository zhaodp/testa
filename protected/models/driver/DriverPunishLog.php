<?php

/**
 * This is the model class for table "{{driver_punish_log}}".
 *
 * The followings are the available columns in table '{{driver_punish_log}}':
 * @property integer $id
 * @property integer $driver_id
 * @property integer $customer_complain_id
 * @property integer $complain_type_id
 * @property string $punish_operator
 * @property string $revert_operator
 * @property integer $driver_score
 * @property integer $block_day
 * @property integer $comment_sms_id
 * @property string $driver_money
 * @property integer $revert
 * @property string $create_time
 * @property string $update_time
 */
class DriverPunishLog extends CActiveRecord
{
    CONST REVERT_NO = 0; //撤销状态：没有撤销
    CONST REVERT_YES = 1; //撤销状态： 已经撤销
    CONST REVERT_NO_EXECUTE = 2; //屏蔽尚未执行
    CONST REJECT_RATE_TYPE=10000; //拒单率
    CONST CANCEL_RATE_TYPE=10001; //销单率
    CONST V2_REWARD_TYPE=10002; //v2后台司管添加
	CONST DRIVER_POINTS_RECOVERY = 10003; //季度代驾分恢复

    private $_driver_punish_log_key           = "DRIVER_PUNISH_LOG_KEY_";

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_punish_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver_id, update_time', 'required'),
            array('driver_id,parent_id,city_id, customer_complain_id, complain_type_id, driver_score, block_day,revert_day, comment_sms_id, revert', 'numerical', 'integerOnly'=>true),
            array('operator, ', 'length', 'max'=>45),
            array('revert_reason,deduct_reason', 'length', 'max'=>1000),
            array('driver_money', 'length', 'max'=>8),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, parent_id, driver_id, customer_complain_id, complain_type_id, operator, driver_score, block_day, comment_sms_id, driver_money, revert, create_time, update_time', 'safe', 'on'=>'search'),
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
            'parent_id'=>'回滚对应id',
            'driver_id' => 'Driverid',
            'city_id' => 'cityID',
            'customer_complain_id' => '投诉ID',
            'complain_type_id' => '投诉类别ID',
            'operator' => '操作人员',
            'driver_score' => '司机扣分',
            'block_day' => '屏蔽天数',
            'revert_day' => '回滚天数',
            'comment_sms_id' => '订单评价ID',
            'driver_money' => '司机扣钱或补偿',
            'revert' => '是否撤销',
            'revert_reason' => '撤销原因',
            'deduct_reason' => '扣分原因',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('parent_id',$this->parent_id);
        $criteria->compare('driver_id',$this->driver_id);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('customer_complain_id',$this->customer_complain_id);
        $criteria->compare('complain_type_id',$this->complain_type_id);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('revert_reason',$this->revert_reason,true);
        $criteria->compare('driver_score',$this->driver_score);
        $criteria->compare('block_day',$this->block_day);
        $criteria->compare('revert_day',$this->revert_day);
        $criteria->compare('comment_sms_id',$this->comment_sms_id);
        $criteria->compare('driver_money',$this->driver_money,true);
        $criteria->compare('revert',$this->revert);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverPunishLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function addData($param){
        $mod = new DriverPunishLog();
        $mod->setAttributes($param,false);
        return $mod->insert();
    }

    /***
     * 司机最后一次扣分
     * @param $driver_id
     */
    public function getLastPunish($driver_id) {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver_punish_log');
        $command->where('driver_id=:driver_id', array(':driver_id'=>$driver_id));
        $command->order('id desc');
        $command->limit('1');
        return $command->queryRow();
    }

    /***
     * 司机最后一次扣分
     * @param $customer_complain_id
     */
    public function getPunishByComplainId($customer_complain_id) {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver_punish_log');
        $command->where('customer_complain_id=:customer_complain_id', array(':customer_complain_id'=>$customer_complain_id));
        return $command->queryRow();
    }

    /***
     * find last punish log which is block day > 0
     * @param $customer_complain_id
     */
    public function getLastPunishLog($driver_id) {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_driver_punish_log');
        $command->where('driver_id=:driver_id and block_day>0 and revert = 0',
            array(':driver_id'=>$driver_id));
        $command->order('id desc');
        return $command->queryRow();
    }

    /*
    *   update log
    *   aiguoxin
    */
    public function updatePunish($id,$parent_id){
        $res = $this->updateByPk($id, array('parent_id'=>$parent_id,'revert'=>self::REVERT_NO));
        return $res;
    }


    public function updatePunishCity($id,$city_id){
        $res = $this->updateByPk($id, array('city_id'=>$city_id));
        return $res;
    }

    /*
    *   add by aiguoxin
    *   get unrevert punish all count
    */
    public function getPunishCount($driver_id){
        $command = Yii::app()->db_readonly->createCommand()
            ->select('COUNT(id)')
            ->from('t_driver_punish_log')
            ->where('driver_id=:driver_id and parent_id=0 order by create_time desc');
        $query = $command->queryScalar(array(':driver_id' => $driver_id));
        return $query;
    }

    /*
    *   add by aiguoxin
    *   get unrevert punish score
    */
    public function getScoreCountByMonth($driver_id, $stat_month){
        $command = Yii::app()->db_readonly->createCommand()
            ->select('sum(driver_score)')
            ->from('t_driver_punish_log')
            ->where("driver_id=:driver_id and parent_id=0 and DATE_FORMAT(create_time,'%Y-%m')=:stat_month");
        $query = $command->queryScalar(array(':driver_id' => trim($driver_id),':stat_month' => $stat_month));
        return $query;
    }

    /**
    *   add by aiguoxin
    *   get driver score detail list 
    */
    public function getPunishListByDriver($driver_id,$page,$pageSize){

        $limitStart = ($page-1)*$pageSize;
        $punishList = Yii::app()->db_readonly->createCommand()
            ->select("driver_score as score, deduct_reason as title, UNIX_TIMESTAMP(create_time) as timestamp")
            ->from('t_driver_punish_log')
            ->where('driver_id=:driver_id and parent_id=0', array(
                ':driver_id' => $driver_id))
            ->order('create_time DESC')
            ->limit($pageSize)
            ->offset($limitStart)
            ->queryAll();

        return $punishList;
    }

    /**
    *   get punish log which city_id=0
    */
    public function getPunishList(){
        $punishList = Yii::app()->db_readonly->createCommand()
            ->select("id,driver_id")
            ->from('t_driver_punish_log')
            ->where('city_id=0')
            ->queryAll();

        return $punishList;
    }

    public function checkPunishRepeat($complain_id,$revert = 0){
        $command = Yii::app()->db->createCommand();
        $command->select('*');
        $command->from('t_driver_punish_log');

        $command->where('customer_complain_id=:ccid and revert = :revert', array(':ccid'=>$complain_id,':revert'=>$revert));
        $command->order('id desc');
        $command->limit('1');
        if( $command->queryRow()){
            return true;
        }
        return false;
    }

    /**
    *   判断是否处理过
    *
    */
    public function isProcessed($complain_id){
        $key = $this->_driver_punish_log_key.$complain_id;
        $res = DriverStatus::model()->single_get($key);
        if($res == null || empty($res)){ //不存在，先放入缓存，第二次访问，则存在(需要考虑驳回再处理情况,时间设置5s)
            DriverStatus::model()->single_set($key,1,5);
            return false;;
        }
        return true;
    }

    /**
    *   获取本月该城市的处罚数，即是否执行过
    *   add by aiguoxin
    */
    public function getCountByCity($city_id,$complain_type_id){
        $currentMonth = date("Y-m");
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('count(1)');
        $command->from('t_driver_punish_log');
        $command->where("city_id=:city_id and complain_type_id=:complain_type_id and DATE_FORMAT(create_time,'%Y-%m')=:currentMonth", 
            array(':city_id'=>$city_id,
                  ':complain_type_id'=>$complain_type_id, 
                  ':currentMonth'=>$currentMonth, 
                ));
        return $command->queryScalar();
    }
}
