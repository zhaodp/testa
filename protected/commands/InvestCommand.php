<?php
/**
 * 调查问卷群发短信
 * User: mengxiangan
 */
Yii::import('application.models.pay.activitySettlementImpl.*');
Yii::import('application.models.pay.orderSettlementImpl.*');
Yii::import('application.models.pay.calculator.*');
Yii::import('application.models.pay.settlement.*');
Yii::import('application.models.schema.customer.*');
Yii::import('application.models.pay.param_settle.*');
Yii::import('application.models.pay.subsidy.*');

class InvestCommand extends LoggerExtCommand
{
    //  一期短信url地址
    const H5_URL_FIRST = 'http://h5.edaijia.cn/webview/smform/index.html';
    const H5_URL_FIRST_TEST = 'http://h5.d.edaijia.cn/webview/smform/index.html';
    // 二期短信url地址
    const H5_URL_SECOND = 'http://h5.edaijia.cn/webview/smform2/index.html';
    const H5_URL_SECOND_TEST = 'http://h5.d.edaijia.cn/webview/smform2/index.html';
    // 短信内容
    const SMS_CONTENT = '作为e代驾最重视的客户，诚邀您参与服务体验调查。填写即奖%d元优惠券，下次使用自动抵扣。';

    // 短连接服务api地址
    const SHORT_URL_TEST = 'http://open.d.api.edaijia.cn/shorturl/get';
    const SHORT_URL_STD = 'http://open.api.edaijia.cn/shorturl/get';

    // Redis 缓存前缀
    const CACHE_INVEST_PREFIX = 'CACHE_INVEST';
    const CACHE_SEND_SMS_PHONE_INVEST_PREFIX = 'CACHE_SEND_SMS_PHONE_INVEST';
    // 每次load 订单数量
    const PAGE_SIZE_STD = 100;
    const PAGE_SIZE_TEST = 2;

    private $short_url = '';
    private $page_size=2;

    public function actionInvestSMS($investId = 0, $count, $city_id = 0, $sourceOrderId = '', $investPeriods = 2, $test = 0)
    {
        EdjLog::info('start send invest -->' . date('Y-m-d H:i:s'));
        // 用以区分是发送线上还是测试环境地址
        if ($test == 0) {
            $this->page_size = self::PAGE_SIZE_TEST;
            $this->short_url= self::SHORT_URL_TEST;
            // 测试环境的orderid
            $currentMaxOrderId = $sourceOrderId == '' ? Order::model()->getCurrentMaxOrderID() : $sourceOrderId;
        } else {
            $this->page_size = self::PAGE_SIZE_STD;
            $this->short_url=self::SHORT_URL_STD;
            $currentMaxOrderId = Order::model()->getCurrentMaxOrderID();
        }
        EdjLog::info("max order_id is :".$currentMaxOrderId);
        // 短信地址
        $smsUrl = $this->determineSmsUrl($test, $investPeriods);

        if ($investId == 0) {
            // 脚本定时发送部分
            // 查询所有处于激活状态的问卷
            $criteria = new CDbCriteria();
            $criteria->compare('status', 1);
            $criteria->addCondition(' start_time<=\'' . date('y-m-d h:i:s') . '\'');
            $criteria->addCondition(' end_time>=\'' . date('y-m-d h:i:s') . '\'');
            $investList = Invest::model()->findAll($criteria);
            foreach ($investList as $invest) {
                EdjLog::info('now start send invest,id is:' . $invest->id);
                $ruleCriteria = new CDbCriteria();
                $ruleCriteria->compare('invest_id', $invest->id);
                $investRule = InvestRules::model()->find($ruleCriteria);
                $replyNum = $invest->reply_num;
                // 发送条数
                $sendCount = $investRule->send_per_time;
                $replyLimit = $investRule->reply_limit;
                // 城市列表
                if (strpos($investRule->city_id, ',')) {
                    $cityIdArr = explode(',', $investRule->city_id);
                } else {
                    $cityIdArr = $investRule->city_id;
                }

                // 是否发送给vip用户
                //$sendToVip = $investRule->send_vip;

                // 暂时先不给vip用户发送
                // 回收限制条件
                if ($replyNum >= $replyLimit) {
                    EdjLog::info('invest id:' . $investId . ' rules verify fail,because of reply limit,reply num is:' . $replyNum . ',reply limit is:' . $replyLimit);
                } else {
                    // 给每个城市发送
                    if (is_array($cityIdArr)) {
                        foreach ($cityIdArr as $cityId) {
                            // 给配置的每个城市发送
                            $this->sendInvest($invest->id, $sendCount, $cityId, $sourceOrderId, $currentMaxOrderId, $smsUrl,  0/*$investRule->send_vip*/);
                        }
                    } else {
                        $this->sendInvest($invest->id, $sendCount, $cityIdArr, $sourceOrderId, $currentMaxOrderId, $smsUrl, 0/*$investRule->send_vip*/);
                    }
                }
            }

        } else {
            // 脚本手动发送部分
            //$ruleCriteria = new CDbCriteria();
            //$ruleCriteria->compare('invest_id', $investId);
            //$investRule = InvestRules::model()->find($ruleCriteria);
            $this->sendInvest($investId, $count, $city_id, $sourceOrderId, $currentMaxOrderId, $smsUrl, 0/*$investRule->send_vip*/);
        }
        EdjLog::info('end send invest -->' . date('Y-m-d H:i:s'));
    }

    /**
     * 用于获取H5页面地址
     * @param $testUrl
     * @param $investPerieds
     */
    private function  determineSmsUrl($testUrl, $investPeriods)
    {
        if ($investPeriods == 1) {
            // 一期H5 url地址
            if ($testUrl == 0) {
                $smsUrl = self::H5_URL_FIRST_TEST;
            } else {
                $smsUrl = self::H5_URL_FIRST;
            }
        } else if ($investPeriods == 2) {
            // 二期H5 url地址
            if ($testUrl == 0) {
                $smsUrl = self::H5_URL_SECOND_TEST;
            } else {
                $smsUrl = self::H5_URL_SECOND;
            }
        }
        return $smsUrl;
    }

    /**
     * 获取短连接
     * @param array $data
     * @return mixed
     */
    private function getShortUrl($data = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $this->short_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        $obj = json_decode($result);
        curl_close($ch);
        return $obj;
    }

    /**
     * 手机正则判断(暂时无用),改用Common::checkPhone去验证
     * @param $phone
     * @return bool
     */
    private function  matchCellPhone($phone)
    {
        // 判断手机号正则式
        $regex = '/^(0|86|17951)?(13[0-9]|15[012356789]|1[78][0-9]|14[57])[0-9]{8}$/';
        if (preg_match($regex, $phone)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 发放问卷
     * @param $investId
     * @param $count
     * @param $city_id
     * @param $sourceOrderId
     * @param $pageSize
     * @param $currentMaxOrderId
     * @param $smsUrl
     */
    private function sendInvest($investId, $count, $city_id, $sourceOrderId, $currentMaxOrderId, $smsUrl, $sendToVip)
    {
        // 计算当前需要取多少次数据
        $pageSize = $this->page_size;
        $pageCount = $count % $pageSize == 0 ? $count / $pageSize : (int)($count / $pageSize) + 1;

        for ($i = 0; $i < $pageCount; $i++) {
            $offset = $i * $pageSize;
            if (($offset + $pageSize) > $count) {
                $limit = $count - $offset;
            } else {
                $limit = $pageSize;
            }
            $param = array('city_id' => $city_id, 'currentMaxOrderId' => $currentMaxOrderId, 'offset' => $offset, 'limit' => $limit,);

            // 取order订单表
            $list = Order::model()->getSendInvestRecord($param, $sourceOrderId, $sendToVip);
            $length = count($list);

            if ($length > 0) {
                $sendNum = $this->sendInvestSms($smsUrl, $investId, $list);
                EdjLog::info("send " . $sendNum . "invest success");
                // 更新发送份数
                $invest = Invest::model()->findByPk($investId);
                // 更新发送次数
                $invest->send_num += $sendNum;
                $invest->update();
            }
        }
    }

    /**
     * 发送问卷短信
     * @param $smsUrl
     * @param $investId
     * @param $list
     */
    private function sendInvestSms($smsUrl, $investId, $list)
    {
        $sendNum = 0;
        $invest = Invest::model()->findByPk($investId);
        $smsContent = sprintf(self::SMS_CONTENT, $invest->coupon);
        foreach ($list as $order) {
            try {
                $phone = $order['phone'];
                $order = $order['order_id'];
                //$cityId = $order['city_id'];
                $url = $smsUrl . '?investId=' . $investId . '&orderId=' . $order;// . '&cityId=' . $cityId;
                //缓存手机key
                $cachePhoneKey = self::CACHE_SEND_SMS_PHONE_INVEST_PREFIX . '-' . $phone;
                $existPhone = RActivity::model()->existActivity($cachePhoneKey);
                if (!$existPhone) {
                    // 同一手机号一周只能发送一次问卷
                    RActivity::model()->addActivity($cachePhoneKey, 7 * 24 * 3600);
                    $cacheKey = self::CACHE_INVEST_PREFIX . '-' . $order . '-' . $investId;
                    // 问卷存活时长为一天
                    $ret = RActivity::model()->addActivity($cacheKey, 24 * 3600);
                    if ($ret) {
                        //短信
                        $data = array('url' => $url);
                        EdjLog::info("now start send invest ,order is :" . $order);
                        // 验证是否是手机号
                        if ( Common::checkPhone($phone)) {
                            try {
                                // 获取短连接地址
                                $retObj = $this->getShortUrl($data);
                                if ($retObj && isset($retObj->code) && $retObj->code === 1) {
                                    // 匹配手机号
                                    $shortUrl = $retObj->result;
                                } else {
                                    EdjLog::info("orderId:" . $order, "get short url fail");
                                    $shortUrl = $url;
                                }
                                EdjLog::info('invest:phone=' . $phone . ',order=' . $order . ',investId=' . $investId/*. ',cityId=' . $cityId*/);
                                // 发送短信
                                $sendRet = Sms::SendSMS($phone, $smsContent . '<' .$shortUrl . '>');
                                if ($sendRet) {
                                    // 统计发送成功的次数
                                    $sendNum++;
                                } else {
                                    EdjLog::info('sms send return:' . json_encode($sendRet));
                                }
                            } catch (Excepiont $e) {
                                EdjLog::info('send sms error' . $e->getMessage());
                                continue;
                            }
                        } else {
                            EdjLog::info('phone no:' . $phone . ' is not valid cellphone');
                        }
                    }
                }else{
                    EdjLog::info("invest already sent in latest 7 days,order id:".$order.'phone is:'.$phone);
                }
            } catch (Excepiont $ex) {
                // 发送异常,继续发送
                EdjLog::info('occur some problem,order is' . $order . $e->getMessage());
                continue;
            }
        }
        return $sendNum;
    }
}
