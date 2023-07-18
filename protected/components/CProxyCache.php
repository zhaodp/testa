<?php

// 手动加载这个文件，为了导入CMemCacheServerConfiguration类定义
// 或许存在更优雅的做法——曾坤 2015/4/22
include(YII_PATH.'/caching/CMemCache.php');

/*
 * 写这个类的目的是要将cache02n上所有用到memcache的地方都替换成
 * Redis。观察一段时间（比如一周）访问memcache的流量为0的时候，
 * 就可以把cache02n上的memcache停掉了。同时，将proxy_mode设置为
 * true，就可以在不改变现有代码的情况下，将原有对memcache的访问
 * 全部切换到RedisHAProxy上——曾坤 2015/4/22
 */
class CProxyCache extends CCache
{
    private $memcache = null;
    private $memcache_servers = array();
    private $redis = null;
    public $useMemcached=false;

    // 不在proxy模式下的时候，CProxyCache可以当做RedisHAProxy
    public $proxy_mode = false;

    // 推迟加载memcache，直到确实需要和memcache交互的时候
    private $memcache_initialized = false;

    public function init()
    {
        parent::init();

        $this->initRedis();
    }

    private function initMemCache()
    {
        $servers=$this->getServers();
        $cache=$this->getMemCache();
        if(count($servers))
        {
            foreach($servers as $server)
            {
                if($this->useMemcached)
                    $cache->addServer($server->host,$server->port,$server->weight);
                else
                    $cache->addServer($server->host,$server->port,$server->persistent,$server->weight,$server->timeout,$server->retryInterval,$server->status);
            }
        }
        else
            $cache->addServer('localhost',11211);

        $this->memcache_initialized = true;
    }

    private function initRedis()
    {
        $this->redis = RedisHAProxy::model();
    }

    public function getMemCache()
    {
        if($this->memcache!==null)
            return $this->memcache;
        else
        {
            $extension=$this->useMemcached ? 'memcached' : 'memcache';
            if(!extension_loaded($extension))
                throw new CException(Yii::t('yii',"CMemCache requires PHP {extension} extension to be loaded.",
                    array('{extension}'=>$extension)));
            return $this->memcache=$this->useMemcached ? new Memcached : new Memcache;
        }
    }

    public function getServers()
    {
        return $this->memcache_servers;
    }

    public function setServers($config)
    {
        foreach($config as $c)
            $this->memcache_servers[]=new CMemCacheServerConfiguration($c);
    }

    protected function getValue($key)
    {
        $value = $this->redis->get($key);
        if (!empty($value)) {
            return $value;
        }

        if ($this->proxy_mode) {
            if (!$this->memcache_initialized) {
                $this->initMemCache();
            }

            $value = $this->memcache->get($key);
            if (!empty($value)) {
                return $value;
            }
        }

        return false;
    }

    protected function getValues($keys)
    {
        throw new CException('this function should not be called');
    }

    protected function setValue($key,$value,$expire)
    {
        return $this->redis->set($key,$value,$expire);
    }

    protected function addValue($key,$value,$expire)
    {
        throw new CException('this function should not be called');
    }

    protected function deleteValue($key)
    {
        if ($keys_deleted = $this->redis->del($key)) {
            return $keys_deleted;
        } elseif ($this->proxy_mode) {
            if (!$this->memcache_initialized) {
                $this->initMemCache();
            }
            return $this->memcache->delete($key, 0);
        } else {
            return false;
        }
    }

    protected function flushValues()
    {
        throw new CException('this function should not be called');
    }
}

