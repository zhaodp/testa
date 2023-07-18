<?php
error_reporting(0);

class JSSDK
{
    private $appId;
    private $appSecret;

    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();
        $url = $_SERVER["HTTP_REFERER"];
        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket()
    {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
//	return 'bxLdikRXVbTPdHSM05e5u6yqZ2hiv5hKyP13GhMvVqtAcGkGghsyjDzgWvmkWqv0RXQhxQmPnqy3cTbJlqef-Q';
        $data = json_decode(file_get_contents("jsapi_ticket_new.json"));
        if ($data->expire_time < time()) {
            $accessToken = $this->getAccessToken();
            //$accessToken = 'hello';
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen("jsapi_ticket_new.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }

        return $ticket;
    }

    private function getAccessToken()
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $redis = new Redis();
	$redis->connect('10.132.38.173',9600);
	$access_token=$redis->hGet('WEIXIN_ACCESS_TOKEN','token');
	$redis->close();      
        #$access_token = WeiXinRedis::model()->getWeiXinToken();
        return $access_token;
    }

    private function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
}

$jssdk = new JSSDK('wx8c8df4a3218410e0', 'd3f82152de8f0d465defbcf10cdc5d8a');
$signPackage = $jssdk->getSignPackage();
$url = $_SERVER["HTTP_REFERER"];
if (!preg_match('/https{0,1}:\/\/([^\/]+\.)*edaijia\.cn/', $url)) {
    #echo "<h1>access_denay</h1>";
    #return;
}
?>
edaijia=(typeof(edaijia)=='undefined'?{}:edaijia);
edaijia.wx_config={
debug: true,
appId: '<?php echo $signPackage["appId"]; ?>',
timestamp: <?php echo $signPackage["timestamp"]; ?>,
nonceStr: '<?php echo $signPackage["nonceStr"]; ?>',
signature: '<?php echo $signPackage["signature"]; ?>'
};
