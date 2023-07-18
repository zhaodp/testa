<?php
/**
 * 司机信息完善
 * @author dingcheng
 * @version 2015-03-13
 */

$data['contact'] = isset($params['contact']) ? trim($params['contact']) : '';
$data['contact_phone'] = isset($params['contact_phone']) ? trim($params['contact_phone']) : '';
$data['contact_relate'] = isset($params['contact_relate']) ? trim($params['contact_relate']) : '';
$callback = isset($params['callback']) ? $params['callback'] : '';
$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
if ( empty($driver_id) ) {
    $ret = array('code' => 1, 'message' => '参数有误11');
    $json_str = json_encode($ret);
    if (isset($callback) && !empty($callback)) {
        $json_str = $callback . '(' .json_encode($ret) . ')';
    }
    echo $json_str ;
    return ;
}

if ( empty($data['contact'])) {
    //在这里查询司机报名信息，返回
    $dr = DriverRecruitment::model()->findByDriverId($driver_id);
    if(!$dr) {
        $ret = array('code' => 1, 'message' => '司机不存在');
        $json_str = json_encode($ret);
        if (isset($callback) && !empty($callback)) {
            $json_str = $callback . '(' .json_encode($ret) . ')';
        }
        echo $json_str ;
        return ;
    }
    $ret = array();
    $ret['ret'] = 0;
    $ret['contact'] = $dr['contact'] ;
    $ret['contact_phone'] = $dr['contact_phone'] ;
    $ret['contact_relate'] = $dr['contact_relate'] ;
    $json_str = json_encode($ret);
    if (isset($callback) && !empty($callback)) {
        $json_str = $callback . '(' .json_encode($ret) . ')';
    }
    echo $json_str ;
    return ;
} else {
    //在这里更新司机报名信息
    $dr = DriverRecruitment::model()->findByDriverId($driver_id);
    if($dr) {
        $dr['contact'] = $data['contact'];
        $dr['contact_phone'] = $data['contact_phone'];
        $dr['contact_relate'] = $data['contact_relate'];
        $dr->save(false) ;
        $ret = array('code' => 0, 'message' => '修改成功');
        $json_str = json_encode($ret);
        if (isset($callback) && !empty($callback)) {
            $json_str = $callback . '(' .json_encode($ret) . ')';
        }
        echo $json_str ;
        return ;
    }else{
        //返回错误
        $ret = array('code' => 1, 'message' => '司机不存在');
        $json_str = json_encode($ret);
        if (isset($callback) && !empty($callback)) {
            $json_str = $callback . '(' .json_encode($ret) . ')';
        }
        echo $json_str ;
        return ;
    }
}
