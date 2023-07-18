<?php

class PincheV2  {


    private $secrect = 'EqYKiaWy6qBRvX0EUoUXC6O3xEeRhz';
    private $host = 'http://pas.ybc.kkpinche.cn/passenger/reportReturn';

    public $decode_json = true;

    private $timestamp;
    private $ver;
    private $key = '10010001';


    private static $_models = array ();


    public function __construct() {
        $this->timestamp = date('Y-m-d H:i');
        $this->ver = 3;

    }
    public static function model($className = __CLASS__) {
        $model = null;
        if (isset(self::$_models[$className]))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

    public function setKey($key) {
        $this->key = $key;
        return $this;
    }


    function synccustomer($gps ,$driver_info,$goback){
        $get = array(

            'longitude'=>$gps['lng'],
            'latitude'=>$gps['lat'],
            'phone'=>$driver_info['phone'],
            'name'=>$driver_info['name'],
            'device_type'=>2,
            'udid'=>$driver_info['udid'],
            'cityId'=>$driver_info['city_id'],
            'type'=>$goback,

        );
        $res = self::get('open.synccustomer',$get);
        return $res;
    }


    public function get($url, $params, $second = 30) {
        $params['appkey'] = $this->key;
        $params['ver'] = $this->ver;
        $params['timestamp'] = $this->timestamp;
        $params['method'] = $url;
        $sig = self::createSigV2_kk($params, $this->secrect);
        $params['sign'] = $sig;
        $query_str = http_build_query($params ,'', '&');
        $url = $this->host.'?'.$query_str;
        $data = self::HttpGet($url,$second);
        if ($this->decode_json) {
            return json_decode($data, true);
        } else {
            return $data;
        }
    }

    public  function HttpGet($url, $second = 10)
    {
        EdjLog::info ("PincheV2,url:".$url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);

        curl_close($ch);
        return $data;
    }


    private function createSigV2_kk($params,$secret) {


        ksort($params);
        $query_string = '';

        foreach($params as $k=>$v) {
            $query_string .= $k.$v;
        }

        //todo 这块修改了
        $sig = md5($query_string.$secret);
        return $sig;

    }

}