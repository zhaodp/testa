<?php

/**
 * Created by PhpStorm.
 * User: zhangxiaoyin
 * Date: 2015/3/10
 * Time: 14:09
 */
class MarketStaticCommand extends CConsoleCommand
{
    /**
     * 每天定时从log文件统计昨日关注数
     * @author zhangxiaoyin
     * php yiic.php MarketStatic Static --fileName=/data/data.txt --use_date=7164
     */
    public function actionStatic($fileName)
    {
        $data = date("Y-m-d", strtotime("-1 day"));
        if (file_exists($fileName . '-' . $data)) {
            $content = file_get_contents($fileName . '-' . $data);
            $contentArr = preg_split('/[\r\n]+/', $content);

            foreach ($contentArr as $item) {
                $num_FromUserName_start = strpos($item, '<FromUserName>') + 23;
                $num_FromUserName_end = strpos($item, '</FromUserName>');

                if ($num_FromUserName_start >= $num_FromUserName_end) {
                    continue;
                }

                $num_CreateTime_start = strpos($item, '<CreateTime>') + 12;
                $num_CreateTime_end = strpos($item, '</CreateTime>');

                if ($num_CreateTime_start >= $num_CreateTime_end) {
                    continue;
                }

                $num_Event_start = strpos($item, '<Event>') + 16;
                $num_Event_end = strpos($item, '</Event>');

                if ($num_Event_start >= $num_Event_end) {
                    continue;
                }


                $open_id = substr($item, $num_FromUserName_start, $num_FromUserName_end - $num_FromUserName_start - 3);
                $create_time = substr($item, $num_CreateTime_start, $num_CreateTime_end - $num_CreateTime_start);
                $create_time = strlen($create_time) == 0 ? time() : $create_time;
                $event = substr($item, $num_Event_start, $num_Event_end - $num_Event_start - 3);

                if ($event == 'subscribe' || $event == 'unsubscribe') {
                    $num_qrscene_start = strpos($item, '<EventKey>') + 19;
                    $num_qrscene_end = strpos($item, '</EventKey>');

                    $code = 0;
                    if ($num_qrscene_start >= $num_qrscene_end) {
                        $code = 0;
                    } else {
                        $qrscene = substr($item, $num_qrscene_start, $num_qrscene_end - $num_qrscene_start - 3);
                        if (strlen($qrscene) > 0) {
                            $num_qrscene_code_start = strpos($qrscene, 'qrscene_') + 8;
                            if ($num_qrscene_code_start < 0) {
                                $code = 0;
                            } else {
                                $code = substr($qrscene, $num_qrscene_code_start);
                            }
                        }
                    }


                    $model = new MarketPomo();
                    $model->open_id = $open_id;
                    $model->subscribe_type = ($event == 'subscribe' ? 1 : 0);
                    $model->event_key = $code;
                    $model->created = $create_time;
                    $model->day = date('Y-m-d', $create_time);
                    if (!$model->save()) {
                        EdjLog::info(json_encode($model->getErrors()));
                    }
                }
            }
        } else {
            EdjLog::info("no file");
        }
    }

    /**
     * 每天定时统计昨日推广上传、审核数量
     * php yiic.php MarketStatic PomoStatic
     */
    public function actionPomoStatic()
    {
        $dateStart = date('Y-m-d 00:00:00', strtotime("-7 day"));
        $dateEnd = date('Y-m-d 00:00:00', time());
        $model = new PromotionMap();
        $pomo_all = $model->getCreatAmount($dateStart, $dateEnd);
        $pomo_pass = $model->getPassAmount($dateStart, $dateEnd);


        $mailTitle = '市场推广数量统计!';
        //$content = $date . '总共上传推广记录数：' . $pomo_all . '条;审核通过数:' . $pomo_pass . '条;待审核数：' . $pomo_pass_ing;

        $content = "推广数据统计:<table border='1'><tr><td style='white-space:nowrap;'>日期</td><td width='8%'>上传数</td><td width='8%'>审核通过数</td><td width='8%'>待主管审核数</td><td width='8%'>待总部审核数</td><td width='8%'>微信关注数</td><td width='8%'>地推渠道微信关注数</td><td width='8%'>地推渠道微信关注下单数</td><td width='8%'>地推渠道微信关注成单数</td><td width='8%'>地推渠道微信历史关注下单数</td><td width='8%'>地推渠道微信历史关注成单数</td></tr>";

        for ($i = 1; $i < 8; $i++) {
            $date = date('Y-m-d', strtotime("-" . $i . " day"));
            $content .= "<tr>";

            $content .= "<td>";
            $content .= $date;
            $content .= "</td>";

            $content .= "<td>";
            $content .= isset($pomo_all[$date]) ? $pomo_all[$date] : 0;
            $content .= "</td>";

            $content .= "<td>";
            $content .= isset($pomo_pass[$date]) ? $pomo_pass[$date] : 0;
            $content .= "</td>";

            $content .= "<td>";
            $content .= $model->getPassingAmount($date, 0);
            $content .= "</td>";

            $content .= "<td>";
            $content .= $model->getPassingAmount($date, 1);
            $content .= "</td>";

            //微信关注数
            $wx = MarketPomo::getAcount($date);
            if (empty($wx)) {
                $content .= "<td>";
                $content .= 0;
                $content .= "</td>";


            } else {
                $content .= "<td>";
                $content .= $wx->subscribe_type;
                $content .= "</td>";


            }


            //微信关注数微信下单数
            $arr_open_ids = MarketPomo::getOpenIdList($date);

            if (empty($arr_open_ids)) {
                $content .= "<td>";
                $content .= 0;
                $content .= "</td>";
            } else {
                $content .= "<td>";
                $content .= count($arr_open_ids);
                $content .= "</td>";
            }


            $arr_wx = $this->getWx($date);

            $content .= "<td>";
            $content .= $arr_wx['book_count_day'];
            $content .= "</td>";

            $content .= "<td>";
            $content .= $arr_wx['order_count_day'];
            $content .= "</td>";

            $content .= "<td>";
            $content .= $arr_wx['book_count'];
            $content .= "</td>";

            $content .= "<td>";
            $content .= $arr_wx['order_count'];
            $content .= "</td>";


            $content .= "</tr>";
        }
        $content .= "</table>";

        $FINANCE_MAIL_TO_LIST = array(
            'pomo@edaijia-inc.cn'
        );
        echo $content;
        Helper::mailAlarm($mailTitle, $content, $FINANCE_MAIL_TO_LIST);
    }


    /**
     * 每天定时统计拉取订单信息
     * php yiic.php MarketStatic OrderMap
     */
    public function actionOrderMap($pid, $limit = 1000)
    {
        $model_process = ProcessList::model()->getTail($pid);
        if ($model_process) {
            $arr_map = OrderQueueMap::getQueueMapList($model_process->tail, $limit);
            if (!empty($arr_map)) {
                foreach ($arr_map as $map) {
                    $queueInfo = OrderQueue::model()->findbypk($map['queue_id']);
                    if ($queueInfo) {
                        $model = new OrderMap();
                        $model->order_id = $map['order_id'];
                        $model->city_id = $queueInfo->city_id;

                        $model->lng = $queueInfo->lng;
                        $model->lat = $queueInfo->lat;

                        $model->day = substr($queueInfo->created, 0, 10);
                        if ($model->save()) {
                            $model_process->tail = $map['id'];
                            $model_process->last_changed_date = date('Y-m-d H:i:s');
                            if (!$model_process->save()) {
                                EdjLog::info(json_encode($model->getErrors()));
                                return false;
                            }
                        } else {
                            EdjLog::info(json_encode($model->getErrors()));
                            return false;
                        }
                    }
                }
            }
        }
    }
        /**
     * 每天定时统计昨日推广上传、审核数量
     * php yiic.php MarketStatic getWx --2014-04-18 00:00:00 --2014-04-18 23:59:59
     */
    public function getWx($day, $limit = 1000)
    {
        $book_count = 0;
        $order_count = 0;

        $book_count_day = 0;
        $order_count_day = 0;

        $offit = 0;
        //获取微信订单列表
        $flag = true;
        while ($flag) {
            $arr_order_ext = OrderExt::WxOrderList($day, $day . ' 23:59:59', $offit, $limit);
            if (!empty($arr_order_ext)) {
                $new_ext = array();
                foreach ($arr_order_ext as $ext) {
                    $new_ext[] = $ext['order_id'];
                }
                //根据订单获取订单信息

                $arr_order = Order::getStatus($new_ext);

                if (!empty($arr_order)) {
                    $phone_order = array();
                    foreach ($arr_order as $order) {
                        $phone_order[$order['phone']] = $order;
                    }

                    $phones = array_keys($phone_order);
                    //根据手机号获取open_ids
                    $arr_wx_phone = WxPhone::getOpenIdList($phones);
                    $arr_open_ids = array_keys($arr_wx_phone);
                    $arr_order_open_ids = MarketPomo::getOpenIdListByOpenIds($arr_open_ids,$day,1);

                    if (!empty($arr_order_open_ids)) {
                        foreach ($arr_order_open_ids as $open_id) {
                            $book_count++;
                            if ($phone_order[$arr_wx_phone[$open_id->open_id]]['status'] == 1) {
                                $order_count++;
                            }
                        }
                    }


                    $arr_order_open_ids_day = MarketPomo::getOpenIdListByOpenIds($arr_open_ids, $day,2);

                    if (!empty($arr_order_open_ids_day)) {
                        foreach ($arr_order_open_ids_day as $open_id_day) {
                            $book_count_day++;
                            if ($phone_order[$arr_wx_phone[$open_id_day->open_id]]['status'] == 1) {
                                $order_count_day++;
                            }
                        }
                    }
                }

            }

            if (count($arr_order_ext) == $limit) {
                $offit = $offit + $limit;
            } else {
                $flag = false;
            }
        }

        $result = array();
        $result['book_count'] = $book_count;
        $result['order_count'] = $order_count;

        $result['book_count_day'] = $book_count_day;
        $result['order_count_day'] = $order_count_day;
        return $result;
    }
}
