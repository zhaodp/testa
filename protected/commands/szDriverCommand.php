<?php
/**
 * 处理深圳司机 加入和删除 都不要推送
 * User: clz
 * Date: 15-04-15
 */

class szDriverCommand extends CConsoleCommand
{

    /**
     * 将制定城市的司机的phone和ext_phone加入黑名单
     * @param int $city_id=6
     */
    public function actionPutDriverToBlackList($city_id = 0){
        $job_title = '将司机放入黑名单';
        echo Common::jobBegin($job_title);
        if($city_id == 0){
            EdjLog::info('请输入要屏蔽司机的城市');
            echo '请输入要屏蔽司机的城市'.PHP_EOL;
            return;
        }

        $driver_list = Driver::model()->findAll('city_id=:city_id', array(':city_id'=>$city_id));
        if(!$driver_list){
            EdjLog::info('city_id='.$city_id.'的城市不存在司机');
            echo 'city_id='.$city_id.'的城市不存在司机'.PHP_EOL;
            return;
        }
        foreach($driver_list as $driver){
            $data = array();
            $data['phone'] = $driver['phone'];
            $data['expire_time'] = '2030-01-01 00:00:00';
            $data['user_id'] = 758;
            $data['created'] = date('Y-m-d H:i:s', time());
            $data['status'] = 1;//已推送
            $data['isdel'] = 0;
            $data['remarks'] = 'city_id='.$city_id;

            $model = new Customer();
            $model->insertDriverToBlackCustomer($data);
            EdjLog::info('已经将phone='.$driver['phone'].'的司机放入黑名单');
            echo '已经将phone='.$driver['phone'].'的司机放入黑名单'.PHP_EOL;

            if(!empty($driver['ext_phone'])){
                $data['phone'] = $driver['ext_phone'];
                $model->insertDriverToBlackCustomer($data);
                EdjLog::info('已经将ext_phone='.$driver['ext_phone'].'的司机放入黑名单');
                echo '已经将ext_phone='.$driver['ext_phone'].'的司机放入黑名单'.PHP_EOL;
            }
        }
        echo Common::jobEnd($job_title);
    }

    /**
     * 将指定城市的司机移除黑名单
     * @param int $city_id
     */
    public function actionClearDriverFromBlackList($city_id = 0)
    {

        $job_title = '删除黑名单司机';
        echo Common::jobBegin($job_title);
        if($city_id == 0){
            EdjLog::info('请输入要屏蔽司机的城市');
            return;
        }
        $customer = new Customer();
        $customer->delCityDriver($city_id);
        echo Common::jobEnd($job_title);
    }

}
