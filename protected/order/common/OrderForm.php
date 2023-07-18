<?php

/**
 * Created by PhpStorm.
 * User: wanghongyuan
 * Date: 2015/4/30
 * Time: 14:08
 */
class OrderForm
{
    public $phone;
    public $city_id;
    public $address;
    public $booking_id;
    public $booking_time;
    public $gps;
    public $driver_id;
    public $type;
    public $google_lng;
    public $google_lat;
    public $source;
    public $bonus_use_limit;
    public $bonus_sn;
    public $is_use_bonus;
    public $app_ver;
    public $token;
    public $gps_type;
    public $lng;
    public $lat;
    public $udid;
    public $number;
    public $contact_phone;
    public $edited;
    public $channel;
    public $os;
    public $fee;
    public $cash_only;
    public $from;
    public $customer_lng;
    public $customer_lat;

    function __construct($params, $type)
    {
        if ($type == OrderDriverMatchType::AUTOMATIC) {
            $this->automatic($params);
        } else {
            $this->chooseDriver($params);
        }
    }

    function  automatic($params)
    {
        $this->token = isset($params ['token']) ? trim($params ['token']) : '';
        $this->number = isset($params ['number']) ? intval($params ['number']) : '';
        $this->contact_phone = isset($params ['contact_phone']) ? trim($params ['contact_phone']) : '';
        $this->address = isset($params ['address']) ? trim($params ['address']) : '';
        $this->lng = isset($params ['lng']) ? trim($params ['lng']) : '';
        $this->lat = isset($params ['lat']) ? trim($params ['lat']) : '';
        $this->gps_type = isset($params ['gps_type']) ? trim($params ['gps_type']) : 'google';
        $this->edited = isset($params ['edited']) ? $params ['edited'] : 0;
        $this->city_id = isset($params ['city_id']) ? intval($params ['city_id']) : '';
        $this->channel = isset($params ['type']) ? $params ['type'] : CustomerApiOrder::QUEUE_CHANNEL_BOOKING;
        $this->source = isset($params ['source']) ? $params ['source'] : Order::SOURCE_CLIENT;
        $this->os = isset($params ['os']) ? $params ['os'] : '';
        $this->app_ver = isset($params ['app_ver']) ? $params ['app_ver'] : '';
        $this->fee = isset($params ['fee']) ? $params ['fee'] : '';
        $this->booking_time = isset($params ['booking_time']) ? intval($params ['booking_time']) : 0;
        $this->cash_only = isset($params ['cash_only']) ? intval($params ['cash_only']) : 0;
        $this->udid = isset($params ['udid']) ? $params ['udid'] : null;
        $this->bonus_sn = isset($params ['bonus_sn']) ? $params ['bonus_sn'] : ''; // 增加优惠券号码
        $this->is_use_bonus = isset($params ['is_use_bonus']) ? intval($params ['is_use_bonus']) : 1; // 是否使用优惠券
        $this->customer_lng = isset($params ['customer_lng']) ? $params ['customer_lng'] : '';
        $this->customer_lat = isset($params ['customer_lat']) ? $params ['customer_lat'] : '';
        $this->from = isset($params ['from']) ? $params ['from'] : '';
    }

    function  chooseDriver($params)
    {
        $this->token = isset($params['token']) ? trim($params['token']) : '';
        $this->driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
        $this->lng = isset($params['lng']) ? trim($params['lng']) : '';
        $this->lat = isset($params['lat']) ? trim($params['lat']) : '';
        $this->gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'google';
        $this->type = isset($params['type']) ? $params['type'] : DalOrder::SINGLE_PUSH_DRIVER;
        $this->city_id = isset($params['city_id']) ? $params['city_id'] : '';

        $this->bonus_sn = isset($params['bonus_sn']) ? trim($params['bonus_sn']) : '';              //增加优惠券号
        $this->is_use_bonus = isset($params['is_use_bonus']) ? intval($params['is_use_bonus']) : 1; //是否使用优惠券
        $this->source = isset($params['source']) ? intval($params['source']) : Order::SOURCE_CLIENT;
        $this->booking_time = isset($params['booking_time']) ? intval($params['booking_time']) : 0;
        $this->app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
        $this->udid = isset($params['udid']) ? $params['udid'] : null;//这个参数没用很奇怪

    }
}