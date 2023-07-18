<?php
/**
 * 司机端API：driver.faq.list 获取知识库二级分类及知识文章列表
 * @return json
 * @param pid 父分类id category_id(可选 默认为全部)
 * @author wanglonghuan 2013-11-05
 * @version 1.0
 */

$token = isset($params['token'])?$params['token']:'';
$driver = DriverStatus::model()->getByToken($token);
if ( empty($driver) ||  $driver->token===null || $driver->token!==$token ) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}
$city_id = $driver->city_id;
//$city_id = 1;
if(empty($params['pid']) || $params['pid']<=0 ){
    $ret = array (
        'code'=>2,
        'message'=>'参数错误 请传正确的分类id');
    echo json_encode($ret);
    return;
}
$params['pageNo'] = ( empty($params['pageNo']) || $params['pageNo']<=0 ) ? 1 : $params['pageNo'];
$params['pageSize'] = empty($params['pageSize']) ? 10 : trim($params['pageSize']);
$offset = $params['pageSize'] * ($params['pageNo'] - 1);


$childCategories = Knowledge::getChildCategoryList($params['pid']);
$keys = array_keys($childCategories);
$cid = empty($childCategories) || !isset($keys[0])?0:$keys[0];
$cid = (empty($params['cid']) || $params['cid']<=0 )?$cid:$params['cid'];

//$city_id = 1;  //上线时去掉此处 用上边driver->city_id 的
$knowledgeList = Knowledge::getKnowledgeList($params['pid'],$cid,$city_id,$params['pageSize'],$offset);

$data = array();
$data['tab_list'] = $childCategories;
$data['knowledge_list'] = $knowledgeList;


$ret = array (
    'code'=>0,
    'data'=>$data,
    'message'=>'读取成功');

echo json_encode($ret);
return;
