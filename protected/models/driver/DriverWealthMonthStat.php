 <?php

/**
 * This is the model class for table "{{driver_wealth_month_stat}}".
 *
 * The followings are the available columns in table '{{driver_wealth_month_stat}}':
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
 * @property integer $order_count
 * @property integer $score_count
 * @property integer $reach_percent
 * @property integer $receive_order_time
 * @property integer $city_id
 * @property string $stat_month
 * @property string $create_time
 * @property string $update_time
 */
class DriverWealthMonthStat extends ReportActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_wealth_month_stat}}';
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
            array('total,five_star_count, reach_count, receive_count, group_count, hotline_count, long_distance_count, week_count, cancel_count, reject_count, reward_count, order_count, score_count, reach_percent,day_order_count, receive_order_time, city_id', 'numerical', 'integerOnly'=>true),
            array('driver_id,stat_month', 'length', 'max'=>10),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id,day_order_count,driver_id, total,five_star_count, reach_count, receive_count, group_count, hotline_count, long_distance_count, week_count, cancel_count, reject_count, reward_count, order_count, score_count, reach_percent, receive_order_time, city_id, create_time, update_time', 'safe', 'on'=>'search'),
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
            'driver_id' => '司机',
            'total'=>'总e币',
            'five_star_count' => '五星',
            'reach_count' => '准时',
            'receive_count' => '快速接单',
            'group_count' => '组长单',
            'hotline_count' => '高峰在线(15分钟为单位)',
            'long_distance_count' => '远距离',
            'week_count' => '周全勤',
            'cancel_count' => '销单',
            'reject_count' => '拒单',
            'reward_count' => '司管奖励',
            'day_order_count' => '日间订单奖励',
            'order_count' => '报单数',
            'score_count' => '扣分值',
            'reach_percent' => '到达率',
            'receive_order_time' => '接单时间',
            'city_id' => 'City',
            'stat_month' => 'Stat Month',
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
        $criteria->compare('total',$this->total);
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
        $criteria->compare('order_count',$this->order_count);
        $criteria->compare('day_order_count',$this->day_order_count);
        $criteria->compare('score_count',$this->score_count);       
        $criteria->compare('reach_percent',$this->reach_percent);
        $criteria->compare('receive_order_time',$this->receive_order_time);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('stat_month',$this->stat_month,true);
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
     * @return DriverWealthMonthStat the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

     /**
    *   add wealth month stat
    *
    **/
    public function addMonthStat($param){

        $driver_wealth_log = new DriverWealthMonthStat();
        $driver_wealth_log_attr = $driver_wealth_log->attributes;
        $driver_wealth_log_attr['driver_id'] = $param['driver_id'];
        $driver_wealth_log_attr['create_time'] = date("Y-m-d H:i:s");
        $driver_wealth_log_attr['total'] = $param['total'];
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
        $driver_wealth_log_attr['stat_month'] = $param['stat_month'];
        $driver_wealth_log_attr['city_id']=$param['city_id'];
        $driver_wealth_log->attributes = $driver_wealth_log_attr;
        return $driver_wealth_log->insert();
    }

    /*
    *   add by aiguoxin
    *   update
    */
    public function updateMonthStat($param){

        $sql = "UPDATE `t_driver_wealth_month_stat` SET 
        `total` = :total,
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
        `day_order_count` = :day_order_count
        WHERE driver_id = :driver_id and stat_month=:stat_month";
        return Yii::app()->dbreport->createCommand($sql)->execute(array(
            ':total' => $param['total'],
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
            ':day_order_count'=>$param['day_order_count'],
            ':driver_id' => $param['driver_id'],
            ':stat_month' => $param['stat_month'],
        ));
    }

    /*
    *   add by aiguoxin
    *   get wealth
    */
    public function getWealthMonth($driver_id,$stat_month){
        $command = Yii::app()->dbreport->createCommand();
        $command->select('*');
        $command->from('t_driver_wealth_month_stat');
        $command->where("driver_id=:driver_id and stat_month=:stat_month order by id desc limit 1",
         array(':driver_id'=>$driver_id,':stat_month'=>$stat_month));
        
        return $command->queryRow();
    }

    /*
    *   add by aiguoxin
    *   获取司机该月在本市e币排名
    *   @param $stat_month 格式‘2014-07-01’
    */
    public function getMonthCityRank($driver_id,$stat_month){
        $driver = Driver::model()->getProfile($driver_id);
        $monthWealth = $this->getWealthMonth($driver_id,$stat_month);
        $rank = 0;
        if(empty($driver) || empty($monthWealth)){
            return 0;
        }
        $driverWealth = $monthWealth['total'];
        $city_id = $driver->city_id;
        $driverSql = 'SELECT COUNT(*) FROM t_driver_wealth_month_stat where city_id=:city_id and stat_month=:stat_month and total>:total ';
        $driverCount = Yii::app()->dbreport->createCommand($driverSql)->bindParam(':city_id', $city_id)->bindParam(':stat_month',$stat_month)->bindParam(':total',$driverWealth)->queryScalar();
        if($driverCount == 0){
            return 0;
        }

        $sql = 'SELECT COUNT(*) FROM t_driver_wealth_month_stat where city_id=:city_id and stat_month=:stat_month';
        $allCityCount = Yii::app()->dbreport->createCommand($sql)->bindParam(':city_id', $city_id)->bindParam(':stat_month',$stat_month)->queryScalar();

        if($allCityCount > 0){
            $rank = number_format($driverCount/$allCityCount,2,'.','');
        }

        return $rank;
    }

}