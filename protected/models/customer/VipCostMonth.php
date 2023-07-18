<?php

Yii::import('application.models.schema.customer.CarVipCostMonth');
Yii::import('application.models.schema.report.ReportFsVipTradeInfo');

class VipCostMonth extends CarVipCostMonth {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeSave() {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = time();
            } else {
                $this->update_time = time();
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 某一城市($city > 0)或所有城市($city == 0) 里 vip 用户 在 $year 年 $month 月 的消费情况
     * @param <int> $city
     * @param <int> $year
     * @param <int> $month
     * @param <string> $type            金额变化类型（out : 消费 ； in : 充值）
     * @return <array> if($type=='out'){return array('sum_amount'=>消费总额, 'sum_count'=>订单总数)}; if($type=='in'){return array('sum_in_amount'=>充值总额)}
     * @author liuxiaobo
     * @since 2014-1-6
     */
    public function allReportCostMonth($city = 0, $year = null, $month = null, $type = 'out') {
        $year = $year === null ? date('Y') : $year;
        $month = $month === null ? date('Y') : $month;
        $startTime = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, 1, $year));
        $endTime = date('Y-m-d H:i:s', mktime(0, 0, 0, $month + 1, 1, $year));
        $where = 'daily_date >= :start_time AND daily_date < :end_time';
        $params = array(
            ':start_time' => $startTime,
            ':end_time' => $endTime,
        );
        if ($city != 0) {
            $where .= ' AND city_id = :city';
            $params[':city'] = $city;
        }

        if ($type == 'out') {
            $where .= ' AND type = 1';
            $data = $this->allOut($where, $params);
        } else if ($type == 'in') {
            $where .= ' AND (type = 0 OR type = 2)';
            $data = $this->allIn($where, $params);
        } else {
            throw new CHttpException(400, '参数不服要求');
        }
        return $data;
    }

    private function allOut($where, $params) {
        $data = array(
            'sum_out_amount' => 0,
            'sum_out_count' => 0,
        );
        $command = Yii::app()->dbreport->createCommand()
                ->select('SUM( amount ) sum_out_amount, COUNT( 1 ) sum_out_count')
                ->from(ReportFsVipTradeInfo::model()->tableName())
                ->where($where);
        $query = $command->queryRow(TRUE, $params);
        if ($query) {
            $data['sum_out_amount'] = empty($query['sum_out_amount']) ? 0 : abs($query['sum_out_amount']);
            $data['sum_out_count'] = empty($query['sum_out_count']) ? 0 : $query['sum_out_count'];
        }
        return $data;
    }

    private function allIn($where, $params) {
        $data = array(
            'sum_in_amount' => 0,
        );
        $command = Yii::app()->dbreport->createCommand()
                ->select('SUM( amount ) sum_in_amount')
                ->from(ReportFsVipTradeInfo::model()->tableName())
                ->where($where);
        $query = $command->queryRow(TRUE, $params);
        if ($query) {
            $data['sum_in_amount'] = empty($query['sum_in_amount']) ? 0 : abs($query['sum_in_amount']);
        }
        return $data;
    }

    /**
     * 生成或修改某条数据的属性
     * @param <int> $city               城市id
     * @param <int> $year               年份
     * @param <int> $month              月份
     * @param <array> $attributes       保存的属性
     * @param <array> $errors           数据错误信息
     * @return <boolean>                是否保存成功
     * @author liuxiaobo
     * @since 2014-1-6
     */
    public function updateByCityYear($city, $year, $month, $attributes = array(), &$errors = array()) {
        if (empty($attributes)) {
            return TRUE;
        }
        $model = self::model()->findByAttributes(array('city_id' => $city, 'month' => $year . '-' . $month));
        if (!$model) {
            $model = new VipCostMonth;
            $model->city_id = $city;
            $model->month = $year . '-' . $month;
            $model->attributes = $attributes;
            $saveOk = $model->save();
        }else{
            $saveOk = $model->saveAttributes($attributes);
        }
        $errors = $model->errors;
        return $saveOk;
    }

    /**
     * 把统计的消费情况保存到数据库
     * @param <int> $year               开始年份
     * @param <int> $month              开始月份
     * @param <int> $afterMonths        统计几个月的数据
     * @param <bool> $canOutTheMonth    可以超出当前月份（生成的数据均为0），默认为不可以
     * @return <int>                    生成或修改了多少条数据
     * @author liuxiaobo
     * @since 2014-1-6
     */
    public function processReportCostMonth($year, $month, $afterMonths = 1, $canOutTheMonth = FALSE, $printLog = FALSE) {
        $time = time();
        $updateItems = 0;                       //修改了多少条数据
        $citys = Dict::items('city');         //获取到已开通代驾的城市
        if($printLog){
            echo 'citys=>'.implode('--', $citys)."\r\n";
        }
        list($initYear, $initMonth) = explode('-', date('Y-m', mktime(0, 0, 0, $month, 1, $year)));     //参数标准化
        //按照城市循环生成数据
        foreach ($citys as $city_id => $city_name) {
            $year = $initYear;
            $month = $initMonth;
            for ($i = 0; $i < $afterMonths; $i++) {        //单个城市 按照月份逐一生成数据
                if($printLog){
                    echo 'city=>'.$city_id."\r\n";
                }
                //判断 传递的年月 大于 当前年月 后 是否继续（$canOutTheMonth==false 的时候会退出循环）
                if (!$canOutTheMonth && (date('Y-m', mktime(0, 0, 0, $month, 1, $year)) > date('Y-m', $time))) {
                    break;
                }
                list($year, $month) = explode('-', date('Y-m', mktime(0, 0, 0, $month, 1, $year)));     //参数标准化
                $attributes = $this->getAttributesForProcessPeport($city_id, $year, $month);            //获取需要修改的属性

                $error = array();
                $updateOk = $this->updateByCityYear($city_id, $year, $month, $attributes, $error);              //修改
                if($printLog){
                    echo '-----$city_id='.$city_id.'$year='.$year.'$month='.$month.'$'.implode('$', $attributes).'---'.($updateOk?'Ok':('Faild'.json_encode($error))).'---'."\r\n";
                }
                if ($updateOk) {
                    $updateItems++;
                }
                $month++;
            }
        }

        return $updateItems;
    }

    /**
     * 返回统计信息所需的属性 ( @link $this->processReportCostMonth() )
     * @param <int> $city_id            城市id
     * @param <int> $year               年份
     * @param <int> $month              月份
     * @return <array>                  属性数组
     * @author liuxiaobo
     * @since 2014-1-6
     */
    public function getAttributesForProcessPeport($city_id, $year, $month) {
        $attributes = array();      //需要修改的参数
        //消费总额  和  订单数量
        $outData = $this->allReportCostMonth($city_id, $year, $month, 'out');
        $attributes['vip_cost_sum_month'] = $outData['sum_out_amount'];
        $attributes['vip_order_count_month'] = $outData['sum_out_count'];

        //充值总额
        $inData = $this->allReportCostMonth($city_id, $year, $month, 'in');
        $attributes['recharge_month'] = $inData['sum_in_amount'];

        //新增vip
        $newVipData = $this->vipCount($city_id, $year, $month, 'new');
        $attributes['vip_new_count'] = $newVipData['count_new_vip'];

        //vip总数
        $countVipData = $this->vipCount($city_id, $year, $month, 'sum');
        $attributes['vip_count_month'] = $countVipData['count_sum_vip'];

        //客户总数
        $countCustomerData = $this->allCustomerCount($city_id, $year, $month);
        $attributes['customer_count_month'] = $countCustomerData['customer_count_month'];

        $allOrderInfo = $this->allOrderInfo($city_id, $year, $month);
        //所有订单的总单数
        $attributes['all_order_count_month'] = $allOrderInfo['all_cost_count_month'];

        //所有订单的总金额
        $attributes['all_cost_sum_month'] = (int)$allOrderInfo['all_cost_sum_month'];

        return $attributes;
    }

    /**
     * 某一城市($city > 0)或所有城市($city == 0)  在 $year 年 $month 月 的 vip数量情况
     * @param <int> $city
     * @param <int> $year
     * @param <int> $month
     * @param <string> $type            金额变化类型（new : 新增vip数量 ； sum : vip总数）
     * @return <array> if($type=='new'){return array('count_new_vip'=>新增vip数量)}; if($type=='sum'){return array('count_sum_vip'=>vip总数)}
     * @author liuxiaobo
     * @since 2014-1-6
     */
    public function vipCount($city, $year, $month, $type = 'new') {
        $year = $year === null ? date('Y') : $year;
        $month = $month === null ? date('Y') : $month;
        $startTime = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, 1, $year));
        $endTime = date('Y-m-d H:i:s', mktime(0, 0, 0, $month + 1, 1, $year));
        $where = 'created >= :start_time AND created < :end_time';
        $params = array(
            ':start_time' => strtotime($startTime),
            ':end_time' => strtotime($endTime),
        );
        if ($city != 0) {
            $where .= ' AND city_id = :city';
            $params[':city'] = $city;
        }

        if ($type == 'new') {
            $data = $this->allVipNew($where, $params);
        } else if ($type == 'sum') {
            $data = $this->allVipSum($where, $params);
        } else {
            throw new CHttpException(400, '参数不服要求');
        }
        return $data;
    }

    private function allVipNew($where, $params) {
        $data = array(
            'count_new_vip' => 0,
        );
        $command = Yii::app()->db_finance->createCommand()
                ->select('COUNT( 1 ) count_new_vip')
                ->from(Vip::model()->tableName())
                ->where($where);
        $query = $command->queryRow(TRUE, $params);
        if ($query) {
            $data['count_new_vip'] = empty($query['count_new_vip']) ? 0 : abs($query['count_new_vip']);
        }
        return $data;
    }

    private function allVipSum($where, $params) {
        $params[':start_time'] = 0;         //开始时间为最早
        $data = array(
            'count_sum_vip' => 0,
        );
        $command = Yii::app()->db_finance->createCommand()
                ->select('COUNT( 1 ) count_sum_vip')
                ->from(Vip::model()->tableName())
                ->where($where);
        $query = $command->queryRow(TRUE, $params);
        if ($query) {
            $data['count_sum_vip'] = empty($query['count_sum_vip']) ? 0 : abs($query['count_sum_vip']);
        }
        return $data;
    }

    /**
     * 获取订单总数 和 订单总金额
     * @param <int> $city
     * @param <int> $year
     * @param <int> $month
     * @return <array>  $result = array('all_cost_sum_month' => 0,'all_cost_count_month' => 0);
     * @author liuxiaobo
     * @since 2014-1-21
     */
    public function allOrderInfo($city, $year, $month){
        $date = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
        $result = BAccount::model()->getAccountsByMonth($date, $city);
        return $result;
    }

    /**
     * 获取客户总数
     * @param type $city
     * @param type $year
     * @param type $month
     * @return type
     */
    public function allCustomerCount($city, $year, $month) {
        $year = $year === null ? date('Y') : $year;
        $month = $month === null ? date('Y') : $month;
        $endTime = date('Y-m-d H:i:s', mktime(0, 0, 0, $month + 1, 1, $year));
        $where = 'create_time < :end_time';
        $params = array(
            ':end_time' => $endTime,
        );
        if ($city != 0) {
            $where .= ' AND city_id = :city';
            $params[':city'] = $city;
        }

        $data = array(
            'customer_count_month' => 0,
        );
        $command = Yii::app()->db_readonly->createCommand()
                ->select('COUNT( 1 ) customer_count_month')
                ->from(CustomerMain::model()->tableName())
                ->where($where);
        $query = $command->queryRow(TRUE, $params);
        if ($query) {
            $data['customer_count_month'] = empty($query['customer_count_month']) ? 0 : abs($query['customer_count_month']);
        }
        return $data;
    }

}
