<?php

/**
 * @author WangJub
 *
 */
class newCustomerFromCallCommand extends CConsoleCommand
{


    public function actionRun($bonusSn19, $bonusSn39)
    {
        $start = time();
        $phoneArr = self::getPhoneArr();
        Putil::report("需要绑定优惠券的手机列表->" . json_encode($phoneArr));
        foreach ($phoneArr as $phone) {
            if (self::getBonusType($phone)) {
                $sms = "19元代驾费已经存入您的账户中，感谢您使用e代驾。点击下载：http://t.cn/RZLZlYu 您也可通过各应用市场搜索“e代驾”下载安装。 (仅限客户端使用)";
                self::bindBonus($phone, $bonusSn19, $sms);
            } else {
                $sms = "39元代驾费已经存入您的账户中，感谢您使用e代驾。点击下载：http://t.cn/RZLZlYu 您也可通过各应用市场搜索“e代驾”下载安装。 (仅限客户端使用)";
                self::bindBonus($phone, $bonusSn39, $sms);
            }
        }
        Putil::report("处理完毕，今天共绑定".count($phoneArr)."个好啊"."处理开始时间".$start."结束时间".time());
        return;

    }

    /**
     * 得到昨天400新客
     */
    private function getPhoneArr()
    {
        $beginYesterday = mktime(07, 0, 0, date('m'), date('d') - 1, date('Y'));
        $endYesterday = mktime(07, 0, 0, date('m'), date('d'), date('Y')) - 1;
        echo $beginYesterday . $endYesterday;
        $command = Yii::app()->db->createCommand();
        $rows = $command
            ->select("phone")
            ->from("t_customer_order_report")
            ->where("first_order_time between :start_time and :end_time and app_num = 0 and other_num =0 and call_num > 0",
                array(':start_time' => $beginYesterday, ':end_time' => $endYesterday))
            ->queryAll();
//        echo $command->text;
        $phoneArr = array();
        foreach ($rows as $phone) {
            if (self::isPhone($phone['phone']))
                array_push($phoneArr, $phone['phone']);
        }
        Putil::report("昨日新客号码" . json_encode($phoneArr));
        return $phoneArr;
    }

    /**
     * @param $phone
     * @return bool true 应绑定19元优惠券
     */
    private function getBonusType($phone)
    {
        $city_id = Helper::PhoneLocation($phone);
//        Putil::report($city_id);
        if ($city_id == 0) {
            Putil::report("所在城市未开通,绑定19元券");
            return true;
        }
        $cityArr = RCityList::model()->getCityFeeEq();
        if (Putil::isNotEmpty($city_id)) {
            Putil::report("手机号归属地" . $city_id . " 19元城市列表" . json_encode($cityArr));
            if (in_array($city_id, $cityArr)) {
                return true;
            }
        }
        return false;
    }


    private function bindBonus($phone, $bonusSn, $sms)
    {
        $res = BonusLibrary::model()->BonusBinding($bonusSn, $phone, 0, 0, 1, 0, 1);
        Putil::report("绑定结果" . json_encode($res));
        if ($res['code'] == 0) {
            Sms::SendSMS($phone, $sms);
        }
    }

    public function actionTest()
    {
//        $beginYesterday = mktime(07, 0, 0, date('m'), date('d') - 1, date('Y'));
//        $endYesterday = mktime(07, 0, 0, date('m'), date('d'), date('Y')) - 1;
//        echo $beginYesterday . $endYesterday;
//        echo date("Y-m-d H:i:s",$beginYesterday);
//        echo date("Y-m-d H:i:s",$endYesterday);

        self::getBonusType("12578945655");
    }

    public function isPhone($q)
    {
        $reg = '/^1\d{10}$/';
        $match = preg_match($reg, $q);
        return $match ? TRUE : FALSE;
    }

}
