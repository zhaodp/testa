<?php
/**
 * config handler mapping 
 * @author wangwenhao
 * 2015-04-07
 */
class ConfigHandlerMapping {
    protected static $_models=array();
    private static $handlerAry = array();

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
            self::init();
        }
        return $model;
    }

    private static function init()
    {
        self::$handlerAry['driver.remarks'] = DriverRemarksConfigHandler::model();
        self::$handlerAry['order.cancel.reasons.received'] = CancelReceivedOrderReasonsConfigHandler::model();
        self::$handlerAry['order.cancel.reasons.ready']=CancelReadyOrderReasonsConfigHandler::model();
    }

    public function getHandler($key){
        $handler = self::$handlerAry[$key];
        return $handler;
    }

    public function getAllKeys()
    {
        $keys = array_keys(self::$handlerAry);
        return $keys;
    }

    public function getNoStarComment($token){
        $no_star_comment = Yii::app()->params['no_star_comment'];
        EdjLog::info("No start comment,token:".$token.",on or off:".$no_star_comment);
        if($no_star_comment=="on"){
            if(empty($token)){
                EdjLog::info("No start comment,token is empty");
                return false;
            }
            $validate = CustomerToken::model()->validateToken($token);
            if (!$validate) {
                EdjLog::info("No start comment,invalid token,token:".$token);
                return false;
            }
            $phone = trim($validate['phone']);
            EdjLog::info("No start comment,valid token,token:".$token.",phone:".$phone);
            $noStartCommentBean = OrderNoStarCommentConfigHandler::model();
            if($noStartCommentBean->isNoStarPhone($phone)){
                EdjLog::info("No start comment,phone in no star,token:".$token.",phone:".$phone);
                return $noStartCommentBean->getConfig($phone);
            }
            EdjLog::info("No start comment,phone not in no star,token:".$token.",phone:".$phone);
            return false;
        }
        return false;
    }
}

