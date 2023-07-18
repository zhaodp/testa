<?php
/**
 * vip周消费统计
 */
Yii::import('application.models.schema.customer.CarVipSingleWeekTrend');
Yii::import('application.models.schema.report.ReportFsVipTradeInfo');

class VipSingleWeekTrend extends CarVipSingleWeekTrend {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 创建周消费记录
     * @param <int> $year               开始年份
     * @param <int> $month              开始月份
     * @param <int> $day                开始日
     * @param <int> $afterWeeks         要创建开始日期之后的多少个星期
     * @param <bool> $canOutTheWeek     是否统计超出当前时间的记录
     * @return int
     */
    public function buildWeekTrend($year = null, $month = null, $day = null, $afterWeeks = 2, $printLog = FALSE, $canOutTheWeek = FALSE) {
        $time = time();
        $saveItems = 0;
        $dayTh = mktime(0, 0, 0, $month, $day, $year);
        list($initDay, $initMonth, $initYear) = explode('-', date('d-m-Y', mktime(0, 0, 0, date('n', $dayTh), ((date('j', $dayTh) + 1) - date('N', $dayTh)), date('Y', $dayTh))));
        $idNum = 0;
        while ($vip = Vip::model()->find('id > :id', array(':id' => $idNum))) {
            if (!$vip) {
                break;
            }
            if($printLog){
                echo 'vip_model--'.implode('--', $vip->attributes)."\r\n";
            }
            $year = $initYear;
            $month = $initMonth;
            $day = $initDay;
            $vipId = $vip->id;
            for ($i = 0; $i < $afterWeeks; $i++) {
                list($year, $month, $day) = explode('-', date('Y-m-d', mktime(0, 0, 0, $month, $day, $year)));     //参数标准化
                $startTime = mktime(0, 0, 0, $month, $day, $year);
                $endTime = mktime(0, 0, 0, $month, $day + 7, $year);
                //判断 传递的年月日 大于 当前年月日 后 是否继续（$canOutTheMonth==false 的时候会退出循环）
                if (!$canOutTheWeek && (date('Y-m-d', $startTime) > date('Y-m-d', $time))) {
                    break;
                }
                $weekArr = Common::getWeekth(mktime(0, 0, 0, $month, $day, $year));
//                echo $month, $day, $year,$weekth['Y'].$weekth['W'];die;
                $weekth = $weekArr['Y'] . $weekArr['W'];
                $saveOk = $this->updateByVipWeek($vipId, $startTime, $endTime, $weekth, $printLog);        //统计一周的信息
                if ($saveOk) {
                    $saveItems++;
                }
                $day += 7;
            }
            $idNum = $vipId;
        }

        return $saveItems;
    }

    /**
     * 修改或添加某一行的数据
     * @param <string> $vipId
     * @param <int> $startTime          开始时间
     * @param <int> $endTime            结束时间
     * @param <int> $weekth             第几周（用于跟vipId一起判断是否已经存在本周记录）
     * @param <boolean> $printLog       是否打印log
     * @return <boolean>                是否修改成功
     */
    public function updateByVipWeek($vipId, $startTime, $endTime, $weekth, $printLog = FALSE) {
        $model = self::model()->findByAttributes(array('vip_id' => $vipId, 'weekth' => $weekth));
        $attributes = array(
            'vip_id' => $vipId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'weekth' => $weekth,
        );
        $db = Yii::app()->dbreport;
        $vipModel = Vip::model()->findByPk($vipId);
        if (!$vipModel) {
            return FALSE;
        }
        $registTime = $vipModel->created;
        if ($registTime > $endTime) {
            return FALSE;
        }

        $attributes['week_order_price'] = $this->weekOrderPrice($vipId, $startTime, $endTime);
        $attributes['week_order_count'] = $this->weekOrderCount($vipId, $startTime, $endTime);
        $attributes['ave_cost'] = $this->preCost($vipId, $registTime, $endTime);
        $attributes['ave_count'] = $this->preCount($vipId, $registTime, $endTime);

        $weekOrderCountCommand = $db->createCommand()
                ->select('count(1) orderCount')
                ->from(ReportFsVipTradeInfo::model()->tableName())
                ->where('vipcard = :vipId AND type = 1 AND daily_date >= :start_time AND daily_date < :end_time')
                ->queryScalar(array(':start_time' => date('Y-m-d H:i:s', $startTime), ':end_time' => date('Y-m-d H:i:s', $endTime), ':vipId' => $vipId));
        $weekOrderCount = $weekOrderCountCommand ? $weekOrderCountCommand : 0;
        $attributes['week_order_count'] = $weekOrderCount;
        if (!$model) {
            $model = new VipSingleWeekTrend;
            $attributes['create_time'] = time();
            $model->attributes = $attributes;
            $saveOk = $model->save();
        } else {
            $saveOk = $model->saveAttributes($attributes);
        }
        if ($printLog) {
            echo $vipId . '--' . $attributes['ave_cost'] . '--' . $attributes['ave_count'] . '--' . $attributes['week_order_price'] . '--' . $attributes['week_order_count'] . '--' . $attributes['ave_cost'] . '--' . date('Y-m-d H:i:s', $startTime) . '--' . $weekth . ($saveOk?'Ok':'Faild') . "\r\n";
        }
        return $saveOk;
    }

    /**
     * 一个vip一段时间内的订单总额
     * @param <string> $vipId
     * @param <int> $startTime
     * @param <int> $endTime
     * @return <int>
     */
    public function weekOrderPrice($vipId, $startTime, $endTime) {
        $db = Yii::app()->dbreport;
        $weekOrderPriceCommand = $db->createCommand()
                ->select('SUM(amount) costSum')
                ->from(ReportFsVipTradeInfo::model()->tableName())
                ->where('vipcard = :vipId AND type = 1 AND daily_date >= :start_time AND daily_date < :end_time')
                ->queryScalar(array(':start_time' => date('Y-m-d H:i:s', $startTime), ':end_time' => date('Y-m-d H:i:s', $endTime), ':vipId' => $vipId));
        $weekOrderPrice = $weekOrderPriceCommand ? $weekOrderPriceCommand : 0;
        $weekOrderPrice = abs($weekOrderPrice);
        return $weekOrderPrice;
    }

    /**
     * 一个vip一段时间内的订单总数量
     * @param <string> $vipId
     * @param <int> $startTime
     * @param <int> $endTime
     * @return <int>
     */
    public function weekOrderCount($vipId, $startTime, $endTime) {
        $db = Yii::app()->dbreport;
        $weekOrderCountCommand = $db->createCommand()
                ->select('count(1) orderCount')
                ->from(ReportFsVipTradeInfo::model()->tableName())
                ->where('vipcard = :vipId AND type = 1 AND daily_date >= :start_time AND daily_date < :end_time')
                ->queryScalar(array(':start_time' => date('Y-m-d H:i:s', $startTime), ':end_time' => date('Y-m-d H:i:s', $endTime), ':vipId' => $vipId));
        $weekOrderCount = $weekOrderCountCommand ? $weekOrderCountCommand : 0;
        return $weekOrderCount;
    }

    /**
     * 一个vip一段时间内的平均周消费
     * @param <string> $vipId
     * @param <int> $startTime
     * @param <int> $endTime
     * @return <int>
     */
    public function preCost($vipId, $registTime, $endTime) {
        $db = Yii::app()->dbreport;
        $preCostCommand = $db->createCommand()
                ->select('SUM(amount) costSum')
                ->from(ReportFsVipTradeInfo::model()->tableName())
                ->where('vipcard = :vipId AND type = 1 AND daily_date >= :start_time AND daily_date < :end_time')
                ->queryScalar(array(':start_time' => date('Y-m-d H:i:s', $registTime), ':end_time' => date('Y-m-d H:i:s', $endTime), ':vipId' => $vipId));
        $preCost = $preCostCommand ? $preCostCommand : 0;
        $weeks = ($endTime - $registTime) / (86400 * 7);
        $aveCost = abs($preCost / $weeks);
        $aveCost = substr($aveCost, 0, 8);
        return $aveCost;
    }

    /**
     * 一个vip一段时间内的平均周订单数量
     * @param <string> $vipId
     * @param <int> $startTime
     * @param <int> $endTime
     * @return <int>
     */
    public function preCount($vipId, $registTime, $endTime) {
        $db = Yii::app()->dbreport;
        $preCostCommand = $db->createCommand()
                ->select('COUNT(1) costCount')
                ->from(ReportFsVipTradeInfo::model()->tableName())
                ->where('vipcard = :vipId AND type = 1 AND daily_date >= :start_time AND daily_date < :end_time')
                ->queryScalar(array(':start_time' => date('Y-m-d H:i:s', $registTime), ':end_time' => date('Y-m-d H:i:s', $endTime), ':vipId' => $vipId));
        $preCountt = $preCostCommand ? $preCostCommand : 0;
        $weeks = ($endTime - $registTime) / (86400 * 7);
        $aveCountt = abs($preCountt / $weeks);
        $aveCountt = substr($aveCountt, 0, 8);
        return $aveCountt;
    }

}
