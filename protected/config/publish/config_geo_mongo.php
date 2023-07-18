<?php
return array(
    // 经纬坐标 -> mongodb 路由表
    'route_map' => array(
        // start_lng, start_lat, end_lng, end_lat, mongo
        array(117.591118,39.596361, 118.998511,40.325469, 'r1'), //灰度测试唐山
        array(117.952165,39.049777, 119.056003,39.610593, 'r1'), //灰度测试唐山
        array(117.869377,39.300606, 117.958489,39.405502, 'r1'), //灰度测试唐山
     ),  

    // mongodb配置
    'mongo_config' => array(
        'r1' => array(
            'rw' => '10.173.161.89:27017,10.173.164.185:27017',
            'ro' => '10.173.161.89:27017,10.173.164.185:27017',
            'repl' => 'edjrep',
        ),  

        'default' => array(
            //'rw' => 'mongo01n.edaijia.cn:27017',
            //'ro' => 'mongoslave.edaijia-inc.cn:27017',

            'rw' => 'mongodriver.edaijia-inc.cn:27020,mongodriver02.edaijia-inc.cn:27020,mongodriver03.edaijia-inc.cn:27020',
            'ro' => 'mongodriver.edaijia-inc.cn:27020,mongodriver02.edaijia-inc.cn:27020,mongodriver03.edaijia-inc.cn:27020',
            'repl' => 'mongodriver',
        ),  
    ),  
);
