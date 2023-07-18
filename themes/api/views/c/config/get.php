<?php
/**
 * 客户端API：c.config.get get all the client side configs
 * 调用的url:
 * @author wangwenhao 2015-04-3
 * @param $config_name_ary  empty will return all the configs    
 *  order.cancel.reasons.received
 *  order.cancel.reasons.ready
 *  driver.remarks
 * @see
 * @since
 */
EdjLog::info ( 'c.config.get parameters:' . json_encode($params));
$ret = array();
$keys = $params['config_name_ary'];
if(empty($keys))
{
    $keys = ConfigHandlerMapping::model()->getAllKeys();
}
else
{
    $keys = explode(',',$params['config_name_ary']);
}

EdjLog::info ( 'c.config.get parameters2:' . json_encode($keys));

foreach($keys as $key)
{
    try
    {
        $handler = ConfigHandlerMapping::model()->getHandler($key);
        {
            if(!is_null($handler))
            {
                $ret[$key] = $handler->getConfig();
            }
        }
    }
    catch (Exception $e) {

        EdjLog::warning('c.config.get run '.$key.' config handler failed, message:' . $e->getMessage() , 'console');
    }
}

$token = isset($params['token'])?$params['token']:"";
if(!empty($token)){
    $noStarComment = ConfigHandlerMapping::model()->getNoStarComment($token);
    if($noStarComment){
        $ret["order.no.star.comment"] = $noStarComment;
    }
}

EdjLog::info ( 'c.config.get ret:' . json_encode($ret));
echo json_encode($ret);

