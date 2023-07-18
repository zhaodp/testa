<?php
/**
 * 无星级回评功能
 * User: liufugang
 * Date: 2015/4/30
 * Time: 12:27
 */
class OrderNoStarCommentConfigHandler{
    protected static $_models=array();

    public static $reasonCodes = array(
        "2"=>array(21,22,23,24,25,26),
        "9"=>array(91,92,93,94,95,96,97,98,99)
    );

    //尾号为2
    public static $reasons2 = array(
        "no_star_comment"=>1,
        "comment"=>array(
            array('code'=>21, 'detail'=>'很熟悉路线'),
            array('code'=>22, 'detail'=>'驾驶很平稳'),
            array('code'=>23, 'detail'=>'服务态度友好'),
            array('code'=>24, 'detail'=>'路线不熟悉'),
            array('code'=>25, 'detail'=>'猛踩刹车油门'),
            array('code'=>26, 'detail'=>'态度不友好')
        )
    );

    //尾号为9
    public static $reasons9 = array(
        "no_star_comment"=>1,
        "comment"=>array(
            array('code'=>91, 'detail'=>'很熟悉路线'),
            array('code'=>92, 'detail'=>'驾驶很平稳'),
            array('code'=>93, 'detail'=>'服务态度友好'),
            array('code'=>94, 'detail'=>'路线不熟悉'),
            array('code'=>95, 'detail'=>'猛踩刹车油门'),
            array('code'=>96, 'detail'=>'态度不友好'),
            array('code'=>97, 'detail'=>'未穿统一服装'),
            array('code'=>98, 'detail'=>'个人卫生不好'),
            array('code'=>99, 'detail'=>'未展示计价器')
        )
    );

    public static $noStarPhoneArray2 = array(
        15321369811,
        15110263493,
        15300070906,
        13161357028,
        13311526921,
        18511760287,
        18010167282
    );

    public static $noStarPhoneArray9 = array(
        13301027632,
        13552891276,
        13321134852,
        15300073731,
        15300067673,
        13466762537,
        13911483068
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

    public function getConfig($phone){
        if(in_array($phone,self::$noStarPhoneArray2)){
            return self::$reasons2;
        }
        if(in_array($phone,self::$noStarPhoneArray9)){
            return self::$reasons9;
        }
        $len = strlen($phone);
        $endStr = substr($phone,$len-1);
        if($endStr==2){
            return self::$reasons2;
        }else if($endStr==9){
            return self::$reasons9;
        }
        return self::$reasons2;
    }
    public function isNoStarPhone($phone=0){
        if(empty($phone)){
            return false;
        }
        if(in_array($phone,self::$noStarPhoneArray2)){
            return true;
        }
        if(in_array($phone,self::$noStarPhoneArray9)){
            return true;
        }
        $len = strlen($phone);
        $endStr = substr($phone,$len-1);
        if($endStr==2||$endStr==9){
            return true;
        }
        return false;
    }

    public function getReason($reasonCodes){
        if(empty($reasonCodes)){
            return false;
        }
        $codes = explode(",",$reasonCodes);
        $reasonStrs = "";
        foreach($codes as $k){
            $v = "";
            $v = $this->getDetail($k);
            if(!empty($v)){
                $reasonStrs = $reasonStrs.$v.",";
            }
        }
        return $reasonStrs;
    }

    private function getDetail($code){
        if(in_array($code,self::$reasonCodes["2"])){
            foreach(self::$reasons2["comment"] as $c){
                $k = $c["code"];
                $v = $c["detail"];
                if($code==$k){
                    return $v;
                }
            }
        }else if(in_array($code,self::$reasonCodes["9"])){
            foreach(self::$reasons9["comment"] as $c){
                $k = $c["code"];
                $v = $c["detail"];
                if($code==$k){
                    return $v;
                }
            }
        }
    }
}
