<?php

class BonusChannelLogService extends BaseService
{
    /**
     * Returns the static service of the specified  class.
     * @param string $className active service class name.
     * @return  the static service class
     */
    public static function service($className = __CLASS__)
    {
        return parent::service($className);
    }
    /**
     * 获取渠道被分配次数
     * @param int $area_id
     * @return int
     * @auther mengtianxue
     */
    public static function getChannelDistriCount($channel = 0)
    {
        return BonusChannelLog::model()->getChannelDistriCount($channel );
    }

    /**
     * 获取渠道被分配数量
     * @param array $log_id
     * @return array
     * @auther mengtianxue
     */
    public static function getChannelDistri($log_id)
    {
        return BonusChannelLog::model()->getChannelDistri($log_id);
    }


    /**
     * 获取城市分配人列表
     * @param int $city_id
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getCityDistri($city_id)
    {
        return BonusChannelLog::model()->getCityDistri($city_id);
    }

    /**
     * 获取分配人分配渠道列表
     * @param string $disTri_by
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelByDistriBy($disTri_by)
    {
        return BonusChannelLog::model()->getChannelByDistriBy($disTri_by);
    }
}
