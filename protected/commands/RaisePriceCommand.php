<?php
/**
 *  * 用来做自己写的代码的基本测试
 *   *
 *    * @author : yuchao@edaijia-inc.cn
 *     * Date: 16/10/14
 *      * Time: 19:03
 *       */

class RaisePriceCommand extends LoggerExtCommand{


  public function actioncityaddprice($cityId){
    $add = RaisePrice::model()->getCityAddPriceByID($cityId);
    if($add){
      echo "succ ";
      print_r($add);
    }else{
      echo "fail";
    }
  }

  public function actiondelallcache(){
    $deleteall = RaisePrice::model()->deleteall();
    if($deleteall){
      echo "delete all raise price succ";
    }else{
      echo "delete fail";
    }
  }

  public function actionloadallcache(){
    $loadall = RaisePrice::model()->loadall();
    if($loadall){
      echo "load all raise price succ";
    }else{
      echo "load fail";
    }
  }


}
