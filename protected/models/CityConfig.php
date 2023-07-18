<?php

/**
 * This is the model class for table "{{city_config}}".
 *
 * The followings are the available columns in table '{{city_config}}':
 * @property integer $id
 * @property string $city_id
 * @property string $city_name
 * @property string $city_prifix
 * @property string $bonus_prifix
 * @property string $city_level
 * @property integer $status
 * @property integer $cast_id
 * @property integer $fee_id
 * @property integer $pay_money
 * @property integer $screen_money
 * @property integer $bonus_back_money
 * @property integer $captital
 * @property string $create_time
 * @property string $update_time
 * @property string $online_time
 * @property integer $type
 * @property string $type_value
 */
class CityConfig extends CActiveRecord
{
    CONST CITY_STATUS_OPEN = 1; //城市开通状态 已经开通
    CONST CITY_STATUS_CLOSE = 0; // 城市开通状态 未开通

    CONST IS_CAPTITAL = 1; //是否是省会  是
    CONST NOT_CAPTITAL = 0;  // 是否是省会城市 否

    // 城市等级
    CONST CITY_LEVEL_S = 'S';
    CONST CITY_LEVEL_A1 = 'A1';
    CONST CITY_LEVEL_A2 = 'A2';
    CONST CITY_LEVEL_A3 = 'A3';
    CONST CITY_LEVEL_B1 = 'B1';
    CONST CITY_LEVEL_B2 = 'B2';
    CONST CITY_LEVEL_B3 = 'B3';
    CONST CITY_LEVEL_C1 = 'C1';
    CONST CITY_LEVEL_C2 = 'C2';
    CONST CITY_LEVEL_C3 = 'C3';

    //日间业务价格策略配置
    CONST DAYTIME_NOTOPEN       = 0;
    CONST DAYTIME_SMALL_CITY    = 1;
    CONST DAYTIME_BIGCITY       = 2;


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{city_config}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id,province_id, city_name, city_prifix, bonus_prifix, status, cast_id, fee_id, pay_money,  bonus_back_money, screen_money, city_level,first_letter,pinyin,online_time', 'required'),
            array('status, pay_money, screen_money, bonus_back_money, captital, type,daytime_price,wash_car_price', 'numerical', 'integerOnly' => true),
            array('city_id, city_name, city_prifix, bonus_prifix, city_level', 'length', 'max' => 10),
            array('type_value', 'length', 'max' => 255),
            array('online_time', 'length', 'max' => 20),
            array('city_prifix', 'unique', 'message'=>'该前缀已经存在','on'=>'insert'),
            array('cast_id, fee_id,daytime_cast', 'length', 'max' => 20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, city_id,province_id, city_name, city_prifix, bonus_prifix, city_level, status, cast_id, fee_id, pay_money, screen_money, bonus_back_money, captital, create_time, update_time, online_time, type, type_value,first_letter,pinyin', 'safe', 'on' => 'search'),
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
            'id' => 'ID',
            'city_id' => '城市编号',
            'province_id' => '省份编号',
            'city_name' => '城市名称',
            'city_prifix' => '城市前缀(唯一)',
            'bonus_prifix' => '优惠劵前缀',
            'city_level' => '城市等级',
            'status' => '开通状态',
            'cast_id' => '信息费收费标准',
            'fee_id' => '城市收费标准',
            'pay_money' => '每次扣款金额',
            'screen_money' => '屏蔽底线金额',
            'bonus_back_money' => '最低起步价',
            'captital' => '是否省会级城市',
            'first_letter'=>'城市名称首字母（BJ）',
            'pinyin'=>'城市全拼(shang,hai)',
            'daytime_price'=>'日间业务价格',
            'daytime_cast'=>'日渐业务信息费',
            'wash_car_price'=>'洗车业务价格',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
            'online_time' => '上线时间',
            'type' => 'Type',
            'type_value' => 'Type Value',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('city_id', $this->city_id, true);
        $criteria->compare('province_id', $this->province_id, true);
        $criteria->compare('city_name', $this->city_name, true);
        $criteria->compare('city_prifix', $this->city_prifix, true);
        $criteria->compare('bonus_prifix', $this->bonus_prifix, true);
        $criteria->compare('city_level', $this->city_level, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('cast_id', $this->cast_id);
        $criteria->compare('fee_id', $this->fee_id);
        $criteria->compare('pay_money', $this->pay_money);
        $criteria->compare('screen_money', $this->screen_money);
        $criteria->compare('bonus_back_money', $this->bonus_back_money);
        $criteria->compare('captital', $this->captital);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('online_time', $this->online_time, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('type_value', $this->type_value, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CityConfig the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->city_prifix = strtoupper($this->city_prifix);
            $this->update_time = date('Y-m-d H:i:s');
            if ($this->isNewRecord) {
                $this->create_time = date('Y-m-d H:i:s');
            }
            return true;
        }
        return parent::beforeSave();
    }

    public function afterSave(){
        RCityList::model()->load();
        //更新h5官网api 数据。
        $h5citylist = $this->h5citylist('old',true);
        $h5citylist = $this->h5citylist('new',true);
        parent::afterSave();
    }

    /*
     * 获取城市开通状态 数组
     */
    public static function getCityStatus(){
        return array(
            self::CITY_STATUS_OPEN=>'开通',
            self::CITY_STATUS_CLOSE=>'关闭'
        );
    }

    /*
     * 获取城市是否时省会常量数组
     */
    public static function getCityCaptital(){
        return array(
            self::IS_CAPTITAL=>'是',
            self::NOT_CAPTITAL=>'否'
        );
    }


    public static function getStartPrice(){
        return array(
            19=>19,
            39=>39,
        );
    }


    /*
     * 获取城市等级 常量数组
     */
    public static function getCityLevel(){
        return array(
            self::CITY_LEVEL_S=>'S',
            self::CITY_LEVEL_A1=>'A1',
            self::CITY_LEVEL_A2=>'A2',
            self::CITY_LEVEL_A3=>'A3',
            self::CITY_LEVEL_B1=>'B1',
            self::CITY_LEVEL_B2=>'B2',
            self::CITY_LEVEL_B3=>'B3',
            self::CITY_LEVEL_C1=>'C1',
            self::CITY_LEVEL_C2=>'C2',
            self::CITY_LEVEL_C3=>'C3',
        );
    }



    public static function getDaytimePrice($price_id = ''){
        $data =  array(
            self::DAYTIME_NOTOPEN       => '不开通',
            self::DAYTIME_SMALL_CITY    => '19元起步（含1小时、5公里）每增加30分钟15元增加1公里加收1元',
            self::DAYTIME_BIGCITY       => '29元起步（含1小时、10公里）每增加30分钟15元增加1公里加收1元'
        );
        if($price_id){
            if(isset($data[$price_id])) return $data[$price_id];
            else return false;
        }else return $data;
    }

    public static function getDaytimeCast($castname=''){
        $data =  array(
            'default'=>'不收取信息费',
            'tenpercent'=>'10%信息费'
        );
        if($castname){
            if(isset($data[$castname])) return $data[$castname];
            else return false;
        }else return $data;
    }

    public static function getWashCarPrice($wash_id=''){
        $data = array(
            0=>'不开通',
            1=>0,
            2=>19,
            3=>29,
            4=>39
        );
        if($wash_id){
            if(isset($data[$wash_id])) return $data[$wash_id];
            else return false;
        }else return $data;
    }

    public function getfeelist($city_id,$isDayTime = false){

        $fee = RCityList::model()->getFee($city_id, $isDayTime);

        $str='  <table class="table">
                <thead>
                    <tr>
                        <th>时间段</th>
                        <th>代驾费(' . $fee['distince'] . '公里起步)</th>
                    </tr>
                </thead>
                <tbody>';

        foreach($fee['part_price'] as $v){
            $str .= ' <tr>
                    <td> ' . $v['start_time'].'-'.$v['end_time'] . ' </td>
                    <td> ' . $v['price'] . ' 元 </td >
                </tr>';
        }
        $daytimeType = CityConfigService::dayTimeStatus($city_id);
        $priceContent = Yii::app()->params['appContent']['priceContent'];
        if($daytimeType){

            $no1 = $priceContent['memo']['4']['zh'];
            $n  = Yii::app()->params['daytime_price_new'][$daytimeType];
            $a = $n['basic_time']/60;
            $no1 = sprintf($no1, $n['start_time'], $n['end_time'], $n['price'],  $a, $n['basic_distance'], $n['beyond_time_unit'], $n['beyond_time_price'],
                $n['beyond_distance_unit'],$n['beyond_distance_price'],$n['beyond_time_unit'],$n['beyond_time_unit'],$n['beyond_distance_unit'],$n['beyond_distance_unit']);
        }else{
            $no1= '';
        }
        $desc1 = $no1.$priceContent['memo']['1']['zh'];//全国开通新价格表
        $open=RCityList::model()->isOpenDayTime($city_id);
        if(!$open){
            $desc1 = '不同时段的代驾起步费以实际出发时间为准。';
        }

        $str .= '</tbody>
            </table>
            <div>
                <p>1、'. $desc1 . '</p>
                <p>2、代驾距离超过' . $fee['distince'] . '公里后，每' . $fee['next_distince'] . '公里加收' . $fee['next_price'] . '元，不足' . $fee['next_distince'] . '公里按' . $fee['next_distince'] . '公里计算。</p>
                <p>3、等候时间每满' . $fee['before_waiting_time'] . '分钟收费' . $fee['before_waiting_price'] . '元，不满' . $fee['before_waiting_time'] . '分钟不收费。</p>
            </div>';

        return $str;


    }




    public function getfeeall($fee_id,$isDayTime = false){

        $fee = $isDayTime ? Common::feeDayTime($fee_id) : Common::fee($fee_id);

        $str='  <table class="table">
                <thead>
                    <tr>
                        <th>时间段</th>
                        <th>代驾费(' . $fee['distince'] . '公里起步)</th>
                    </tr>
                </thead>
                <tbody>';

        foreach($fee['part_price'] as $v){
            $str .= ' <tr>
                    <td> ' . $v['start_time'].'-'.$v['end_time'] . ' </td>
                    <td> ' . $v['price'] . ' 元 </td >
                </tr>';
        }
        $priceContent = Yii::app()->params['appContent']['priceContent'];


        $desc1 = $priceContent['memo']['1']['zh'];

        $str .= '</tbody>
            </table>
            <div>
                <p>1、'. $desc1 . '</p>
                <p>2、代驾距离超过' . $fee['distince'] . '公里后，每' . $fee['next_distince'] . '公里加收' . $fee['next_price'] . '元，不足' . $fee['next_distince'] . '公里按' . $fee['next_distince'] . '公里计算。</p>
                <p>3、等候时间每满' . $fee['before_waiting_time'] . '分钟收费' . $fee['before_waiting_price'] . '元，不满' . $fee['before_waiting_time'] . '分钟不收费。</p>
            </div>';

        return $str;


    }


    public function getAllOnlineCity(){
        $time = date('Y-m-d H:i:s');
        $res  = $this->findAll( 'status= :status and  online_time < :online_time',
            array( ':status' => self::CITY_STATUS_OPEN, ':online_time' => $time));

        return $res;
    }

    /**
     * 获取所有未上线城市ID
     * @author  yuchao
     */
    public function getAllOfflineCity(){
        $time = date('Y-m-d H:i:s');
        $res  = $this->findAll( 'status= :status and  online_time < :online_time',
            array( ':status' => self::CITY_STATUS_CLOSE, ':online_time' => $time));

        return $res;
    }

    public function calculatorFee($city_id,$distance,$booking_time, $wait_time){
        $city_fee_func_name = RCityList::model()->getCityByID($city_id,'fee_id');
        $city_fee_func_name = $city_fee_func_name ? $city_fee_func_name: 'conventional';
        //print_r($city_fee_func_name);
        if(!method_exists('Common',$city_fee_func_name)){
            $city_fee_func_name = 'conventional';
        }
        $res = Common::$city_fee_func_name($distance,$booking_time,$wait_time);
        //print_r($res);
        return $res;

    }

    public function calculatorCast($order){
        //查看优惠信息
        $driver_id = $order['driver_id'];
        $driver_fee_discount = Common::driver_fee_discount($driver_id);

        $function_name = RCityList::model()->getCityByID($order['city_id'],'cast_id');
        $function_name = $function_name ? $function_name: '_castWX';
        if($function_name == '_castFree') return 0; //免信息费
        if(!method_exists('Common',$function_name)){
            $function_name = '_castWX';
        }

        $cast = Common::$function_name($order);
        return $driver_fee_discount * $cast;

    }


    public function getCityLevelAr($key_name = '订单趋势'){
        $city_model = RCityList::model();
        $city_list = $city_model->getcityGroupByLevel();
        $value = array();
        if($city_list){
            krsort($city_list);
            $first = array_slice($city_list,0,1);
            $key_tmp = array_keys($first);
            if($key_tmp[0] == 'S'){
                array_shift($city_list);
                $city_list['S'] = $first['S'];
            }

            foreach($city_list as $k => $v){
                if($k == 'C3'){
                    foreach($v as $city_id){
                        $city_info = $city_model->getCityByID($city_id);
                        $online_time = $city_info['online_time'];
                        $ym = date('Y年m月',strtotime($online_time));
                        $key = $ym.'开通C3类城市'.$key_name;
                        $value[$key][] = $city_id;
                        //print_r($city_info);die;
                    }
                    krsort($value);
                }else {
                    $value[$k.'类城市'.$key_name] = $v;
                }
            }

        }
        return $value;
    }

    /**
     * h5官网使用的城市列表和收费列表
     * @showNew  old:展示就的全部时段的价格表 new:展示新的，刨除日间业务的价格表
     * @return array
     */
    public function h5citylist($showNew,$refresh = false) {
        if($showNew == 'new'){
            $cache_key = 'h5_city_list_new';
        }else{
            $cache_key = 'h5_city_list';
        }
        $res = Yii::app()->cache->get($cache_key);
        if(!$refresh && $res) return $res;
        //缓存失效则重新查询并缓存
        $data = RCityList::model()->getAllCity();
        if($data){
            foreach($data as $city_id => $v){
                if($v['city_name'] == '未开通') continue;
                $d[$city_id]['name'] = $v['city_name'];
                $d[$city_id]['fee_id'] = $v['fee_id'];
                $open = RCityList::model()->isOpenDayTime($city_id);
                if($open && $v['fee_id'] == 'wx_single'){ 
                    $d[$city_id]['fee_id'] = 'sz_single';
                }
                if(!$open && $v['fee_id'] == 'hz_single'){ 
                    $d[$city_id]['fee_id'] = 'zs_single';
                }
                $d[$city_id]['pinyin'] = $v['pinyin'];
                $d[$city_id]['daytime_price'] = $v['daytime_price'];
            }
        }
        if($showNew == 'new'){
            $feelist = Common::feeDayTimeForSite('',1);
           $daytime_price = Yii::app()->params['daytime_price_client'];
           $priceContent = Yii::app()->params['appContent']['priceContent'];
           foreach($daytime_price as $k => $va){
               $no1 = $priceContent['memo']['4']['zh'];
               $n  = Yii::app()->params['daytime_price_new'][$k];
               $a = $n['basic_time']/60;
               $no1 = sprintf($no1, $n['start_time'], $n['end_time'], $n['price'],  $a, $n['basic_distance'], $n['beyond_time_unit'], $n['beyond_time_price'],
                   $n['beyond_distance_unit'],$n['beyond_distance_price'],$n['beyond_time_unit'],$n['beyond_time_unit'],$n['beyond_distance_unit'],$n['beyond_distance_unit']);

               $daytime_price[$k]['desc'] = $no1.$priceContent['memo']['1']['zh'];
           }

           $res = array('city_list' => $d,'fee_list' => $feelist,'daytime_price' => $daytime_price);

            // $res = array('city_list'=>$d,'fee_list'=>$feelist,'daytime_price'=>Yii::app()->params['daytime_price_old'][1]);
        }
        else{
            $feelist = Common::fee('',1);
            $res = array('city_list'=>$d,'fee_list'=>$feelist);
        }

        Yii::app()->cache->set($cache_key,$res,7200);
        return $res;
    }

    /**
     * 通过城市名称获取城市id 如该城市未开通 或字典里没有，返回 未知 城市id
     * @param $cityName
     * @author duke
     * @return int
     */
    public static function getIdByName($cityName){
        $city = RCityList::model()->getOpenCityByName($cityName);
        if(isset($city['city_id'])) {
            return $city['city_id'];
        }
        $code_city_id  = Dict::code('city', $cityName);
        if($code_city_id) return $code_city_id;
        return Dict::code('city', '未开通');
    }

    public static function getNameById($city_id){
        $city = RCityList::model()->getOpenCityByID($city_id,'city_name');
        if($city) {
            return $city;
        }

        $city  = Dict::item('city' , $city_id);
        if($city) return $city;
        return '未开通';
    }

    /**
     * 判断城市是否开通，
     * @param $city_id
     * @return bool
     */
    public function checkOpenOrNot($city_id){
        return true;
    }

    /**
     *   获取洗车开通城市id
     *
     */
    public function getAllWashCity(){
        $today=date("Y-m-d H:i:s",time());
        $washCitys = $this->findAll('status=1 and online_time<:today and wash_car_price!=0',
            array(':today'=>$today));
        return $washCitys;
    }

    /**
    *   日间业务下线
    */
    public function downDayTimeCity($city_id){
        $model = new CityConfig ();
        $count = $model->updateAll ( array ('daytime_price' => 0 ), 
                                        'city_id = :city_id', 
                                        array (':city_id' => $city_id ));
        return $count;
    }

}
