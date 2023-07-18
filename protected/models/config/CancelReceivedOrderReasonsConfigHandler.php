<?php
/**retrieve received order cancel reason list
 * @author wangwenhao
 * 2015-04-07
 */
class CancelReceivedOrderReasonsConfigHandler extends ConfigBaseHandler{
    protected static $_models=array();
    public static $s_reasons=array(
        "desc"=>"请选择您取消代驾订单的原因，我们会加强改进服务",
        "cancel_reasons"=>array(
            array('code'=>101, 'detail'=>'司机到位太慢，等不及'),
            array('code'=>102, 'detail'=>'代驾收费太贵'),
            array('code'=>103, 'detail'=>'司机要求取消订单'),
            array('code'=>104, 'detail'=>'找到了其他代驾'),
            array('code'=>105, 'detail'=>'暂时不需要代驾了'),
            array('code'=>109, 'detail'=>'') //others
        )
            );

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    public function getConfig(){
        $reasons = CancelOrderReasonsRedis::model()->getReceivedOrderCancelReasons();
        if(empty($reasons)){
            $reasons=self::$s_reasons;
        }

        return $reasons;

    }
}

