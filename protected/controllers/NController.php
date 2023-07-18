<?php

class NController extends CController {

    public function actionNewNearby($lng=0.0, $lat=0.0, $id) {
        $lng = doubleval($lng);
        $lat = doubleval($lat);
        $count = 50;
        $max_distance = 10000;
        $idel_driver=DriverGPS::model()->nearby_client($lng, $lat, 0, $count, $max_distance);

        foreach($idel_driver as &$driver) {
            $driver_id = $driver['driver_id'];

            //create sms url
            $appkey=Yii::app()->params['edj_api_key'];
            $params=array(
                'appkey'=>$appkey,
                'ver'=>'3',
                'func'=>'client/sendsms',
                'wid'=>$driver['driver_id'],
                'queue_id'=>$id,
                'timestamp'=>date('Y-m-d H:i')
            );

            $sig=Api::createSigV2($params, $appkey);

            $url='/v2/index.php?r=api&'.http_build_query($params).'&sig='.$sig;
            $driver['url'] = $url;
        }
        echo json_encode($idel_driver);
        exit;
    }
}