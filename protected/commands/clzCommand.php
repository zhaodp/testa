<?php

class clzCommand extends LoggerExtCommand
{

    /**
     * 初始化39元模板活动
     * @param $act_name
     * @param $phone(当数据库不存在这个phone时用)
     * @param $init_num
     */
    public function actionInitActivity($act_name, $phone, $init_num)
    {
        if (empty($act_name)) {
            echo '活动标题不能为空'.PHP_EOL;
            return;
        }
        if (empty($init_num) || $init_num <= 0) {
            echo '优惠劵初始化数量不能为0'.PHP_EOL;
            return;
        }
        echo '开始初始化活动....'.PHP_EOL;
        $cache = new RActivity();
        $cache->delCache($act_name, $phone, $init_num);
        $log = new Bonus39Log();
        echo '开始清理' . $act_name . '的数据...'.PHP_EOL;
        $log->deleteAll('act_name=:act_name', array(':act_name' => $act_name));
        echo '结束清理' . $act_name . '的数据...'.PHP_EOL;
        echo '活动已成功初始化,请继续测试....'.PHP_EOL;
    }

    public function actionRemoveActivity($act_name)
    {
        if (empty($act_name)) {
            echo '活动标题不能为空'.PHP_EOL;
            return;
        }
        echo $act_name.'开始关闭活动....'.PHP_EOL;
        $cache = new RActivity();
        $cache->removeOpenLock($act_name);
        echo $act_name.'结束关闭活动...'.PHP_EOL;
    }

    /**
     * 测试百度地图启动页活动
     */
    public function actionTestBind()
    {
        $params = array();
        $params['act_name'] = 'baidu_map_test';
        $params['open_id'] = 'open_id_aaaaaaaaaa';
        $params['phone'] = '13718731568';
        QueueProcess::model()->baidu_map_binding_bonus($params);
    }


}
