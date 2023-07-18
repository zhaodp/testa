<?php
return array(
    'brokers' => "kafka01.edaijia-inc.cn:9092,kafka02.edaijia-inc.cn:9092,kafka03.edaijia-inc.cn:9092,kafka04.edaijia-inc.cn:9092,kafka05.edaijia-inc.cn:9092,kafka06.edaijia-inc.cn:9092",
    //topic名的映射,推荐用class名字做key
    //测试环境和线上用不同的配置文件
    'topicmap' => array(
        "RDriverPositionToKafka" => "driver_location_new",
        "ROrderToKafka" => "order",
        "vip_customer_change" => "vip_customer_change",
	"SubmitOrderAutoService_saveOrderInfoJob" => "finished_order_picture"
     ),
);
