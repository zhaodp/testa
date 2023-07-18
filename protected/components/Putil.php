<?php

/**
 * Created by IntelliJ IDEA.
 * User: wangjun
 * Date: 15/1/9
 * Time: 下午5:26
 */
class Putil
{

    public static function  jsonResponse($code, $result)
    {
        $response = new Response($code, $result);
        echo json_encode($response);
    }

    public static function isEmpty($var)
    {
        if (isset($var) && !empty($var))
            return false;
        return true;
    }

    public static function isNotEmpty($var)
    {
        return !self::isEmpty($var);
    }


    public static function report($msg, $level = 0, $isPrintEcho = 1)
    {
        if ($isPrintEcho == 1) {
            echo $msg."\n";
        }
        if ($level == 0) {
            EdjLog::info($msg);
        } elseif ($level == 1)
            EdjLog::warning($msg);
    }


}

?>