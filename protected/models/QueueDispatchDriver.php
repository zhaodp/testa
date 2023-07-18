<?php
/**
 * 自动派单司机队列
 * @author dayuer
 *
 */
class QueueDispatchDriver extends CRedis {
	public $host='redis03n.edaijia.cn';
	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';
	private $_queue='dispatch_queue';
	
	
	// We set the gap to be 60s because single booking order 
	// will wait for 60s now. It should never be smaller than
	// the smallest polling time
	private $_gap=60;         //暂时修改为90s锁定（修改时要把下边的类成员变量一并修改）;
	const DRIVER_LOCK_GAP=60; //增加司机锁定变量

	public static function model($className=__CLASS__) {
		$model=null;
		if (isset(self::$_models[$className]))
			$model=self::$_models[$className];
		else {
			$model=self::$_models[$className]=new $className(null);
		}
		return $model;
	}

	/**
	 * 在派单司机队列中添加司机
	 * @param string $driver_id
	 */
	public function insert($driver_id) {
		$token_key='DRIVER_DISPATCH_'.$driver_id;
		
		if ($this->redis->exists($token_key)) {
			//echo $driver_id.'.'; //显示派单次数到控制台
			$timestamp=$this->redis->get($token_key);
			if ((time()-$timestamp)<$this->_gap) {
				return 0;
			}
		}
		
		$this->redis->set($token_key, time(), time()+$this->_gap);
		return 1;
	}

    /**
     * 判断司机是否为锁定状态
     * @param string $driver_id
     */
    public function isUnLock($driver_id) {
        
        $token_key='DRIVER_DISPATCH_'.$driver_id;
        if ($this->redis->exists($token_key)) {
            $timestamp=$this->redis->get($token_key);
            if ((time()-$timestamp)>$this->_gap) {
                return 1;
            }else{
                return 0;
            }
        }
        return 1;
    }

	/**
	 * 解锁已超时的司机
	 */
	public function unlock() {
		$token_key='DRIVER_DISPATCH_*';
		
		$drivers=$this->redis->keys($token_key);
		if ($drivers) {
			foreach($drivers as $driver_key) {
				$driver_id = substr($driver_key, strlen("DRIVER_DISPATCH_"));
				$timestamp=$this->redis->get($driver_key);
				if ((time()-$timestamp)>=$this->_gap) {
					$this->delete($driver_id);
				}
			}
		}
	}

	/**
	 * 从派单队列中删除司机
	 * @param string $driver_id
	 */
	public function delete($driver_id) {
		$token_key='DRIVER_DISPATCH_'.$driver_id;
		
		$ret=$this->redis->del($token_key);
		return $ret;
	}

	/**
	 * 清除队列全部司机
	 */
	public function clean() {
		$token_key='DRIVER_DISPATCH_*';
		
		$drivers=$this->redis->keys($token_key);
		foreach($drivers as $token) {
			$this->redis->del($token);
		}
		return true;
	}

	/**
	 * 返回全部队列司机名单
	 */
	public function showall() {
		$tmp=array();
		$token_key='DRIVER_DISPATCH_*';
		
		$drivers=$this->redis->keys($token_key);
		if ($drivers) {
			
			foreach($drivers as $driver_key) {
				$timestamp=$this->redis->get($driver_key);
				$tmp[]=array(
						substr($driver_key, strlen("DRIVER_DISPATCH_")),
						$timestamp
				);
			}
		}
		return $tmp;
	}
	
	/**
	 * 获取司机锁定的时间戳
	 * @param string $driver_id
	 * @return int $timestamp
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2014-01-09
	 */
	public function get($driver_id) {
		if (empty($driver_id)) {
			return false;
		}
		
		$token_key='DRIVER_DISPATCH_'.$driver_id;
		if ($this->redis->exists($token_key)) {
			$timestamp=$this->redis->get($token_key);
		} else {
			$timestamp = 0;
		}
		return $timestamp;
	}
}
