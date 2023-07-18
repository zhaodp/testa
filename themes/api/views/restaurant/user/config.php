<?php
/**
 * 获取商铺采集配置文件信息
 * User: zhanglimin
 * Date: 13-8-14
 * Time: 下午3:50
 */

Yii::import('application.config.*');
require_once ("config_restaurant.php");
$params = config_restaurant::get_config_params();

$tmp = array();
$i = 0 ;
//给手机端重新解析城市数据
foreach($params['cities'] as $citys){
    $j = 0 ;
    $regions_tmp = array();
    if(is_array($citys['regions'])){

        foreach($citys['regions'] as $regions){
            $k = 0 ;
            $business_circle_tmp = array();
            if(is_array($regions['business_circle'])){
                foreach($regions['business_circle'] as $business_circle){
                    $business_circle_tmp[$k] = array(
                        'name'=>$business_circle['name'],
                        'value'=>$business_circle['value'],
                    );
                    $k++;
                }
            }
            $regions_tmp[$j] = array(
              'name'=>$regions['name'],
              'value'=>$regions['value'],
              'business_circle'=>$business_circle_tmp,
            );
            $j++;
        }
    }
    $tmp[$i] = array(
      'name'=>$citys['name'],
      'value'=>$citys['value'],
      'regions' =>$regions_tmp,
    );
    $i++;
}

$params['cities'] = $tmp;

//给手机端重新解析物料详情
sort($params['materials']);

echo json_encode($params);
return ;