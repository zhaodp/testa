<?php
class WxPayConfig
{
    const DEBUG_ = false;
    //财付通商户号
    const PARTNER = "1219297701";//"1900000109";
    //财付通密钥
    const PARTNER_KEY = "f2b7c7d8dbc2a92b99833c624934727d";//"8934e7d15453e97507ef794cf7b0519d";
    //appid
    const APP_ID = "wx739de821c251fd49";//"wxd930ea5d5a258f4f";
    //appsecret
    const APP_SECRET = "5e18e76f217dd0f52bfa00214ae8459f";//"db426a9829e4b49a0dcac7b4162da6b6";
    //paysignkey(非appkey)
    const APP_KEY = "uMjn7go6sJNs6KP2MNIdCIPYaE7sln5YRDsjZNhNkNBNGvveYI3HvveAbhB4Joc6N3bEY7t3vSjkuL6maeo3j3soybxeHCiFob27PL2kcg4ws6hANk6pWzfjJ3kEotMG";
//    const APP_KEY = "L8LrMqqeGRxST5reouB0K66CaYAWpqhAVsq7ggKkxHCOastWksvuX1uvmvQclxaHoYd3ElNBrNO2DHnnzgfVG9Qs473M3DTOZug5er46FhuGofumV8H2FVR9qkjSlC5K";

    //支付完成后的回调处理页面,*替换成notify_url.asp所在路径
//    const notify_url = "http://localhost/php/notify_url.php";
    const notify_url = "http://api.d2.edaijia.cn/wxnotify";
}
