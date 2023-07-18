<?php
/**
 * Created by PhpStorm.
 * User: hesongtao
 * Date: 15/4/15
 * Time: 22:05
 */

class guangfaTestCommand extends LoggerExtCommand
{
    private function calc_7164($calc_type,$start,$end)
    {
        try {
            //$start = strtotime('2015-02-01');
            //$end = strtotime('2015-02-28');
            $data = array();

            //==================================================================
            /*$db_finance_dsn = 'mysql:dbname=db_finance;host=dbfinance.edaijia.cn';
            $db_finance_user = 'db_edj_finance';
            $db_finance_password = 'Gp1uJQfq1aXLPi9';
            $db_finance_dbh = new PDO($db_finance_dsn, $db_finance_user, $db_finance_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            */
            $sql = "
                select A.order_id as order_id, B.name as name, B.card_id as card_id, B.card_number as quota
                from
                t_customer_bonus as A inner join t_bank_customer_bonus as B on A.customer_phone = B.phone
                where A.bonus_sn = '7164'
                and A.order_id != 0
                and A.used >= $start
                and A.used <= $end
            ";

//            $sth = $db_finance_dbh->prepare($sql);
//            $sth->execute();
            $coupon_usage_data = Yii::app()->db_finance->createCommand($sql)->queryAll();;
            if (empty($coupon_usage_data)) {
                die;
            } else {
                foreach ($coupon_usage_data as $d) {
                    $data[] = array('order_id' => $d['order_id'],
                        'name' => $d['name'],
                        'quota' => $d['quota'],
                        'card_id' => $d['card_id']);
                }
            }

            //==================================================================
//            $db_order_dsn = 'mysql:dbname=db_order;host=10.157.128.16';
//            $db_order_user = 'edaijia_order_ro';
//            $db_order_password = 'EorderDaijia125ro';
//
//            $db_order_dbh = new PDO($db_order_dsn, $db_order_user, $db_order_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $db_order_sql = 'select order_date, phone, contact_phone, city_id, income from t_order where order_id = :order_id';
            $sth = Yii::app()->dborder->createCommand($sql);
            foreach ($data as $i => $d) {
                $sth->bindParam(':order_id', $d['order_id']);
                $sth->execute();
                $order_detail = $sth->queryAll();
                if (!empty($order_detail)) {
                    foreach($order_detail as $o_detail) {
                        $data[$i]['order_date'] = $order_detail['order_date'];
                        $data[$i]['city_id'] = $order_detail['city_id'];
                        $data[$i]['phone'] = $order_detail['phone'];
                        $data[$i]['contact_phone'] = $order_detail['contact_phone'];
                        $data[$i]['income'] = $order_detail['income'];
                    }

                }
            }

            //==================================================================
//            $db_car_dsn = 'mysql:dbname=db_car;host=10.200.118.217';
//            $db_car_user = 'sp_car_master';
//            $db_car_password = 'uMTNwWqnqjt5CKPa';

            //$db_car_dbh = new PDO($db_car_dsn, $db_car_user, $db_car_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $db_car_sql = "select name as city_name from t_dict where dictname = 'city' and code = :city_id";
            $sth = Yii::app()->db->createCommand($db_car_sql);
            foreach ($data as $i => $d) {
                $sth->bindParam(':city_id', $d['city_id']);
                //$sth->execute();
                $dict_detail = $sth->queryScalar();
                if (!empty($dict_detail)) {
                    $data[$i]['city_name'] = $dict_detail;
                }
            }

            //==================================================================

            $card_holders_info = array();

            ($handle = fopen("premium.csv", "r")) or die;
            while (($line = fgetcsv($handle)) !== FALSE) {
                if (isset($card_holders_info[$line[2]])) {
                    var_dump($line);
                }
                $card_holders_info[$line[2]] = array(
                    'class' => $line[4],
                    'expire' => $line[7]
                );
            }
            fclose($handle);

            ($handle = fopen("platinum.csv", "r")) or die;
            while (($line = fgetcsv($handle)) !== FALSE) {
                $card_holders_info[$line[2]] = array(
                    'class' => $line[4],
                    'expire' => $line[7]
                );
            }
            fclose($handle);

            foreach ($data as $i => $d) {
                if (isset($card_holders_info[$d['phone']])) {
                    $data[$i]['class'] = $card_holders_info[$d['phone']]['class'];
                }
            }

            if ($calc_type == 'detail') {

                //echo "orderid,name,phone,contact_phone,card_id,class,order_date,city_name,income";
                ($handle = fopen("detail.csv", "w")) or die;
                $export_detail = array();
                foreach ($data as $d) {
//                    echo $d['order_id'].",".$d['name'].",".$d['phone'].",".$d['contact_phone'].",".$d['card_id']
//                        .",".$d['class'].",".$d['order_date'].",".$d['city_name'].",".$d['income'];
                    fputcsv($handle, array($d['order_id'], $d['name'], $d['phone'],
                        $d['contact_phone'], $d['card_id'], $d['class'], $d['order_date'],
                        $d['city_name'], $d['income']
                    ));
                }

            } else if ($calc_type == 'summary') {
                ($handle = fopen("summary.csv", "w")) or die;
                $export_summary = array();
                foreach ($data as $i => $d) {
                    if (isset($export_summary[$d['phone']])) {
                        $export_summary[$d['phone']]['used'] += 1;
                        $export_summary[$d['phone']]['left'] -= 1;
                    } else {
                        $export_summary[$d['phone']] = array('name' => $d['name'],
                            'phone' => $d['phone'],
                            'contact_phone' => $d['contact_phone'],
                            'card_id' => $d['card_id'],
                            'class' => $d['class'],
                            'start' => '2015/1/6',
                            'end' => '2015/12/31',
                            'quota' => intval($d['quota']),
                            'used' => 1,
                            'left' => intval($d['quota']) - 1
                        );
                    }
                }

                foreach ($export_summary as $k => $v) {
                    fputcsv($handle, array_values($v));
                }
                fclose($handle);
            }

        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    private function calc_6432($calc_type)
    {
        try {
            $start = strtotime('2015-02-01');
            $end = strtotime('2015-02-28');
            $data = array();

            //==================================================================
            $db_finance_dsn = 'mysql:dbname=db_finance;host=dbfinance.edaijia.cn';
            $db_finance_user = 'db_edj_finance';
            $db_finance_password = 'Gp1uJQfq1aXLPi9';
            $db_finance_dbh = new PDO($db_finance_dsn, $db_finance_user, $db_finance_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $sql = "
                select A.order_id as order_id, B.name as name, B.card_id as card_id, B.club_number as quota
                from
                t_customer_bonus as A inner join t_bank_customer_bonus as B on A.customer_phone = B.phone
                where A.bonus_sn = '6432'
                and A.order_id != 0
                and A.used >= $start
                and A.used <= $end
            ";

            $sth = $db_finance_dbh->prepare($sql);
            $sth->execute();
            $coupon_usage_data = $sth->fetchAll();
            if (empty($coupon_usage_data)) {
                die;
            } else {
                foreach ($coupon_usage_data as $d) {
                    $data[] = array('order_id' => $d['order_id'],
                        'name' => $d['name'],
                        'quota' => $d['quota'],
                        'class' => '铂金俱乐部',
                        'card_id' => $d['card_id']);
                }
            }

            //==================================================================
            $db_order_dsn = 'mysql:dbname=db_order;host=10.157.128.16';
            $db_order_user = 'edaijia_order_ro';
            $db_order_password = 'EorderDaijia125ro';

            $db_order_dbh = new PDO($db_order_dsn, $db_order_user, $db_order_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $db_order_sql = 'select order_date, phone, contact_phone, city_id, income from t_order where order_id = :order_id';
            $sth = $db_order_dbh->prepare($db_order_sql);
            foreach ($data as $i => $d) {
                $sth->bindParam(':order_id', $d['order_id'], PDO::PARAM_INT);
                $sth->execute();
                $order_detail = $sth->fetch();
                if (!empty($order_detail)) {
                    $data[$i]['order_date'] = $order_detail['order_date'];
                    $data[$i]['city_id'] = $order_detail['city_id'];
                    $data[$i]['phone'] = $order_detail['phone'];
                    $data[$i]['contact_phone'] = $order_detail['contact_phone'];
                    $data[$i]['income'] = $order_detail['income'];
                }
            }

            //==================================================================
            $db_car_dsn = 'mysql:dbname=db_car;host=10.200.118.217';
            $db_car_user = 'sp_car_master';
            $db_car_password = 'uMTNwWqnqjt5CKPa';

            $db_car_dbh = new PDO($db_car_dsn, $db_car_user, $db_car_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $db_car_sql = "select name as city_name from t_dict where dictname = 'city' and code = :city_id";
            $sth = $db_car_dbh->prepare($db_car_sql);
            foreach ($data as $i => $d) {
                $sth->bindParam(':city_id', $d['city_id']);
                $sth->execute();
                $dict_detail = $sth->fetch();
                if (!empty($dict_detail)) {
                    $data[$i]['city_name'] = $dict_detail['city_name'];
                }
            }

            //==================================================================

            if ($calc_type == 'detail') {
                echo "orderid,name,phone,contact_phone,card_id,class,order_date,city_name,income";
                //($handle = fopen("detail.csv", "w")) or die;
                $export_detail = array();
                foreach ($data as $d) {
                    echo $d['order_id'].",".$d['name'].",".$d['phone'].",".$d['contact_phone'].",".$d['card_id']
                        .",".$d['class'].",".$d['order_date'].",".$d['city_name'].",".$d['income'];
                    /*fputcsv($handle, array($d['order_id'], $d['name'], $d['phone'],
                        $d['contact_phone'], $d['card_id'], $d['class'], $d['order_date'],
                        $d['city_name'], $d['income']
                    ));*/
                }
                //fclose($handle);

                $this->splitIncome();
            } else if ($calc_type == 'summary') {
                ($handle = fopen("summary.csv", "w")) or die;
                $export_summary = array();
                foreach ($data as $i => $d) {
                    if (isset($export_summary[$d['phone']])) {
                        $export_summary[$d['phone']]['used'] += 1;
                        $export_summary[$d['phone']]['left'] -= 1;
                    } else {
                        $export_summary[$d['phone']] = array('name' => $d['name'],
                            'phone' => $d['phone'],
                            'contact_phone' => $d['contact_phone'],
                            'card_id' => $d['card_id'],
                            'class' => $d['class'],
                            'start' => '2015/1/6',
                            'end' => '2015/12/31',
                            'quota' => intval($d['quota']),
                            'used' => 1,
                            'left' => intval($d['quota']) - 1
                        );
                    }
                }
                foreach ($export_summary as $k => $v) {
                    fputcsv($handle, array_values($v));
                }
                fclose($handle);
            }

        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    private function calc_95508()
    {
        try {
            $start = strtotime('2015-02-01');
            $end = strtotime('2015-02-28');
            $data = array();

            //==================================================================
            $db_finance_dsn = 'mysql:dbname=db_finance;host=dbfinance.edaijia.cn';
            $db_finance_user = 'db_edj_finance';
            $db_finance_password = 'Gp1uJQfq1aXLPi9';
            $db_finance_dbh = new PDO($db_finance_dsn, $db_finance_user, $db_finance_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $sql = "
                select order_id
                from
                t_customer_bonus as A
                where A.bonus_sn = '95508'
                and A.order_id != 0
                and A.used >= $start
                and A.used <= $end
            ";

            $sth = $db_finance_dbh->prepare($sql);
            $sth->execute();
            $coupon_usage_data = $sth->fetchAll();
            if (empty($coupon_usage_data)) {
                die;
            } else {
                foreach ($coupon_usage_data as $d) {
                    $data[] = array('order_id' => $d['order_id']);
                }
            }

            //==================================================================
            $db_order_dsn = 'mysql:dbname=db_order;host=10.157.128.16';
            $db_order_user = 'edaijia_order_ro';
            $db_order_password = 'EorderDaijia125ro';

            $db_order_dbh = new PDO($db_order_dsn, $db_order_user, $db_order_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $db_order_sql = 'select order_date, contact_phone, city_id, income from t_order where order_id = :order_id';
            $sth = $db_order_dbh->prepare($db_order_sql);
            foreach ($data as $i => $d) {
                $sth->bindParam(':order_id', $d['order_id'], PDO::PARAM_INT);
                $sth->execute();
                $order_detail = $sth->fetch();
                if (!empty($order_detail)) {
                    $data[$i]['order_date'] = $order_detail['order_date'];
                    $data[$i]['city_id'] = $order_detail['city_id'];
                    $data[$i]['contact_phone'] = $order_detail['contact_phone'];
                    $data[$i]['income'] = $order_detail['income'];
                }
            }

            //==================================================================
            $db_car_dsn = 'mysql:dbname=db_car;host=10.200.118.217';
            $db_car_user = 'sp_car_master';
            $db_car_password = 'uMTNwWqnqjt5CKPa';

            $db_car_dbh = new PDO($db_car_dsn, $db_car_user, $db_car_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $db_car_sql = "select name as city_name from t_dict where dictname = 'city' and code = :city_id";
            $sth = $db_car_dbh->prepare($db_car_sql);
            foreach ($data as $i => $d) {
                $sth->bindParam(':city_id', $d['city_id']);
                $sth->execute();
                $dict_detail = $sth->fetch();
                if (!empty($dict_detail)) {
                    $data[$i]['city_name'] = $dict_detail['city_name'];
                }
            }

            //==================================================================

            // 95508只需要detail，不需要summary
            ($handle = fopen("detail.csv", "w")) or die;
            $export_detail = array();
            foreach ($data as $d) {
                fputcsv($handle, array($d['order_id'],
                    $d['contact_phone'], $d['order_date'],
                    $d['city_name'], $d['income']
                ));
            }
            fclose($handle);

            $this->splitIncome();

        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    private function splitIncome()
    {
        ($in = fopen("detail.csv", "r")) or die;
        ($out = fopen("out_detail.csv", "w")) or die;
        while (($line = fgetcsv($in)) !== FALSE) {
            $order_info = Order::model()->getOrderPriceInfo($line[0]);
            if (!empty($order_info)) {
                array_splice($line, -1, 0, $order_info['base_price']);
                array_splice($line, -1, 0, $order_info['premium_price']);
                array_splice($line, -1, 0, $order_info['wait_price']);
            }
            fputcsv($out, $line);
            usleep(5 * 1000);
        }
        fclose($in);
        fclose($out);
    }

    public function actionIndex($bonus_id, $calc_type,$start_date,$end_date)
    {

        $start_date = empty($start_date)?(date('Y-m')."-01"):$start_date;
        $end_date = empty($end_date)?date('Y-m-d'):$end_date;

        if ($bonus_id == 95508) {
            $this->calc_95508();
        } else if ($bonus_id == 7164 && $calc_type == 'detail') {
            $this->calc_7164('detail',$start_date,$end_date);
        } else if ($bonus_id == 7164 && $calc_type == 'summary') {
            $this->calc_7164('summary',$start_date,$end_date);
        } else if ($bonus_id == 6432 && $calc_type == 'detail') {
            $this->calc_6432('detail');
        } else if ($bonus_id == 6432 && $calc_type == 'summary') {
            $this->calc_6432('summary');
        } else {
            exit('parameter error');
        }
    }


}