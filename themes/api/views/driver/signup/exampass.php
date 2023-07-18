<?php
/**
 * 司机报名h5---考试通过
 * @author luzhe
 * @version 2014-08-10
 */
$id_card = isset($params['id_card']) ? trim($params['id_card']) : '';
$q_id    = isset($params['q_id']) ? trim($params['q_id']) : '';
$callback= isset($params['callback'])?$params['callback']:'';

if(empty($id_card) || empty($q_id)){
    $ret = array('code' => 1 , 'message' => '参数错误');
    $json_str = json_encode($ret);
    if(isset($callback)&&!empty($callback)){
        $json_str=$callback.'('.$json_str.')';
    }
    echo $json_str;Yii::app()->end();
} 
 $is_suc = DriverExamTestSuccess::model()->addClientExamSuccess($q_id,$id_card);
 if(isset($is_suc)){
    if($is_suc){
	$ret = array('code' => 0 , 'message' => '考试通过');
    	$json_str = json_encode($ret);
    	if(isset($callback)&&!empty($callback)){
        	$json_str=$callback.'('.$json_str.')';
    	}
    	echo $json_str;Yii::app()->end();
    }else{
	$ret = array('code' => 1 , 'message' => '请先报名再参加考试');
    	$json_str = json_encode($ret);
    	if(isset($callback)&&!empty($callback)){
        	$json_str=$callback.'('.$json_str.')';
    	}
    	echo $json_str;Yii::app()->end();
    }
 }else{
    $ret = array('code' => 1 , 'message' => '在更新考试信息过程中发生错误');
    $json_str = json_encode($ret);
    if(isset($callback)&&!empty($callback)){
        $json_str=$callback.'('.$json_str.')';
    }
    echo $json_str;Yii::app()->end();
}
