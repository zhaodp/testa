<?php

class VisitFilter extends CFilterWidget {
    public $visitTotalNum = 1000;

    public $visitCycle = 'd';

    public $calculateNum = false;

    private $_key = null;

    protected $redis = null;

    public $redisHost = 'redis01n.edaijia.cn';

    public $redisPort = 6379;

    public $redisPass ='k74FkBwb7252FsbNk2M7';

    public $redisDb = 0;


    public function init() {
        $this->initRedis();
        $this->visit();
    }

    /**
     * 初始化redis
     */
    public function initRedis() {
        $this->redis = new Redis();
        $this->redis->pconnect($this->redisHost, $this->redisPort);
        $this->redis->auth($this->redisPass);
        $this->redis->select($this->redisDb);
    }

    /**
     * 访问处理
     */
    protected function visit() {
        $key = $this->getKey();
        if (!is_null($key)) {
            if ($this->getInCycle()) {
                $this->calculateNum();
            }
            else $this->resetVistiNum();

            $timestamp_key = $this->getTimestampKey();
            $this->redis->set($timestamp_key, time());
        }
    }

    /**
     * 计算访问
     */
    protected function calculateNum() {
        $key = $this->getKey();
        if (!$this->calculateNum) {
            $reject_key = $this->getRejectKey();
            $value = intval($this->redis->get($reject_key));
            if ($value!=0) {
                $this->stopAction = true;
                return;
            }
        }
        $num = $this->redis->incr($key);
        if ($num >= $this->visitTotalNum) {
            $this->stopAction = true;
            if (!$this->calculateNum) {
                $this->redis->set($reject_key, 1);
                $this->redis->expire($reject_key, 3600);
            }
        }
    }

    /**
     * @return bool
     * 获得是否在周期内
     */
    protected function getInCycle() {
        $key = $this->getKey();
        if (is_null($key)) return true;

        $timestamp_key = $this->getTimestampKey();
        $timestamp = intval($this->redis->get($timestamp_key));
        switch($this->visitCycle) {
            case 'y':
                return date('Y') == date('Y', $timestamp);
                break;
            case 'm':
                return date('m') == date('m', $timestamp);
                break;
            case 'i':
                return date('H') == date('H', $timestamp);
                break;
            case 'i':
                return date('i') == date('i', $timestamp);
                break;
            default :
                return date('d') == date('d', $timestamp);
        }
    }

    protected function resetVistiNum() {
        $this->redis->set($this->_key, 0);
    }

    protected function getTimestampKey() {
        return $this->_key.'timstamp';
    }

    protected function getRejectKey() {
        return $this->_key.'reject';
    }

    protected function getKey() {
        if (!is_null($this->_key)) return $this->_key;
        else {
            $current_user = Yii::app()->user->id;
            if (is_null($current_user)) return null;

            $key = '';

            $controller=$this->getController();
            $key.=$controller->getUniqueId().'/';

            if ($controller->getAction() === null) return null;

            $action = $controller->getAction();
            $key .=$action->getId();

            $this->_key = $current_user.$key;
            return $this->_key;
        }
    }

    public function filter($filterChain)
    {
        $params = Yii::app()->params;

        if (isset($params['useVisitFiler']) &&  $params['useVisitFiler']==true) {
            $this->init();
            if(!$this->stopAction)
            {
                $filterChain->run();
                $this->run();
            }
            else {
                throw new CHttpException(403,"Request rate out");
            }
        }
    }

}

