<?php

class BonusLibraryService extends BaseService
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return  the static model class
     */
    public static function service($className = __CLASS__)
    {
        return parent::service($className);
    }


    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($pageSize = NULL)
    {
        return BonusLibrary::model()->search($pageSize);
    }


    public function searchDistri($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '')
    {
        return BonusLibrary::model()->searchDistri($city_id, $channel, $dateStart, $dateEnd);
    }


    public function searchBonusAll($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '')
    {
        return BonusLibrary::model()->searchBonusAll($city_id, $channel, $dateStart, $dateEnd);
    }


    /**统计已分配实体卷数量
     * @param int $city_id
     * @param int $channel
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function searchBonusDistriedCount($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '')
    {
        return BonusLibrary::model()->searchBonusDistriedCount($city_id, $channel, $dateStart, $dateEnd);
    }


    /**统计未分配(库存)实体卷数量列表
     * @param int $city_id
     * @param int $channel
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function searchBonusList($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '', $Column
        , $arr = array())
    {
        return BonusLibrary::model()->searchBonusList($city_id, $channel, $dateStart, $dateEnd, $Column, $arr);
    }

    /**统计已使用实体卷数量
     * @param int $city_id
     * @param int $channel
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function searchBonusUsedCount($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '')
    {
        return BonusLibrary::model()->searchBonusUsedCount($city_id, $channel, $dateStart, $dateEnd);
    }

    public function searchChannelBonus($city_id = 0, $channel = '', $disTri_by = '', $dateStart, $dateEnd)
    {
        return BonusLibrary::model()->searchChannelBonus($city_id, $channel, $disTri_by, $dateStart, $dateEnd);
    }


    public function searchBonusDisCount($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '', $arr_bonus_id = array())
    {
        return BonusLibrary::model()->searchBonusDisCount($city_id, $channel, $dateStart, $dateEnd, $arr_bonus_id);
    }


    /**统计未分配(库存)实体卷数量
     * @param int $city_id
     * @param int $channel
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     */
    public function searchBonusCount($city_id = 0, $channel = 0, $dateStart = '', $dateEnd = '', $Column, $arr = array())
    {
        return BonusLibrary::model()->searchBonusCount($city_id, $channel, $dateStart, $dateEnd, $Column, $arr);
    }

    public function searchBonusDistried($city_id = 0, $channel = 0, $type = 0, $arr, $arr_bonus_id = array(), $arr_channel_id = array())
    {
        return BonusLibrary::model()->searchBonusDistried($city_id, $channel, $type, $arr, $arr_bonus_id, $arr_channel_id);
    }


    public function searchBonusDistring($city_id = 0, $channel = 0, $type = 0, $arr)
    {
        return BonusLibrary::model()->searchBonusDistring($city_id, $channel, $type, $arr);
    }


    public function searchSelectBonusCount($start, $end, $city_id)
    {
        return BonusLibrary::model()->searchSelectBonusCount($start, $end, $city_id);
    }


    public function selectCityList()
    {
        return BonusLibrary::model()->selectCityList();
    }


    /**
     * 检查优惠码是否可用
     * @param $bonus_sn
     * @param int $password
     * @param null $status 为null查询全部，0为未绑定的优惠券，1为已经绑定的优惠券
     * @return int
     * author mengtianxue
     */
    public function checkBonusUse($bonus_sn, $password = 0, $status = null)
    {
        return BonusLibrary::model()->checkBonusUse($bonus_sn, $password, $status);
    }

    /**
     * 修改优惠码当前状态
     * 注意：如果是固定码，优惠码状态不变
     * @param $bonus_sn
     * @param $status 0.未绑定 1.绑定
     * @param int $end_date
     * @return bool
     * author mengtianxue
     */
    public function updateStatus($bonus_sn, $status, $end_date = 0)
    {
        return BonusLibrary::model()->updateStatus($bonus_sn, $status, $end_date);
    }

    /**
     * 优惠券绑定
     * @param $bonus_sn
     * @param $phone
     * @param $password
     * @return bool|void
     * 0：不存在，1：成功，2：还未生效，不能绑定 3：优惠码失效
     * author mengtianxue
     */

    public function BonusBinding($bonus_sn, $phone, $password = 0, $channel = 0, $app_ver = 1, $send_sms = 1, $repeat_bind = 0)
    {
        return BonusLibrary::model()->BonusBinding($bonus_sn, $phone, $password, $channel, $app_ver, $send_sms, $repeat_bind);
    }


    /**
     * 商家优惠券绑定
     * @param $bonus_sn
     * @param $phone
     * @return bool
     * author mengtianxue
     */
    public function merchantsBind($bonus_sn, $phone)
    {
        return BonusLibrary::model()->merchantsBind($bonus_sn, $phone);
    }


    /**
     * 优惠券绑定
     * @param $bonusCode
     * @param $bonus_sn
     * @param $phone
     * @param $is_send 默认是1 发送短信
     * @return array
     * author mengtianxue
     */
    public function Binding($bonusCode, $bonus_sn, $phone, $is_send = 1, $channel = 0)
    {
        return BonusLibrary::model()->Binding($bonusCode, $bonus_sn, $phone, $is_send, $channel);
    }

    /**
     * 获取一张要使用优惠券
     * @param $phone
     * @param $status
     * @return array|bool
     * @auther mengtianxue
     */
    public function getBonus_sn($phone, $status, $bonus_use_limit = 0, $app_ver = 0)
    {
        return BonusLibrary::model()->getBonus_sn($phone, $status, $bonus_use_limit, $app_ver);
    }


    /**
     * 优惠券占用
     * @param $phone 手机号
     * @param $status 呼叫类型
     * @param $order_id 订单id
     * @param $is_sms 是否发送短信
     * @return bool
     * author mengtianxue
     */
    public function BonusOccupancy($phone, $order_id, $status, $is_sms = 0, $bonus_use_limit = 0, $app_ver = 0, $driver_phone = 0)
    {
        return BonusLibrary::model()->BonusOccupancy($phone, $order_id, $status, $is_sms, $bonus_use_limit, $app_ver, $driver_phone);
    }


    public function getBonusName($id)
    {
        return BonusLibrary::model()->getBonusName($id);
    }


    /**
     * 优惠券占用
     * @param $phone 手机号
     * @param $bonus_sn 呼叫类型
     * @param $order_id 订单id  传订单号是占用，传0取消占用
     * @param $is_sms 是否发送短信
     * @return bool
     * author mengtianxue
     */
    public function BonusOccupancyBySn($phone, $bonus_sn, $order_id, $is_sms = 0, $driver_phone = 0)
    {
        return BonusLibrary::model()->BonusOccupancyBySn($phone, $bonus_sn, $order_id, $is_sms, $driver_phone);
    }

    /**
     * 优惠券的使用
     * @param $phone
     * @param $order_id
     * @param $money
     * @param $type 1为使用，2为取消使用
     * @return bool
     * author mengtianxue
     */
    public function BonusUsed($phone, $order_id, $money = 0, $type = 1)
    {
        return BonusLibrary::model()->BonusUsed($phone, $order_id, $money, $type);
    }

    /**
     * 商家客户取消当前订单时取消优惠券绑定
     * @param $bonus_sn
     * @param $phone
     * @param $order_id
     * @return bool
     * author mengtianxue
     */
    public function cancelBonus($bonus_sn, $phone, $order_id = 0)
    {
        return BonusLibrary::model()->cancelBonus($bonus_sn, $phone, $order_id);
    }

    /**
     * 固定优惠码
     * @param array $fixedArr
     * @return bool
     * @author daiyihui
     */
    public function doFixedCouponInsert($fixedArr = array())
    {
        return BonusLibrary::model()->doFixedCouponInsert($fixedArr);
    }

    /**
     * 获取最大的number   由于number是字符串类型，用复制方法不行，只能拼接查询
     * @param $prefix
     * @param $len
     * @return int
     * @auther mengtianxue
     */
    public function getMaxNumber($prefix, $len)
    {
        return BonusLibrary::model()->getMaxNumber($prefix, $len);
    }

    /**
     * 批量生成区域码
     * @param array $areaArr
     * @param array $codeRules
     * @return bool
     * @author daiyihui
     */
    public function doAreaCouponInsert($areaArr = array(), $codeRules = array())
    {
        return BonusLibrary::model()->doAreaCouponInsert($areaArr, $codeRules);
    }

    /**
     * 获取优惠码
     * @param array $rules
     * @param string $areaId
     * @return bool|string
     * @auther mengtianxue
     */
    public function getCouponNum($rules = array(), $areaId = '')
    {
        return BonusLibrary::model()->getCouponNum($rules, $areaId);
    }

    /**
     * 随机生成一个优惠码
     * @param $params
     * @return int
     * @auther mengtianxue
     */
    public function randBonusCode($params)
    {
        return BonusLibrary::model()->randBonusCode($params);
    }

    /**
     * 检测该优惠码是否存在
     * @param $bonusCode
     * @return bool
     */
    public function checkIsBonus($bonusCode)
    {
        return BonusLibrary::model()->checkIsBonus($bonusCode);
    }

    //检测指定优惠劵是否含有优惠码
    public function checkBonus($bounsId, $bonusCode)
    {
        return BonusLibrary::model()->checkBonus($bounsId, $bonusCode);
    }


    /**
     *
     * @param $bonus_sn
     * @return mixed
     * author mengtianxue
     */
    public function getBonusByBonus_sn($bonus_sn, $sn_type = 2)
    {
        return BonusLibrary::model()->getBonusByBonus_sn($bonus_sn, $sn_type);
    }

    /**
     * 获取优惠码信息
     * @param $bonus_sn
     * @return mixed
     * author mengtianxue
     */
    public function getBonusInfo($bonus_sn)
    {
        return BonusLibrary::model()->getBonusInfo($bonus_sn);
    }

    /**
     * 获取最大的区域码id号
     * @return mixed
     * author mengtianxue
     */
    public function getMaxAreaId()
    {
        return BonusCode::model()->getMaxAreaID();
    }


    /**
     * 添加优惠券
     * @param $driver_id
     * @return bool
     * author mengtianxue
     */
    public function addBonusLibrary($driver_id)
    {
        return BonusLibrary::model()->addBonusLibrary($driver_id);
    }


    /**
     * bonusLibrary 列表展示
     * @param $data
     * @auther mengtianxue
     */
    public function ownerShow($data)
    {
        return BonusLibrary::model()->ownerShow($data);
    }

    /**
     * 获取渠道分配和使用统计
     * @param $owner
     * @param int $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusByOwner($owner = '', $bonus_id = 0, $fileName = '')
    {
        return BonusLibrary::model()->getBonusByOwner($owner, $bonus_id, $fileName);
    }

    /**
     * 根据渠道和规则，绑定一张优惠券
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getOneBonus($bonus_id)
    {
        return BonusLibrary::model()->getOneBonus($bonus_id);
    }


    public function channelBind($channel, $money, $phone)
    {
        return BonusLibrary::model()->channelBind($channel, $money, $phone);
    }

    /**
     * 市场活动优惠券绑定
     * @param $phone
     * @param array $bonus_id
     * @param int $is_sms
     * @return array
     * @auther mengtianxue
     */
    public function ActivitiesBind($phone, $bonus_id = array(), $is_sms = 1)
    {
        return BonusLibrary::model()->ActivitiesBind($phone, $bonus_id, $is_sms);
    }

    /**
     * 根据渠道和规则，绑定一张优惠券
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusCount($bonus_sn)
    {
        return BonusLibrary::model()->getBonusCount($bonus_sn);
    }

    /**
     * 分配实体卷
     * @param $bonus_sns
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function disTriByBonusIDs($bonus, $dis_city)
    {
        return BonusLibrary::model()->disTriByBonusIDs($bonus, $dis_city);
    }

    /**
     * 分配实体卷
     * @param $bonus_sns
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function disTriByBonusBetweenIDs($bonus, $dis_city)
    {
        return BonusLibrary::model()->disTriByBonusBetweenIDs($bonus, $dis_city);
    }


    /**
     * 获取该城市分配人列表
     * @param $city_id
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function disTriList($city_id = 1)
    {
        return BonusLibrary::model()->disTriList($city_id);
    }

    /**
     * 标记坏卡
     * @param $bonus_sn
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function erroCard($bonus_sn)
    {
        return BonusLibrary::model()->erroCard($bonus_sn);
    }


    /**
     * 统计渠道分配列表数量
     * @param $channel_id
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function channelBonusCount($channel, $dateStart = '', $dateEnd = '')
    {
        return BonusLibrary::model()->channelBonusCount($channel, $dateStart, $dateEnd);
    }


    /**
     * 根据获取坏卡
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusErrorCard($bonus_sn, $type = 0, $start, $end, $dis_city, $city_id = 0)
    {
        return BonusLibrary::model()->getBonusErrorCard($bonus_sn, $type, $start, $end, $dis_city, $city_id);
    }

    /**
     * 根据获取坏卡
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusErrorCard1($bonus_sn, $type = 0, $start, $end)
    {
        return BonusLibrary::model()->getBonusErrorCard1($bonus_sn, $type, $start, $end);
    }

    /**
     * 根据获取坏卡
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusNumByBonusSn($bonus_sn)
    {
        return BonusLibrary::model()->getBonusNumByBonusSn($bonus_sn);
    }


    /**
     * 标记坏卡
     * @param $bonus_sn
     * @return mixed
     * @auther zhangxiaoyin
     */
    public function updateUseInfo($bonus)
    {
        return BonusLibrary::model()->updateUseInfo($bonus);
    }

    public function useUpdate($params, $bonus_sn)
    {
        return BonusLibrary::model()->useUpdate($params, $bonus_sn);
    }

    /**
     * 根据bonus_sn 查询 bonus_id
     * @param $bonus_sn
     * @return mixed
     * @auther zhangxiaoyin
     */
    public static function getBonusIdBySn($bonus_sns)
    {
        return BonusLibrary::model()->getBonusIdBySn($bonus_sns);
    }

    /**
     * 客户补贴评价返优惠券给客户优惠券绑定
     * @param $bonus_sn v2后台建立的固定码
     * @param $phone  客户手机号
     * @param $password
     * @return bool|void
     * 0：不存在，1：成功，2：还未生效，不能绑定 3：优惠码失效
     * author mengtianxue
     */

    public function bonusBindingSubsidy($bonus_sn, $phone, $password = 0, $channel = 0)
    {
        return BonusLibrary::model()->bonusBindingSubsidy($bonus_sn, $phone, $password, $channel);
    }
}

