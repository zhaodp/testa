<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bidong
 * Date: 14-1-23
 * Time: 下午11:58
 * To change this template use File | Settings | File Templates.
 */

Yii::import('application.models.schema.report.*');

class BOrderTrend extends ReportDailyOrderPerHour
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave() {
        if(parent::beforeSave()){
            $this->update_time = date('Y-m-d H:i:s', time());
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 统计某个城市某天每小时订单
     * @param $city 城市ID
     * @param $day  统计日期
     * @author bidong 2014-1-24
     *
     */
    public function countOrderTrendByPerHour($city_id,$day){


        $where = 'booking_time>=:begin_date AND booking_time<:end_date ';
        $params = array(':begin_date' => strtotime($day), ':end_date' => strtotime($day)+86400);
        if($city_id>0){
            $where .= ' AND city_id=:city_id ';
            $params[':city_id'] = $city_id;
        }
        $sql = "SELECT
                    sum(FROM_UNIXTIME(booking_time, '%H') = '01') as one,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '02') as two,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '03') as three,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '04') as four,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '05') as five,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '06') as six,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '07') as seven,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '08') as eight,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '09') as nine,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '10') as ten,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '11') as eleven,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '12') as twelve,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '13') as thirteen,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '14') as fourteen,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '15') as fifteen,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '16') as sixteen,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '17') as seventeen,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '18') as eighteen,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '19') as nineteen,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '20') as twenty,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '21') as twenty_one,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '22') as twenty_two,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '23') as twenty_three,
                    sum(FROM_UNIXTIME(booking_time, '%H') = '00') as twenty_four
                FROM {{order}}
                WHERE " . $where;

        $result = Order::getDbReadonlyConnection()->createCommand($sql)
                  ->queryRow(true, $params);
        return $result;
    }



    /**
     * 解析日期
     * @param $dateTime
     * @return array
     * @author bidong 2014-1-24
     */
    public function analyzeTime($dateTime){
        $ret=array('year'=>'0','month'=>'0','day'=>'0','hour'=>'0');
        if($dateTime){
            $today=getdate(strtotime($dateTime));
            $ret['year']=$today['year'];
            $ret['month']=$today['mon'];
            $ret['day']=$today['mday'];
            $ret['hour']=$today['hours'];
        }
        return $ret;
    }



    /**
     * 返回统计日期
     * @param Timestamp $dateTime
     * @return bool|string
     */
    public function formatDate($dateTime)
    {
        //判断时间是否7点前后
        $count_date = '';
        $hour = date("H", $dateTime);
        $hour = intval($hour);
        if ($hour >= 7) {
            $count_date = date("Y-m-d", $dateTime);
        }
        if ($hour < 7) {
            $count_date = date("Y-m-d", $dateTime - 86400);
        }

        return $count_date;
    }
    
    public function buildReportData($day = null, $afterDays = 2, $printLog = FALSE, $canOutTheDay = FALSE){
        $time = time();
        $updateItems = 0;                       //修改了多少条数据
        $citys = Dict::items('city');         //获取到已开通代驾的城市
        if($printLog){
            echo 'citys=>'.implode('--', $citys)."\r\n";
        }
        $initDay = $day;
        //按照城市循环生成数据
        foreach ($citys as $city_id => $city_name) {
            $day = $initDay;
            for ($i = 0; $i < $afterDays; $i++) {        //单个城市 按照日期逐天生成数据
                if($printLog){
                    echo 'city=>'.$city_id."\r\n";
                }
                //判断 传递的日期 大于 当前日期 后 是否继续（$canOutTheDay==false 的时候会退出循环）
                if (!$canOutTheDay && (date('Ymd', strtotime($day)) > date('Ymd', $time))) {
                    break;
                }
                $attributes = $this->countOrderTrendByPerHour($city_id, $day);
                $attributes['city_id'] = $city_id;
                $attributes['day'] = $day;
                $model = self::model()->findByAttributes(array('city_id'=>$city_id, 'day'=>$day));
                if(!$model){
                    $model = new BOrderTrend();
                }
                $model->attributes = $attributes;
                $saveOk = $model->save();
                if($printLog){
                    echo '-----$city_id='.$city_id.'$day='.$day.'$'.implode('$', $attributes).'---'.($saveOk?'Ok':'Faild').'---'."\r\n";
                }
                if ($saveOk) {
                    $updateItems++;
                }
                $day = date('Ymd', mktime(0, 0, 0, date('n',strtotime($day)), date('j',strtotime($day))+1, date('Y',strtotime($day))));  //往后加一天
            }
        }
    }
}
