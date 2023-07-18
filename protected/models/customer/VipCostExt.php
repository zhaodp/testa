<?php

Yii::import('application.models.schema.customer.CarVipCostExt');

class VipCostExt extends CarVipCostExt {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeSave() {
        $time = time();
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = $time;     //创建时间
            }
            $this->update_time = $time;         //修改时间
            return true;
        }
        return false;
    }

    /**
     * 刷新本表数据（vip消费扩展信息）
     * @param <bool> $printLog      是否打印log
     * @param <int> $time           时间戳
     * @return int
     * @author liuxiaobo
     * @since 2014-1-20
     */
    public function refreshData($printLog = FALSE, $time = NULL) {
        if ($time === NULL) {
            $time = date('Y-m-d', time());
        }
        $week = Common::getWeekth(strtotime($time));
        $weekth = $week['Y'] . $week['W'];
        $saveItems = 0;
        $idNum = 0;
        while ($vip = Vip::model()->find('id > :id', array(':id' => $idNum))) {
            if (!$vip) {
                break;
            }
            $vipId = $vip->id;
            $idNum = $vipId;
            $models = VipSingleWeekTrend::model()->findAllByAttributes(
                array('vip_id' => $vipId), array('condition' => 'weekth <= ' . $weekth, 'order' => 'weekth DESC', 'limit' => 3), array(':weekth' => $weekth)
            );
            if (!$models) {
                if ($printLog) {
                    echo '-error-' . $vipId . '没有此vip用户的消费统计信息（可能是刚刚开通的vip用户 或 vip信息有误）-error-' . "\r\n";
                }
                continue;
            }

            $attributes = array();
            $vipCostExt = VipCostExt::model()->findByAttributes(array('vip_id' => $vipId));
            if (!$vipCostExt) {
                $vipCostExt = new VipCostExt;
            }
            $attributes['vip_id'] = $vipId;
            $attributes['ave_count'] = $models[0]->ave_count;
            $attributes['ave_cost'] = $models[0]->ave_cost;
            $attributes['last_week_cost'] = isset($models[1]) ? $models[1]->week_order_price : 0;
            $attributes['last_week_count'] = isset($models[1]) ? $models[1]->week_order_count : 0;
            $attributes['last_second_week_cost'] = isset($models[2]) ? $models[2]->week_order_price : 0;
            $attributes['last_second_week_count'] = isset($models[2]) ? $models[2]->week_order_count : 0;
            $attributes['change_cost'] = $attributes['last_week_cost'] - $attributes['last_second_week_cost'];
            $attributes['change_count'] = $attributes['last_week_count'] - $attributes['last_second_week_count'];
            $attributes['change_rate_cost'] = $attributes['change_cost'] == 0 ? '0' : ($attributes['last_second_week_cost'] == 0 ? '100' : substr(($attributes['change_cost'] / $attributes['last_second_week_cost'] * 100), 0, 8));
            $attributes['change_rate_count'] = $attributes['change_count'] == 0 ? '0' : ($attributes['last_second_week_count'] == 0 ? '100' : substr(($attributes['change_count'] / $attributes['last_second_week_count'] * 100), 0, 8));
            $vipCostExt->attributes = $attributes;
            $saveOk = $vipCostExt->save();

            if ($saveOk) {
                $saveItems++;
            }
            if ($printLog) {
                echo '[' . implode(']--[', $attributes) . ']--' . ($saveOk?'Ok':'Faild') . "\r\n";
            }
        }

        return $saveItems;
    }

}
