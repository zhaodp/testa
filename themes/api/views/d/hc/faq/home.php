<?php
/**
 * 司机端API：driver.faq.home 获取知识库一级分裂接口
 * @return json
 * @author wanglonghuan 2013-11-05
 * @version 1.0
 */
//$params['pageNo'] = ( empty($params['pageNo']) || $params['pageNo']<=0 ) ? '1' : $params['pageNo'];

$categoryList = Knowledge::getKnowledgeCategoryList();

$ret = array (
    'code'=>0,
    'data'=>array('faq_category'=>$categoryList),
    'message'=>'读取成功'
	);


echo json_encode($ret);
