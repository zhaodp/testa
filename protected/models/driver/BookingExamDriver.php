<?php
/**
 * This is the model class for table "{{booking_exam_setting}}".
 *
 * The followings are the available columns in table '{{driver_recruitment}}':
 * @property integer $id
 * @property integer $city_id * @property integer $date
 * @property integer $hours_1
 * @property integer $hours_2
 * @property integer $hours_3
 */
class BookingExamDriver extends CActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{booking_exam_driver}}';
	}

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 司机预约
     */
    public function driverBook($city_id, $date, $id_card, $hour){

        if(!DriverOrder::model()->checkOpenCity($city_id)){
            return array(__LINE__, '该城市未开通预约');
        }
        //非法时段
        if(!isset(BookingExamSetting::$hours_desc[$hour])){
            return array(__LINE__, '时段不存在');
        }

        //不能报名当天之前的日期
        if($date < date('Ymd')){
            return array(__LINE__, '预约日期已过');
        }

        //验证 card_id 是否已报名
        $driver_recruitment = DriverRecruitment::model()->findByIDCard($id_card);
        if(empty($driver_recruitment)){
            return array(__LINE__, '司机没有报名');
        }
        //验证用户是否已经通过考试
        if($driver_recruitment->road_new == DriverRecruitment::STATUS_ROAD_FIELD_PASS){
            return array(__LINE__, '已经通过考试');
        }
        //验证用户是否已经预约且考试日期未过
        $book_record = self::model()->find('id_card = :id_card AND date >= :date ORDER BY date DESC',
            array(':id_card' => $id_card,
            ':date' => date('Ymd'),
            )
        );
        if(!empty($book_record)){
            //当天之后有预约记录，不可预约
            if($book_record->date > date('Ymd')){
                return array(__LINE__, '已经预约');
            }
            //当天已经预约，不能约当天，可以预约当天以后
            if($book_record->date == $date){
                return array(__LINE__, '今天你已预约，不可再预约今天');
            }
        }

        //setting 中没有这天的设置，不能报名
        $day_data = BookingExamSetting::model()->getDayData($date, $city_id);
        if(is_null($day_data)){
            return array(__LINE__, '该城市这天不可以预约');
        }

        //当天该时段已经没有名额
        if($day_data[BookingExamSetting::$hours_used[$hour]] >=
            $day_data[BookingExamSetting::$hours_name[$hour]]){
                return array(__LINE__, '这个时间段报名已满');
            }

        $model = new BookingExamDriver();
        $model->id_card = $id_card;
        $model->city_id = $city_id;
        $model->date = $date;
        $model->hours = $hour;
        //$driver_recruitment->status = 8; //合代码后用const替换
        try{
            $model->save();
            //$driver_recruitment->save();
            $res = DriverRecruitment::model()->updateRoadStatus($id_card, DriverRecruitment::STATUS_ROAD_RESERVATION);
        }catch(Exception $e){
            return array(__LINE__, $e->getMessage());
        }

        //修改相应时段的报名人数
        $column_name = BookingExamSetting::$hours_used[$hour];
        $day_data->$column_name++;
        $day_data->save(FALSE);
        return array(0, null);
    }

    public function getDriverBookingStatus($id_card){

        return self::model()->findByAttributes(array('id_card' => $id_card));
    }

    /***
     * @param $id_card
     * 获取司机最近的一条预约
     */
    public function getDriverLastBooking($id_card){
        $booking = self::model()->find('id_card = :id_card ORDER BY date DESC',
            array(':id_card' => $id_card)
        );
        return $booking;
    }

    /**
     * @param $cityId
     * @param $date int 20150101
     * @return mixed
     * 获取城市某天预约列表
     */
    public function getBookingList($cityId,$date){
        $bookingList = self::model()->findAll('city_id = :city_id and date=:date order by hours',
            array(':city_id' => $cityId,'date'=>$date)
        );
        $list = array();
        foreach($bookingList as $booking){
            $idCard = $booking['id_card'];
            $recruitment = DriverRecruitment::model()->findByIDCard($idCard);
            if($recruitment){
                $list[]=array('driverName'=>$recruitment['name'],
                    'driverIdCard'=>$booking['id_card'],
                    'hours'=>$booking['hours']);
            }
        }
        return $list;
    }

    /**
     * 取回某城市某天的预约列表
     * @return array(date, hours, name, id_card)
     */
    public function getListByDate($date, $city_id){
        $sql = "SELECT date, hours, name, t_driver_recruitment.id_card FROM t_booking_exam_driver, t_driver_recruitment WHERE t_booking_exam_driver.date = '{$date}' AND t_booking_exam_driver.city_id = {$city_id} AND t_booking_exam_driver.id_card = t_driver_recruitment.id_card ORDER BY hours;";
        $result = Yii::app()->db_readonly->createCommand($sql)->queryAll();
        return $result;
    }

    /*
     * 根据城市取出下一天之后的所有预约司机
     * 取出数据：司机手机，报名时间段，报名日期
     */
    public function getDriverListAfterNextDay($city_id, $hours){
        $date = date('Ymd');
        $sql = "SELECT book.date, book.hours, driver.mobile FROM t_booking_exam_driver book, t_driver_recruitment driver WHERE date > {$date} AND book.city_id = {$city_id} AND driver.id_card = book.id_card AND book.hours in (:hours);";
        $command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(':hours', $hours);
        return $command->queryAll();
    }

}
