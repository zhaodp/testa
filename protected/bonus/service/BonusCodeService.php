<?php
Yii::import('application.models.bonus');

class BonusCodeService extends  BaseService
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
    public function search($order = NULL)
    {
        return BonusCode::model()->search($order);
    }

    public function getBonusMerchantsByIdArray($idArray)
    {
        return BonusCode::model()->getBonusMerchantsByIdArray($idArray);
    }


    /**
     * 根据id获取优惠劵信息
     * @param $id
     * @param $status
     * @param $sn_type
     * @param $condition
     * @return mixed
     * author zhangxiaoyin
     */
    public function getBonusCodeById($id, $status = null, $sn_type = null, $condition = 'and')
    {
        return BonusCode::model()->getBonusCodeById($id, $status, $sn_type, $condition);
    }


    /**
     * 获取某个字段的值
     * @param $id
     * @param $field_name
     * @return string
     * @auther zhangxiaoyin
     */
    public function getFieldValue($id, $field_name)
    {
        return BonusCode::model()->getFieldValue($id, $field_name);
    }

    /**
     * 修改优惠劵的状态（0.待审核、1。已审核、2.审核不通过、3.删除）
     * @param $id
     * @param $status
     * @return bool
     * author zhangxiaoyin
     */
    public function updateStatus($id, $status)
    {
        return BonusCode::model()->updateStatus($id, $status);
    }

    /**
     * 根据bonusId获取短信内容
     * @param <int> $id
     * @return <string> 短信内容
     * created by zhangxiaoyin
     */
    public function getSmsById($id)
    {
        return BonusCode::model()->getSmsById($id);
    }


    /**
     * 根据bonusId获取使用信息
     * @param <int> $id
     * @return <string> 使用信息
     * created by zhangxiaoyin
     */
    public function getUseInfoById($id)
    {
        return BonusCode::model()->getUseInfoById($id);
    }

    /**
     * 优惠劵使用时限制验证
     * @param $id
     * @param $order
     * @return bool
     * author zhangxiaoyin
     */
    public function bonusUsedVerified($id, $order)
    {
        //检查传入的id不为空
        if (!empty($id)) {
            //检查优惠劵是否存在
            $bonusCode = $this->getBonusCodeById($id, self::STATUS_APPROVED);
            if ($bonusCode) {
                //vip用户限制
                $vip_phone = VipService::service()->getVipPhoneInfo($order['phone']);
                if (empty($vip_phone)) {
                    return false;
                }
                //验证是否能在当前城市使用
                $city_arr = BonusCodeCityService::service()->getBonusCodeCityID($bonusCode['id'], $order['city_id']);
                if (empty($city_arr)) {
                    return false;
                }

                //验证使用渠道限制
                $channel_limited = $order['source'];
                if ($bonusCode['channel_limited'] == BonusCode::CHANNEL_LIMITED_APP && in_array($channel_limited, Order::$client_source)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 优惠劵绑定时验证
     * @param $id
     * @param $phone
     * @param $bonus_sn
     * @return bool
     * author mengtianxue
     */
    public function bonusBindingVerified($id, $phone, $bonus_sn)
    {
        if (!empty($id)) {
            //检查优惠劵是否存在
            $bonusCode = $this->getBonusCodeById($id, BonusCode::STATUS_APPROVED);
            if ($bonusCode) {
                //vip用户限制
                $vip_phone =  VipService::service()->getVipPhoneInfo($phone);
                if (empty($vip_phone)) {
                    return false;
                }

                //用户限制验证
                $is_user_limited = $this->is_user_limited($bonusCode['user_limited'], $phone);
                //重复使用限制验证
                $is_repeat_limited = $this->is_repeat_limited($bonusCode['repeat_limited'], $phone, $bonus_sn);

                if ($is_user_limited && $is_repeat_limited) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 用户限制
     * @param $user_limited
     * @param $phone
     * @return bool
     * author mengtianxue
     */
    public function is_user_limited($user_limited, $phone)
    {
        //查看用户是否已有成单数据
        $isNewCustomer = CustomerOrderReportService::service()->isNewCustomer($phone);

        $back = false;
        switch ($user_limited) {
            case 0: //不限
                $back = true;
                break;
            case 1: //限老用户使用
                if (!$isNewCustomer) {
                    $back = true;
                }
                break;
            case 2: //限新用户使用
                if ($isNewCustomer) {
                    $back = true;
                }
                break;
        }

        return $back;
    }

    /**
     * 重复使用限制验证
     * @param $repeat_limited
     * @param $phone
     * @param $bonus_sn
     * @return bool
     * author mengtianxue
     */
    public function is_repeat_limited($repeat_limited, $phone, $bonus_sn, $repeat_bind = 1)
    {
        $back = false;
        if ($repeat_limited == 2) {
            $bonus = CustomerBonusService::service()->existsCustomerBonus($bonus_sn);
            if ($bonus) {
                $back = true;
            }
        } else { //1.可重复使用
            if ($repeat_bind == 1) {
                return true;
            } else {
                $bonus = CustomerBonusService::service()->existsCustomerBonusWithPhone($bonus_sn, $phone);
                if ($bonus) {
                    $back = true;
                }
            }
        }
        return $back;
    }

    /**
     * @param $type_id
     * @return array|CActiveRecord|mixed|null
     * author mengtianxue
     */
    public function getBonusCode($type_id)
    {
        return BonusCode::model()->getBonusCode($type_id);
    }

    /**
     * 获取最大的area_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getMaxAreaID()
    {
        return BonusCode::model()->getMaxAreaID();
    }

    /**
     * 根据渠道，获取优惠劵ID
     * @param $channel
     * @param $money
     * @return array
     * @auther mengtianxue
     */
    public function getBonusIDByChannle($channel, $money = 0)
    {
        return BonusCode::model()->getBonusIDByChannle($channel,$money);
    }


    /**
     * 根据优惠码ID，获取优惠劵使用类型
     * @param $bonus_ids
     * @return array
     * @auther mengtianxue
     */
    public static function getBonusTypeByIDs($bonus_ids)
    {
        return BonusCode::getBonusTypeByIDs($bonus_ids);
    }


    /**
     * 根据优惠码ID，获取优惠劵名称
     * @param $bonus_ids
     * @return array
     * @auther mengtianxue
     */
    public static function getBonusNameByIDs($bonus_ids)
    {
        return BonusCode::getBonusNameByIDs($bonus_ids);
    }


    /**
     * 根据优惠劵名称获取优惠码ID
     * @param $bonus_name
     * @return array
     * @auther mengtianxue
     */
    public static function getBonusIDByName($bonus_name)
    {
        return BonusCode::getBonusIDByName($bonus_name);
    }

    /**
     * 根据优惠劵名称获取优惠码ID
     * @param $bonus_name
     * @return array
     * @auther mengtianxue
     */
    public static function getBonusByIds($bonus_ids)
    {
        return BonusCode::getBonusByIds($bonus_ids);
    }
}
