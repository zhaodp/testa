<?php
//获取App文字内容
$ret = Yii::app()->params['appContent'];
$ret['priceContent'] = '';
$return = array();

foreach ($ret as $key=>$value){
	if($key != 'priceContent')
		$return[$key] = $value;
}
echo json_encode($return);