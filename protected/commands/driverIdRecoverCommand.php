<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-7-5
 * Time: 下午1:18
 * To change this template use File | Settings | File Templates.
 */

class DriverIdRecoverCommand extends CConsoleCommand {
    /**
     * 被选中账号回收JOB，每天上班前运行一次，暂定早八点
     */
    public function actionDriverIdRecover() {
        if (date('H', time()) == 8) {
            $address = new DriverIdPool();
            $count = $address->getCountDriverId(0, DriverIdPool::STATUS_TMP_USE);
            $page_size = 500;
            $page_num = ceil($count/$page_size);
            for($i=1; $i<=$page_num; $i++) {
                $start = ($i-1)*$page_size;
                $status = DriverIdPool::STATUS_TMP_USE;
                $sql = "SELECT id, driver_id FROM t_driver_id_pool WHERE status={$status} LIMIT {$start}, {$page_size}";
                $command = Yii::app()->db->createCommand($sql);
                $data = $command->queryAll();
                if (is_array($data) && count($data)) {
                    foreach ($data as $v) {
                        if ($address->checkDriverIdStatusAndChange($v['driver_id'])) {
                            echo $v['driver_id']. "++ used"."\n";
                        } else {
                            if ($address->recoverDriverId($v['id'])) {
                                echo $v['driver_id']."++ recover"."\n";
                            } else {
                                echo $v['driver_id']."++ recover_error"."\n";
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 上线初次根据t_driver表中按城市获得一个可用最小工号，
     * 仅上线前运行一次
     */
    public function actionCreateDriverId() {
        echo Common::jobBegin();
        $city_list = Dict::items('city');
        unset($city_list[0]);
        $city_id_list = array_keys($city_list);
        $driver_model = new Driver();
        foreach ($city_id_list as $city_id) {
            $_driver_id = $driver_model->getNewDriverId($city_id);
            if ($_driver_id) {
                $driver_id_model = new DriverIdPool();
                $driver_id_model->driver_id = $_driver_id;
                $driver_id_model->city_id = $city_id;
                $driver_id_model->created = date('Y-m-d H:i:s', time());
                $driver_id_model->status = DriverIdPool::STATUS_USABLE;
                echo $city_list[$city_id]. '++'.intval($driver_id_model->save())."\n";
            }
        }
        echo Common::jobEnd();
    }

    public function actionCheckDriverIdStatus() {
        echo Common::jobBegin();
        $address = new DriverIdPool();
        $count = $address->getCountDriverId(0, DriverIdPool::STATUS_USABLE);
        $page_size = 500;
        $page_num = ceil($count/$page_size);
        for($i=1; $i<=$page_num; $i++) {
            $start = ($i-1)*$page_size;
            $status = DriverIdPool::STATUS_USABLE;
            $sql = "SELECT id, driver_id FROM t_driver_id_pool WHERE status={$status} LIMIT {$start}, {$page_size}";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $data = $command->queryAll();
            if (is_array($data) && count($data)) {
                foreach ($data as $v) {
                    if (Driver::getProfile($v['driver_id'])) {
                        $driver_model = DriverIdPool::model()->findAll("driver_id='{$v['driver_id']}'");
                        if ($driver_model) {
                            foreach ($driver_model as $_model) {
                                $_model->status = DriverIdPool::STATUS_USED;
                                echo $v['driver_id'].'--'.intval($_model->save())."\n";
                            }
                        }
                    }
                }
            }
        }
        echo Common::jobEnd();
    }
}
