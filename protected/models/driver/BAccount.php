<?php
/**
 * 司机账目逻辑
 * User: mtx
 * Date: 13-12-4
 * Time: 下午2:48
 * auther mengtianxue
 */

Yii::import('application.models.schema.driver.CarEmployeeAccount');

class BAccount extends CarEmployeeAccount
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableInstantiate($date)
    {
        if ($date) {
            CarEmployeeAccount::$table_name = $date;
        }
        $employee_account = new CarEmployeeAccount();
        return $employee_account;
    }

    /**
     * 账单信息
     * @param $params
     * @return CActiveDataProvider
     * @auther mengtianxue
     */
    public function getAccountBill($params)
    {
        $month = $params['month'];
        $model = $this->tableInstantiate($month);

        $criteria = new CDbCriteria;
        $criteria->compare('city_id', $params['city_id']);
        $criteria->compare('channel', $params['channel']);
        if (!empty($params['operator'])) {
            $criteria->addSearchCondition('operator', $params['operator']);
        }

        if (!empty($params['created'])) {
            $start_time = strtotime($params['created']);
            $end_time = $start_time + 86399;
            $criteria->addBetweenCondition('created', $start_time, $end_time);
        }

        return new CActiveDataProvider($model, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20
            ),
        ));
    }

    /**
     * 根据查询条件获取信息费总额
     * @param $params
     * @return array|mixed|null
     * @auther mengtianxue
     */
    public function getAccountCount($params)
    {
        $month = $params['month'];
        $model = $this->tableInstantiate($month);

        $criteria = new CDbCriteria;
        $criteria->select = "sum(cast) as cast";
        $criteria->compare('city_id', $params['city_id']);
        $criteria->compare('channel', $params['channel']);
        $criteria->compare('operator', $params['operator']);

        if (!empty($params['created'])) {
            $start_time = strtotime($params['created']);
            $end_time = $start_time + 86399;
            $criteria->addBetweenCondition('created', $start_time, $end_time);
        }
        $account = $model->find($criteria);
        return $account->cast;
    }


    /**
     * 获取订单详情
     * @param $params
     * @return array|CActiveRecord|mixed|null
     * @auther mengtianxue
     */
    public function getDailyAccount($params)
    {
        $month = $params['month'];
        $model = $this->tableInstantiate($month);

        $criteria = new CDbCriteria;
        $criteria->select = "FROM_UNIXTIME(created, '%Y-%m-%d %H:%i:%s') as created, user, cast, comment, operator";
        $criteria->compare('city_id', $params['city_id']);
        $criteria->compare('channel', $params['channel']);
        $criteria->compare('operator', $params['operator']);

        if (!empty($params['created'])) {
            $start_time = strtotime($params['created']);
            $end_time = $start_time + 86399;
            $criteria->addBetweenCondition('created', $start_time, $end_time);
        }
        $account = $model->findAll($criteria);
        return $account;
    }

    /**
     * 根据时间获取天总额
     * @param $daily
     * @param $city_id
     * @return array|mixed|null
     * @auther mengtianxue
     */
    public function getDailyCountAccount($daily = 0, $city_id = 0)
    {
        $params = array();
        //如果不传递参数，默认为当前
        if ($daily == 0) {
            $daily = date('Y-m-d');
        }

        $month = date('Ym', strtotime($daily));
        $model = $this->tableInstantiate($month);
        $criteria = new CDbCriteria;
        $criteria->select = "city_id, sum(cast) as cast";
        $criteria->addCondition('type != 0');

        $start_time = strtotime($daily);
        $end_time = $start_time + 86400;
        $criteria->addCondition('created >= :start_time and created < :end_time');
        $params[':start_time'] = $start_time;
        $params[':end_time'] = $end_time;
        if (!empty($city_id)) {
            $criteria->addCondition('city_id = :city_id');
            $params[':city_id'] = $city_id;
        }
        $criteria->params = $params;
        $account = $model->find($criteria);
        $cast = 0;
        if ($account) {
            $cast = $account->cast;
        }
        return $cast;
    }



    /**
     * 根据时间获取天VIP消费总额
     * @param $daily
     * @param $city_id
     * @return array|mixed|null
     * @auther mengtianxue
     */
    public function getDailyVipCountAccount($daily = 0, $city_id = 0)
    {
        $params = array();
        //如果不传递参数，默认为当前
        if ($daily == 0) {
            $daily = date('Y-m-d');
        }

        $month = date('Ym', strtotime($daily));
        $model = $this->tableInstantiate($month);
        $criteria = new CDbCriteria;
        $criteria->select = "city_id, sum(cast) as cast";
        $criteria->addCondition('type = 3');

        $start_time = strtotime($daily);
        $end_time = $start_time + 86400;
        $criteria->addCondition('created >= :start_time and created < :end_time');
        $params[':start_time'] = $start_time;
        $params[':end_time'] = $end_time;
        if (!empty($city_id)) {
            $criteria->addCondition('city_id = :city_id');
            $params[':city_id'] = $city_id;
        }
        $criteria->params = $params;
        $account = $model->find($criteria);
        $cast = 0;
        if ($account) {
            $cast = $account->cast;
        }
        return $cast;
    }

    /**
     * 获取订单的费用
     * @param $date_time
     * @param $order_id
     * @return array|CActiveRecord|mixed|null
     * @auther mengtianxue
     */
    public function getAccountByOrder_id($date_time, $order_id)
    {
        $month = date('Ym', strtotime($date_time));
        $model = $this->tableInstantiate($month);
        $criteria = new CDbCriteria;
        $criteria->select = "*";
        $criteria->addCondition('order_id = :order_id');
        $criteria->params = array(':order_id' => $order_id);
        $account = $model->findAll($criteria);
        return $account;
    }
    
    /**
     * 根据城市获取消费总金额、订单总数（$city_id=0时，代表所有城市）
     * @param <datetime> $date_time
     * @param <int> $city_id
     * @return <mixed>  array|false 
     * @author liuxiaobo
     * @since 2014-1-21
     */
    public function getAccountsByMonth($date_time, $city_id=0)
    {
        $result = array(
            'all_cost_sum_month' => 0,
            'all_cost_count_month' => 0,
        );
        $month = date('Ym', strtotime($date_time));
        $where = 'type = 0 ';
        $params = array();
        if($city_id > 0 && $cityPrefix = Dict::item('city_prefix', $city_id)){
            $min = $cityPrefix . '00001';
            $max = $cityPrefix . '99999';
            $where .= ' AND user >= :min AND user <= :max ';
            $params[':min'] = $min;
            $params[':max'] = $max;
        }
        //为了屏蔽抛出的错误（可能会存在找不到数据表的情况）
        try{
            $command = Yii::app()->db_finance->createCommand()
                    ->select('SUM( cast ) all_cost_sum_month, COUNT(1) all_cost_count_month')
                    ->from($this->tableInstantiate($month)->tableName())
                    ->where($where);
            $query = $command->queryRow(TRUE, $params);
            if($query){
                $result = $query;
            }
        }catch(Exception $e){
            //print_r($e->getMessage());
        }
        return $result;
    }

}





