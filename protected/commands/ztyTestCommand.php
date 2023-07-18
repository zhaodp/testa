<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-5
 * Time: 下午4:46
 * To change this template use File | Settings | File Templates.
 */
class ztyTestCommand extends CConsoleCommand {

    /**
     * 测试鉴权接口
     */
    public function actionGetContent($phone='15710010037', $usenum='1') {
        $model = new CarClub();
        $params = array(
            'phone' => $phone,
            'usenum' => $usenum
        );
        $r = $model->verify($params);
        var_dump($r);
    }

    /**
     * 测试司机接单后执行动作
     */
    public function actionPartnerOrderCreateSync($order_id) {
        $order_channel = Order::model()->getOrderChannel($order_id);
        if ($order_channel) {
            $partner = PartnerFactory::factory($order_channel);
            if ($partner instanceof AbstractPartner) {
                /*
                if (method_exists($partner,'afterOrderSaveHandler')) {
                    $partner->afterOrderSaveHandler($order_id);
                }
                */
                var_dump($partner->afterOrderSave($order_id));
            }
        }
    }

    /**
     * 测试司机报单后执行动作
     * @param $order_id
     */
    public function actionPartnerOrderCompleteSync($order_id) {
        $order_channel = Order::model()->getOrderChannel($order_id);
        if ($order_channel) {
            $partner = PartnerFactory::factory($order_channel);
            if ($partner instanceof AbstractPartner) {
                /*
                if (method_exists($partner,'completeOrderHandler')) {
                    $partner->completeOrderHandler($order_id);
                }
                */
                var_dump($partner->completeOrder($order_id));
            }
        }
    }

     /**
     * 测试司机销单后执行动作
     * @param $order_id
     */
    public function actionPartnerCancelCompleteSync($order_id) {
        $order_channel = Order::model()->getOrderChannel($order_id);
        if ($order_channel) {
            $partner = PartnerFactory::factory($order_channel);
            if ($partner instanceof AbstractPartner) {
                /*
                if (method_exists($partner,'cancelOrderHandler')) {
                    $partner->cancelOrderHandler($order_id);
                }
                */
                var_dump($partner->cancelOrder($order_id));
            }
        }
    }

    /**
     * 直接测试OrderProcess中司机接单后执行方法
     * @param $order_id
     */
    public function actionTestAfterCreateOrder($order_id) {
        $process = new QueueProcess();
        $r = $process->partner_order_create_sync($order_id);
        var_dump($r);
    }

    /**
     * 直接测试OrderProcess中司机报单后执行方法
     * @param $order_id
     */
    public function actionTestAfterCompleteOrder($order_id) {
        $process = new QueueProcess();
        $r = $process->partner_order_complete_sync($order_id);
        var_dump($r);
    }


    /**
     * 直接测试OrderProcess中司机销单后执行方法
     * @param $order_id
     */
    public function actionTestAfterCancelOrder($order_id) {
        $process = new QueueProcess();
        $r = $process->partner_order_cancel_sync($order_id);
        var_dump($r);
    }

    public function actionTestBonus() {
        $b = '15393';
        $p = '13911682480';
        $r = BonusLibrary::model()->merchantsBind($b, $p);
        var_dump($r);
    }

    /*平安测试*/
    public function actionPVerify() {
        $params = array(
            'id_card' => '0037',
            'password' => '327688',
        );
        $ping_an = new PingAn();
        var_dump($ping_an->verify($params));
    }

    public function actionVerify() {
        $pingan = new PingAn();
        $params = array(
            'id_card' => '0037',
            'password' => '584384',
        );
        $result = $pingan->verify($params);
        var_dump($result);
    }

    public function actionGetOrderId() {
        $pingan = new PingAn();
        $result = $pingan->getPartnerOrderId('0037', '012580', '2416616', 1);
        var_dump($result);
    }

    public function actionOrderValid() {
        $pingan = new PingAn();
        //$result = $pingan->decrypt('b0257ff772f7bec0001608554af3c3350edde676f684bcefe62f9b57b20940667e81799ea51a4546', '234ade3f4a3dc6ba');
        $result = $pingan->OrderValid('2416616', '20131114114816217266', 'TAS');
        var_dump($result);
    }

    public function actionTestDriverSave($order_id){
        $channel = Order::model()->getOrderChannel($order_id);
        if ($channel) {
            $partner = PartnerFactory::factory($channel);
            if ($partner instanceof AbstractPartner) {
                if (method_exists($partner,'afterOrderSaveHandler')) {
                    $partner->afterOrderSaveHandler($order_id);
                }
            }
        }
    }

    public function actionTest() {
        $ping = new PingAn();
        $result = 'b0257ff772f7bec0ad8ea5ca740bafcfd8f71feb360f9d0c079bbb5e1120d3ad7e81799ea51a4546&da9581784824ab65749f8cb94e5ba3bd';
        $num_tmp = explode('&', $result);
        $num_tmp = $ping->decrypt($num_tmp[0], '234ade3f4a3dc6ba');
        $msg = explode('@', $num_tmp);
        unset($msg[0]);
        $return = array(
            'status' => 1,
            'msg' => $msg
        );
        var_dump($return);
    }

    public function actionBonusBing() {
        var_dump(BonusLibrary::model()->merchantsBind('60869', '15710010037'));
    }

    public function actionGetDriverStatus($driver_id) {
        $model = DriverStatus::model()->get($driver_id);
    }

    public function actionTestOnlinExam() {
        $id_card= '41010319791027651X';
        $model = new Question;
        $arrExam = $model->getQuestionList($id_card, '18', 0);
        $exam_array = array();
        if (!empty($arrExam)) {
            foreach ($arrExam as $_question) {
                $_tmp['id'] = $_question->id;
                $_tmp['title'] = $_question->title;
                $_tmp['contents'] = json_decode($_question->contents, true);
                $_tmp['correct'] = $_question->correct;
                $_tmp['track'] = $_question->track;
                $_tmp['type'] = $_question->type;
                $exam_array[$_question->id] = $_tmp;
            }
        }
        $q_num = DriverExamTest::model()->getQuestionID($id_card);
        $exam_list = array_filter(explode(',',$q_num));
        var_dump($exam_list, $exam_array);
    }

    public function actionTestExam() {
        $model = new DriverExamTest();
        var_dump($model->delExamTest('210283198610241934'));
    }

    public function actionResetDriverData($date = '2013-11-01') {
        $model = Driver::model()->findAll('created >= "'.$date.'"');
        if ($model) {
            foreach ($model as $m) {
                if ($m->id_card) {
                    $recruiment_model = DriverRecruitment::model()->find('id_card=:id_card', array(':id_card'=>$m->id_card));
                    if ($recruiment_model) {
                        if ($recruiment_model->status !=4) {
                            $recruiment_model->status = 4;
                            $recruiment_model->driver_id = $m->user;
                            echo $m->user.'---'.$recruiment_model->save()."\n";
                        }
                    }
                }
            }
        }
    }

    public function actionCheckIdCard($id_card) {
        var_dump(Common::checkIdCard($id_card));
        var_dump(Common::getAgeByIdCard($id_card));
        //var_dump(Common::getDriverYear('2008-12-07'));
        var_dump(Common::getBirthDayByIdCard($id_card));
    }

    public function actionFetchData() {
        $limit = 100;
        $pg = 1;
        $count =  Yii::app()->db_readonly->createcommand("select count(*) from t_driver where mark!=3 and city_id in (1,4,7,8,14)")->queryScalar();
        $page_count = ceil($count/$limit);
        $age_arr = array();
        $driver_arr = array();
        $nu = 0;
        while($pg<=$page_count) {
            $start = ($pg-1)*$limit;
            $sql = "select user, id_card, license_date,city_id from t_driver where mark!=3 and city_id in (1,4,7,8,14)limit {$start}, {$limit}";
            $model = Yii::app()->db_readonly->createcommand($sql)->queryAll();
            if (is_array($model) && count($model)) {
                foreach ($model as $m) {
                    $city_id = $m['city_id'];
                    $age = Common::getAgeByIdCard($m['id_card']);
                    $age = intval($age);
                    echo $age .'--'. $m['user'] ."\n";
                    $i = 20;
                    while ($i<=60) {
                        if ($age>=$i && $age<$i+5) {
                            $age_arr[$city_id][$i] = isset($age_arr[$city_id][$i]) ? $age_arr[$city_id][$i] : 0;
                            $age_arr[$city_id][$i] = intval($age_arr[$city_id][$i]+1);
                            /*
                            $age_arr[$i]['order'] = isset($age_arr[$i]['order']) ? $age_arr[$i]['order'] : 0;
                            $_order = DriverExt::model()->find('driver_id=:driver_id',
                                array (
                                    ':driver_id' => $m['user']
                                )
                            );
                            if ($_order) {
                                $age_arr[$i]['order'] = intval($age_arr[$i]['order'] + $_order->all_count);
                            }
                            */
                        }
                        $i = intval($i+5);
                    }
                    $nu++;
                    echo $nu."\n";
                }
            }
            $pg++;
        }
        var_dump($nu);
        var_dump($age_arr);
    }

    public function actionFetchData2() {
        $limit = 100;
        $pg = 1;
        $count =  Yii::app()->db_readonly->createcommand("select count(*) from t_driver where mark!=3 and city_id in (1,4,7,8,14)")->queryScalar();
        $page_count = ceil($count/$limit);
        $age_arr = array();
        $nu = 0;
        while($pg<=$page_count) {
            $start = ($pg-1)*$limit;
            $sql = "select user, id_card, license_date,city_id from t_driver where mark!=3 and city_id in (1,4,7,8,14) limit {$start}, {$limit}";
            $model = Yii::app()->db_readonly->createcommand($sql)->queryAll();
            if (is_array($model) && count($model)) {
                foreach ($model as $m) {
                    $city_id = $m['city_id'];
                    //$age_arr['num'] = isset($age_arr['num']) ? $age_arr['num'] : 0;
                    $age = Common::getDriverYear($m['license_date']);
                    $age = intval($age);
                    echo $age .'--'. $m['user'] ."\n";
                    if ($age >=5 && $age <8) {

                        $age_arr[$city_id]['5'] = isset($age_arr[$city_id]['5']) ? intval($age_arr[$city_id]['5']) : 0;
                        $age_arr[$city_id]['5'] = $age_arr[$city_id]['5']+1;
                        /*
                        $sms = $this->getNum($m['user']);
                        $age_arr['5']['order'] = isset($age_arr['5']['order']) ? intval($age_arr['5']['order']) : 0;
                        $age_arr['5']['order'] = $age_arr['5']['order']+intval($sms);
                        */
                    } else if ($age >=8 && $age <12) {
                        $age_arr[$city_id]['8'] = isset($age_arr[$city_id]['8']) ? intval($age_arr[$city_id]['8']) : 0;
                        $age_arr[$city_id]['8'] = $age_arr[$city_id]['8']+1;
                        /*
                        $sms = $this->getNum($m['user']);
                        $age_arr['8']['order'] = isset($age_arr['8']['order']) ? intval($age_arr['8']['num']) : 0;
                        $age_arr['8']['order'] = $age_arr['8']['order']+$sms;
                        */
                    } else if ($age >=12 && $age <15) {
                        $age_arr[$city_id]['12'] = isset($age_arr[$city_id]['12']) ? intval($age_arr[$city_id]['12']) : 0;
                        $age_arr[$city_id]['12'] = $age_arr[$city_id]['12']+1;
                        /*
                        $sms = $this->getNum($m['user']);
                        $age_arr['12']['order'] = isset($age_arr['12']['order']) ? intval($age_arr['12']['num']) : 0;
                        $age_arr['12']['order'] = $age_arr['12']['order']+$sms;
                        */
                    } else if ($age >=15 && $age <20) {
                        $age_arr[$city_id]['15'] = isset($age_arr[$city_id]['15']) ? intval($age_arr[$city_id]['15']) : 0;
                        $age_arr[$city_id]['15'] = $age_arr[$city_id]['15']+1;
                        /*
                        $sms = $this->getNum($m['user']);
                        $age_arr['15']['order'] = isset($age_arr['15']['order']) ? intval($age_arr['15']['num']) : 0;
                        $age_arr['15']['order'] = $age_arr['15']['order']+$sms;
                        */
                    } else if ($age>=20) {
                        $age_arr[$city_id]['20'] = isset($age_arr[$city_id]['20']) ? intval($age_arr[$city_id]['20']) : 0;
                        $age_arr[$city_id]['20'] = $age_arr[$city_id]['20']+1;
                        /*
                        $sms = $this->getNum($m['user']);
                        $age_arr['20']['order'] = isset($age_arr['20']['order']) ? intval($age_arr['20']['num']) : 0;
                        $age_arr['20']['order'] = $age_arr['20']['order']+$sms;
                        */
                    }
                    $nu++;
                    echo $nu."\n";
                }
            }
            $pg++;
        }
        var_dump($nu);
        var_dump($age_arr);
    }

    public function getNum($driver_id) {
        $sql = "select count(*) from t_comment_sms where driver_id='{$driver_id}' and level=3 and sms_type=0 and created>='2013-09-01' and created<='2013-10-31'";
        $count =  Yii::app()->db_readonly->createcommand($sql)->queryScalar();
        return intval($count);
    }

    public function actionGetData($city_id, $use_date) {
        $date = CompanyKpiCommon::getMonthFirstAndLastDay($use_date);
        $begin_ts = strtotime($date[0].' 00:00:00');
        $end_ts = strtotime($date[0].' 23:59:59');
        $min_id_sql = "select min(order) FROM `t_order` WHERE city_id ={$city_id} AND `status` IN ( 1, 4 ) AND created >={$begin_ts} AND created <={$end_ts}";
        $max_id_sql = "select min(order) FROM `t_order` WHERE city_id ={$city_id} AND `status` IN ( 1, 4 ) AND created >={$begin_ts} AND created <={$end_ts}";
        $min_id = Yii::app()->db_readonly->createcommand($min_id_sql)->queryScalar();
        $max_id = Yii::app()->db_readonly->createcommand($max_id_sql)->queryScalar();
        while ($min_id < $max_id) {
            $_tmp_max_id = $min_id+100;
            $sql = "SELECT phone, count( * ) FROM `t_order` WHERE city_id ={$city_id} AND order_id>={$min_id} AND order_id<={$_tmp_max_id} AND `status` IN ( 1, 4 ) GROUP BY phone ORDER BY count( * ) DESC";
            $d = Yii::app()->db_readonly->createcommand($sql)->queryAll();
            if (is_array($d) && count($d)) {
            }
        }
    }

    public function actionGetData2() {
        $city_id = array(1,4,7,8,14);
        $month = array('08','09',10,11);
        $distance = array();
        foreach ($city_id as $c) {
            foreach ($month as $m) {
                $d = '2013'.$m;
                $date = CompanyKpiCommon::getMonthFirstAndLastDay($d);
                $f = strtotime($date[0].' 00:00:00');
                $e = strtotime($date[1].' 23:59:59');
                $sql_100 = "SELECT count(*) FROM `t_order` where city_id={$c} and distance>=100 and distance<200 and status in (1,4) and created>={$f} and created<{$e}";
                echo $c.'--'.$m.'--100公里--'.Yii::app()->db_readonly->createcommand($sql_100)->queryScalar()."\n";
                 $sql_200 = "SELECT count(*) FROM `t_order` where city_id={$c} and distance>=200 and distance<300 and status in (1,4) and created>={$f} and created<{$e}";
                echo $c.'--'.$m.'--200公里--'.Yii::app()->db_readonly->createcommand($sql_200)->queryScalar()."\n";
                 $sql_300 = "SELECT count(*) FROM `t_order` where city_id={$c} and distance>=300 and status in (1,4) and created>={$f} and created<{$e}";
                echo $c.'--'.$m.'--300公里--'.Yii::app()->db_readonly->createcommand($sql_300)->queryScalar()."\n";
            }

        }
    }

    public function actionGetData3() {
        $city_id = array(1,4,7,8,14);
        $month = array('08','09',10,11);
        $distance = array();
        foreach ($city_id as $c) {
            foreach ($month as $m) {
                $d = '2013'.$m;
                $date = CompanyKpiCommon::getMonthFirstAndLastDay($d);
                $f = strtotime($date[0].' 00:00:00');
                $e = strtotime($date[1].' 23:59:59');
                $sql = "SELECT sum(income) as p, count(*) as n FROM `t_order` where city_id={$c} and status in (1,4) and created>={$f} and created<{$e}";
                $result = Yii::app()->db_readonly->createcommand($sql)->queryRow();

                echo $c.'--'.$m.'--'.$result['p'].'--'.$result['n'].'--'.($result['p']/$result['n'])."\n";
            }

        }
    }

    public function actionGetData4() {
        $city_id = array(1,4,7,8,14);
        $month = array('08','09',10,11);
        $distance = array();
        foreach ($city_id as $c) {
            foreach ($month as $m) {
                $d = '2013'.$m;
                $date = CompanyKpiCommon::getMonthFirstAndLastDay($d);
                $f = strtotime($date[0].' 00:00:00');
                $e = strtotime($date[1].' 23:59:59');
                $sql_100 = "SELECT sum(IF(income>0,income,0)) as p,count(IF(income>0, true, null)) as n FROM `t_order` where city_id={$c} and distance>=100 and distance<200 and status in (1,4) and created>={$f} and created<{$e}";
                $r_100 = Yii::app()->db_readonly->createcommand($sql_100)->queryRow();
                echo $r_100['n']>0 ? $c.'--'.$m.'--100公里--'.($r_100['p']/$r_100['n'])."\n" : "0\n";
                $sql_200 = "SELECT sum(IF(income>0,income,0)) as p,count(IF(income>0, true, null)) as n FROM `t_order` where city_id={$c} and distance>=200 and distance<300 and status in (1,4) and created>={$f} and created<{$e}";
                $r_200 = Yii::app()->db_readonly->createcommand($sql_200)->queryRow();
                echo $r_200['n'] >0 ? $c.'--'.$m.'--200公里--'.($r_200['p']/$r_200['n'])."\n" : "0\n";
                $sql_300 = "SELECT sum(IF(income>0,income,0)) as p,count(IF(income>0, true, null)) as n FROM `t_order` where city_id={$c} and distance>=300 and status in (1,4) and created>={$f} and created<{$e}";
                $r_300 = Yii::app()->db_readonly->createcommand($sql_300)->queryRow();
                echo $r_300['n']>0 ? $c.'--'.$m.'--300公里--'.($r_300['p']/$r_300['n'])."\n" : "0\n";
            }

        }
    }


    public function actionGetData5() {
        $limit = 100;
        $pg = 1;
        $count =  Yii::app()->db_readonly->createcommand("select count(*) from t_driver where mark!=3 and city_id in (1,4,7,8,14)")->queryScalar();
        $page_count = ceil($count/$limit);
        $age_arr = array();
        $driver_arr = array();
        $nu = 0;
        while($pg<=$page_count) {
            $start = ($pg-1)*$limit;
            $sql = "select user, id_card, license_date,city_id from t_driver where mark!=3 and city_id in (1,4,7,8,14) limit {$start}, {$limit}";
            $model = Yii::app()->db_readonly->createcommand($sql)->queryAll();
            if (is_array($model) && count($model)) {
                foreach ($model as $m) {
                    $city_id = $m['city_id'];
                    $_driver_id = $m['user'];
                    $ext_sql = "select all_count from t_driver_ext where driver_id='{$_driver_id}'";
                    $_order = Yii::app()->db_readonly->createcommand($ext_sql)->queryRow();
                    if ($_order && isset($_order['all_count']) && $_order['all_count']>0) {
                        $i = ceil(intval($_order['all_count'])/50)*50;
                        $age_arr[$city_id][$i] = isset($age_arr[$city_id][$i]) ? $age_arr[$city_id][$i] : 0;
                        $age_arr[$city_id][$i] = intval($age_arr[$city_id][$i] + 1);
                    }
                }
            }
            $pg++;
        }
        $list = array(1,4,7,8,14);
        foreach ($list as $c) {
            foreach ($age_arr[$c] as $i=>$v) {
                echo $c.','.$i.','.$v."\n";
            }
        }
    }

    public function actionGetData6() {
        $city_id = array(1,4,7,8,14);
        $month = array('08', '09', '10', '11');
        echo "司机投诉客户"."\n";
        foreach ($city_id as $c) {
            foreach ($month as $m) {
                $d = '2013'.$m;
                $date = CompanyKpiCommon::getMonthFirstAndLastDay($d);
                $f = strtotime($date[0].' 00:00:00');
                $e = strtotime($date[1].' 23:59:59');
                $sql = "SELECT count(*) as num, driver_user as driver_id FROM `t_driver_complaint` where city={$c} and create_time>={$f} and create_time<={$e} group by driver_user";
                $_order = Yii::app()->db_readonly->createcommand($sql)->queryAll();
                foreach ($_order as $v) {
                    echo $c.','.$m.','.$v['num'].','.$v['driver_id']."\n";
                }
            }
        }
    }

    public function actionGetData7() {
        $city_id = array(1,4,7,8,14);
        $month = array('08', '09', '10', '11');
        echo "客户投诉司机"."\n";
        foreach ($city_id as $c) {
            foreach ($month as $m) {
                $d = '2013'.$m;
                $date = CompanyKpiCommon::getMonthFirstAndLastDay($d);
                $f = $date[0].' 00:00:00';
                $e = $date[1].' 23:59:59';
                $sql = "SELECT count(*) as num, customer_phone FROM `t_customer_complain` where city_id={$c} and create_time>='{$f}' and create_time<='{$e}' group by customer_phone";
                $_order = Yii::app()->db_readonly->createcommand($sql)->queryAll();
                foreach ($_order as $v) {
                    echo $c.','.$m.','.$v['num'].','.$v['customer_phone']."\n";
                }
            }
        }
    }

    public function actionTestRecommend($id=1) {
       // var_dump(DriverRecommand::model()->validateRecommend($id));
        //$p =  CityTools::Pinyin('重庆');
        //CityTools::getAllCityList();
        //echo CityTools::getFirstLetter($p);
        var_dump(CityTools::getOpenCity());

    }

    public function actionTestPartner($order_id) {
        /*
        $common = new PartnerCommon();
        $order_channel = Order::model()->getOrderChannel($order_id);
        $result = $common->checkForbidSmsByChannel($order_channel);
        var_dump($result);
        */
        $city_prefix = Dict::items('city_prefix');
        //$str = "<a href='#'>测试</a>";
        //echo Common::clean_xss($str);
        var_dump(Dict::item('city_prefix', 1));
    }

    public function actionTestDriverId($city_id) {
        $address = new DriverIdPool();
        $min_id = $address->getMinDriverId($city_id);
        //$id = $address->getDriverIdToEntry($city_id);
        var_dump($min_id);
    }
}