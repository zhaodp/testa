<?php
/**
 * php protected/yiic bonus DriverBonusByDay    之前的数据
 * php protected/yiic bonus DriverBonusByYesterDay    每天的数据
 * @author leo
 *
 */

class bonusCommand extends LoggerExtCommand 
{
    /**
     * 更新从2012-10-1至今自绑自销及当天绑定当天消息数目
     * 该action为上线后仅运行一次，
     *
     */
    public function actionBBonus()
    {
        $arr=array();
        $arr['phone']='15210268972';
        $arr['bonus_use_limit']=5;
        //$bonus=BBonus::model()->getCustomerBonus($arr);
        //echo serialize($bonus);

        echo '5.3.1' < BonusCode::APP_VER ?0:1;
    }


    /**
     * 更新从2012-10-1至今自绑自销及当天绑定当天消息数目
     * 该action为上线后仅运行一次，
     *
     */
    public function actionBonusBind()
    {
        $phone='15210268972';
        $bonus_sn='1009839819';

        $bonus=BonusLibrary::model()->BonusBinding($bonus_sn,$phone,0,0,1);
        echo serialize($bonus);
    }

    /**
     * 更新从2012-10-1至今自绑自销及当天绑定当天消息数目
     * 该action为上线后仅运行一次，
     *
     */
    public function actionDriverBonusByDay()
    {
        // 开始时间
        $cdate = strtotime('2012-10-1');
        $now = time() - 86400;
        $start = $now;
        $cdateArr = array();
        while ($start > $cdate) {
            $cdateArr[] = date("Y-m-d", $start);
            $start = $start - 86400;
        }
        // 所有优惠码
        $sql = "SELECT DISTINCT bonus_sn FROM t_customer_bonus WHERE bonus_type_id=8 ORDER BY id DESC";
        $command = Yii::app()->db_finance->createCommand($sql);
        $allBonus = $command->queryAll();
        $command->reset();
        if (is_array($allBonus) && count($allBonus)) {
            foreach ($allBonus as $val) {
                $bonus = $val['bonus_sn'];
                foreach ($cdateArr as $date) {
                    $data = $this->getData($bonus, $date);
                    if ($data['bind_count'] > 0 || $data['used_count'] > 0 || $data['bonus'] > 0 || $data['consumption_day'] > 0 || $data['bind_self'] > 0) {
                        $_result = $this->updateByOneDay($bonus, $data, $date);
                        echo $bonus . '---' . $date . '---' . intval($_result) . "\n";
                    }
                }
            }
            echo "end";
        }
    }


    /**
     * 统计昨天昨日，绑定数、使用数、消费数、自绑自销、当天绑定当天消费数目
     * 运行时间：每日07：00后
     * php yiic bonus DriverBonusByYesterDay
     */
    public function actionDriverBonusByYesterDay()
    {
		$startTime = strtotime(date("Y-m-d 07:00:00", strtotime("-1 day")));
        $endTime	= strtotime(date("Y-m-d 07:00:00"));
        $date = date("Y-m-d", time());
        // 得到所有城市
        $DcityPrefix = Dict::model()->findAll(array(
            'condition' => 'dictname=:dictname',
            'params' => array(
                ':dictname' => 'bonus_city'
            )
        ));
        $cityPrefix = array();
        foreach ($DcityPrefix as $item) {
            $cityPrefix [$item->code] = $item->name;
        }

        $error_occured = false;
        // 期限内的优惠码
        $allBonus = array();
        $command = Yii::app()->db_finance->createCommand();
        $command->select(' bonus_sn')->from('t_customer_bonus')->where(' created >=' . $startTime . ' and created<' . $endTime . ' and bonus_type_id=8')->group('bonus_sn')->order('id desc');
        $allBonus = $command->queryAll();
        $command->reset();
        if (!empty ($allBonus)) {
            foreach ($allBonus as $bonus) {
                if ($this->driverBonusByYesterDayProcessed($bonus['bonus_sn']) === false) {
                    $data = $this->getData($bonus['bonus_sn'], $date);
                    if ($data['bind_count'] > 0 || $data['used_count'] > 0 || $data['bonus'] > 0 || $data['consumption_day'] > 0 || $data['bind_self'] > 0) {
                        if ($this->updateByOneDay($bonus ['bonus_sn'], $data, $date) === false) {
                            $error_occured = true;
                        } else if ($this->markDriverBonusByYesterDayProcessed($bonus['bonus_sn']) !== true){
                            EdjLog::info('markDriverBonusByYesterDayProcessed failed');
                        }
                    }
                } else {
                    EdjLog::info($this->getDriverBonusByYesterDayCheckingKey($bonus['bonus_sn']).' has already been processed.');
                }
            }
        }

        return $error_occured ? -1 : 0;
    }

    private function getDriverBonusByYesterDayCheckingKey($coupon)
    {
        $namespace = 'COUPON';
        $prefix = date('Y-m-d');

        return "$namespace|$prefix|driverbonusbyyesterday|$coupon";
    }

    private function driverBonusByYesterDayProcessed($coupon)
    {
        if (empty($coupon)) {
            return false;
        }

        return RedisHAProxy::model()->get($this->getDriverBonusByYesterDayCheckingKey($coupon)) !== false;
    }

    private function markDriverBonusByYesterDayProcessed($coupon)
    {
        if (empty($coupon)) {
            return false;
        }

        return RedisHAProxy::model()->set($this->getDriverBonusByYesterDayCheckingKey($coupon), 1, 24*60*60);
    }

    /**
     * 获得某个bonus某天的数据
     */
    public function getData($bonus, $date)
    {
        $driver = CustomerBonus::getDriverByBonus($bonus);
        //绑定数
        $data['bind_count'] = $this->getBonusBindByOneDay($bonus, $date);
        //使用数
        $data['used_count'] = $this->getUsedByOneDay($bonus, $date);
        //收入金额
        $data['bonus'] = $this->getBonusByOneDay($bonus, $date);
        //当天绑定当天消费
        $data['consumption_day'] = $this->getConsumptionByOneDay($bonus, $date);
        //自绑自消
        $data['bind_self'] = $this->getBindSelfByOneDay($bonus, $date);
        $data['driver_id'] = $driver['id'];
        $data['name'] = $driver['name'];
        $data['city_id'] = $driver['city_id'];
        $data['bonus_code'] = $bonus;
        $data['created'] = $date . ' 00:00:00';
        return $data;
    }

    /**
     * 获得指定某天开始结束时间戳
     */
    public static function getDateStartAndEnd($date)
    {
        /*
        $startTime = strtotime ( $date . ' 00:00:00' );
        $endTime = $startTime + (60 * 60 * 24);
        */

        $endTime = strtotime(date('Y-m-d 07:00:00', strtotime($date)));
        $startTime = $endTime - 86400;
        return array(
            'start' => $startTime,
            'end' => $endTime,
        );
    }

    /**
     * 查询某天内一个优惠券被绑定次数
     */
    public function getBonusBindByOneDay($bonus, $date)
    {
        $date_arr = self::getDateStartAndEnd($date);
        $command = Yii::app()->db_finance->createCommand();
        $sql = "select count(1) as num from t_customer_bonus where created >= {$date_arr['start']} and created<{$date_arr['end']} and bonus_sn='{$bonus}'";
        //$command->select ( ' count(1) as num ' )->from ( 't_customer_bonus' )->where ( ' created >= ' . $date_arr['start'] . ' and created< ' . $date_arr['end'] . ' and bonus_sn=' . $bonus );
        $command = Yii::app()->db_finance->createCommand($sql);
        $bindNum = $command->queryScalar();
        $command->reset();
        return intval($bindNum);
    }

    /**
     * 查询某天使用数量
     */
    public function getUsedByOneDay($bonus, $date)
    {
        $driver = CustomerBonus::getDriverByBonus($bonus);
        $date_arr = self::getDateStartAndEnd($date);
        $splitDate = explode('-', $date);
        $month = sprintf("%02d", $splitDate [1]);
        if ($splitDate [0] == '2012' || $month < 3) {
            $table = 't_employee_account_' . $splitDate [0] . $month;
            $command = Yii::app()->db_finance->createCommand();
            $command->select('count(1) as used')->from($table)->where('type = 9 and user=:driver_id and created >=' . $date_arr['start'] . ' and created<' . $date_arr['end'], array(
                ':driver_id' => $driver['id']
            ));
            $used = $command->queryScalar();
            $command->reset();
        } else {
            // 总表
            $table = 't_employee_account';
            $command = Yii::app()->db_finance->createCommand();
            $command->select('count(1) as used')->from($table)->where('type = 9 and user="' . $driver['id'] . '" and created >=' . $date_arr['start'] . ' and created<' . $date_arr['end']);
            $used = $command->queryScalar();
            $command->reset();
        }
        return $used;
    }

    /**
     * 每天收入金额
     */
    public function getBonusByOneDay($bonus, $date)
    {
        $driver = CustomerBonus::getDriverByBonus($bonus);
        $date_arr = self::getDateStartAndEnd($date);
        // 解析从哪个表读取数据
        $splitDate = explode('-', $date);
        $month = sprintf("%02d", $splitDate [1]);
        // 读出使用情况
        // 分表
        if ($splitDate [0] == '2012' || $month < 3) {
            $table = 't_employee_account_' . $splitDate [0] . $month;
            $command = Yii::app()->db_finance->createCommand();
            $command->select('SUM(cast) as cast')->from($table)->where('type = 9 and user="' . $driver['id'] . '" and created >=' . $date_arr['start'] . ' and created<' . $date_arr['end']);
            $cast = $command->queryScalar();
            $command->reset();
        } else {
            // 总表
            $table = 't_employee_account';
            $command = Yii::app()->db_finance->createCommand();
            $command->select('SUM(cast) as cast')->from($table)->where('type = 9 and user="' . $driver['id'] . '" and created >=' . $date_arr['start'] . ' and created<' . $date_arr['end']);
            $cast = $command->queryScalar();
            $command->reset();
        }
        return $cast;
    }

    /**
     *  按天获得司机当天绑定当天消费总数
     */
    public function getConsumptionByOneDay($bonus, $date)
    {
        $date_arr = self::getDateStartAndEnd($date);
        $sql = "select count(*) as consumption_day from t_customer_bonus where created>='{$date_arr['start']}' and created<'{$date_arr['end']}' and updated>='{$date_arr['start']}' and updated<'{$date_arr['end']}' and bonus_sn={$bonus}";
        $command = Yii::app()->db_finance->createCommand($sql);
        $consumption_day = $command->queryScalar();
        $command->reset();
        return intval($consumption_day);
    }

    /**
     *  按天获得自绑自消数目
     */
    public function getBindSelfByOneDay($bonus, $date)
    {
        $date_arr = self::getDateStartAndEnd($date);
        $driver = CustomerBonus::getDriverByBonus($bonus);

        $sql = "SELECT order_id FROM t_customer_bonus WHERE created >= {$date_arr['start']} and created< {$date_arr['end']} and bonus_sn={$bonus} and order_id>0";
        $command = Yii::app()->db_finance->createCommand($sql);
        $order_info = $command->queryAll();
        $command->reset();

	    if(empty($order_info)) {
	       return 0;
	    }

	    $order_ids = array();
	    foreach($order_ids as $item) {
	        $order_ids[] = $item['order_id'];
	    }
        if($driver&&isset($driver['id'])&& !empty($order_ids)){
            $bind_self_count = Order::model()->getCount_OrderIn_DriverID($order_ids, $driver['id']);
        }else{
            $bind_self_count=0;
        }

        return intval($bind_self_count);
    }

    /**
     * 更新某天数据
     */
    public function updateByOneDay($bonus, $data, $date)
    {
        $model = DriverBonusRankReport::model()->find('bonus_code=:bonus_sn AND created=:created', array(
            ':bonus_sn' => $bonus,
            ':created' => $date,
        ));
        $is_new_record = false;
        if (!$model) {
            $model = new DriverBonusRankReport();
            $is_new_record = true;
        }
        if (isset($data['driver_id']))
            $model->driver_id = $data['driver_id'];
        if (isset($data['name']))
            $model->name = $data['name'];
        if (isset($data['city_id']))
            $model->city_id = $data['city_id'];
        if (isset($data['bonus_code']))
            $model->bonus_code = $data['bonus_code'];
        if (isset($data['bonus']))
            $model->bonus = is_numeric($data['bonus']) ? $data['bonus'] : 0;
        if (isset($data['bind_count']))
            $model->bind_count = $data['bind_count'];
        if (isset($data['used_count']))
            $model->used_count = $data['used_count'];
        if (isset($data['consumption_day']))
            $model->consumption_day = $data['consumption_day'];
        if (isset($data['bind_self']))
            $model->bind_self = $data['bind_self'];
        if (isset($data['created']))
            $model->created = $data['created'];
        return $model->save(false);
    }

    // 绑定数
    public function getBonusBindByDay($dateArr, $bonus, $city)
    {
        // 得到该优惠码城市和所属司机
        $city_id = substr($bonus, 0, 2);
        $driver_id = substr($bonus, 2, 4);
        $driver_id = $city [$city_id] . $driver_id;
        $driver = Driver::getProfile($driver_id);
        if ($driver_id) {
            foreach ($dateArr as $date) {
                $startTime = strtotime($date . ' 00:00:00');
                $endTime = $startTime + (60 * 60 * 24);

                // 查询该月该优惠码的绑定情况
                $command = Yii::app()->db_finance->createCommand();
                $command->select(' count(1) as num ')->from('t_customer_bonus')->where(' created >= ' . $startTime . ' and created< ' . $endTime . ' and bonus_sn=' . $bonus);
                $bindNum = $command->queryScalar();
                $command->reset();
                if ($bindNum != 0) {
                    // 更新当天数据库

                    $model = DriverBonusRankReport::model()->find('bonus_code=:bonus_sn AND created=:created', array(
                        ':bonus_sn' => $bonus,
                        ':created' => $date . ' 00:00:00'
                    ));
                    if ($model) {
                        // 更新
                        $data = $model->attributes;
                        $data ['bind_count'] = $bindNum;
                        $model->attributes = $data;
                        $model->update();
                    } else {
                        // 插入
                        $insertModel = new DriverBonusRankReport ();
                        $data = array();
                        $data ['driver_id'] = $driver_id;
                        $data ['name'] = $driver->name;
                        $data ['city_id'] = $city_id;
                        $data ['bonus_code'] = $bonus;
                        $data ['bonus'] = 0;
                        $data ['bind_count'] = $bindNum;
                        $data ['used_count'] = 0;
                        $data ['consumption_day'] = 0;
                        $data ['bind_self'] = 0;
                        $data ['created'] = $date . ' 00:00:00';
                        $insertModel->attributes = $data;
                        $insertModel->insert();
                    }

                }
            }

        }
    }

    // 使用数量
    public function getBonusUsedByDay($dateArr, $bonus, $city)
    {
        $a = 0;
        // 解析司机ID
        $city_id = substr($bonus, 0, 2);
        $driver_id = substr($bonus, 2, 4);
        $driver_id = $city [$city_id] . $driver_id;
        $driver = Driver::getProfile($driver_id);
        if ($driver) {
            foreach ($dateArr as $date) {
                $startTime = strtotime($date . ' 00:00:00');
                $endTime = $startTime + (60 * 60 * 24);
                // 解析从哪个表读取数据
                $splitDate = explode('-', $date);
                $month = sprintf("%02d", $splitDate [1]);
                // 读出使用情况
                // 分表
                if ($splitDate [0] == '2012' || $month < 3) {
                    $table = 't_employee_account_' . $splitDate [0] . $month;
                    $command = Yii::app()->db_finance->createCommand();
                    $command->select('count(1) as used')->from($table)->where('type = 9 and user=:driver_id and created >=' . $startTime . ' and created<' . $endTime, array(
                        ':driver_id' => $driver_id
                    ));
                    $used = $command->queryScalar();
                    $command->reset();
                } else {
                    // 总表
                    $table = 't_employee_account';
                    $command = Yii::app()->db_finance->createCommand();
                    $command->select('count(1) as used')->from($table)->where('type = 9 and user=:driver_id and created >=' . $startTime . ' and created<' . $endTime, array(
                        ':driver_id' => $driver_id
                    ));
                    $used = $command->queryScalar();
                    $command->reset();
                }
                $a += $used;
                if ($used != 0) {
                    // 更新当天数据库
                    /* -----DB----- */
                    $model = DriverBonusRankReport::model()->find('bonus_code=:bonus_sn AND created=:created', array(
                        ':bonus_sn' => $bonus,
                        ':created' => $date . ' 00:00:00'
                    ));
                    if ($model) {
                        // 更新
                        $data = $model->attributes;
                        $data ['used_count'] = $used;
                        $model->attributes = $data;
                        $model->update();
                    } else {
                        // 插入
                        $insertModel = new DriverBonusRankReport ();
                        $data = array();
                        $data ['driver_id'] = $driver_id;
                        $data ['name'] = $driver['name'];
                        $data ['city_id'] = $city_id;
                        $data ['bonus_code'] = $bonus;
                        $data ['bonus'] = 0;
                        $data ['bind_count'] = 0;
                        $data ['used_count'] = $used;
                        $data ['consumption_day'] = 0;
                        $data ['bind_self'] = 0;
                        $data ['created'] = $date . ' 00:00:00';
                        $insertModel->attributes = $data;
                        $insertModel->insert();
                    }
                    /* -----DB END----- */
                }
            }
            //echo "\n shiyong：".$a;
        }

    }

    // 钱
    public function getBonusByDay($dateArr, $bonus, $city)
    {
        $a = 0;
        // 解析司机ID
        $city_id = substr($bonus, 0, 2);
        $driver_id = substr($bonus, 2, 4);
        $driver_id = $city [$city_id] . $driver_id;
        $driver = Driver::getProfile($driver_id);
        if ($driver) {
            foreach ($dateArr as $date) {
                $startTime = strtotime($date . ' 00:00:00');
                $endTime = $startTime + (60 * 60 * 24);
                // 解析从哪个表读取数据
                $splitDate = explode('-', $date);
                $month = sprintf("%02d", $splitDate [1]);
                // 读出使用情况
                // 分表
                if ($splitDate [0] == '2012' || $month < 3) {
                    $table = 't_employee_account_' . $splitDate [0] . $month;
                    $command = Yii::app()->db_finance->createCommand();
                    $command->select('SUM(cast) as cast')->from($table)->where('type = 9 and user=:driver_id and created >=' . $startTime . ' and created<' . $endTime, array(
                        ':driver_id' => $driver_id
                    ));
                    $cast = $command->queryScalar();
                    $command->reset();
                } else {
                    // 总表
                    $table = 't_employee_account';
                    $command = Yii::app()->db_finance->createCommand();
                    $command->select('SUM(cast) as cast')->from($table)->where('type = 9 and user=:driver_id and created >=' . $startTime . ' and created<' . $endTime, array(
                        ':driver_id' => $driver_id
                    ));
                    $cast = $command->queryScalar();
                    $command->reset();
                }
                $a += $cast;
                //echo $date.":".$splitDate [0].":".$table."\n";
                if ($cast != 0) {
                    // 更新当天数据库
                    /* -----DB----- */
                    $model = DriverBonusRankReport::model()->find('bonus_code=:bonus_sn AND created=:created', array(
                        ':bonus_sn' => $bonus,
                        ':created' => $date . ' 00:00:00'
                    ));
                    if ($model) {
                        // 更新
                        $data = $model->attributes;
                        $data ['bonus'] = $cast;
                        $model->attributes = $data;
                        $model->update();
                    } else {
                        // 插入
                        $insertModel = new DriverBonusRankReport ();
                        $data = array();
                        $data['driver_id'] = $driver_id;
                        $data['name'] = $driver['name'];
                        $data['city_id'] = $city_id;
                        $data['bonus_code'] = $bonus;
                        $data['bonus'] = 0;
                        $data['bind_count'] = 0;
                        $data['used_count'] = $cast;
                        $data['consumption_day'] = 0;
                        $data['bind_self'] = 0;
                        $data['created'] = $date . ' 00:00:00';
                        $insertModel->attributes = $data;
                        $insertModel->insert();
                    }
                    /* -----DB END----- */
                }
            }
            //echo "\n jiage".$a;
        }
    }

    // 昨天绑定数
    public function getBonusBindByYesterDay($startTime, $endTime, $bonus, $city)
    {

        // 得到该优惠码城市和所属司机
        $city_id = substr($bonus, 0, 2);
        $driver_id = substr($bonus, 2, 4);
        $driver_id = $city [$city_id] . $driver_id;
        $driver = Driver::getProfile($driver_id);
        $date = date('Y-m-d', $startTime);

        if ($driver) {

            // 查询昨天该优惠码的绑定情况
            $command = Yii::app()->db_finance->createCommand();
            $command->select(' count(1) as num ')->from('t_customer_bonus')->where(' created >= ' . $startTime . ' and created< ' . $endTime . ' and bonus_sn=' . $bonus);
            $bindNum = $command->queryScalar();
            $command->reset();
            if ($bindNum != 0) {
                // 更新当天数据库
                /* -----DB----- */
                $model = DriverBonusRankReport::model()->find('bonus_code=:bonus_sn AND created=:created', array(
                    ':bonus_sn' => $bonus,
                    ':created' => $date . ' 00:00:00'
                ));
                if ($model) {
                    // 更新
                    $data = $model->attributes;
                    $data ['bind_count'] = $bindNum;
                    $model->attributes = $data . ' 00:00:00';
                    $model->update();
                } else {
                    // 插入
                    $insertModel = new DriverBonusRankReport ();
                    $data = array();
                    $data ['driver_id'] = $driver_id;
                    $data ['name'] = $driver->name;
                    $data ['city_id'] = $city_id;
                    $data ['bonus_code'] = $bonus;
                    $data ['bonus'] = 0;
                    $data ['bind_count'] = $bindNum;
                    $data ['used_count'] = 0;
                    $data ['created'] = $date . ' 00:00:00';
                    $insertModel->attributes = $data;
                    $insertModel->insert();
                }
                /* -----DB END----- */
            }

        }
    }

    // 昨日使用数量
    public function getBonusUsedByYesterDay($startTime, $endTime, $bonus, $city)
    {
        $a = 0;
        // 解析司机ID
        $date = date('Y-m-d', $startTime);
        $city_id = substr($bonus, 0, 2);
        $driver_id = substr($bonus, 2, 4);
        $driver_id = $city [$city_id] . $driver_id;
        $driver = Driver::getProfile($driver_id);
        if ($driver) {
            $startTime = strtotime($date . ' 00:00:00');
            $endTime = $startTime + (60 * 60 * 24);
            // 解析从哪个表读取数据
            $splitDate = explode('-', $date);
            $month = sprintf("%02d", $splitDate [1]);
            // 读出使用情况


            // 总表
            $table = 't_employee_account';
            $command = Yii::app()->db_finance->createCommand();
            $command->select('count(1) as used')->from($table)->where('type = 9 and user=:driver_id and created >=' . $startTime . ' and created<' . $endTime, array(
                ':driver_id' => $driver_id
            ));
            $used = $command->queryScalar();
            $command->reset();

            $a += $used;
            if ($used != 0) {
                // 更新当天数据库
                /* -----DB----- */
                $model = DriverBonusRankReport::model()->find('bonus_code=:bonus_sn AND created=:created', array(
                    ':bonus_sn' => $bonus,
                    ':created' => $date . ' 00:00:00'
                ));
                if ($model) {
                    // 更新
                    $data = $model->attributes;
                    $data ['used_count'] = $used;
                    $model->attributes = $data;
                    $model->update();
                } else {
                    // 插入
                    $insertModel = new DriverBonusRankReport ();
                    $data = array();
                    $data ['driver_id'] = $driver_id;
                    $data ['name'] = $driver->name;
                    $data ['city_id'] = $city_id;
                    $data ['bonus_code'] = $bonus;
                    $data ['bonus'] = 0;
                    $data ['bind_count'] = 0;
                    $data ['used_count'] = $used;
                    $data ['created'] = $date . ' 00:00:00';
                    $insertModel->attributes = $data;
                    $insertModel->insert();
                }
                /* -----DB END----- */
            }

            //echo "\n shiyong：".$a;
        }

    }

    // 昨天钱
    public function getBonusByYesterDay($startTime, $endTime, $bonus, $city)
    {
        $a = 0;
        // 解析司机ID
        $date = date('Y-m-d', $startTime);
        $city_id = substr($bonus, 0, 2);
        $driver_id = substr($bonus, 2, 4);
        $driver_id = $city [$city_id] . $driver_id;
        $driver = Driver::getProfile($driver_id);
        if ($driver) {
            $startTime = strtotime($date . ' 00:00:00');
            $endTime = $startTime + (60 * 60 * 24);
            // 解析从哪个表读取数据
            $splitDate = explode('-', $date);
            $month = sprintf("%02d", $splitDate [1]);
            // 读出使用情况

            // 总表
            $table = 't_employee_account';
            $command = Yii::app()->db_finance->createCommand();
            $command->select('SUM(cast) as cast')->from($table)->where('type = 9 and user=:driver_id and created >=' . $startTime . ' and created<' . $endTime, array(
                ':driver_id' => $driver_id
            ));
            $cast = $command->queryScalar();
            $command->reset();

            $a += $cast;
            //echo $date.":".$splitDate [0].":".$table."\n";
            if ($cast != 0) {
                // 更新当天数据库
                /* -----DB----- */
                $model = DriverBonusRankReport::model()->find('bonus_code=:bonus_sn AND created=:created', array(
                    ':bonus_sn' => $bonus,
                    ':created' => $date . ' 00:00:00'
                ));
                if ($model) {
                    // 更新
                    $data = $model->attributes;
                    $data ['bonus'] = $cast;
                    $model->attributes = $data;
                    $model->update();
                } else {
                    // 插入
                    $insertModel = new DriverBonusRankReport ();
                    $data = array();
                    $data ['driver_id'] = $driver_id;
                    $data ['name'] = $driver->name;
                    $data ['city_id'] = $city_id;
                    $data ['bonus_code'] = $bonus;
                    $data ['bonus'] = 0;
                    $data ['bind_count'] = 0;
                    $data ['used_count'] = $cast;
                    $data ['created'] = $date . ' 00:00:00';
                    $insertModel->attributes = $data;
                    $insertModel->insert();
                }
                /* -----DB END----- */
            }

            //echo "\n jiage".$a;
        }
    }

    //昨日当天绑定当天消息
    public function getConsumptionByYesterDay($startTime, $endTime, $bonus, $city)
    {
        $date = date('Y-m-d', $startTime);
        $city_id = substr($bonus, 0, 2);
        $driver_id = substr($bonus, 2, 4);
        $driver_id = $city [$city_id] . $driver_id;
        $driver = Driver::getProfile($driver_id);
        if ($driver) {
            $sql = "select count(*) as consumption_day from t_customer_bonus where created>='{$startTime}' and created<'{$endTime}' and updated>='{$startTime}' and updated<'{$endTime}' and bonus_sn={$bonus}";
            $command = Yii::app()->db_finance->createCommand($sql);
            $consumption_day = $command->queryScalar();
            $command->reset();
            if ($consumption_day > 0) {
                // 更新当天数据库
                /* -----DB----- */
                $model = DriverBonusRankReport::model()->find('bonus_code=:bonus_sn AND created=:created', array(
                    ':bonus_sn' => $bonus,
                    ':created' => $date . ' 00:00:00'
                ));
                if ($model) {
                    // 更新
                    $data = $model->attributes;
                    $model->consumption_day = $consumption_day;
                    $model->update();
                } else {
                    // 插入
                    $insertModel = new DriverBonusRankReport ();
                    $data = array();
                    $data ['driver_id'] = $driver_id;
                    $data ['name'] = $driver->name;
                    $data ['city_id'] = $city_id;
                    $data ['bonus_code'] = $bonus;
                    $data ['bonus'] = 0;
                    $data ['consumption_day'] = $consumption_day;
                    $data ['bind_self'] = 0;
                    $data ['used_count'] = 0;
                    $data ['created'] = $date . ' 00:00:00';
                    $insertModel->attributes = $data;
                    $insertModel->insert();
                }
                /* -----DB END----- */
            }
        }
    }

    /**
     * 获取当月天数   然后循环跑数据
     * @param null $date
     * author mengtianxue
     * php yiic.php bonus CustomerBonusDate --date=2013-03-01
     */

    public function actionCustomerBonusDate($date = null)
    {
        if ($date !== null) {
            $days = date("t", strtotime($date));
            for ($d = 1; $d <= $days; $d++) {
                $day = date('Y-m', strtotime($date)) . "-" . $d;
                echo $day . "\n";
                $this->actionCustomerBouns($day);
            }
        }
    }


    /**
     * 统计每天的优惠劵绑定和消费记录
     * @param null $date 时间
     * author mengtianxue
     * php yiic.php bonus CustomerBonus --date=2013-03-01
     */
    public function actionCustomerBonus($date = null)
    {
        //默认是当天时间
        if ($date === null) {
            $start_time = date("Y-m-d 00:00:00", strtotime("-1 day"));
            $end_time = date("Y-m-d 23:59:59");
        } else {
            $start_time = $date . " 00:00:00";
            $end_time = $date . " 23:59:59";
        }

        // 获取每个城市的司机
        $city_list = Dict::items('city');

        foreach ($city_list as $city => $city_name) {
            $driverBonusArray = array();
            $city_id = $city;
            if ($city_id > 0) {
                // 查找每个司机发卡和返现
                $driver_list = Driver::model()->getDriverList($city_id, array(0, 1));
                foreach ($driver_list as $driver) {
                    $driver_id = $driver['driver_id'];
                    $bonus_sn = $this->getBonusCode($driver_id);
                    $driverBonusArray['bonus_sn'] = $bonus_sn;
                    $driverBonusArray['name'] = $driver['name'];
                    $driverBonusArray['driver_id'] = $driver_id;
                    $driverBonusArray['city_id'] = $city_id;

                    // 如果司机发卡和返现不为0 存储
                    $driverBonus = $this->getDriverBonus($bonus_sn, $start_time, $end_time);
                    $driverBonusArray['bonus_count'] = empty($driverBonus) ? 0 : $driverBonus;
                    $employeeAccountBonus = $this->getEmployeeAccountBonus($bonus_sn, $start_time, $end_time);
                    $driverBonusArray['used_count'] = empty($employeeAccountBonus) ? 0 : $employeeAccountBonus;
                    if ($driverBonus || $employeeAccountBonus) {
                        $driverBonusArray['amount'] = $driverBonusArray['used_count'] * 20;
                        $driverBonusArray['report_time'] = date('Ymd', strtotime($start_time));
                        $driverBonusArray['created'] = date('Y-m-d H:i:s');
                        $insertModel = new CustomerBonusReport ();
                        $insertModel->attributes = $driverBonusArray;
                        $insertModel->insert();
                        echo $driver_id . "存在\n";
                    } else {
                        echo $driver_id . "不存在\n";
                    }
                }
            }
        }
    }

    /**
     * 获取师傅的新客邀请码
     * @param $driver
     * @return string
     * author mengtianxue
     */
    public function getBonusCode($driver)
    {
        $city_prefix = substr($driver, 0, 2);
        $driver_num = substr($driver, 2);
        $bonus_city = $this->getBonusCity('bonus_city', $city_prefix);
        $bonus_sn = $bonus_city . $driver_num;
        return $bonus_sn;

    }

    /**
     * 获取城市id
     * @param $dictname
     * @param $name
     * @return string
     * author mengtianxue
     */
    public function getBonusCity($dictname, $name)
    {
        $city_list = Dict::model()->find(array(
            'select' => 'code',
            'condition' => 'dictname = :dictname and name = :name',
            'params' => array(
                ':dictname' => $dictname,
                ':name' => $name)
        ));

        if (empty($city_list)) {
            return '';
        }
        return $city_list->code;
    }

    /**
     * 根据优惠劵号码获取某个时间段的绑定总数
     * @param $bonus_sn
     * @param $start_time
     * @param $end_time
     * @return mixed
     * author mengtianxue
     */
    public function getDriverBonus($bonus_sn, $start_time, $end_time)
    {
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);

        $bonusCount = Yii::app()->db_finance->createCommand()
            ->select("count(1)")
            ->from("t_customer_bonus")
            ->where('bonus_sn = :bonus_sn and created between :start_time and :end_time',
                array(':bonus_sn' => $bonus_sn, ':start_time' => $start_time, ':end_time' => $end_time))
            ->queryScalar();
        return $bonusCount;

    }

    /**
     * 根据$bonus_sn 获取使用数据
     * @param $bonus_sn
     * @param $start_time
     * @param $end_time
     * @return mixed
     * author mengtianxue
     */
    public function getEmployeeAccountBonus($bonus_sn, $start_time, $end_time)
    {
        $start_time = strtotime($start_time);
        $end_time = strtotime($end_time);
        $table_date = date('Ym', $start_time);
        $table_name = ($table_date == date("Ym")) ? "t_employee_account" : "t_employee_account_" . $table_date;
        $usedCount = Yii::app()->db_finance->createCommand()
            ->select("count(1)")
            ->from($table_name)
            ->where('type = :type and created between :start_time and :end_time and comment LIKE :comment',
                array(':type' => EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN, ':start_time' => $start_time, ':end_time' => $end_time, ':comment' => "%$bonus_sn%"))
            ->queryScalar();
        return $usedCount;
    }


}
