<?php

/**
 * This is the model class for table "{{driver_order}}".
 *
 * The followings are the available columns in table '{{driver_order}}':
 * @property integer $id
 * @property string $order_number
 * @property string $logistics_number
 * @property integer $city_id
 * @property integer $export_times
 * @property string $driver_id
 * @property string $driver_name
 * @property integer $driver_phone
 * @property string $height
 * @property string $clothing_size
 * @property integer $order_status
 * @property string $receiver_name
 * @property string $receiver_phone
 * @property string $receiver_addr
 * @property string $order_time
 * @property string $update_time
 */
class DriverOrder extends CActiveRecord
{
    /**
     * @var array
     * 开通城市
     */
    public static $open_city = array(
        14,//天津
        1,//北京
        5,//广州
    );

    //装备押金200
    const DEPOSIT_MONEY=200;
    //扣除押金类型
    const DEPOSIT_TYPE='工服装备押金';

    /**
     * 订单状态
     */
    const STATUS_UN_PAY = 0; //未付款
    const STATUS_PAYED = 1;//已付款
    const STATUS_TO_CARD = 2; //待制卡
    const STATUS_CARD_MADE = 3; //已制卡
    const STATUS_ENTRY = 4; //已入库
    const STATUS_DELIVER = 5; //已发货
    const STATUS_SIGNED = 6; //已签收
    const STATUS_OFFLINE=7;//离职后订单押金退还
    const STATUS_EXCEPTION=8;//订单异常状态


    public static $order_status = array(
        self::STATUS_UN_PAY,
        self::STATUS_PAYED,
        self::STATUS_TO_CARD,
        self::STATUS_CARD_MADE,
        self::STATUS_ENTRY,
        self::STATUS_DELIVER,
        self::STATUS_SIGNED,
        self::STATUS_OFFLINE,
        self::STATUS_EXCEPTION,
    );

    public static $status_dict = array(
        self::STATUS_UN_PAY=>'未付款',
        self::STATUS_PAYED=>'已付款',
        self::STATUS_TO_CARD=>'待制卡',
        self::STATUS_CARD_MADE=>'已制卡',
        self::STATUS_ENTRY=>'已入库',
        self::STATUS_DELIVER=>'已发货',
        self::STATUS_SIGNED=>'已签收',
        self::STATUS_OFFLINE=>'离职后订单押金退还',
        self::STATUS_EXCEPTION=>'异常'
    );

    public $start_time = '';
    public $end_time = '';


    public static $sku_info = array(
        1=>array(
            array(
                'name'=>'2014版马甲（新）',
                'type'=>'马甲',
                'number'=>1,
                'price'=>14,
                'size'=>1
            ),
            array(
                'name'=>'支架',
                'type'=>'支架',
                'number'=>1,
                'price'=>14,
                'size'=>0
            ),
            array(
                'name'=>'2014版T恤（新）',
                'type'=>'T恤',
                'number'=>1,
                'price'=>14,
                'size'=>1
            ),
            array(
                'name'=>'正式工牌',
                'type'=>'工牌',
                'number'=>1,
                'price'=>14,
                'size'=>0
            ),
            array(
                'name'=>'胸卡卡套',
                'type'=>'胸卡卡套',
                'number'=>1,
                'price'=>14,
                'size'=>0
            ),
        ),
        2=>array(),
    );

    public static $status_array = array(
        self::STATUS_UN_PAY => '未付款',
        self::STATUS_PAYED=>'已付款',
        self::STATUS_TO_CARD => '待制卡',
        self::STATUS_CARD_MADE => '已制卡',
        self::STATUS_ENTRY => '已入库',
        self::STATUS_DELIVER => '已发货',
        self::STATUS_SIGNED => '已签收',
        self::STATUS_OFFLINE=>'离职后订单押金退还',
        self::STATUS_EXCEPTION=>'异常'
    );

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_order}}';
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
            array('city_id, export_times, product_suit, order_status', 'numerical', 'integerOnly' => true),
            array('order_number, logistics_number, logistics_company, driver_id, driver_name, receiver_name', 'length', 'max' => 20),
            array('height, clothing_size', 'length', 'max' => 10),
            array('receiver_phone,driver_phone,weight', 'length', 'max' => 15),
            array('receiver_addr_province, receiver_addr_city, receiver_addr_county', 'length', 'max' => 45),
            array('receiver_addr,reason', 'length', 'max' => 255),
            array('order_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, order_number, reason, weight, logistics_number, logistics_company, city_id, export_times, driver_id, driver_name, driver_phone, height, clothing_size, product_suit, order_status, receiver_name, receiver_phone, receiver_addr_province, receiver_addr_city, receiver_addr_county, receiver_addr, order_time, update_time', 'safe', 'on' => 'search'),
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
            'order_number' => '订单编号',
            'logistics_number' => '物流编号',
            'logistics_company' => '物流公司',
            'city_id' => '城市',
            'export_times' => '导出次数',
            'driver_id' => '司机工号',
            'driver_name' => '司机姓名',
            'driver_phone' => '司机电话',
            'height' => '身高',
            'clothing_size' => '尺寸',
            'product_suit' => 'sku_id',
            'order_status' => '状态',
            'receiver_name' => '收货人姓名',
            'receiver_phone' => '收货人电话',
            'receiver_addr_province' => '收货省',
            'receiver_addr_city' => '收货城市',
            'receiver_addr_county' => '收货县',
            'receiver_addr' => '详细收货地址',
            'reason' => '异常原因',
            'weight' => '订单重量',
            'order_time' => '下单时间',
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

        $criteria = new CDbCriteria;
        $criteria->compare('order_number',$this->order_number,true);
        $criteria->compare('logistics_number',$this->logistics_number,true);
        $criteria->compare('logistics_company', $this->logistics_company, true);
        $criteria->compare('city_id', $this->city_id);
        $criteria->compare('export_times', $this->export_times);
        $criteria->compare('driver_id', $this->driver_id, true);
        $criteria->compare('driver_name', $this->driver_name, true);
        $criteria->compare('driver_phone', $this->driver_phone);
        $criteria->compare('height', $this->height, true);
        $criteria->compare('clothing_size', $this->clothing_size, true);
        $criteria->compare('product_suit', $this->product_suit);
        $criteria->compare('order_status', $this->order_status);
        $criteria->compare('receiver_name', $this->receiver_name, true);
        $criteria->compare('receiver_phone', $this->receiver_phone, true);
        $criteria->compare('receiver_addr_province', $this->receiver_addr_province, true);
        $criteria->compare('receiver_addr_city', $this->receiver_addr_city, true);
        $criteria->compare('receiver_addr_county', $this->receiver_addr_county, true);
        $criteria->compare('weight', $this->weight, true);
        $criteria->compare('reason', $this->reason, true);
        $criteria->compare('receiver_addr',$this->receiver_addr,true);
        $criteria->compare('update_time',$this->update_time,true);

        if($this->start_time){
            $criteria->addCondition('order_time >= :start_time');
            $criteria->params[':start_time'] = $this->start_time.' 00:00:00';
        }
        if($this->end_time){
            $criteria->addCondition('order_time <= :end_time');
            $criteria->params[':end_time'] = $this->end_time.' 23:59:59';
        }
        return new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
                'pagination' => array(
                    'pageSize' => 30
                ))
        );
    }


    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverOrder the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @param $driver_id
     * @return mixed
     * 更改司机所有订单已付款状态
     */
    public function restoreOrder($driver_id){
        $success = false;
        $order_list = DriverOrder::model()->findAll("driver_id=:driver_id and order_status!=0",
            array('driver_id' => $driver_id)
        );
        foreach($order_list as $order){
            //更改订单状态为离职退还
            $res = $this->updateStatus($order->id,self::STATUS_OFFLINE);
            if($res){
                $success = $res;
            }
        }
        return $success;
    }


    /***
     * @param $city_id
     * @return bool
     * 判断城市是否开通
     */
    public function checkOpenCity($city_id){
        return in_array($city_id,self::$open_city);
    }

    /**
     * 增加订单记录,用户完善信息后
     * @param $recruitment_id 招聘表对应id
     * @return 是否成功
     */
    public function addDriverOrder($recruitment_id,$can_repeat = false)
    {
        $driverRecruitment = DriverRecruitment::model()->findByPK($recruitment_id);
        if(!$driverRecruitment){
            EdjLog::warning('生成订单失败，t_driver_recruitment中找不到id='.$recruitment_id);
        }
        $driverOrder = new DriverOrder();
        $driverOrder->city_id = $driverRecruitment['city_id'];
        $driverOrder->driver_name = $driverRecruitment['name'];
        $driverOrder->driver_phone = $driverRecruitment['mobile'];
        $driverOrder->driver_id = $driverRecruitment['driver_id'];
        //重新入职，订单信息以t_driver表为准
        $driver = DriverStatus::model()->get($driverOrder->driver_id);
        if($driver){
            $driverOrder->city_id = $driver->city_id;
            $driverOrder->driver_name = $driver->info['name'];
            $driverOrder->driver_id = $driver->driver_id;
        }
        $driverOrder->height = $driverRecruitment['height'];
        $driverOrder->clothing_size = $driverRecruitment['size'];
        $driverOrder->receiver_name = $driverRecruitment['mail_name'];
        $driverOrder->receiver_phone = $driverRecruitment['mail_phone'];
        $driverOrder->receiver_addr_province = $driverRecruitment['mail_province'];
        $driverOrder->receiver_addr_city = $driverRecruitment['mail_city'];
        $driverOrder->receiver_addr_county = $driverRecruitment['mail_district'];
        $driverOrder->receiver_addr = $driverRecruitment['mail_addr'];
        $driverOrder->order_time = date("Y-m-d H:i:s");

        //一天之类不能生成多个同种套装订单
        $yesterday = date("Y-m-d H:i:s",strtotime("-1 day"));
        $res = DriverOrder::model()->findAll("driver_id=:driver_id and product_suit=:product_suit and order_time>:yesterday",
            array('driver_id' => $driverOrder->driver_id,
                  'product_suit'=>$driverOrder->product_suit,
                  'yesterday'=>$yesterday)
        );
        if(!$can_repeat && $res){
            EdjLog::warning('t_driver_recruitment='.$recruitment_id.'一天之内不能创建多条订单');
            return false;
        }

        //存入数据库
        $driverOrder->save(false);
        $driverOrder->getErrors();
        $id = $driverOrder->id;
        if ($id) {
            //生成订单id,更新订单号
            $attr = array('order_number'=>$this->genOrderId($driverOrder->city_id,$id));
            $res = DriverOrder::model()->updateByPk($id, $attr);
            if($res){
                //echo 't_driver_recruitment='.$recruitment_id.'更新订单号成功，id='.$id.PHP_EOL;
                EdjLog::info('t_driver_recruitment='.$recruitment_id.'更新订单号成功，id='.$id);
                return true;
            }else{
                //echo 't_driver_recruitment='.$recruitment_id.'更新订单号失败，id='.$id.PHP_EOL;
                EdjLog::info('t_driver_recruitment='.$recruitment_id.'更新订单号失败，id='.$id);
            }
        }else{
            //echo 't_driver_recruitment='.$recruitment_id.'生成订单成功，id='.$id.PHP_EOL;
            EdjLog::info('t_driver_recruitment='.$recruitment_id.'生成订单成功，id='.$id);
        }
        return false;
    }

    /**
     * @param $city_id
     * @param $id
     * 规则：城市前缀+自增id
     * @return order_number
     */
    private function genOrderId($city_id, $id)
    {
        $pre_city = RCityList::model()->getCityByID($city_id,'city_prifix');
        return $pre_city . $id;
    }

    public static function getStatus($status = '', $is_search = false)
    {
        $status_array = array();
        //if($is_search)
        //$status_array['']= '全部';
        $status_array[self::STATUS_UN_PAY] = '未付款';
        $status_array[self::STATUS_TO_CARD] = '待制卡';
        $status_array[self::STATUS_CARD_MADE] = '已制卡';
        $status_array[self::STATUS_ENTRY] = '已入库';
        $status_array[self::STATUS_DELIVER] = '已发货';
        $status_array[self::STATUS_SIGNED] = '已签收';
        if ($status !== '') {
            if (isset($status_array[$status]))
                return $status_array[$status];
            else return false;

        }
        return $status_array;
    }


    /**
     * @param $order_num
     * @param $status
     * @param $operator
     * @return boolean
     * 通过订单号更改订单状态
     */
    public function updateStatusByOrderNum($order_num,$status,$operator='system'){
        $res = false;
        $order = DriverOrder::model()->find('order_number=:order_number',
            array(':order_number'=>$order_num));
        if($order){
             //更新
            $lastStatus = $order['order_status'];
            if(self::STATUS_UN_PAY != $status) { //如果不是第一个状态，需要判断前置状态
                $index = $status - 1; //找到前一个元素
                EdjLog::info('lastStatus='.$lastStatus.',status='.self::$order_status[$index]);
                if (isset(self::$order_status[$index]) && $lastStatus == self::$order_status[$index]) {
                    EdjLog::info('order_number=' . $order_num . '找到，更新状态..');
                    $res = $this->updateStatus($order['id'], $status, $operator);
                    if(($status == self::STATUS_SIGNED) && $res){
                        //签收后 报名状态改为已经签收
                        $order_info = $this->getInfoByOrderNum($order_num);
                        if($order_info && isset($order_info->driver_id)){
                            $ret = DriverRecruitment::model()->updateStatusByDriverId($order_info->driver_id,DriverRecruitment::STATUS_SIGNED);
                        }
                    }
                }
            }
        }
        return $res;
    }

    /**
     * @param $order_num
     * @param $reason
     * @param string $operator
     * @return bool
     * 强制刷新状态，不管前置状态
     */
    public function updateOrder2ExceptionStatus($order_num,$reason,$operator='system'){
        $res = false;
        $order = DriverOrder::model()->find('order_number=:order_number',
            array(':order_number'=>$order_num));
        if($order) {
            DriverOrder::model()->updateByPk($order['id'],array('reason'=>$reason,));
            $res = $this->updateStatus($order['id'], self::STATUS_EXCEPTION, $operator);
            EdjLog::info('order_number=' . $order_num . '更新状态为异常..reason='.$reason);
        }
        return $res;
    }

    /**
     * @param $order_num
     * @param $weight
     * @return bool
     * 更新订单重量
     */
    public function updateOrderWeight($order_num,$weight,$logistics_name,$logistics_num){
        $res = false;
        $order = DriverOrder::model()->find('order_number=:order_number',
            array(':order_number'=>$order_num));
        if($order) {
            $res = DriverOrder::model()->updateByPk($order['id'],array('weight'=>$weight,'logistics_number'=>$logistics_num,'logistics_company'=>$logistics_name));
            EdjLog::info('order_number=' . $order_num . '更新重量..weight='.$weight.' 物流更新：'.$logistics_name.'--'.$logistics_num);
        }
        return $res;
    }

    public function updateStatus($id, $status, $operator = 'system'){
        $result = DriverOrder::model()->updateByPk($id,array('order_status'=>$status));
        if($result){
            $log_mod = new DriverOrderLog();
            $data = array(
                'order_id'=>$id,
                'status'=> $status,
                'operator'=> $operator
            );
            $log_mod->attributes = $data;
            $log_mod->save();
        }
        return $result;
    }


    public function upload($condition){
        $data = $this->searchNew($condition);
        $excel_data = array();
        if($data['count']){
            $father_dirname = 'driver_pic_'.substr(md5(json_encode($condition)),0,6).rand(1234,9999);
            $dir_name = '/tmp/'.$father_dirname;
            //$dir_name = mb_convert_encoding($dir_name,'GBK','UTF-8');
            if(!is_dir($dir_name)){
                $dir = mkdir($dir_name);
            }else{
                $dir = true;
            }

            if(!$dir){
                echo 'dir cannot make';
                return false;
            }
            //$data= array('bj')

            foreach($data['data'] as $v){

                if(!isset($v->driver_id)){ continue;}
                $driver_info = Driver::model()->getProfile($v->driver_id);
                if(!$driver_info) continue;
                $sub_dir_name = $dir_name.'/'.$driver_info->user.'_'.$driver_info->name;
                //$sub_dir_name = mb_convert_encoding($sub_dir_name,'GBK','UTF-8');

                if(!is_dir($sub_dir_name)){
                    $sub_dir = mkdir($sub_dir_name);
                }
                else{
                    $sub_dir = true;
                }
                if($sub_dir){
                    $driver_head_name  =  $driver_info->user.'_head.jpg';
                    $driver_qrcode = $driver_info->user.'_code.jpg';
                    if($driver_info->picture ){
                        if( !file_exists($sub_dir_name.'/'.$driver_head_name)){
                            $download_head = Common::downloadFile($driver_info->picture, $sub_dir_name, $driver_head_name);
                        }
                        else $download_head = true;
                    }else{
                        $download_head = false;
                    }

                    if($driver_info->two_code_pic){
                        if( !file_exists($sub_dir_name.'/'.$driver_qrcode))
                        $download_qrcode = Common::downloadFile($driver_info->two_code_pic, $sub_dir_name, $driver_qrcode);
                        else
                            $download_qrcode = true;
                    }else
                        $download_qrcode = false;

                    $excel_data[$v->driver_id] = $v;
                    //导出次数增1
                    $this->updateExportTimes($v->id);
                }
            }

            // 创建excel
            if($excel_data)
            $this->createExcel($excel_data,$dir_name.'/名单'.date('Y-m-d').'.xls');
            $zip_name = '';
            if(isset($condition['order_status']) && $condition['order_status'] != -1) {
                $status_arr = self::$status_array;
                $zip_name .= $status_arr[$condition['order_status']];
            }
            if(isset($condition['export_times']) && $condition['export_times'] != -1){
                $zip_name.='_次数-'.$condition['export_times'];
            }
            if(isset($condition['order_start'])){
                $zip_name.='_开始-'.$condition['order_start'];
            }
            if(isset($condition['order_end'])){
                $zip_name.='_结束-'.$condition['order_end'];
            }

            if(isset($condition['name']) && $condition['name']){
                $zip_name.='_姓名-'.$condition['name'];
            }
            if(isset($condition['order_number']) && $condition['order_number']){
                $zip_name.='_orderId-'.$condition['order_number'];
            }
            if(isset($condition['driver_id']) && $condition['driver_id']){
                $zip_name.='_工号-'.$condition['driver_id'];
            }
            if(!$zip_name){
                $zip_name = 'default'.date('His').'.zip';
            }else{
                $zip_name .= date('His').'.zip';
            }

            $save_zip_name = '/tmp/'.$zip_name;
            $zip = Zip::zipDir($dir_name,$save_zip_name);

            if(file_exists($save_zip_name)){
                $bucketname =  'driverdoc';
                $time = time();
                $path = 'driverOrder';
                $upload_model = new UpyunUpload($bucketname);
                $path = $path.'/'.substr(md5($save_zip_name.$time),0,2);
                //echo
                $is_upload = $upload_model->uploadFile($save_zip_name, $path, $zip_name);

                if ($is_upload) {
                    $info = config_upyun::get_config_params($bucketname);
                    $zip_url = $info['up_base_url'].$path.'/'.$zip_name;
                    unlink($save_zip_name);
                    //exec('rm -rf '.$father_dirname);
                    return $zip_url;
                }
                EdjLog::info('upyun upload field local dir:'.$save_zip_name);
                unlink($save_zip_name);
                //exec('rm -rf '.$father_dirname);
                return false;
            }
            else{
                EdjLog::info('zip file field dir_name:'.$dir_name.' zip_name:'.$save_zip_name);
            }
        }
        EdjLog::info('driver data is empty ,condition:'.json_encode($condition));
    }

    /**
     * @param $driver_id
     * @param $mail_province
     * @param $mail_city
     * @param $mail_district
     * 更改未发货所有状态的司机订单，采用新地址
     */
    public function updateAddr($driver_id,$mail_province,$mail_city,$mail_district,$mail_name,$mail_phone){
        $statusStr = self::STATUS_DELIVER.','.self::STATUS_SIGNED.','.self::STATUS_OFFLINE;
        $driverOrders=DriverOrder::model()->findAll("order_status not in(:statusStr) and driver_id=:driver_id",
            array(':driver_id' => $driver_id,':statusStr'=>$statusStr));
        foreach($driverOrders as $order){
            DriverOrder::model()->updateByPk($order->id,
                array('receiver_addr_province'=>$mail_province,
                    'receiver_addr_city'=>$mail_city,
                    'receiver_addr_county'=>$mail_district,
                    'receiver_name'=>$mail_name,
                    'receiver_phone'=>$mail_phone));
        }
        EdjLog::info('更新司机='.$driver_id.'未发货订单收货地址成功');
    }


    public function createExcel($data,$address){
        Yii::import('application.vendors.phpexcel.*');
        Yii::import('application.vendors.phpexcel.PHPExcel.*');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(false);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1','订单编号(请勿修改)')
            ->setCellValue('B1','司机姓名')
            ->setCellValue('C1','司机工号');


        $i = 2;
        foreach($data as $item){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $item->order_number)
                ->setCellValue('B'.$i, $item->driver_name)
                ->setCellValue('C'.$i, $item->driver_id);
            $i += 1;
        }


        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $res = $objWriter->save($address);
        return $res;
    }


    public function updateExportTimes($id){
        $res = $this->updateCounters(array('export_times'=>1),'id='.$id);
        return $res;
    }

    /**
     * @param $orders
     * @return PHPExcel
     * @throws PHPExcel_Exception
     * 设置物流订单相关excel格式
     */
    public function setOrderExcel($orders){
        //导出
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(false);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1','订单编号(请勿修改)')
            ->setCellValue('B1','商品名称(工卡、支架、T恤、马甲)')
            ->setCellValue('C1','司机姓名')
            ->setCellValue('D1','司机工号')
            ->setCellValue('E1','司机身高')
            ->setCellValue('F1','司机上衣尺码')
            ->setCellValue('G1','收货人姓名')
            ->setCellValue('H1','收货人电话')
            ->setCellValue('I1','收货人所在省')
            ->setCellValue('J1','收货人所在市')
            ->setCellValue('K1','收货人所在县')
            ->setCellValue('L1','收货人详细地址')
            ->setCellValue('M1','异常原因(非异常订单请勿填写)')
            ->setCellValue('N1','商品重量')
            ->setCellValue('O1','快递公司名称(必填，发货后填写)')
            ->setCellValue('P1','快递单号(必填，发货后填写)');

        $i = 2;
        foreach($orders as $item){
            /*
             * changed by wangwenchao 获取省份汉字名称
             */
            $province = ChinaDistrictData::getSubDistricts();
            $province_name = $province[$item->receiver_addr_province];
            //获取城市汉字名称
            $city = ChinaDistrictData::getSubDistricts($item->receiver_addr_province);
            $city_name = $city[$item->receiver_addr_city];
            //获取区汉字名称
            $district = ChinaDistrictData::getSubDistricts($item->receiver_addr_city);
            $district_name=$district[$item->receiver_addr_county];
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$i, $item->order_number)
                ->setCellValue('B'.$i, '工卡、支架、T恤、马甲、卡套')
                ->setCellValue('C'.$i, $item->driver_name)
                ->setCellValue('D'.$i, $item->driver_id)
                ->setCellValue('E'.$i, $item->height)
                ->setCellValue('F'.$i, $item->clothing_size)
                ->setCellValue('G'.$i, $item->receiver_name)
                ->setCellValue('H'.$i, $item->receiver_phone)
//                ->setCellValue('I'.$i, $item->receiver_addr_province)
                ->setCellValue('I'.$i, $province_name)
//                ->setCellValue('J'.$i, $item->receiver_addr_city)
                ->setCellValue('J'.$i,$city_name)
//                ->setCellValue('K'.$i, $item->receiver_addr_county)
                ->setCellValue('K'.$i, $district_name)
                ->setCellValue('L'.$i, $item->receiver_addr)
                ->setCellValue('M'.$i, $item->reason)
                ->setCellValue('N'.$i, $item->weight)
                ->setCellValue('O'.$i, '')
                ->setCellValue('P'.$i, '');
            $i += 1;
        }
        return $objPHPExcel;
    }


    public function searchNew($condition){
        $criteria=$this->completeCardSearch($condition);
        $result = DriverOrder::model()->findAll($criteria);
        $total = count($result);
        $data = array('data'=>$result,'count'=>$total);
        return $data;
    }

    public function completeCardSearch($condition){
        EdjLog::info('cardsearch_condition:'.json_encode($condition).'date:'.date('Y-m-d H:i:s'));
        $yesterday = $today = date("Y-m-d",strtotime("-1 day"));
        //$today = date("Y-m-d",time());
        $order_status = isset($condition['order_status']) ? $condition['order_status'] : 2;
        $export_times = isset($condition['export_times']) ? $condition['export_times'] : -1;
        $order_start = isset($condition['order_start']) ? $condition['order_start'] : $yesterday;
        $order_end = isset($condition['order_end']) ? $condition['order_end'] : $today;
        $name = isset($condition['name']) ? $condition['name'] : '';
        $order_number = isset($condition['order_number']) ? $condition['order_number'] : '';
        $driver_id = isset($condition['driver_id']) ? $condition['driver_id'] : '';

        if($order_start){
            $order_start = $order_start.' 00:00:00';
        }
        if($order_end){
            $order_end = $order_end.' 23:59:59';
        }

        $criteria = new CDbCriteria();
        $params = array();

        if($order_status == -1){ //全部状态时候，只包括未制卡和已制卡
            $order_status = implode(",", array(DriverOrder::STATUS_TO_CARD,DriverOrder::STATUS_CARD_MADE));
            $criteria->addCondition('order_status in('.$order_status.')');
        }else{
            $criteria->addCondition('order_status = :order_status');
            $params[':order_status'] = $order_status;
        }

        if ($driver_id) {
            $criteria->addCondition('driver_id = :driver_id');
            $params[':driver_id'] = $driver_id;
        }
        if ($name) {
            $criteria->addCondition('driver_name = :driver_name');
            $params[':driver_name'] = $name;
        }

        if ($order_number) {
            $criteria->addCondition('order_number = :order_number');
            $params[':order_number'] = $order_number;
        }


        if ($order_start && $order_end) {
            $criteria->addCondition('order_time BETWEEN :order_start AND :order_end');
            $params[':order_start'] = $order_start;
            $params[':order_end'] = $order_end;
        } elseif ($order_start && empty($order_end)) {
            $criteria->addCondition('order_time >= :order_start');
            $params[':order_start'] = $order_start;
        } elseif (empty($order_start) && $order_end) {
            $criteria->addCondition('order_time < :$order_end');
            $params[':order_end'] = $order_end;
        }

        //增加按通知次数查询
        if ($export_times >= 0) {
            if ($export_times > 4) {
                $criteria->addCondition('export_times > :export_times');
            } else {
                $criteria->addCondition('export_times = :export_times');
            }
            $params[':export_times'] = $export_times;
        }

        $criteria->params = $params;
        return $criteria;
    }


    public function getInfoByOrderNum($order_num){
        $res = $this->find('order_number = :o',array(':o'=>$order_num));
        return $res;
    }
}


