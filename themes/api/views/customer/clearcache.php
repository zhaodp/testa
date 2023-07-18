<?php
/**
 * 删除cache接口
 * @var $params['key']
 */
$cacheKey = $params['key'];
$clearCache = Yii::app()->cache->delete($cacheKey);
if( $clearCache ){
	echo "成功清除缓存";
}else{
	echo "清除缓存失败";
}