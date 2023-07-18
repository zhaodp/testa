<?php
$id_card = $this->getParam('id_card', FALSE);
$city_id = $this->getParam('city_id', FALSE);

//缺少参数
if(FALSE === ($id_card && $city_id)){
    $this->renderError(__LINE__, '缺少必要的参数');
}

//验证用户是否已经预约且考试日期未过
$book_record = BookingExamDriver::model()->find('id_card = :id_card AND date >= :date',
    array(':id_card' => $id_card,
    ':date' => date('Ymd'),
)
        );
if(!empty($book_record)){
    $data = array();
    $prefix = array(
        1 => '上午',
        2 => '下午',
        3 => '下午',
    );
    $date = $book_record->date;
    $year =  substr('20150102', 0, 4);
    $data['fullDate'] = substr('20150102', 0, 4);
    $data['_date'] = date('n月j日', strtotime($book_record->date));
    $data['_time'] = $prefix[$book_record->hours] . ' ' . BookingExamSetting::$hours_desc[$book_record->hours];
    $city = RCityList::model()->getCityByID($city_id);
    $data['city_name'] = $city['city_name'];
    $this->outputJson($data, 'success', 1);
}


$current_month = BookingExamSetting::model()->getMonthData(date('Ym'), $city_id);
$next_month = BookingExamSetting::model()->getMonthData(date('Ym', strtotime(date('Y-m-1') . ' +1 months')), $city_id);

if(is_numeric($current_month) || is_numeric($next_month)){
    $this->renderError($data, '该城市该月没有可预约信息');
}

//取出后30天数据
$next_thirty_days = array();
$start = date('Ymd', strtotime('+1 days'));
$end = date('Ymd', strtotime('+31 days'));
foreach(array_merge($current_month, $next_month) as $date_data){
    if($date_data['date'] >= $start && $date_data['date'] <= $end){
        $next_thirty_days[] = $date_data;
    }
}

$data = array();
foreach($next_thirty_days as $date_data){
    $date = date('m 月 d 日', strtotime($date_data['date']));
    $time_status = array();
    $hours_setting = BookingHoursSetting::model()->getHourStartEndByCity($city_id);
    foreach(BookingExamSetting::$hours_desc as $hour_id => $hour_desc){
        $start_col = "hour_{$hour_id}_start";
        $end_col = "hour_{$hour_id}_end";
        $hour_desc = $hours_setting[$start_col] . ' ~ ' . $hours_setting[$end_col];
        $time_status[] = array(
            'time' => $hour_desc,
            'apm' => $date_data[BookingExamSetting::$hours_name[$hour_id]] -
            $date_data[BookingExamSetting::$hours_used[$hour_id]],
        );
    }
    $data[] = array(
        'fullDate' => $date_data['date'],
        'date' => $date,
        'timeStatus' => $time_status,
    );
}


$this->outputJson($data);
