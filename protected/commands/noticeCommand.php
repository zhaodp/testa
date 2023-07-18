<?php
/**
 * 公告 cron 执行
 * User: zhanglimin
 * Date: 13-8-29
 * Time: 下午3:21
 */
class NoticeCommand extends CConsoleCommand {


    /**
     * @author zhanglimin
     * 预约发布公告审核通过
     * php protected/yiic notice bookingPush
     * 每分钟执行
     */
    public function actionBookingPush(){
        echo Common::jobBegin("预约发布公告连接数据库开始");
        $list = Yii::app()->db_readonly->createCommand()
            ->select("id,city_ids,content,audio_url,type,category,title,booking_push_datetime,audio_second")
            ->from("{{new_notice}}")
            ->where('is_delete = 0 and is_pass = 1 and is_check = 0 and booking_push_flag = 1 and deadline > :deadline and booking_push_datetime <= :booking_push_datetime', array (
                ':deadline'=>date('Y-m-d H:i:s'),':booking_push_datetime'=>date('Y-m-d H:i:s')))
            ->order('id asc ')
            ->queryAll();
        echo Common::jobEnd("预约发布公告连接数据库结束");
        if(!empty($list)){
            echo Common::jobBegin("预约发布公告审核通过开始");
            foreach($list as $val){
                $ret =NewNotice::model()->updateByPk($val['id'], array (
                    'is_check'=>1));
                //插入缓存 redis
                $paramsRedis=array();
                $paramsRedis['city_ids']=$val['city_ids'];
                $paramsRedis['notice_id']=$val['id'];
                $redisTrue=NoticeStatus::model()->add($paramsRedis);
                if($ret && $redisTrue){
                    $city_ids=explode(',', $val['city_ids']);
                    if($val['type']==1){
                        foreach ($city_ids as $city_id) {
                            $data = array(
                                'notice_id'=>$val['id'],
                                'city_id'=>$city_id,
                                'content'=>$val['content'],
                                'url'=>$val['audio_url'],

                                'category'=>NewNotice::$categorys[$val['category']],
                                'title'=>trim($val['title']),
                                'created'=>date("m-d H:i",strtotime($val['booking_push_datetime'])), //预约发布时间 格式 月-日 时:分
                                'audio_time'=>$val['audio_second'], //语言时长
                            );
                            echo Common::jobBegin($city_id."----预约发布公告推送信息开始");
                            NewNotice::model()->pushAudioMsg($data);
                            echo Common::jobEnd($city_id."----预约发布公告连推送结束");                        
                        }
                        
                    }
                    //添加未推送公告通知
                    foreach ($city_ids as $city_id) {
                        //add by aiguoxin push unread notice
                        EdjLog::info('push message to driver..............');
                        NewNotice::model()->pushCommonMsg($city_id);
                    }
                    echo "booking_push pass --  notice_id:{$val['id']} type:{$val['type']}\r\n";
                }
            }
            echo Common::jobEnd("预约发布公告审核通过结束");
        }

    }


    /**
     * @author zhanglimin
     * 移除一周前redis过期公告与司机己读id
     * php protected/yiic notice clearDeadlineCache
     * 每周一执行一次
     */
    public function actionClearDeadlineCache(){
        $list = Yii::app()->db_readonly->createCommand()
            ->select("id,city_ids")
            ->from("{{new_notice}}")
            ->where('is_check=1 and deadline < :deadline and create_time < :create_time', array (
                ':deadline'=>date('Y-m-d H:i:s'),':create_time'=>date('Y-m-d H:i:s',strtotime("-1 week",time()))))
            ->order('id asc ')
            ->queryAll();
        if(!empty($list)){
            echo Common::jobBegin("清除过期公告缓存开始");
            foreach($list as $val){
                if(!empty($val['city_ids'])){
                    $arr = array(
                        'notice_id' =>  trim($val['id']),
                        'city_ids' =>  trim($val['city_ids']),
                    );
                    echo '开始清除notice='.$val['id'].PHP_EOL;
                    //改用队列
                    $task=array(
                        'method'=>'remove_expire_notice',
                        'params'=>$arr,
                    );
                    Queue::model()->putin($task,'message');
                    sleep(1);//减缓如队列速度，防止堵塞报警
                    echo "clear cache --  notice_id:{$val['id']} city_ids:{$val['city_ids']}\r\n";
                    sleep(1);//休眠1s，防止队列堵塞报警
                }
            }
            echo Common::jobEnd("清除过期公告缓存结束");
        }
    }
}