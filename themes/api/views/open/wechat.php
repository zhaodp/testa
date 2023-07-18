<?php
$code = isset($_GET["code"]) ? $_GET["code"] : '';
$id = isset($_GET["openid"]) ? $_GET["openid"] : '';
$a_token = isset($_GET["a_token"]) ? $_GET["a_token"] : '';
$r_token = isset($_GET["r_token"]) ? $_GET["r_token"] : '';
$type = isset($_GET["type"]) ? $_GET["type"] : '';
$callback = isset($_GET['callback']) ? $_GET['callback'] : '';

switch ($type) {
    case "openid":
        $sUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx8c8df4a3218410e0&secret=d3f82152de8f0d465defbcf10cdc5d8a&code=" . $code . "&grant_type=authorization_code";
        break;
    case "info":
        $sUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $a_token . "&openid=" . $id . "&lang=zh_CN";
        break;
    case "refresh":
        $sUrl = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=" . $id . "&grant_type=refresh_token&refresh_token=" . $r_token;
        break;
    default:
}
$str = file_get_contents($sUrl);
print_r($callback . "($str)");
?>
