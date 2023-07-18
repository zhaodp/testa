 <?php

/**
 * This is the model class for table "{{driver_wealth_log}}".
 *
 * The followings are the available columns in table '{{driver_wealth_log}}':
 * @property integer $id
 * @property string $driver_id
 * @property integer $type
 * @property integer $wealth
 * @property integer $city_id
 * @property string $create_time
 * @property string $update_time
 */
class DriverWealthLog extends ReportActiveRecord
{

    const ALL_EMONEY_MANAGER=6000000;//600W给司管分配使用

    /**type**/
    const WEEK_TYPE=1; //周全勤奖 
    const QUICK_ACCEPT_TYPE=2;//快速接单奖励 
    const HOT_ONLINE_TYPE=3;//高峰上线 
    const REACH_INTIME_TYPE=4;//准时抵达 
    const FIVE_STAR_TYPE=5;//五星评价 
    const LONG_DISTANCE_TYPE=6;//远距离 
    const GROUP_TYPE=7;//组长责任鼓励 
    const REJECT_TYPE=8;//拒单
    const CANCEL_TYPE=9;//销单
    const DRIVER_REWARD=10;//司管奖励,额外奖励
    const CROWN_TYPE=11;//皇冠兑换
    const DAY_ORDER_INFO_TYPE=12;//填写日间订单信息
    const INVEST_TYPE=13;//问卷调查奖励
    const REWARD_PUNISH_TYPE=14;//奖赏或处罚


    /**wealth**/
    const WEEK_WEALTH=80;
    const QUICK_RECEIVE_ORDER_WEALTH=3;
    const HOT_ONLINE_WEALTH=1;
    const REACH_INTIME_WEALTH=2;
    const FIVE_STAR_WEALTH=10;
    const LONG_DISTANCE_WEALTH=5;
    const GROUP_WEALTH=15;
    const REJECT_WEALTH=-20;
    const CANCEL_WEALTH=-20;
    const CROWN_WEALTH=-1000;
    const DRIVER_REWARD_WEALTH=2;//恶劣天气,高峰在线额外奖励2e
    const DAY_ORDER_INFO_WEALTH=5;
    const INVEST_REWARD_WEALTH=39; //奖励，暂时没其他地方用，可以随时改
    const REWARD_PUNISH_WEALTH=1; //动态变化,不是常量,设置成1主要是为了统计天总数

    public static $typeName= array(
        '1' => '周全勤奖',
        '2'=>'快速接单奖励',
        '3'=>'高峰上线',
        '4'=>'准时抵达',
        '5'=>'五星评价',
        '6'=>'远距离',
        '7'=>'组长责任鼓励',
        '8'=>'拒单',
        '9'=>'销单',
        '10'=>'司管奖励',
        '11'=>'皇冠兑换',
        '12'=>'填写日间订单信息',
        '13'=>'问卷调查奖励',
        '14'=>'奖赏或处罚', //文案根据des显示
        );

    public static $typeWealth= array(
        '1' => self::WEEK_WEALTH,
        '2'=> self::QUICK_RECEIVE_ORDER_WEALTH,
        '3'=> self::HOT_ONLINE_WEALTH,
        '4'=> self::REACH_INTIME_WEALTH,
        '5'=> self::FIVE_STAR_WEALTH,
        '6'=> self::LONG_DISTANCE_WEALTH,
        '7'=> self::GROUP_WEALTH,
        '8'=> self::REJECT_WEALTH,
        '9'=> self::CANCEL_WEALTH,
        '10'=>self::DRIVER_REWARD_WEALTH, 
        '11'=>self::CROWN_WEALTH,
        '12'=>self::DAY_ORDER_INFO_WEALTH,
        '13'=>self::INVEST_REWARD_WEALTH,
        '14'=>self::REWARD_PUNISH_WEALTH,
        );

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_wealth_log}}';
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
            array('type, wealth, city_id', 'numerical', 'integerOnly'=>true),
            array('driver_id', 'length', 'max'=>10),
            array('id,create_time,des', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id,des, driver_id, type, wealth, city_id, create_time, update_time', 'safe', 'on'=>'search'),
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
            'type' => 'Type',
            'wealth' => 'Wealth',
            'city_id' => 'City',
            'des'=>'des',
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
        $criteria->compare('type',$this->type);
        $criteria->compare('wealth',$this->wealth);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('des',$this->des);
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
     * @return DriverWealthLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
    *   日间订单接口，报单完，增加5个e
    *   @param $driver_id司机工号，$order_id订单号
    */
    public function addDayOrderWealth($driver_id,$order_id){
        $city_id = 0;
        $create_time=date("Y-m-d H:m:s");
        $driver = DriverStatus::model()->get($driver_id);
        if($driver){
            $city_id = $driver->city_id;
        }
        echo 'driver_id='.$driver_id.',日间报单order_id='.$order_id.PHP_EOL;
        EdjLog::info('driver_id='.$driver_id.',日间报单order_id='.$order_id);
        $res = $this->addLog($driver_id,self::DAY_ORDER_INFO_TYPE,self::DAY_ORDER_INFO_WEALTH,$city_id,$create_time);
        if($res){
            DriverExt::model()->addWealth($driver_id,self::DAY_ORDER_INFO_WEALTH);
        }else{
            EdjLog::info('driver_id='.$driver_id.', day order  add log insert field    ：'.$order_id);
        }

    }

    /**
    * 车牌号填写不正确扣50e
    *
    */
    public function deductWealth($driver_id,$order_id){
        $des='车牌号填写不正确';
        $wealth=-50;
        $city_id = 0;
        $create_time=date("Y-m-d H:m:s");
        $driver = DriverStatus::model()->get($driver_id);
        if($driver){
            $city_id = $driver->city_id;
        }
        echo 'driver_id='.$driver_id.',车牌号填写不正确，订单号：'.$order_id.PHP_EOL;
        EdjLog::info('driver_id='.$driver_id.',车牌号填写不正确，订单号：'.$order_id);
        $res = $this->addLog($driver_id,self::REWARD_PUNISH_TYPE,$wealth,$city_id,$create_time,$des);
        if($res){
            DriverExt::model()->addWealth($driver_id,$wealth);
        }else{
            EdjLog::info('driver_id='.$driver_id.',deduct log insert field    ：'.$order_id);
        }
    }


    /**
    *   add wealth log
    *
    **/
    public function addLog($driver_id,$type,$wealth,$city_id,$create_time='',$des=''){
        if(empty($create_time)){
            $create_time=date("Y-m-d 08:00:00",strtotime("-1 day"));//默认设置为昨天,今天统计昨天的，昨天7到今天7点
        }

        $driver_wealth_log = new DriverWealthLog();
        $driver_wealth_log_attr = $driver_wealth_log->attributes;
        $driver_wealth_log_attr['driver_id'] = $driver_id;
        $driver_wealth_log_attr['create_time'] = $create_time;
        $driver_wealth_log_attr['type'] = $type;
        $driver_wealth_log_attr['wealth'] = $wealth;
        $driver_wealth_log_attr['city_id']=$city_id;
        $driver_wealth_log_attr['des']=$des;
        $driver_wealth_log->attributes = $driver_wealth_log_attr;
        return $driver_wealth_log->insert();
    }

    /**
    *   分类统计司机指定时间的财富，比如：date=2014-07-01则获取2014-07-01 7:00:00到2014-07-02 7:00:00的数据
    *
    */
    public function getWealth($driver_id,$date){
        $start_time = date("Y-m-d 07:00:00",strtotime($date));
        $start_time_stamp = strtotime($start_time); //获取当天7点的时间戳
        $end_time_stamp = $start_time_stamp + 60*60*24;//第二天7点时间戳
        $end_time =date('Y-m-d H:i:s', $end_time_stamp);

        $sql="select sum(wealth) as sum,type,des from t_driver_wealth_log 
                        where driver_id=:driver_id and create_time>=:start_time and create_time<=:end_time group by type";
        $command = Yii::app()->dbreport->createCommand($sql);
        $command->bindParam(":start_time", $start_time);
        $command->bindParam(":end_time",$end_time);
        $command->bindParam(":driver_id",$driver_id);
        $wealth_list = $command->queryAll();
        return $wealth_list;
    }

    /**
    *   获取司机指定时间的财富，比如：date=2014-07-01则获取2014-07-01 7:00:00到2014-07-02 7:00:00的数据
    *
    */
    public function getWealthListByType($driver_id,$date,$type){
        $start_time = date("Y-m-d 07:00:00",strtotime($date));
        $start_time_stamp = strtotime($start_time); //获取当天7点的时间戳
        $end_time_stamp = $start_time_stamp + 60*60*24;//第二天7点时间戳
        $end_time =date('Y-m-d H:i:s', $end_time_stamp);

        $sql="select wealth,type,des from t_driver_wealth_log 
                        where driver_id=:driver_id and type=:type and create_time>=:start_time and create_time<=:end_time";
        $command = Yii::app()->dbreport->createCommand($sql);
        $command->bindParam(":start_time", $start_time);
        $command->bindParam(":end_time",$end_time);
        $command->bindParam(":driver_id",$driver_id);
        $command->bindParam(":type",$type);
        $wealth_list = $command->queryAll();
        return $wealth_list;
    }
    
}