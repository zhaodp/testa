<?php
class RecruitmentCommand extends LoggerExtCommand{
    public function actionFixUsedBookingNum($start = 0, $days = 30, $city_id = 0){
        $end = date('Ymd', strtotime($start . "{$days} days"));
        //修正所有有报名的hours_1时段数据
        $sql = "UPDATE t_booking_exam_setting t1 INNER JOIN (SELECT date, t_booking_exam_driver.city_id,hours, COUNT(1) AS cnt FROM t_booking_exam_driver, t_driver_recruitment WHERE hours = 1 AND t_booking_exam_driver.id_card = t_driver_recruitment.id_card GROUP BY city_id, date) t2 ON t1.date = t2.date AND t1.city_id = t2.city_id SET t1.used_hours_1 = t2.cnt;";
        Yii::app()->db->createCommand($sql)->execute();
        //将所有没有报名的hours_1时段置0
        $sql = "UPDATE t_booking_exam_setting t1 LEFT JOIN (SELECT date, t_booking_exam_driver.city_id,hours, COUNT(1) AS cnt FROM t_booking_exam_driver, t_driver_recruitment WHERE hours = 1 AND t_booking_exam_driver.id_card = t_driver_recruitment.id_card GROUP BY city_id, date) t2 ON t1.date = t2.date AND t1.city_id = t2.city_id SET t1.used_hours_1 = 0 WHERE cnt IS NULL;";
        Yii::app()->db->createCommand($sql)->execute();
        //修正所有有报名的hours_2时段数据
        $sql = "UPDATE t_booking_exam_setting t1 INNER JOIN (SELECT date, t_booking_exam_driver.city_id,hours, COUNT(1) AS cnt FROM t_booking_exam_driver, t_driver_recruitment WHERE hours = 2 AND t_booking_exam_driver.id_card = t_driver_recruitment.id_card GROUP BY city_id, date) t2 ON t1.date = t2.date AND t1.city_id = t2.city_id SET t1.used_hours_2 = t2.cnt;";
        Yii::app()->db->createCommand($sql)->execute();
        //将所有没有报名的hours_2时段置0
        $sql = "UPDATE t_booking_exam_setting t1 LEFT JOIN (SELECT date, t_booking_exam_driver.city_id,hours, COUNT(1) AS cnt FROM t_booking_exam_driver, t_driver_recruitment WHERE hours = 2 AND t_booking_exam_driver.id_card = t_driver_recruitment.id_card GROUP BY city_id, date) t2 ON t1.date = t2.date AND t1.city_id = t2.city_id SET t1.used_hours_2 = 0 WHERE cnt IS NULL;";
        Yii::app()->db->createCommand($sql)->execute();
        //修正所有有报名的hours_3时段数据
        $sql = "UPDATE t_booking_exam_setting t1 INNER JOIN (SELECT date, t_booking_exam_driver.city_id,hours, COUNT(1) AS cnt FROM t_booking_exam_driver, t_driver_recruitment WHERE hours = 3 AND t_booking_exam_driver.id_card = t_driver_recruitment.id_card GROUP BY city_id, date) t2 ON t1.date = t2.date AND t1.city_id = t2.city_id SET t1.used_hours_3 = t2.cnt;";
        Yii::app()->db->createCommand($sql)->execute();
        //将所有没有报名的hours_3时段置0
        $sql = "UPDATE t_booking_exam_setting t1 LEFT JOIN (SELECT date, t_booking_exam_driver.city_id,hours, COUNT(1) AS cnt FROM t_booking_exam_driver, t_driver_recruitment WHERE hours = 3 AND t_booking_exam_driver.id_card = t_driver_recruitment.id_card GROUP BY city_id, date) t2 ON t1.date = t2.date AND t1.city_id = t2.city_id SET t1.used_hours_3 = 0 WHERE cnt IS NULL;";
        Yii::app()->db->createCommand($sql)->execute();
    }

    public function actionClearFunnelCache($city){
        CompanyKpiCommon::clearFunnelCache($city);
    }
}
