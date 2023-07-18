<?php
/**
 * 新增商家信息
 * User: zhanglimin
 * Date: 13-8-14
 * Time: 下午5:21
 */

$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

$latitude=isset($params['latitude'])&&!empty($params['latitude']) ? trim($params['latitude']) : "";

$longitude=isset($params['longitude'])&&!empty($params['longitude']) ? trim($params['longitude']) : "";

$name = isset($params['name'])&&!empty($params['name']) ? trim($params['name']) : "";

$address = isset($params['address'])&&!empty($params['address']) ? trim($params['address']) : "";

if (
    empty($token) ||
    empty($longitude) ||
    empty($latitude) ||
    empty($name) ||
    empty($address)
){
    $ret=array(
        'code'=>2,
        'message'=>'参数不正确!'
    );
    echo json_encode($ret);
    return;
}

$restaurantToken = RestaurantToken::model()->validateToken($token);

if(empty($restaurantToken)){
    $ret=array(
        'code'=>1,
        'message'=>'token失效!'
    );
    echo json_encode($ret);
    return;
}
$longitude=number_format(doubleval($longitude), 6);
$latitude=number_format(doubleval($latitude), 6);

$photo_str = isset($params['photos']) ? rtrim($params['photos'],"|") : "" ;// 照片
$photos = array();
if(!empty($photo_str)){
    $photos = explode("|",$photo_str);
}

$materials_str = isset($params['materials']) ? rtrim($params['materials'],"|") : "" ;// 物料详情
$materials = array();
if(!empty($materials_str)){
    $materials = explode("|",$materials_str);
}

//添加task队列
$task=array(
    'method'=>'restaurant_add',
    'params'=>array(
        'latitude' => $latitude,
        'longitude' => $longitude,
        'name' => $name , // 名字
        'address' => $address ,// 地址
        'photos' =>$photos,
        'contact' => isset($params['contact']) ? $params['contact'] : "" ,// 联系人
        'title' => isset($params['contact_job']) ? $params['contact_job'] : "" ,// 联系人职位
        'telephone' => isset($params['work_phone']) ? $params['work_phone'] : "" ,// 固定电话
        'city' => isset($params['city']) ? $params['city'] : "" ,// 城市
        'mobile' => isset($params['mobile_phone']) ? $params['mobile_phone'] : "" ,// 手机
        'district' => isset($params['region']) ? $params['region'] : "" ,// 行政区
        'zone' => isset($params['business_circle']) ? $params['business_circle'] : "" ,// 商圈
        'type' => isset($params['business_type']) ? $params['business_type'] : "" ,// 商家类型类型
        'tables' => isset($params['tables']) ? $params['tables'] : "" ,// 桌数
        'tables_type' => isset($params['tables_type']) ? $params['tables_type'] : "" ,// 桌数类型
        'remark' => isset($params['appendixes']) ? $params['appendixes'] : "" ,// 备注
        'updated' => isset($params['materials_checked_at']) ? $params['materials_checked_at'] : "" ,// 物料最后检查时间
        'cost' => isset($params['cost']) ? $params['cost'] : 0 ,// 消费
        'demand_index' => isset($params['demand_index']) ? $params['demand_index'] : 0 ,// 代驾需求指数

        'competition_arr' => array(
            'restaurant_info'=>array(
                'channel_type'=>isset($params['channel_type']) ? $params['channel_type'] : "self",// 渠道类型，"自有" | "酒商"
                'has_competition'=>isset($params['has_competition']) ? $params['has_competition'] : 0,// 是否有竞品物料
                'has_competition_wiped'=>isset($params['has_competition_wiped']) ? $params['has_competition_wiped'] : 0,// 竞品物料是否已清除
                'has_materials'=>isset($params['has_materials']) ? $params['has_materials'] : 0,// 是否已进店
            ),
            'materials_info'=> $materials,
        ),

        'user_id' => $restaurantToken->user_id , //用户ID

    )
);

Queue::model()->putin($task,'task');

$ret=array(
    'code'=>0,
    'message'=>'成功'
);
echo json_encode($ret);
return;

