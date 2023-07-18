<?php
//保存反馈内容

$dataFeedback = array(); 
$dataFeedback['email'] = $params['email'];
$dataFeedback['content'] = $params['content'];
$dataFeedback['device'] = $params['device'];
$dataFeedback['os'] = $params['os'];
$dataFeedback['macaddress'] = $params['macaddress'];
$dataFeedback['version'] = $params['version'];
$dataFeedback['source'] = $params['from'];
$dataFeedback['created'] = time();

$ret = array (
	'code' => '1',
	'message' => '保存失败'
);

$model = new CustomerFeedback();
$model->attributes = $dataFeedback;
if ($model->save())
{
	$ret = array (
		'code' => '0',
		'message' => '反馈提交成功'
	);
}

echo json_encode($ret);