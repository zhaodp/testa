<?php

/**
 * Created by IntelliJ IDEA.
 * User: wangjun
 * Date: 15/1/9
 * Time: 下午5:26
 */
class PHttp
{

    public static function  jsonResponse($code, $result)
    {
        header("Content-type: text/html; charset=utf-8");
        $response = new Response($code, $result);
        echo json_encode($response);
    }

    public static function paramIsNotEmpty($var)
    {
        if ((isset($_GET[$var]) && !empty($_GET[$var])) || (isset($_POST[$var]) && !empty($_POST[$var]))) {
            return true;
        }

        return false;
    }

    public static function paramIsEmpty($var)
    {
        return !self::paramIsNotEmpty($var);
    }

    public static function getParam($var)
    {
        if ((isset($_GET[$var]) && !empty($_GET[$var]))) {
            return $_GET[$var];
        } elseif ((isset($_POST[$var]) && !empty($_POST[$var]))) {
            return $_POST[$var];
        }
        return null;

    }

}

class Response
{
    public $code;
    public $result;

    function  __construct($code, $result)
    {
        $this->code = $code;
        $this->result = $result;
    }
}

?>