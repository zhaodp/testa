<?php
return array(
    'payment' => array(
        'alipayConfig' => array(
            'partner' => '2088501737596294',  //合作身份者id，以2088开头的16位纯数字
            'key' => 'dk1hiq6a5z6b4z1hyu2zieiza1bsykub',  //安全检验码，以数字和字母组成的32位字符
            'seller_email' => 'shanliying@oucamp.com',  //签约支付宝账号或卖家支付宝帐户
            'return_url' => 'http://www.edaijia.cc/alipay/return_url.php',  //页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
            'notify_url' => 'http://www.edaijia.cc/alipay/notify_url.php',  //服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
            'sign_type' => 'MD5',  //签名方式 不需修改
            'input_charset' => 'utf-8',  //字符编码格式 目前支持 gbk 或 utf-8
            'transport' => 'http',  //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
            'gateway' => 'https://mapi.alipay.com/gateway.do?',  //支付宝网关地址
            'https_verify_url' => 'https://mapi.alipay.com/gateway.do?service=notify_verify&',  //HTTPS形式消息验证地址
            'http_verify_url' => 'http://notify.alipay.com/trade/notify_query.do?',//HTTP形式消息验证地址
        ),
        //银联支付配置信息
        'unionPayConfig' => array(
            'sign_msg' => '64867890Edaijia',            //签名信息
            'sign_method' => 'MD5',           //签名方法
            'mer_id' => '860000000000036',       //商户号
            'security_key' => 'ZHSsXTD5puC6XIsvHIHmVkKs3K8Dz5al',                //商户秘钥
            'mer_back_end_url' => 'http://call.edaijia.cn/notify',   //银联回调后台通知地址
            'mer_front_end_url' => '',    //银联前台通知地址
            'upmp_trade_url' => 'http://222.66.233.198:8080/gateway/merchant/trade',  //银联测试后台交易地址
            'upmp_query_url' => 'http://222.66.233.198:8080/gateway/merchant/query'  //银联测试后台交易查询地址
        ),
        //pp钱包生产环境配置
        'ppmoneyConfig' => array(
            //'merchantId'=>'1000002153',
            'merchantId' => '1000002145',
            'productId' => '100001',
            //'key'=>'qJ7nqKZbEf1rDtLvycjV1fJV0/VUyJmK',
            'key' => 'i187O4DXI/tdShF+D6gxJ4+KblZIriDn',
            'payMethod' => 'BANKCARD_PAY',
            'notify_url' => 'api.edaijia.cn/ppnotify',
        ),
        'wxConfig' => array(
            'merchantId' => '1267036801',
            'key' => '5E7Ta2HkO1M7xqWEoxoyot5cSzmWmh7d'
        ),
    ),
);
            
            
            
            
            
