<?php
/**
 *
 * 封装财务对于缓存的一些列操作
 *
 * Created by PhpStorm.
 * User: tuan
 * Date: 1/21/15
 * Time: 23:48
 */

final class FinanceCacheUtils {

    /** 缓存的前缀 */
    const CACHE_KEY_PREFIX = 'Finance_Cache_';
    
    const CACHE_KEY_SHENZHEN_PREFIX = 'SHENZHEN_BLACK_';
    
    public static function addBlack($phone){
        $key = self::CACHE_KEY_SHENZHEN_PREFIX.trim($phone);
        RPay::model()->addFinanceLock($key , 1, 0);
    }
    
    public static function getBlack($phone){
        $key = self::CACHE_KEY_SHENZHEN_PREFIX.trim($phone);
        return RPay::model()->getFinanceLock($key);
    }

    /**
     *
     * 设置 锁, 该方法会把传入的 key 前面加上 财务默认的前缀
     *
     * @param $key
     * @param $value
     * @param $expired
     * @return 成功返回 true, 失败返回 false
     */
    public static function setLock($key, $value, $expired){
        try{
            $redisKey = self::keyGen($key);
            $ret = RPay::model()->addFinanceLock($redisKey, $value, $expired);
            return $ret;
        }catch (Exception $e){
            EdjLog::error('set finance redis --- '.$e->getMessage());
            return false;
        }
    }

    /**
     * 获得锁
     *
     * @param $key
     * @param $defaultValue | 如果异常,或者 redis 没有数据,会返回默认值
     */
    public static function getLock($key, $defaultValue = false){
        try{
            $redisKey = self::keyGen($key);
            $ret      = RPay::model()->getFinanceLock($redisKey);
            if($ret){
                return $ret;
            }else{
                return $defaultValue;
            }
        }catch (Exception $e){
            EdjLog::error('get finance redis --- '.$e->getMessage());
            return $defaultValue;
        }
    }

    /**
     * 缓存 key 生成
     *
     * @param $key
     * @return string
     */
    private static function keyGen($key){
        return self::CACHE_KEY_PREFIX.$key;
    }
}