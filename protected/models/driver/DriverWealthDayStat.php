 <?php

/**
 * This is the model class for table "{{driver_wealth_day_stat}}".
 *
 * The followings are the available columns in table '{{driver_wealth_day_stat}}':
 * @property integer $id
 * @property string $driver_id
 * @property integer $five_star_count
 * @property integer $reach_count
 * @property integer $receive_count
 * @property integer $group_count
 * @property integer $hotline_count
 * @property integer $long_distance_count
 * @property integer $week_count
 * @property integer $cancel_count
 * @property integer $reject_count
 * @property integer $reward_count
 * @property integer $city_id
 * @property string $stat_day
 * @property string $create_time
 * @property string $update_time
 */
class DriverWealthDayStat extends ReportActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_wealth_day_stat}}';
    }

     /**
     * @return CDbConnection database connection
     */
    public function getDbConnection()
    {
        return Yii::app()->dbreport;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('update_time', 'required'),
            array('reward_punish_count,invest_count,day_order_count,five_star_count, total_wealth, reach_count, receive_count, group_count, hotline_count, long_distance_count, week_count, cancel_count, reject_count, reward_count, city_id', 'numerical', 'integerOnly'=>true),
            array('driver_id,stat_day', 'length', 'max'=>10),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, driver_id,reward_punish_count,invest_count, five_star_count, reach_count, receive_count, group_count, hotline_count, long_distance_count, week_count, cancel_count, reject_count, reward_count, city_id, create_time, update_time', 'safe', 'on'=>'search'),
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
            'five_star_count' => 'Five Star Count',
            'reach_count' => 'Reach Count',
            'receive_count' => 'Receive Count',
            'group_count' => 'Group Count',
            'hotline_count' => 'Hotline Count',
            'long_distance_count' => 'Long Distance Count',
            'week_count' => 'Week Count',
            'cancel_count' => 'Cancel Count',
            'reject_count' => 'Reject Count',
            'reward_count' => 'Reward Count',
            'day_order_count' => 'Day Order Count',
            'invest_count'=>'invest_count',
            'reward_punish_count'=>'reward_punish_count',
            'total_wealth' => 'total_wealth',
            'city_id' => 'City',
            'stat_day' => 'Stat Day',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
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
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('five_star_count',$this->five_star_count);
        $criteria->compare('reach_count',$this->reach_count);
        $criteria->compare('receive_count',$this->receive_count);
        $criteria->compare('group_count',$this->group_count);
        $criteria->compare('hotline_count',$this->hotline_count);
        $criteria->compare('long_distance_count',$this->long_distance_count);
        $criteria->compare('week_count',$this->week_count);
        $criteria->compare('cancel_count',$this->cancel_count);
        $criteria->compare('reject_count',$this->reject_count);
        $criteria->compare('reward_count',$this->reward_count);
        $criteria->compare('day_order_count',$this->day_order_count);
        $criteria->compare('reward_punish_count',$this->reward_punish_count);
        $criteria->compare('total_wealth',$this->total_wealth);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('stat_day',$this->stat_day,true);
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
     * @return DriverWealthDayStat the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
    *   add wealth day stat
    *
    **/
    public function addDayStat($param){
        $driver_wealth_log = new DriverWealthDayStat();
        $driver_wealth_log_attr = $driver_wealth_log;
        $driver_wealth_log_attr['driver_id'] = $param['driver_id'];
        $driver_wealth_log_attr['create_time'] = date("Y-m-d H:i:s");
        $driver_wealth_log_attr['five_star_count'] = $param['five_star_count'];
        $driver_wealth_log_attr['reach_count'] = $param['reach_count'];
        $driver_wealth_log_attr['receive_count'] = $param['receive_count'];
        $driver_wealth_log_attr['group_count'] = $param['group_count'];
        $driver_wealth_log_attr['hotline_count'] = $param['hotline_count'];
        $driver_wealth_log_attr['long_distance_count'] = $param['long_distance_count'];
        $driver_wealth_log_attr['week_count'] = $param['week_count'];
        $driver_wealth_log_attr['cancel_count'] = $param['cancel_count'];
        $driver_wealth_log_attr['reject_count'] = $param['reject_count'];
        $driver_wealth_log_attr['reward_count'] = $param['reward_count'];
        $driver_wealth_log_attr['day_order_count'] = $param['day_order_count'];
        $driver_wealth_log_attr['invest_count'] = $param['invest_count'];
        $driver_wealth_log_attr['reward_punish_count'] = $param['reward_punish_count'];
        $driver_wealth_log_attr['total_wealth'] = $param['total_wealth'];
        $driver_wealth_log_attr['stat_day'] = $param['stat_day'];
        $driver_wealth_log_attr['city_id']=$param['city_id'];
        // $driver_wealth_log->attributes = $driver_wealth_log_attr;
        return $driver_wealth_log->insert();
    }

    /**
    *   更新
    *
    */
     public function updateDayStat($param){
        $sql = "UPDATE `t_driver_wealth_day_stat` SET 
        `total_wealth` = :total_wealth,
        `five_star_count` = :five_star_count,
        `reach_count` = :reach_count, 
        `receive_count` = :receive_count, 
        `group_count` = :group_count, 
        `hotline_count` = :hotline_count, 
        `long_distance_count` = :long_distance_count, 
        `week_count` = :week_count,
        `cancel_count` = :cancel_count, 
        `reject_count` = :reject_count, 
        `reward_count` = :reward_count,
        `day_order_count` = :day_order_count,
        `invest_count`=:invest_count,
        `reward_punish_count`=:reward_punish_count
        WHERE driver_id = :driver_id and stat_day=:stat_day";
        return Yii::app()->dbreport->createCommand($sql)->execute(array(
            ':total_wealth' => $param['total_wealth'],
            ':five_star_count' => $param['five_star_count'],
            ':reach_count' => $param['reach_count'],
            ':receive_count' => $param['receive_count'],
            ':group_count' => $param['group_count'],
            ':hotline_count' => $param['hotline_count'],
            ':long_distance_count' => $param['long_distance_count'],
            ':week_count' => $param['week_count'],
            ':cancel_count' => $param['cancel_count'],
            ':reject_count' => $param['reject_count'],
            ':reward_count' => $param['reward_count'],
            ':day_order_count' => $param['day_order_count'],
            ':invest_count' => $param['invest_count'],
            ':reward_punish_count' => $param['reward_punish_count'],
            ':driver_id' => $param['driver_id'],
            ':stat_day' => $param['stat_day'],
        ));
       }

       /**
       *    更新周统计结果到周一里
       */
       public function updateDayStatForWeek($driver_id,$stat_day){
            $sql = "UPDATE `t_driver_wealth_day_stat` SET 
            `total_wealth` = total_wealth+:week_wealth,
            `week_count` = 1
            WHERE driver_id = :driver_id and stat_day=:stat_day";
            return Yii::app()->dbreport->createCommand($sql)->execute(array(
                ':week_wealth'=> DriverWealthLog::WEEK_WEALTH,
                ':driver_id' => $driver_id,
                ':stat_day' => $stat_day,
            ));
       }

    /**
    *   get today count
    *   
    */
    public function getTotalByDay($day){
        $sql = 'SELECT COUNT(*) FROM t_driver_wealth_day_stat where create_time>:day';
        $count = Yii::app()->dbreport->createCommand($sql)->bindParam(':day', $day)->queryScalar();
        return $count;
    }

    /**
    *   获取财富列表,根据司机id
    *   
    */
    public function getWealthList($driver_id,$pageNo,$pageSize){

        $limitStart = ($pageNo-1)*$pageSize;
        $list = Yii::app()->dbreport->createCommand()
            ->select("*")
            ->from('t_driver_wealth_day_stat')
            ->where('driver_id=:driver_id', array(
                ':driver_id' => $driver_id))
            ->order('create_time DESC')
            ->limit($pageSize)
            ->offset($limitStart)
            ->queryAll();

        return $list;
    }

    /**
    *   获取司机具体一天的财富信息
    */
    public function getWealth($driver_id,$stat_day){
        $sql = 'SELECT * FROM t_driver_wealth_day_stat where driver_id=:driver_id and stat_day=:stat_day';
        $wealth = Yii::app()->dbreport->createCommand($sql)->bindParam(':stat_day', $stat_day)->bindParam(':driver_id', $driver_id)->queryRow();
        return $wealth;
    }

    /**
    *  获取本市当天司机排名
    *
    */
    public function getDayCityRank($driver_id,$stat_day){
        $driver = Driver::model()->getProfile($driver_id);
        $driverWealth = $this->getWealth($driver_id,$stat_day);
        $rank = 0;
        if(empty($driver) || empty($driverWealth)){
            return 0;
        }
        $city_id = $driver->city_id;
        
        $driverSql = 'select count(1) from t_driver_wealth_day_stat where city_id=:city_id and stat_day=:stat_day and total_wealth>:total_wealth';
        $driverCount = Yii::app()->dbreport->createCommand($driverSql)->bindParam(':city_id', $city_id)->bindParam(':stat_day',$stat_day)->bindParam(':total_wealth',$driverWealth['total_wealth'])->queryScalar();

        if($driverCount == 0){
            return 0;
        }

        $sql = 'select count(1) from t_driver_wealth_day_stat where city_id=:city_id and stat_day=:stat_day';
        $allCityCount = Yii::app()->dbreport->createCommand($sql)->bindParam(':city_id', $city_id)->bindParam(':stat_day',$stat_day)->queryScalar();

        if($allCityCount > 0){
            $rank = number_format($driverCount/$allCityCount,2,'.','');
        }

        return $rank;
    }

    /**
    *   获取当月恶劣天气奖励补助发放
    *
    */
    public function getWeatherRewardMonth(){
        $start_day=date('Y-m-01'); 
        $end_day=date('Y-m-d'); 

        $sql = 'select sum(reward_count) from t_driver_wealth_day_stat where stat_day>=:start_day and stat_day<=:end_day';
        $reward_count = Yii::app()->dbreport->createCommand($sql)->bindParam(':start_day', $start_day)->bindParam(':end_day',$end_day)->queryScalar();
        return $reward_count*DriverWealthLog::DRIVER_REWARD_WEALTH;
    }
}