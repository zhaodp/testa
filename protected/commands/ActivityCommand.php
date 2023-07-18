<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-4-15
 * Time: 下午5:05
 * auther mengtianxue
 */
Yii::import('application.models.schema.report.*');

class ActivityCommand extends CConsoleCommand
{
    /**
     * 活动统计
     * @auther mengtianxue
     * php yiic.php Activity ActivityReport --date='2013-01-02'
     */
    public function actionActivityReport($date = null)
    {
        if ($date === null) {
            $end_time = strtotime(date('Y-m-d 07:00:00', time()));
            $start_time = $end_time - 86400;
        } else {
            $start_time = strtotime($date . ' 07:00:00');
            $end_time = $start_time + 86400;
        }

        $arr = Order::model()->getActivityReport($start_time, $end_time);

        if ($arr) {
            $arr['complate_p'] = $arr['complate_count'] - $arr['complate_driver_b'] - $arr['complate_customer_b'];
            $arr['company_subsidy'] = $arr['driver_subsidy'] + $arr['customer_subsidy'];
            $arr['driver_account'] = $arr['order_account'];
            $arr['day_date'] = date('Ymd', $start_time);
            $arr['create_date'] = date('Y-m-d H:i:s');
            $add_activity = $this->addActivity($arr);
            if ($add_activity) {
                echo "添加成功\n";
            } else {
                echo "添加失败\n";
            }
        } else {
            echo "没有数据\n";
        }
    }

    /**
     * 添加活动记录  如果存在，删除后重新插入
     * @param $params
     * @return bool
     * @auther mengtianxue
     */
    public function addActivity($params)
    {
        $activity = ReportActivityReport::model()->find('day_date = :day_date and city_id = :city_id',
            array(':day_date' => $params['day_date'], 'city_id' => $params['city_id']));
        if ($activity) {
            ReportActivityReport::model()->deleteByPk($activity->id);
        }
        $activity = new ReportActivityReport();
        $activity->attributes = $params;
        $add_activity = $activity->insert();
        if ($add_activity) {
            return true;
        } else {
            return false;
        }
    }



    /**
    *  每天定时删除到期的活动消息
    *
    */
    public function actionDelActMsg(){
        $list = Yii::app()->db_readonly->createCommand()
            ->select("endtime,url,id,title")
            ->from("{{marketing_activity}}")
            ->where('endtime <= :endtime', array (
                ':endtime'=>date('Y-m-d H:i:s')))
            ->order('id asc ')
            ->queryAll();
        foreach ($list as $act) {
            echo '开始删除活动id'.$act['id'].PHP_EOL;
            CustomerMessage::model()->delActMsg($act['url']);
        }
    }

     /**
     * @author aiguoxin
     * 预约发布公告审核通过
     * php protected/yiic notice bookingPush
     * 每分钟执行
     */
    public function actionActPush(){
        echo Common::jobBegin("预约活动连接数据库开始");
        $key = 'act_push_';
        $list = Yii::app()->dbreport->createCommand()
            ->select("id,title,content,url,citys,customer_type,platform,app_ver,push_time")
            ->from("{{act_push}}")
            ->where('status=0 and push_time <= :push_time', array (
                ':push_time'=>date('Y-m-d H:i:s')))
            ->order('id asc ')
            ->queryAll();
        echo Common::jobEnd("预约活动连接数据库结束");
        echo '$list = '.count($list).PHP_EOL;
        if(!empty($list)){
            echo Common::jobBegin("预约活动推送开始");
            foreach($list as $val){
                $ret =ActPush::model()->updateByPk($val['id'], array (
                    'status'=>1));
                if($ret){
                        $citys = explode(',', $val['citys']);
                        foreach ($citys as $city) {
                            //每周一个城市只接收一条活动推送
                            if(DriverStatus::model()->single_get($key.$city) != null){
                                echo 'city='.$city.',本周已经发送过一次';
                                EdjLog::info('city='.$city.',本周已经发送过一次');
                                continue;
                            }else{
                                DriverStatus::model()->single_set($key.$city,1,24*3600*7);
                            }

                            $params=array(
                                'city'=>$city,
                                'customer_type'=>$val['customer_type'],
                                'title'=>$val['title'],
                                'content'=>$val['content'],
                                'url'=>$val['url'],
                                'version'=>$val['app_ver'],
                                'platform'=>$val['platform'],
                                );
                            $this->actionActPushForCity($params);
                        }
                       
                }
            }
            echo Common::jobEnd("预约活动推送结束");
        }
    }


    /**
    *   给单个城市用户推送push
    *
    */
    public function actionActPushForCity($params){
        $city = $params['city'];
        $customer_type = $params['customer_type'];
        $title = $params['title'];
        $content = $params['content'];
        $url = $params['url'];
        $version = $params['version'];
        $platform = $params['platform'];

        echo '开始处理活动'.$content.',城市id='.$city.PHP_EOL;
        EdjLog::info('开始处理活动'.$content.',城市id='.$city);

        $offset = 0;
        $pageSize = 5000;
        while (true) {
            //获取用户
            $sql = 'select * from t_customer_main where city_id = '.$city.' and app_ver >= "'.$version.'" order by id asc limit '.$offset.','.$pageSize;
            $customerList = Yii::app()->db_readonly->createCommand($sql)->queryAll();
            echo '开始处理活动'.$content.',城市id='.$city.',user='.count($customerList).PHP_EOL;
            if ($customerList) {
                //发送push
                foreach ($customerList as $customer) {
                    EdjLog::info('开始处理活动'.$content.',phone='.$customer['phone']);
                    //每个手机号码，每个活动只推送一次
                    if(DriverStatus::model()->single_get($customer['phone'].$url)){
                        EdjLog::info('phone='.$customer['phone'].' has noticed');
                        continue;
                    }
                    //test
                    // $customer['phone']='15101061387';
                    $isNewCustomer=CustomerOrderReport::model()->isNewCustomer($customer['phone']);
                    if(MarketingActivity::NEW_CUSTOMER == $customer_type && $isNewCustomer){//新客户
                        ClientPush::model()->pushMsgForAct($customer['phone'],$title,$content,$url,$version,$platform);
                        echo '开始处理活动title='.$title.',content='.$content.',城市id='.$city.',phone='.$customer['phone'].PHP_EOL;
                        EdjLog::info('开始处理活动title='.$title.',content='.$content.',城市id='.$city.',phone='.$customer['phone']);
                    }else if(MarketingActivity::OLD_CUSTOMER == $customer_type && !$isNewCustomer){//老客户
                        ClientPush::model()->pushMsgForAct($customer['phone'],$title,$content,$url,$version,$platform);
                        echo '开始处理活动title='.$title.',content='.$content.',城市id='.$city.',phone='.$customer['phone'].PHP_EOL;
                        EdjLog::info('开始处理活动title='.$title.',content='.$content.',城市id='.$city.',phone='.$customer['phone']);
                    }else if(MarketingActivity::UNLIMITED_CUSTOMER == $customer_type){//所有客户
                        ClientPush::model()->pushMsgForAct($customer['phone'],$title,$content,$url,$version,$platform);
                        echo '开始处理活动title='.$title.',content='.$content.',城市id='.$city.',phone='.$customer['phone'].PHP_EOL;
                        EdjLog::info('开始处理活动title='.$title.',content='.$content.',城市id='.$city.',phone='.$customer['phone']);
                    }
                    DriverStatus::model()->single_set($customer['phone'].$url,1,24*3600*7);
                }
            } else {
                echo 'ok done '.$city;
                break;
            }
            sleep(3);
            $offset += $pageSize;
        }
    }
} 
