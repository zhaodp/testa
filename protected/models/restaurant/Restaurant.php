<?php
/**
 * This is the model class for table "{{restaurant}}".
 *
 * The followings are the available columns in table '{{restaurant}}':
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $latitude
 * @property string $longitude
 * @property string $telephone
 * @property integer $city
 * @property string $contact
 * @property integer $title
 * @property string $mobile
 * @property integer $district
 * @property integer $zone
 * @property integer $type
 * @property integer $cost
 * @property integer $tables
 * @property integer $user_id
 * @property string $updated
 * @property string $created
 */

Yii::import('application.config.*');
require_once ("config_restaurant.php");
class Restaurant extends CActiveRecord
{

    public $cost_min = NULL;        //人均消费范围最小值
    public $cost_max = NULL;        //人均消费范围最大值
    public $table_min = NULL;       //桌数范围最小值
    public $table_max = NULL;       //桌数范围最大值
    public $competition = NULL;     //竞品
    public $materials = NULL;       //物料
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{restaurant}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, address, latitude, longitude, telephone, city, contact, title, mobile, district, zone, type, cost, tables, user_id, updated, created,tables_type,demand_index', 'required'),
            array('city, title, district, zone, type, cost, tables, user_id , flag ,demand_index ', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>50),
            array('address', 'length', 'max'=>200),
            array('remark', 'length', 'max'=>255),
            array('latitude, longitude', 'length', 'max'=>15),
            array('telephone', 'length', 'max'=>30),
            array('contact', 'length', 'max'=>20),
            array('mobile', 'length', 'max'=>25),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, address, latitude, longitude, telephone, city, contact, title, mobile, district, zone, type, tables_type , cost, tables, user_id, updated, created , flag , remark ,demand_index', 'safe', 'on'=>'search'),
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
            'name' => '商家名称',
            'address' => '地址',
            'latitude' => '纬度',
            'longitude' => '经度',
            'telephone' => '固定电话',
            'city' => '城市',
            'contact' => '联系人',
            'title' => '职位',
            'mobile' => '手机',
            'district' => '区域',
            'zone' => '商圈',
            'type' => '商家类型',
            'cost' => '人均消费',
            'tables' => '桌数/房间数',
            'tables_type' => '桌数类型',
            'user_id' => '录入人员',
            'updated' => '最后更新时间',
            'created' => 'Created',
            'flag' => 'Flag',
            'remark' => '备注',
            'demand_index' => '代驾需求指数',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('address',$this->address,true);
        $criteria->compare('latitude',$this->latitude,true);
        $criteria->compare('longitude',$this->longitude,true);
        $criteria->compare('telephone',$this->telephone,true);
        $criteria->compare('city',$this->city);
        $criteria->compare('contact',$this->contact,true);
        $criteria->compare('title',$this->title);
        $criteria->compare('mobile',$this->mobile,true);
        $criteria->compare('district',$this->district);
        $criteria->compare('zone',$this->zone);
        $criteria->compare('type',$this->type);
        if($this->cost_min !== NULL && $this->cost_min > 0){
            $criteria->compare('cost', '>= '.$this->cost_min);
        }
        if($this->cost_max !== NULL && $this->cost_max > 0){
            $criteria->compare('cost', '<= '.$this->cost_max);
        }
        if($this->table_min !== NULL && $this->table_min > 0){
            $criteria->compare('tables', '>= '.$this->table_min);
        }
        if($this->table_max !== NULL && $this->table_max > 0){
            $criteria->compare('tables', '<= '.$this->table_max);
        }
        $criteria->compare('cost',$this->cost);
        $criteria->compare('tables_type',$this->tables_type);
        if($this->user_id){
            $user = Yii::app()->db_readonly->createCommand()->select('user_id')->from('{{admin_user}}')->where('name = :name')->queryScalar(array(':name'=>$this->user_id));
            $criteria->compare('user_id',$user === FALSE ? '-1' : $user);
        }
        $criteria->compare('updated',$this->updated,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('demand_index',$this->created);
        $criteria->compare('flag',0); //0正常  1 删除
        
        if($this->competition !== NULL || $this->materials !== NULL){
            $criteria->join = 'LEFT JOIN {{restaurant_attr}} `r` ON ( r.restaurant_id = t.id AND r.keyword = "restaurant_info" )';
            if($this->competition !== NULL){
                $criteria->addCondition('r.value LIKE "0,'.$this->competition.'%" OR r.value LIKE "1,'.$this->competition.'%"');
            }
            if($this->materials !== NULL){
                $criteria->addCondition('r.value LIKE "%'. ($this->materials) .'"');
            }
        }

        if(!isset($_GET['Restaurant_sort'])){
            $criteria->order = " id desc";
        }

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>40,
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Restaurant the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 新加商铺数据
     */
    public function insertInfo($params = array()){
        if(empty($params)){
            return false;
        }
        $msg = "";
        $log = $params;
        $competition_arr = $params['competition_arr'];
        $photos = $params['photos'];
        unset($params['competition_arr']);
        unset($params['photos']);
        $restaurant = new Restaurant();
        $restaurant->attributes = $params;

        if($restaurant->save()){

            $condition = array(
                'restaurant_id' => $restaurant->id,
                'user_id' => $restaurant->user_id,
                'created' =>time(),
            );

            if(!empty($photos)){
                //保存图片
                RestaurantImage::model()->insertPhotosInfo($photos , $condition);
                unset($photos);
            }

            //属性
            RestaurantAttr::model()->insertAttInfo($competition_arr,$condition);

            $condition['comments'] = json_encode($log);
            //记录LOG
            RestaurantLog::model()->insertLogInfo($condition);

            unset($competition_arr);
            unset($photos);
            unset($log);
        }else{
            $msg  = '商家信息添加失败';
        }

        return $msg;


    }




    /**
     * 获取商家属性
     * @param int $type_id
     * @param string $keyword
     * @return string
     */
    public function getRestaurantAttrInfo($type_id = 0 , $keyword = "restaurant_info"){
        $ret = "无";
        $restaurantAttrObj = RestaurantAttr::model()->find(' restaurant_id=:restaurant_id and keyword=:keyword',array('restaurant_id'=>$this->id,'keyword'=>$keyword));
        if(!empty($restaurantAttrObj)){
            $restaurantAttrArr = explode(",",$restaurantAttrObj->value);
            if($keyword == "restaurant_info"){
                //竞品详情属性
                switch($type_id){
                    case 0 :
                        //渠道类型
                        $ret =  RestaurantAttr::$channel_type_name[$restaurantAttrArr[$type_id]];
                        break;
                    case 1 :
                        //竞品物料(竞品概况)
                        $ret =  RestaurantAttr::$has_competition[$restaurantAttrArr[$type_id]]."竞品物料";
                        break;
                    case 2 :
                        //竞品物料是否已清除
                        $ret =  RestaurantAttr::$has_competition_wiped[$restaurantAttrArr[$type_id]];
                        break;
                    case 3 :
                        //是否已进店(物料概况)
                        $ret =  RestaurantAttr::$has_materials[$restaurantAttrArr[$type_id]];
                        break;
                }
            }elseif($keyword == "materials_info"){
                //物料详情

                $ret =  $restaurantAttrArr[$type_id] ==  0 ? "无" : "有";

            }
        }
        return $ret;
    }

    /**
     * 获取区域信息名称
     * @param string $name
     * @param $val
     * @return string
     */
    public function getCitiesName($type="city"){
        $name = "";
        $config = config_restaurant::get_config_params();
        switch($type){
            case 'city':
                $name = $config['cities'][$this->city]['name'];
            break;
            case 'district':
                $name = $config['cities'][$this->city]['regions'][$this->district]['name'];
                break;
            case 'zone':
                $name = $config['cities'][$this->city]['regions'][$this->district]['business_circle'][$this->zone]['name'];
                break;

        }

        return $name;
    }

    /**
     * 获取城市列表
     * @return <array>
     * author liuxiaobo
     */
    public function getCities(){
        $cities = array();
        $config = config_restaurant::get_config_params();
        foreach ($config['cities'] as $city){
            $cities[$city['value']] = $city['name'];
        }

        return $cities;
    }

    /**
     * 根据城市获取区域列表
     * @return <array>
     * author liuxiaobo
     */
    public function getDistrictByCity($city=0){
        $childs = array();
        $config = config_restaurant::get_config_params();
        if(!isset($config['cities'][$city]['regions'])){
            return $childs;
        }
        $areas = $config['cities'][$city]['regions'];
        foreach ($areas as $area) {
            $childs[$area['value']] = $area['name'];
        }

        return $childs;
    }

    /**
     * 根据区域获取商圈列表
     * @return <array>
     * author liuxiaobo
     */
    public function getZoneByDistrict($city, $district){
        $childs = array();
        $config = config_restaurant::get_config_params();
        if(!isset($config['cities'][$city]['regions'][$district]['business_circle'])){
            return $childs;
        }
        $areas = $config['cities'][$city]['regions'][$district]['business_circle'];
        foreach ($areas as $area) {
            $childs[$area['value']] = $area['name'];
        }

        return $childs;
    }

    /**
     * 返回图片列表
     */
    public function getPhotoList(){
        $img_list = "";
        $list = RestaurantImage::model()->findAll('restaurant_id=:id',array('id'=>$this->id));
        if(!empty($list)){
            foreach($list as $obj){
                $img_list .= CHtml::image($obj->url.'_small','',array('width'=>'50px;','height'=>'80px',"func"=>"click","middle"=>$obj->url.'_middle'));
              }
        }
        return $img_list;

    }

    /**
     * 获取职位名称
     * @param string $type
     * @return string
     */
    public function getTitleName($type = ""){
        if(empty($type)){ return "";}
        $job_arr = array();
        $config = config_restaurant::get_config_params();
        foreach($config['contact_job'] as $val){
            $job_arr[$val['value']] = $val['name'];
        }
        return $job_arr[$type];
    }

    /**
     * 获取商家桌子类型
     * @param string $type
     * @return string
     */
    public function getTablesTypeName($type = ""){
        if(empty($type)){ return "";}
        $tables_type_arr = array();
        $config = config_restaurant::get_config_params();
        foreach($config['tables_type'] as $val){
            $tables_type_arr[$val['value']] = $val['name'];
        }
        return $this->tables."  (".$tables_type_arr[$type].")";
    }

    /**
     * 获取代驾需求指数名称
     * @param string $type
     * @return string
     */
    public function getDemandIndexName($type = ""){
        $demand_index_arr = array();
        $config = config_restaurant::get_config_params();
        foreach($config['demand_index'] as $val){
            $demand_index_arr[$val['value']] = $val['name'];
        }
        return $demand_index_arr[$type];
    }

    /**
     * 获取商家类型
     * @param string $type
     * @return string
     */
    public function getBusinessTypeName($type = ""){
        if(empty($type)){ return "";}
        $business_type = array();
        $config = config_restaurant::get_config_params();
        foreach($config['business_type'] as $val){
            $business_type[$val['value']] = $val['name'];
        }
        return $business_type[$type];

    }

    /**
     * 获取当前用户当前和所有的商家数据
     * @param string $user_id
     * @return array
     */
    public function getCount($user_id =""){
        $ret = array('all'=>0,'today'=>0);
        $ret['all'] = $this->getAllCount($user_id);
        $ret['today'] = $this->getTodayCount($user_id);
        return $ret;
    }

    /**
     * 获取当前商家所有的统计数量
     * @param string $user_id
     */
    public function getAllCount($user_id = ""){
        $times = 0 ;
        if(empty($user_id)){
            return $times;
        }
        $sql = " select count(id) as times from t_restaurant where user_id=:user_id";
        $times = Yii::app()->db_readonly->createCommand($sql)->queryScalar(array('user_id'=>$user_id));
        return $times;
    }

    /**
     * 获取当前用户今天商家的统计数量
     * @param string $user_id
     * @return int
     */
    public  function getTodayCount($user_id = ""){
        $times = 0 ;
        if(empty($user_id)){
            return $times;
        }
        $date_start = date("Y-m-d")." 00:00:00";
        $date_end = date("Y-m-d")." 23:59:59";
        $sql = " select count(id) as times from t_restaurant where user_id=:user_id and created between :date_start and :date_end";
        $times = Yii::app()->db_readonly->createCommand($sql)->queryScalar(array('user_id'=>$user_id,'date_start'=>$date_start,'date_end'=>$date_end));
        return $times;
    }



}
