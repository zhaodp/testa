<?php
/**
 *
 * 封装对于时间的各种处理方法
 *
 * User: tuan
 * Date: 15/4/14
 * Time: 11:43
 */



class CalendarUtils {


    public static function getPreTime($time, $type){
        switch($type){
            case 'yesterday':
                return CalendarUtils::getPreDay($time, 1);
            case 'last_week':
                return CalendarUtils::getPreWeek($time, 1);
            case 'last_month':
                return CalendarUtils::getPreMonth($time, 1);
            default:
                return 0;
        }
    }

    /**
     * 获得前几周的东西
     *
     * @param $time
     * @param int $count
     * @return int
     */
    public static function getPreWeek($time, $count = 1){
        return strtotime('-'.$count.' weeks', $time);
    }

    public static function getPreDay($time, $count = 1){
        return strtotime('-'.$count.' days', $time);
    }

    public static function getPreMonth($time, $count = 1){
        return strtotime('-'.$count.' months', $time);
    }
}