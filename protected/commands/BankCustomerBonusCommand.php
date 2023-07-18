<?php

class bankCustomerBonusCommand extends LoggerExtCommand
{

    /**
     * 通知昨日消费客户剩余优惠券信息
     * php yiic.php bankCustomerBonus BankCustomerBonusYesterday --card_bonus=7164 --club_bonus=6432
     */
    public function actionBankCustomerBonusYesterday($card_bonus, $club_bonus)
    {
        $date = date('Y-m-d 15:00:00', strtotime("-1 day"));
        $start_date = strtotime($date);
        $end_date = $start_date + 86400;

        $update_phone = 0;

        EdjLog::info('开始本轮同步start:');
        $phone_list = CustomerBonus::model()->selectCustomerBonusNum($card_bonus, $club_bonus, $start_date, $end_date);
        if (empty($phone_list)) {
            EdjLog::info('结束本轮同步end:$phone_list null');
            return;
        }
        $data_list = CustomerBonus::model()->selectPhoneList($card_bonus, $club_bonus, $phone_list);
        $count = count($data_list);
        $arr_sns = array($card_bonus, $club_bonus);
        $arr_ids = BonusLibrary::getBonusIdBySn($arr_sns);
        $arr_bonus_date = BonusCode::model()->getBonusByIds($arr_ids);
        if (empty($arr_bonus_date) || count($arr_bonus_date) < count($arr_ids)) {
            EdjLog::info('有无效优惠券');
            return;
        }
        if ($count > 0) {
            foreach ($data_list as $data) {
                try {
                    $message = '感谢使用代驾服务。';
                    $card_bonus_num = $data->channel;
                    $club_bonus_num = $data->use_money;

                    if ($card_bonus_num == 0 && $club_bonus_num == 0) {
                        $message = $message . '您的顶级客户代驾服务权益已全部享受完。如还需代驾服务，请以现金支付。';
                    } else {
                        $message = $message . '您还有';
                        if ($card_bonus_num > 0) {
                            $message = $message . $card_bonus_num . '次顶级客户代驾服务权益，有效期' . substr($arr_bonus_date[$arr_ids[$card_bonus]], 0, 10) . '，';
                        }
                        if ($club_bonus_num > 0) {
                            $message = $message . $club_bonus_num . '次俱乐部代驾服务权益，有效期' . substr($arr_bonus_date[$arr_ids[$club_bonus]], 0, 10) . '。';
                        }
                    }
                    $message = $message . '[广发银行]';
                    $result = Sms::SendSMS($data->customer_phone, $message);

                    if ($result) {
                        EdjLog::info('phone:' . $data->customer_phone . '优惠券数量信息push succ');
                    } else {
                        EdjLog::info('phone:' . $data->customer_phone . '优惠券数量信息push error');
                    }
                } catch (Exception $e) {
                    EdjLog::error('phone:' . $data->customer_phone . '优惠券数量信息push error');
                }

            }
        }

        echo '结束本轮同步end:';
    }


    /**
     * 绑定优惠券
     * php yiic.php bankCustomerBonus BindBankCustomerBonus --card_bonus=7164 --club_bonus=6432 --flag=1 --thread=1 --limit=1000
     */
    public function actionBindBankCustomerBonus($card_bonus, $club_bonus, $flag, $thread, $limit = 1000)
    {
        $start = ($flag - 1) * $limit;
        $end = $start;
        EdjLog::info('begin:' . $start);
        $bonus_card = BonusLibrary::model()->getBonusByBonus_sn($card_bonus, 1);
        $bonus_club = BonusLibrary::model()->getBonusByBonus_sn($club_bonus, 1);
        //echo serialize($bonus_club);
        $error_num = 0;
        $card_all_num = 0;
        $club_all_num = 0;
        if ($bonus_card && $bonus_club) {
            $bonus_card_id = $bonus_card['bonus_id'];
            $bonus_club_id = $bonus_club['bonus_id'];

            $params = array();
            while (true) {
                EdjLog::info('sync start:' . $start);
                try {
                    $customer_list = BankCustomerBonus::model()->getList($flag, $thread, $limit);
                    $phone = '';
                    if (count($customer_list) > 0) {
                        foreach ($customer_list as $list) {
                            $phone = trim($list->phone);
                            $card_number = $list->card_number;
                            $club_number = $list->club_number;
                            try {
                                $delete = CustomerBonus::model()->deleteNewCustomerBonus($phone, 8);
                                if ($delete) {
                                    if ($card_number > 0) {
                                        $this->bind($bonus_card_id, $card_bonus, $card_number, $phone);
                                        $card_all_num = $card_all_num + $card_number;
                                    }

                                    if ($club_number > 0) {
                                        $this->bind($bonus_club_id, $club_bonus, $club_number, $phone);
                                        $club_all_num = $club_all_num + $club_number;
                                    }

                                    $list->status = 1;
                                    $list->last_changed_date = date('Y-m-d H:m:s');

                                    if (!$list->save()) {
                                        $error_num++;
                                        EdjLog::info('接触绑定新客优惠券error:' . $phone);
                                        EdjLog::info(serialize($list->getErrors()));
                                    }

                                    $start = $this->id;
                                } else {
                                    $error_num++;
                                }
                            } catch (Exception $e1) {
                                EdjLog::error($e1->getMessage());
                            }
                        }
                    }

                    EdjLog::info('sync end:' . $start);
                    if (count($customer_list) < $limit) {
                        break;
                    }
                    $start = $start + ($thread - 1) * $limit;
                } catch (Exception $e) {
                    EdjLog::error($e->getMessage());
                }

            }
            echo '共数据:' . $start . '条记录;绑定card_bonus:' . $card_all_num . '张;绑定club_bonus:' . $club_all_num . '张';
            EdjLog::info('共数据:' . $start . '条记录;绑定card_bonus:' . $card_all_num . '张;绑定club_bonus:' . $club_all_num . '张');
        }

    }

    private function bind($bonus_card_id, $card_bonus, $number, $phone)
    {
        $bonus_info = BonusCode::model()->getBonusCodeById($bonus_card_id);
        if ($bonus_info) {
            $params = array(
                'id' => $bonus_info['id'],
                'sn_type' => $bonus_info['sn_type'],
                'channel' => $bonus_info['channel'],
                'bonus_sn' => $card_bonus,
                'parityBit' => 0,
                'money' => $bonus_info['money'],
                'back_type' => $bonus_info['back_type'],
                'end_date' => $bonus_info['end_date'],
                'user_limited' => $bonus_info['user_limited'],
                'channel_limited' => $bonus_info['channel_limited'],
            );

            for ($i = 0; $i < $number; $i++) {
                CustomerBonus::model()->AddCustomerBonus($params, $phone);
            }
            return true;
        } else {
            return false;
        }
    }
}


