<?php
/**
 * 客户端API：c.welcome 日间业务欢迎页
 * 调用的url:
 * @author duke 2014-12-03
 * @param $params    $params['_callback']
 *
 * @return json
 * @see  c.welcome
 * @since
 */

$lng = $params['longitude'];
$lat = $params['latitude'];
$cityName = isset($params['cityName']) ? $params['cityName'] : '';

if ($cityName=='') {
    //查询百度地图返回城市名称
    $cityName = GPS::model()->getCityByBaiduGPS($lng,$lat);
}else{
    //临时处理 bidong 2014-1-28
    $city= explode("市",$cityName);
    if(count($city)>1){
        $cityName=$city[0];
    }
    //临时处理
}

$city_id = CityConfig::model()->getIdByName($cityName);
$data = array();

/////start
$is_old_version = false;
if(in_array($city_id,array(4))){ //新日间业务价格表 客户端先上线杭州
    $is_old_version = false;
}

//日间业务
$daytime_type = RCityList::model()->getCityById($city_id,'daytime_price');
$update_time = RCityList::model()->getCityById($city_id,'update_time');
$update_time = strtotime($update_time);
if($daytime_type && isset(Yii::app()->params['daytime_price'][$daytime_type])){
    if($is_old_version){
        $data = array(
            // 'update_time'=>1417574645,
            'spacing'=>60,
            'welcome'=>array(
                array(
                    'img'=>'http://pic.edaijia.cn/client/yindao01.png',
                    //'href'=>'http://wap.edaijia.cn/'
                ),
                array(
                    'img'=>'http://pic.edaijia.cn/client/yindao02.png',
                    //'href'=>'http://wap.edaijia.cn/'
                ),
                array(
                    'img'=>'http://pic.edaijia.cn/client/yindao03.png'
                ),
                array(
                    'img'=>'http://pic.edaijia.cn/client/yindao04.png'
                )
            ),
            'ad'=>array(
                'http://pic.edaijia.cn/client/lunbo01.png',
                'http://pic.edaijia.cn/client/lunbo02.png',
                'http://pic.edaijia.cn/client/lunbo03.png',
                'http://pic.edaijia.cn/client/lunbo04.png',
            ),
            'banner'=>array(
                'http://pic.edaijia.cn/client/baner.jpg'
            )
        );

    }else{
        $yindao = Yii::app()->params['daytime_yindao'][$daytime_type];
        $lunbo = Yii::app()->params['daytime_lunbo'][$daytime_type];
        $banner = Yii::app()->params['daytime_banner'][$daytime_type];
        $data = array(
            // 'update_time'=>1417574665,
            'spacing'=>60,
            'welcome'=>$yindao,
            'ad'=>$lunbo,
            'banner'=>$banner
        );
    }

}
else{
    $data = array(
        // 'update_time'=>1417574665,
        'spacing'=>60,
        'welcome'=>array(
//            array(
//                'img'=>'http://pic.edaijia.cn/client/yindao01.png',
//                //'href'=>'http://wap.edaijia.cn/'
//            ),
//            array(
//                'img'=>'http://pic.edaijia.cn/client/yindao02.png',
//                //'href'=>'http://wap.edaijia.cn/'
//            ),
//            array(
//                'img'=>'http://pic.edaijia.cn/client/yindao03.png'
//            ),
//            array(
//                'img'=>'http://pic.edaijia.cn/client/yindao04.png'
//            )
        ),
        'ad'=>array(
//            'http://pic.edaijia.cn/client/lunbo01.png',
//            'http://pic.edaijia.cn/client/lunbo02.png',
//            'http://pic.edaijia.cn/client/lunbo03.png',
//            'http://pic.edaijia.cn/client/lunbo04.png',
        ),
        'banner'=>array(
//            'http://pic.edaijia.cn/client/baner.jpg'
        )
    );
}

$open=RCityList::model()->isOpenDayTime($city_id);
if(!$open){ //下线城市或新开通城市，用另外的轮播图
    // $data['update_time']=1417574649;
    $data['spacing']=100;
    $data['welcome']=array(
        array(
            'img'=>'http://pic.edaijia.cn/client/yindao02_new.png',
//            'href'=>'http://h5.edaijia.cn/activities/PICC2?from=weixin_app'
        ),
        array(
            'img'=>'http://pic.edaijia.cn/client/yindao03_new.png'
        ),
        array(
            'img'=>'http://pic.edaijia.cn/client/yindao04_new.png'
        )
    );
    $data['ad']=array(
        'http://pic.edaijia.cn/client/lunbo02.png',
        'http://pic.edaijia.cn/client/lunbo03.png',
        'http://pic.edaijia.cn/client/lunbo04.png',
    );
    $data['banner']=array();
}

$data['update_time']=1429912800; //动态从city_config表获取，这个会与客户端时间缓存比较，如果大于客户端，则会从服务器更新图片

$ret = array(
    'code'=>0,
    'message'=>'欢迎页',
    'data'=>$data
);
EdjLog::info('Cwelcomes ---- '.json_encode($ret));
echo json_encode($ret);