<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/24
 * Time: 上午10:54
 * api返回json封装
 */
class JsonResponse{

    const validMsgFail='身份验证失败';
    const EXPIRE_CODE=2; //身份验证错误码
    /***
     * @param $params
     * @param string $message
     * @return string
     * 成功返回json
     */
    public static function success($params,$message='请求成功'){
        $params['code']=0;
        if(!isset($params['message'])) {
            $params['message'] = $message;
        }
        return self::JSON($params);
    }

    /**
     * @param $params
     * @param string $message
     * @return string
     * 失败返回json
     */
    public static function fail($params,$message='请求失败'){
        $code = 1;
        if(isset($params['code'])){
            $code = $params['code'];
        }
        $params['code']=$code;
        if(!isset($params['message'])) {
            $params['message'] = $message;
        }
        return self::JSON($params);
    }


    public static function arrayRecursive(&$array, $function, $apply_to_keys_also = false){
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                if(!is_bool($value)){
                     $array[$key] = $function($value);
                }
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }

    /**************************************************************
     *
     *  将数组转换为JSON字符串（兼容中文）
     *  @param  array   $array      要转换的数组
     *  @return string      转换得到的json字符串
     *  @access public
     *
     *************************************************************/
    public  static function JSON($array) {
        self::arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);
        return urldecode($json);
    }


}
