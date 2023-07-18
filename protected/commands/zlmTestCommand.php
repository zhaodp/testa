<?php
/**
 * User: zhanglimin
 * Date: 13-7-1
 * Time: 下午5:27
 */
class ZlmTestCommand extends CConsoleCommand {

    /**
     * 通过对角经纬度画距型
     */
    public function actionT(){

       /*
        南京
       $l_lng_start = 118.548351 ;
        $l_lng_end = 118.973788 ;

        $l_lat_start = 31.849889 ;
        $l_lat_end = 32.145823 ;*/

        /**
         * 北京
         */
        $l_lng_start = 116.263595 ;
        $l_lng_end = 116.511958 ;

        $l_lat_start = 39.790812 ;
        $l_lat_end = 40.001608 ;

        $t = 1786;
        $step = 0.0085 ;
        for($x = $l_lng_start ; $x< $l_lng_end ;$x+=$step){
            for($y = $l_lat_start ; $y < $l_lat_end ; $y+=$step){
                $t++;
                $sql = "insert into t_area_position values ($t,'BJ',$x,$y) ";
                Yii::app()->dbreport->createCommand($sql)->execute();
            }
        }
    }


    /**
     * 取订单
     */
    public function actionGetOrderQueue(){
        $time = date("Y-m-d H:i:s");
        $order_queue = DispatchOrderQueue::model()->getWaitFormQueue($time);
        print_r($order_queue);
    }

    /**
     * 获取司机
     * @param $queue_id
     */
    public function actionGetDrives($queue_id){
        $drivers = DispatchDriver::model()->getDrives($queue_id);
        print_r($drivers);
    }

    public function actionF(){
        $params['order_id'] = 1;
        $params['street'] = 'test';
        Order::model()->updateByPk($params['order_id'] , array('location_start' => $params['street']));
    }


    /**
     * 通过地名查找以往附近的地名
     */
    public function actionHistory(){
        $address = '簋街';
        $lat= 39.947873;
        $lng =  116.426736;
        $gps_type ='baidu';
        $city_name = "北京";
        $result = AddressCallHistory::model()->nearby($lng,$lat,$gps_type ,$address ,$city_name );
        print_r($result);
        //print_r($result);
    }

    /**
     * 初始化mongo地址池数据
     */
    public function actionLoadAddressMongo(){
        AddressCallHistory::model()->init();
    }

    public function actionDispatch(){
        $drivers = array(
            array(
                'driver_id'=>'test1',
            ),
            array(
                'driver_id'=>'test2',
            ),
            array(
                'driver_id'=>'test3',
            ),
            array(
                'driver_id'=>'test4',
            ),
            array(
                'driver_id'=>'test5',
            ),
        );
        /*$i = 1;
        $num = 1;
        foreach ($drivers as $driver) {
            $flag=QueueDispatchDriver::model()->insert($driver['driver_id']);

            if(!$flag){
                continue;
            }
            if($i<=$num){
                $i++;
            }else {
                break;
            }
        }*/


        $number = 1;
        $dispatch = 0;
        for($plan_dispatch_num =0 ; $plan_dispatch_num < $number-$dispatch;$plan_dispatch_num++){
            echo $plan_dispatch_num;
            $i = 1;
            foreach ($drivers as $driver) {
                if($i<=$plan_dispatch_num){

                    $flag=QueueDispatchDriver::model()->insert($driver['driver_id']);
                    //测试工号不派单 BY AndyCong 2013-07-16
                    $test_drivers = Common::getTestDriverIds();
                    //原来这里写错了，会重复推给一个司机 add by sunhongjing
                    if(!$flag || in_array($driver['driver_id'], $test_drivers)){
                        continue;
                    }else{
                        echo "pai!";
                        $dispatch++;
                        $i++;
                    }
                }else {
                    break;
                }
            }
        }


    }

    public function actionReload(){
        $a = DriverStatus::model()->reload('BJ9006');
        var_dump($a);
    }


    public function actionL(){
        $params = array(
            'queue_id'=>'272032',
            'driver_id'=>'BJ9006',
            'flag'=>2,
            'accept_time'=>date("Y-m-d H:i:s"),
            'success_time'=>date("Y-m-d H:i:s"),
        );
        AutoDispatchLog::model()->acceptDriverStatusUpdate($params);
    }

    public function actionX(){
        $data = array(
            'driver_id' => 'BJ9006',
            'goback' => 1,
            'lng' => 116.46211040627,
            'lat' => 32.220224,
            'status' => 1,
        );
        DriverGobackLog::model()->insertInfo($data);
    }


   public function actionCancel(){
       $data = array(
           'cancel_type' => 22,
           'log' => "test",
           'content' => "tttttt",
           'order_id' => "",//"1084818",
           'order_number' => "BJ901113728405434",
           'isComplaint' => 0,
           'driver_id' => "BJ9012",
           'name' => "testf",
           'city_id' => "1",
           'phone' => "13611126764",
           'complaint_type' => "2",
           'complaint' => "asdfffffff",
           'start_time' => date("Y-m-d H:i:s"),
           'source'=>1,
       );
       $flag = QueueProcess::model()->push_new_order_cancel($data);
       var_dump($flag);
   }

    /**
     * 刷order订单表销单类型
     * php yiic zlmTest reloadOrderType
     */
    public function actionReloadOrderType(){

        $sql_array = array(
            "刷订单表客人单方面取消类型数据开始"=>"update t_order set cancel_type = 11 where cancel_type = 1", //与客人协商一致，取消订单
            "刷订单表客人咨询或拨错电话类型数据开始"=>"update t_order set cancel_type = 23 where cancel_type = 2", //代驾咨询
            //"刷订单表未接听到客人电话类型数据开始"=>"update t_order set cancel_type = 16 where cancel_type = 3", #这种情况以后不再有了 这怎么处理
            "刷订单表骚扰电话类型数据开始"=>"update t_order set cancel_type = 22 where cancel_type = 4 ", //与代驾无关私人电话
            //"刷订单表其他原因类型数据开始"=>"update t_order set cancel_type = 34 where cancel_type = 5 ", #没有其它情况
            "刷订单表司机间电话类型数据开始"=>"update t_order set cancel_type = 21 where cancel_type = 6", //司机间通话
            "刷订单表已接其他订单类型数据开始"=>"update t_order set cancel_type = 11 where cancel_type = 7", //与客人协商一致，取消订单
            "刷订单表距离过远，无法满足客户约定时间到达类型数据开始"=>"update t_order set cancel_type = 11 where cancel_type = 8", //与客人协商一致，取消订单
        );
        foreach($sql_array as $val=>$sql_str){
            echo $val."\n";
            echo $sql_str."\n";
            Yii::app()->db->createCommand($sql_str)->execute();
            echo "结束\n";
        }
   }

    /**
     * 刷投诉表类型数据
     * php yiic zlmTest reloadComplaintType
     */
    public function actionReloadComplaintType(){

        $sql_array = array(
            "刷投诉表客人呼叫多人只用一人类型数据开始"=>"update t_driver_complaint set complaint_type = 14 where complaint_type = 1", //客人呼叫多人，只用先到司机
            "刷投诉表抵达后无法联系客人类型数据开始"=>"update t_driver_complaint set complaint_type = 12 where complaint_type = 2", //客户拒接电话
            "刷投诉表订单其他类型数据开始"=>"update t_driver_complaint set complaint_type = 99 where complaint_type = 3", //其它
            "刷投诉表等候超过30分钟，客人单方面取消类型数据开始"=>"update t_driver_complaint set complaint_type = 13 where complaint_type = 4 ", //等候超过30分钟，客人单方面取消
            "刷投诉表拒付或少付费用类型数据开始"=>"update t_driver_complaint set complaint_type = 31 where complaint_type = 5 ", //等候超过30分钟，客人单方面取消
           # "刷投诉表在计价器跳字前被迫到达类型数据开始"=>"update t_driver_complaint set complaint_type = 13 where complaint_type = 6", //等候超过30分钟，客人单方面取消
        );

        foreach($sql_array as $val=>$sql_str){
            echo $val."\n";
            echo $sql_str."\n";
            Yii::app()->db->createCommand($sql_str)->execute();
            echo "结束\n";
        }
    }

    public function actionCancelMsg(){
        $params = array(
            'phone'=>"13811121479", //客户电话
            'isComplaint'=>1, //是否投拆
            'cancel_type'=>17, //销单类型,
            'complaint_type'=>32, //投诉类型,
            'driver_id'=>"BJ1161", //司机工号,
            'driver_name'=>"李宁", //司机姓名,
            'driver_phone'=>"13911523123", //司机电话,
      );
        $msg = Order::model()->sendSmsCancelComplaintMessage($params);
        var_dump($msg);
    }

    /**
     * 测试客户客户端
     */
    public function actionCustomerClientRegister(){
        $data = array(
            'udid' => 1234456,
            'client_id' => '1888990',
            'phone' => '12345',
        );
        QueueProcess::model()->customer_client_register($data);

    }

    /**
     * 返回又拍云下载地址
     */
    public function actionUpyun(){
        $md5_file = '714eae4e4740c8d090385b712d8b8bfdxx';
        $filesize = 21001244444;
        $type = "audio/amr4";
        $data = UpyunUpload::model()->getUrlInfo($md5_file , $filesize , $type);
        print_r($data);

    }

    public function actionPushMsg(){
        $data = array(
            'lng'=>116.466432,
            'lat'=>39.911896,
            'gps_type'=>'baidu',
            'address'=>'建外SOHO10号楼',
            'phone'=>13811111111,
            'url'=>"http://edidi.b0.upaiyun.com/90ac6e8aa2869892f6393568dbd1b1e2",
        );

        QueueProcess::model()->customer_gen_order($data);
    }


    public function actionLogin(){
        $data = array(
          'phone' => 13811121479,
          'authtoken' => md5(time()),
        );
        $flag  = QueueProcess::model()->restaurant_login($data);
        var_dump($flag);
    }

    public function actionRestaurantAdd(){
       $params = array(
           'latitude' => 56.331223,
           'longitude' => 116.1231233,
            'name' => '测试'.date("Y-m-d H:i:s") , // 名字
            'address' => '测试地址'.date("Y-m-d H:i:s")  ,// 地址
            'photos' => isset($data['photos']) ? $data['photos'] : array('http://baidu.com','http://google.cn') ,// 照片
            'contact' => isset($data['contact']) ? $data['contact'] : "联系人" ,// 联系人
            'title' => isset($data['contact_job']) ? $data['contact_job'] : 2 ,// 联系人职位
            'telephone' => isset($data['work_phone']) ? $data['work_phone'] : "固定电话" ,// 固定电话
            'city' => isset($data['city']) ? $data['city'] : 1 ,// 城市
            'mobile' => isset($data['mobile_phone']) ? $data['mobile_phone'] : "手机" ,// 手机
            'district' => isset($data['region']) ? $data['region'] : 1 ,// 行政区
            'zone' => isset($data['business_circle']) ? $data['business_circle'] : 2 ,// 商圈
            'type' => isset($data['business_type']) ? $data['business_type'] : 1 ,// 商家类型类型
            'tables' => isset($data['tables']) ? $data['tables'] : 100 ,// 桌数
            'tables_type' => isset($data['tables_type']) ? $data['tables_type'] : 2 ,// 桌数类型
            'remark' => isset($data['appendixes']) ? $data['appendixes'] : "备注" ,// 备注
            'updated' => isset($data['materials_checked_at']) ? $data['materials_checked_at'] : date("Y-m-d H:i:s") ,// 物料最后检查时间
            'cost' => isset($data['cost']) ? $data['cost'] : 330 ,// 消费
            'competition_arr' => array(
                'restaurant_info'=>array(
                    'channel_type'=>isset($data['channel_type']) ? $data['channel_type'] : "vintners",// 渠道类型，"自有" | "酒商"
                    'has_competition'=>isset($data['has_competition']) ? $data['has_competition'] : 0,// 是否有竞品物料
                    'has_competition_wiped'=>isset($data['has_competition_wiped']) ? $data['has_competition_wiped'] : 0,// 竞品物料是否已清除
                    'has_materials'=>isset($data['has_materials']) ? $data['has_materials'] : 0,// 是否已进店
                ),
                'materials_info'=> isset($data['materials']) ? $data['materials'] : array(0,0,1,0,0,1,1) ,// 物料详情
            ),

            'user_id' => 195 , //用户ID

        );

        QueueProcess::model()->restaurant_add($params);
        //print_r($params);
    }


    public function actionSubmit(){
        $params = array(
            'order_id' => '1158630',
            'order_number'=>'775877',
            'driver_id'=>'BJ90101',
        );
        QueueProcess::model()->order_submit($params);
    }

    public function actionPrologin(){
        $phone = 13811121479;
        $passCode = UserToken::model()->getSmsPassCode($phone);
        var_dump($passCode);
        exit;
    }

    public function actionUpdateSim(){
        $data = array(
            'imei' => 'A0000042CDB18F',
            'sim'=>'111',
            'sim_new'=>'111',
        );
        $flag = QueueProcess::model()->update_driver_phone_sim($data);
        var_dump($flag);
    }

    public function actionCallLog(){
        $data = array(
            'driver_id'=>'BJ9006',
            'simcard'=>'',
            'imei'=>'',
            'user'=>'BJ9004',
            'token'=>'00be4d35692743b56459a3eeaaa696ee',
            'phone'=>'18911883373',
            'type'=>'1',
            'longitude'=>'116.417',
            'latitude'=>'39.9442',
            'callTime'=>'20130109104050',
            'endTime'=>'20130109104250',
            'talkTime'=>'65',
            'status'=>0,
        );
        $flag = QueueProcess::model()->driver_calllog($data);
        var_dump($flag);
    }


    public function actionLog(){
        $data = array(
            'route'=>'xxx/33',
            'mod_code'=>'1001',
            'opt_type'=>'insert',
            'data_log'=> 123,
            'opt_user'=>'test',
        );
        $flag = OperationLog::model()->insertLog($data);
        var_dump($flag);
    }


    public function actionGetLogList(){
        $data = array(
          'mod_code'=>'1001',
          'limit'=>1,
           'start_date' =>'2013-08-23 18:05:46',
           'end_date' =>'2013-08-24 18:05:46',
        );
        $ret = OperationLog::model()->getLogList($data);
        print_r($ret);
    }


}