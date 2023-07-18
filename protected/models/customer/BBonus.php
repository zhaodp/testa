<?php

/**
 * 优惠劵绑定列表
 * User: mtx
 * Date: 14-3-13
 * Time: 下午2:04
 * auther mengtianxue
 */
class BBonus extends FinanceActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 获得一个优惠券的信息
     *
     * @param $bonusSn
     * @param $orderId
     * @param $phone
     * @return array
     */
    public function getBonusInfoBySn($bonusSn, $orderId, $phone)
    {
        $customerBonus = CustomerBonus::model()->getCustomerBonus($phone, $orderId, $bonusSn);
        $money = 0;
        $name = '';
        if ($customerBonus) {
            $bonus = $customerBonus[0];
            $money = isset($bonus['use_money']) ? $bonus['use_money'] : 0;
            $bonusId = isset($bonus['bonus_type_id']) ? $bonus['bonus_type_id'] : 0;
            if (!empty($bonusId)) {
                $bonusCode = BonusCode::model()->getBonusCodeById($bonusId);
                if ($bonusCode) {
                    $name = isset($bonusCode['rename']) ? $bonusCode['rename'] : '';
                }
            }
        }
        return array(
            'money' => $money,
            'name' => $name,
        );
    }

    /**
     * 返回订单 优惠券能够抵消的钱
     *
     * @param $orderId
     * @param $phone
     * @return float
     */
    public function  getBonusMoneyByOrderId($orderId, $phone)
    {
        $bonus = CustomerBonus::model()->getBonusUsed($phone, $orderId);
        $bonusMoney = 0.00;
        if ($bonus) {
            $bonusMoney = $bonus['balance'];
        }
        return $bonusMoney;
    }

    /**
     * 根据bonus_id 获取优惠劵信息
     * @param $bonus_id
     * @return mixed
     * @auther mengtianxue
     */
    public function getBonusCode($bonus_id)
    {
        return BonusCode::model()->getBonusCodeById($bonus_id);
    }


    /**
     * 获取API优惠劵列表
     * @param array $params
     *        参数：$phone，$status, $pageNO, $pageSize, $type
     * @return array
     * @auther mengtianxue
     */
    public function getCustomerBonus($params = array())
    {
        $data = array();
        try {
            EdjLog::info('getCustomerBonus input --- ' . json_encode($params));
            $phone = isset($params['phone']) ? $params['phone'] : 0;
            $pageNO = isset($params['pageNO']) ? $params['pageNO'] : 0;
            $pageSize = isset($params['pageSize']) ? $params['pageSize'] : 10;
            $sort = isset($params['sort']) ? $params['sort'] : 2;
            $status = isset($params['status']) ? $params['status'] : 1;
            $channel = isset($params['channel']) ? $params['channel'] : 0;
            $type = isset($params['type']) ? $params['type'] : 0;
            $bonus_use_limit = isset($params['bonus_use_limit']) ? intval($params['bonus_use_limit']) : 0;
            $day_type = 0;
            if ($type == 0) {
                //$customer = CustomerMain::getInfoByPhone($phone);
                $customer=CustomerService::service()->getCustomerInfo($phone,1);
                if (!($customer && $customer->city_id)) {
                    EdjLog::info('信息为空:' . $phone);
                    return $data;
                }

                $daytime_type = CityConfigService::dayTimeStatus($customer->city_id);
                $calltime = time();
                if($daytime_type && isset(Yii::app()->params['daytime_price'][$daytime_type])){
                    $detail_price = Yii::app()->params['daytime_price'][$daytime_type];
                    $start_time = strtotime(date('Y-m-d', $calltime) . ' ' . $detail_price['start_time']);
                    $end_time = strtotime(date('Y-m-d', $calltime) . ' ' . $detail_price['end_time']);
                    if ($calltime > $start_time && $calltime < $end_time) {
                        $day_type = 1;
                    }
                }

            }

            //获取排序类型 1：为正常排序 否  则为使用排序
            if ($sort == 1) {
                $sort = 'id desc';
            } else if ($sort == 2) {
                $sort = 'user_limited desc, balance desc, end_date asc';
            } else {
                $sort = 'created desc';
            }

            $params = array();
            $offset = $pageNO * $pageSize;
            $where = 'id > 0';

            if (!empty($channel)) {
                $where .= ' and channel = :channel ';
                $params[':channel'] = $channel;
            }
            if (in_array($type, Order::$callcenter_sources) ||
                in_array($type, Order::$callcenter_input_sources)
            ) {
                //400订单
                $where .= ' and channel_limited = 2 ';
            }
            //sn_type = 2 是贵宾卡，在优惠劵列表中不展示
            $where .= ' and customer_phone = :customer_phone and sn_type != :sn_type';
            $params[':customer_phone'] = $phone;
            $params[':sn_type'] = 2;

            $customer_order_report = CustomerOrderReport::model()->getCustomerOrder(array('phone' => $phone));
            //优惠劵的状态，1：可使用，2.已使用，3.过期 4.所有　
            switch ($status) {
                case 1:
                    $where .= ' and order_id = :order_id and end_date > :end_date';
                    $params[':order_id'] = 0;
                    $params[':end_date'] = date('Y-m-d H:i:s');

                    //判断是否是新老客
                    if ($customer_order_report) {
                        $where .= ' and user_limited != :user_limited';
                        $params[':user_limited'] = 2;
                    } else {
                        //新客户
                        $where .= ' and user_limited != 1';
                    }


                    if ($type > 0) {
                        //报单
                        if (in_array($type, Order::$daytime_sources)) {
                            $where .= ' and bonus_use_limit in (1,2) ';
                        } else {
                            $where .= ' and bonus_use_limit in (0,2) ';
                        }
                    } else {
                        //列表
                        if ($bonus_use_limit == 0) {
                            $where .= ' and bonus_use_limit in (0,1,2) ';
                        } else {
                            if ($day_type == 1) {
                                $where .= ' and bonus_use_limit in (1,2) ';
                            } else {
                                $where .= ' and bonus_use_limit in (0,2) ';
                            }
                        }
                    }


                    break;
                case 2:
                    $where .= ' and (used != 0 or (used = 0 and order_id != 0))';
                    $sort = 'used desc';
                    break;
                case 3:
                    $where .= ' and order_id = :order_id and end_date < :end_date';
                    $params[':order_id'] = 0;
                    $params[':end_date'] = date('Y-m-d H:i:s');
                    //判断是否是新老客
                    if ($customer_order_report) {
                        $where .= ' or (user_limited = 2 and customer_phone = :customer_phones and order_id = :order_ids)';
                        $params[':customer_phones'] = $phone;
                        $params[':order_ids'] = 0;
                    }
                    break;
                case 4:
                    break;
                default:
                    $where .= ' and order_id != :order_id and end_date > :end_date';
                    $params[':order_id'] = 0;
                    $params[':end_date'] = date('Y-m-d H:i:s');

                    //判断是否是新老客
                    if ($customer_order_report) {
                        $where .= ' and user_limited != :user_limited';
                        $params[':user_limited'] = 2;
                    }
                    break;
            }

            $customer_bonus = Yii::app()->db_finance->createCommand()
                ->select("*")
                ->from("{{customer_bonus}}")
                ->where($where, $params)
                //    ->limit($pageSize)
                //    ->offset($offset)
                ->order($sort)
                ->queryAll();

            if ($customer_bonus) {
                if ($status == 4) {
                    $tmp_customer_bonus = array();
                    $now_string_time = date('Y-m-d H:i:s');
                    //未使用
                    foreach ($customer_bonus as $bonus) {
                        if ($bonus['order_id'] == 0 && $bonus['end_date'] > $now_string_time && (!$customer_order_report || $bonus['user_limited'] != 2)) {
                            $bonus['status'] = 1;
                            $tmp_customer_bonus[] = $bonus;
                        }
                    }
                    //已使用　
                    foreach ($customer_bonus as $bonus) {
                        if ($bonus['used'] != 0 || ($bonus['used'] == 0 && $bonus['order_id'] != 0)) {
                            $bonus['status'] = 2;
                            $tmp_customer_bonus[] = $bonus;
                        }
                    }
                    //已过期
                    foreach ($customer_bonus as $bonus) {
                        if (($bonus['order_id'] == 0 && $bonus['end_date'] < $now_string_time) ||
                            ($customer_order_report && $bonus['user_limited'] == 2 && $bonus['customer_phone'] == $phone && $bonus['order_id'] == 0)
                        ) {
                            $bonus['status'] = 3;
                            $tmp_customer_bonus[] = $bonus;
                        }
                    }
                    $customer_bonus = $tmp_customer_bonus;
                }
                //把尚未生效的优惠券从列表中移除
                if ($status == 1) {
                    $bonus_temp = array();
                    foreach ($customer_bonus as $bonus) {
                        $bonus_code = BonusCode::model()->getBonusCode($bonus["bonus_type_id"]);
                        if(strtotime($bonus_code["effective_date"])<time()) {
                            $bonus_temp[] = $bonus;
                        }
                    }
                    $customer_bonus = $bonus_temp ;
                }
                for ($k = $offset; $k < $offset + $pageSize; ++$k) {
                    if (count($customer_bonus) <= $k) {
                        break;
                    }
                    $bonus = $customer_bonus[$k];
                    $tmp_data = array();
                    $tmp_data['id'] = $bonus['id'];
                    $tmp_data['sn'] = $bonus['bonus_sn'];
                    $tmp_data['money'] = $bonus['balance'];
                    $tmp_data['limit_time'] = $bonus['end_date'];
                    $tmp_data['status'] = isset($bonus['status']) ? $bonus['status'] : $status;
                    $tmp_data['used_time'] = $bonus['updated'];
                    $tmp_data['channel_limited'] = $bonus['channel_limited'];
                    $bonus_code = $this->getBonusCode($bonus['bonus_type_id']);
                    if ($bonus_code) {
                        $tmp_data['name'] = $bonus_code['rename'];
                        $tmp_data['remark'] = empty($bonus_code['Introducte']) ? '' : $bonus_code['Introducte'];
                    }
                    if ($bonus_code['bonus_use_limit'] == 2) {
                        $tmp_data['support_channel'] = array(0, 1);
                    } else {
                        $tmp_data['support_channel'] = array(intval($bonus_code['bonus_use_limit']));
                    }

                    $data[] = $tmp_data;
                }
            }
            EdjLog::info('getCustomerBonus output --- ' . json_encode($data));
        } catch (Exception $e) {
            EdjLog::info('getCustomerBonus output --- ' . json_encode($e));
        }
        return $data;
    }
}
