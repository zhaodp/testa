<?php


class BonusChannelService extends BaseService
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

    public function search()
    {
        return BonusChannel::model()->search();
    }

    /**
     * 获取城市
     * @return array
     * @auther mengtianxue
     */
    public function getArea()
    {
        return BonusChannel::model()->getArea();
    }

    /**
     * 获取渠道
     * @param int $area_id
     * @return array
     * @auther mengtianxue
     */
    public function getChannel($area_id = 0)
    {
        return BonusChannel::model()->getChannel($area_id);
    }


    /**
     * 获取渠道
     * @param int $area_id
     * @return array
     * @auther mengtianxue
     */
    public function getChannelInfoList($area_id = 0, $channel = '', $arr = array())
    {
        return BonusChannel::model()->getChannelInfoList($area_id, $channel, $arr);
    }

    /**
     * 获取渠道数量
     * @param int $area_id
     * @return int
     * @auther mengtianxue
     */
    public static function getChannelCount($area_id = 0)
    {
        return BonusChannel::model()->getChannelCount($area_id);
    }

    /**
     * 获取渠道数量
     * @param array $area_id
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelCountByIds($area_id)
    {
        return BonusChannel::model()->getChannelCountByIds($area_id);
    }

    /**
     * 获取详情
     * @param int $id
     * @return array
     * @auther mengtianxue
     */
    public function getInfoById($id = 0)
    {
        return BonusChannel::model()->getInfoById($id);
    }

    /**
     * 获取渠道名称,被分配次数
     * @param int $id
     * @return string
     * @auther mengtianxue
     */
    public static function getChannelNameById($id)
    {
        return BonusChannel::model()->getChannelNameById($id);
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function actionGetInfo($id)
    {
        $model = ChannelBonus::model()->findByPk($id);
        return $model;
    }


    /**
     * 统计渠道数量
     * @param int $area_id
     * @return int
     * @auther mengtianxue
     */
    public static function getChannelCountAll($city_id = 0, $dateStart = '', $dateEnd = '')
    {
        return BonusChannel::model()->getChannelCountAll($city_id, $dateStart, $dateEnd);
    }


    /**
     * 获取渠道数量
     * @param int $channel
     * @return array
     * @auther mengtianxue
     */
    public static function getChannelIdByName($channel)
    {
        return BonusChannel::model()->getChannelIdByName($channel);
    }


    /**
     * 分配实体卷
     * @param $bonus_sns
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function disUpdateDistriBy($bonus)
    {
        return BonusChannel::model()->disUpdateDistriBy($bonus);
    }


    /**
     * 获取渠道列表
     * @param int $channel
     * @return array
     * @auther mengtianxue
     */
    public static function getChannelListIdByName($channel, $city)
    {
        return BonusChannel::model()->getChannelListIdByName($channel, $city);
    }

    /**
     * 获取渠道基本信息
     * @param int $id
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelInfo($id)
    {
        return BonusChannel::model()->getChannelInfo($id);
    }
}
