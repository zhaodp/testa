<?php
class OrganizationService {



    public $api_domain = '';
    public $appkey = '';
    public $secret='';


    private static $instance;
    public static function getInstance()
    {
        if (empty ( self::$instance ))
        {
            self::$instance = new OrganizationService();
        }
        return self::$instance;
    }

    public function __construct()
    {
        //环境判断
        $env=Common::getEnvironment();
        switch($env){
            case "dev":
                $this->api_domain = "http://api.edaijia.cc/rest";
                $this->appkey = "51000016";
                $this->secret = 'afcd1b0c-e29f-11e1-92bc-00163e0107dd';
                break;
            case "test":
                $this->api_domain = "http://api.d.edaijia.cn/rest";
                $this->appkey = "51000016";
                $this->secret = 'afcd1b0c-e29f-11e1-92bc-00163e0107dd';
                break;
            case "online":
                $this->api_domain = "https://api.edaijia.cn/rest";
                $this->appkey = "61000030";
                $this->secret = '4y4idt8e-xl2d-qi8u-czop-lupt7eckhmu7';
                break;
            default:
                $this->api_domain = "http://api.edaijia.cn/rest";
        }
    }





    /**
     * post请求
     * @param $url string
     * @param $host string
     */
    private function http_post($url, $host = '', $params = array(), $timeout=5, $ms = false){
        if(is_array($params)){
            $postString = http_build_query($params);
        }else{
            $postString = $params;
        }

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if(!empty($host)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $host));
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'YOUKU.COM PREMIUM API PHP5 Client ver: ' . phpversion());
            if(true === $ms){
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
            }else{
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            }
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $context = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded'    . "\r\n".
                        'Host: '.$host . "\r\n".
                        'User-Agent: PHP5 Client ver: ' . phpversion() . "\r\n".
                        'Content-length: ' . strlen($postString),
                    'content' => $postString
                )
            );
            $contextId = stream_context_create($context);
            $handle = fopen($url, 'r', false, $contextId);
            $result = '';
            if ($handle) {
                while (!feof($handle)) {
                    $result .= fgets($handle, 4096);
                }
                fclose($handle);
            }
        }

        return $result;
    }
    /**
     * 发起HTTP GET请求
     *
     * @param string $url 请求URL
     * @param string $host 请求主机HOST
     * @param array $params 请求参数
     */
    public function http_get($url, $host = '', $params = array(), $timeout=3, $ms = false){
        if(is_array($params)){
            $getString= '';
            $getParams = array();
            foreach ($params as $key=>$val) {
                $getParams[] = $key.'='.($val);
                $getString = implode('&', $getParams);
            }
            //$getString = http_build_query($params);
        }else{
            $getString = $params;
        }
        $url .= "?" . $getString;
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            if(!empty($host)){
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $host));
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP5 Client ver: ' . phpversion());
            if(true === $ms){
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
            }else{
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            }
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            // 发起请求
            $handle = fopen ( $url, 'r' );
            $result = "";
            if ($handle) {
                while ( ! feof ( $handle ) ) {
                    $result .= fread ( $handle, 4096 );
                }
                fclose ( $handle );
            }
        }
        return $result;
    }

    private function createSig($params){
        if (isset($params["sig"])) unset($params["sig"]);
        $secret = $this->secret;
        ksort($params);
        $query_string = '';
        foreach($params as $k=>$v) {
            $query_string .= $k.$v;
        }
        return md5($query_string.$secret);
    }
    /**
     * 获取某一个城市下的机构列表
     * wandoperateApi
     *
     * @return array|String
     */
    public function getOrganizationByCity($city_id)
    {
        $appKey = $this->appkey;
        $params = [
            "ver"       => 3,
            "method"    => "common.organization",
            "city_id"  => $city_id,
            "timestamp" => date("Y-m-d H:i",time()),
            "appkey"    => $appKey
        ];
        $sig = $this->createSig($params);
        $params["sig"] = $sig;


        try{
            $url =  $this->api_domain;

            for($i = 1 ; $i <= 5 ; $i++){
                $res = $this->http_get($url, '', $params);
                $result = json_decode($res,true);
                $msg = __METHOD__.' result= '.$res.' times:'.$i;
                if(!empty($result)){
                    //print_r($res);
                    return $result;
                    EdjLog::info($msg);
                    break;
                }else{
                    $msg = __METHOD__.' result= '.$res.' times:'.$i;
                    //print_r($res);
                    EdjLog::error($msg);
                }
            }
        }catch(Exception $e){
            EdjLog::error(__METHOD__.' error::'.$e->getMessage());
            return false;
        }
    }


}
