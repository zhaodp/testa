<?php
/**retrieve ready  order cancel reason list
 * @author wangwenhao
 * 2015-04-07
 */
class CancelReadyOrderReasonsConfigHandler extends ConfigBaseHandler{
    proTected static $_models=array();
    public static $s_reasons = array
        (
        "desc"=>"请选择您取消代驾订单的原因，我们会加强改进服务",
        "cancel_reasons"=>array(
            array('code'=>121, 'detail'=>'司机故意拖延时间'),
            array('code'=>122, 'detail'=>'服务态度差'),
            array('code'=>123, 'detail'=>'跟司机协商消单后继续代驾'),
            array('code'=>124, 'detail'=>'代驾收费太贵'),
            array('code'=>125, 'detail'=>'暂时不需要代驾了'),
            array('code'=>129, 'detail'=>'') //others
        )
        );

    public Static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    public function getConfig(){
        $reasons = CancelOrderReasonsRedis::model()->getReadyOrderCancelReasons();
        if(empty($reasons)){
            $reasons = self::$s_reasons;
        }

        return $reasons;

    }
}

