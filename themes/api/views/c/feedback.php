<?php
/**
 * 客户端API：c.feedback 意见反馈,需要登陆，不登陆就不要反馈了
 *
 * @author
 * @param token
 * @param
 *
 * @return json 成功返回成功信息，异常返回错误代码，需要附带返回结果的例子
 * @example
 */

//验证参数格式
$token = isset($params['token']) ? $params['token'] : '';
$content = isset($params['content']) ? trim($params['content']) : '';
$device = isset($params['device']) ? trim($params['device']) : '';
$os = empty($params['os']) ? '' : trim($params['os']);
$macaddress = isset($params['macaddress']) ? trim($params['macaddress']) : '';
$version = isset($params['version']) ? trim($params['version']) : '';
$source = isset($params['from']) ? trim($params['from']) : '';

if (empty($token) && empty($content)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

//验证token
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array(
        'code' => 1,
        'message' => '验证失败',
    );
    echo json_encode($ret);
    return;
}

$dataFeedback = array();
$dataFeedback['email'] = $validate['phone'];
$dataFeedback['content'] = $content;
$dataFeedback['device'] = $device;
$dataFeedback['os'] = $os;
$dataFeedback['macaddress'] = $macaddress;
$dataFeedback['version'] = $version;
$dataFeedback['source'] = $source;
$dataFeedback['created'] = time();

$feedback_count = CustomerFeedback::model()->getFeedBackCountByPhone($validate['phone']);

$model = new CustomerFeedback();
$model->attributes = $dataFeedback;
if ($model->save()){
    //add by aiguoxin 加入建议列表
    CustomerSuggestion::model()->initSuggestion($validate['phone'],$dataFeedback['content'],
        CustomerSuggestion::TYPE_FEEDBACK,$model->attributes['id']);
    $ret = array(
        'code' => '0',
        'message' => '反馈提交成功'
    );
    echo json_encode($ret);
    return;
}else{
    $ret = array(
        'code' => '0',
        'message' => '反馈提交成功'
    );
    echo json_encode($ret);
    return;
}

