<?php
/**
 * 客户端API:open.customer.activity 获取本城市最新市场活动 
 */
MarketingActivityRedis::model()->clearCache();
 $ret = array(
        'code' => 0,
        'message' => '清理成功'
    );
    echo json_encode($ret);
    return;
