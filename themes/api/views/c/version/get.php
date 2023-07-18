<?php
/**
 * 客户端API：c.version.get 获取App版本号
 * 调用的url:
 * @author sunhongjing 2013-10-14
 * @see  app.version.get
 * @since
 */
$ret = Yii::app()->params['appVersion'];
echo json_encode($ret);