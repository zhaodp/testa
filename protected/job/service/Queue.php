<?php
/**
 * 基于redis的queue队列
 */
class Queue {
    private static $_models;

    public $queue_max_length = array(
    );

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    //确定redis
    private function select_redis($type) {
        return QueuePool::model()->get_zone($type);
    }

    private function trim($queue_name) {

        $type = str_replace("queue_", "", $queue_name);
        $max = 0;
        if (isset($this->queue_max_length[$type])) {
            $max = intval($this->queue_max_length[$type]);
        }
        if ($max>0) {
            $zone = $this->select_redis($type);
            if($zone) {
                $zone['redis']->lTrim($queue_name, 0, $max-1);
            }
            else {
                EdjLog::error("can not find zone, queue name: " . $type);
                return;
            }
        }
    }

    /**
     * 放入队列，统一队列对外暴露方法,增加类型默认放task队列，指定了就放对应的队列，同时如果不在指定类型内的，也放默认队列
     *
     * @author sunhongjing 2013-07-04
     * @param unknown_type $params
     * @param unknown_type $type
     * @return mixed
     */
    public function putin($params=null, $type){
        $type = empty($type) ? 'error' : strtolower($type);

                $base_qname = QNameManagerService::model()->get_base_qname($type);

        if(!empty($base_qname)) {
            $this->queue_name = 'queue_'.$base_qname;
        }else{
            $this->queue_name = 'queue_error';
        }

        if ($params===null) {
            return $this->get();
        } else {
            return $this->add($params);
        }
    }

    /**
     * 取一条队列数据,封装多个队列，统一调用方法
     * @author sunhongjing 2013-07-09
     * @param string $type
     * @return array
     */
    public function getit($type='default')
    {
        $base_qname = QNameManagerService::model()->get_base_qname($type);

        if(!empty($base_qname)) {
            $this->queue_name = 'queue_'.$base_qname;
        }else{
            return array();
        }

        $zone = $this->select_redis($type);
        if($zone) {
            if($zone['brpop']) {
                $json = '';
                $result = $zone['redis']->brPop($this->queue_name, $zone['brpop']);
                if(!empty($result) && isset($result[1])) {
                    $json = $result[1];
                }
            }
            else {
                $json = $zone['redis']->rPop($this->queue_name);
            }
        }
        else {
            EdjLog::error("can not find zone, queue name: " . $type);
            return array();
        }

        return json_decode($json, true);
    }

    /**
     * 返回队列接收的类型列表
     * @author sunhongjing 2013-07-04
     * @return array
     */
    public function getQueueTypeList()
    {
        $list = QNameManager::model()->findall();
        if($list) {
            return $list;
        }

        EdjLog::error("Error: get queue list from database");
        return array();
    }

    /**
     * 设置或者读取位置队列
     * @param array $params
     * @return mixed
     */
    public function position($params=null) {
        $this->queue_name='queue_position';

        if ($params===null) {
            return $this->get();
        } else {
            return $this->add($params);
        }
    }

    /**
     * 心跳队列
     * @param string $params
     * @return mixed
     */
    public function heartbeat($params=null) {
        $this->queue_name='queue_heartbeat';

        if ($params===null) {
            return $this->get();
        } else {
            return $this->add($params);
        }
    }

    /**
     * 最高优先级队列
     * @param string $params
     * @return mixed
     */
    public function task($params=null) {
        $this->queue_name='queue_task';

        if ($params===null) {
            return $this->get();
        } else {
            return $this->add($params);
        }
    }

    /**
     * 保存日志到数据库
     * @param string $params
     * @return mixed
     */
    public function dumplog($params=null) {
        $this->queue_name='queue_dumplog';

        if ($params===null) {
            return $this->get();
        } else {
            return $this->add($params);
        }
    }

    /**
     * 返回各个队列中的任务总数
     */
    public function length() {

        $queue = $this->getQueueTypeList();

        $queue_length=array();
        $reg = "/P[0-9]+$/";
        foreach($queue as $item) {
            $base_qname = $item->base_qname;
            $zone = $this->select_redis($base_qname);
            $key = 'queue_'.$base_qname;
            if($zone) {
                $len = $zone['redis']->lLen($key);
                if(isset($item->max) && $len > $item->max) {
                    $key = '!'.$key;
                }

                $pkey = '';
                if(preg_match($reg, $zone['name'])) {
                    $pkey = $key.'@'.$zone['name'];
                }
                else {
                    $pkey = $key.'@'.$zone['name']."_P".$item->level;
                }

                $queue_length[$pkey] = $len;
            }
            else {
                EdjLog::error("can not find zone, queue name: " . $key);
            }
        }

        return $queue_length;
    }

    private function get() {
        $type = str_replace("queue_", "", $this->queue_name);
        $zone = $this->select_redis($type);
        if($zone) {
            if($zone['brpop']) {
                $json = '';
                $result = $zone['redis']->brPop($this->queue_name, $zone['brpop']);
                if(!empty($result) && isset($result[1])) {
                    $json = $result[1];
                }
            }
            else {
                $json = $zone['redis']->rPop($this->queue_name);
            }
        }
        else {
            EdjLog::error("can not find zone, queue name: " . $key);
            return array();
        }
        return json_decode($json, true);
    }

    private function add($params) {
        $json=json_encode($params);
        $type = str_replace("queue_", "", $this->queue_name);
        $zone = $this->select_redis($type);
        $retrun = 0;
        if($zone) {
            $return = $zone['redis']->lPush($this->queue_name, $json);
        }
        else {
            EdjLog::error("can not find zone, queue name: " . $type);
        }

        return $return;
    }

    public function processTask($task) {
        if(!isset($task['method'], $task['params'])) {
            $task_content = json_encode($task);
            EdjLog::error("can not run task due to no 'method' or 'params' specified, task is $task_content");
            return;
        }

        $method=$task['method'];
        $params=$task['params'];
        $class = isset($task['class']) ? $task['class'] : "QueueProcess";
        EdjLog::info("REDIS_QUEUE_OUT CLASS:$class METHOD:$method PARAMS:".json_encode($params));

        try {
            //throw new Exception("Value must be 1 or below");
            $queue_process=new $class();
            // check this method is exist, if not throw ReflectionException
            new ReflectionMethod($queue_process, $method);
            call_user_func_array(array($queue_process, $method), array($params));
        } catch(Exception $e) {
            $errmsg = $e->getMessage();
            EdjLog::error("execption queue_run method:$method err: $errmsg");
        }
    }
}
