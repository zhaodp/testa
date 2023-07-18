<?php
/**
 *  * 用来做自己写的代码的基本测试
 *   *
 *    * @author : yuchao@edaijia-inc.cn
 *     * Date: 16/10/14
 *      * Time: 19:03
 *       */
Yii::import('application.models.admin.AdminLogs');

class TAdminLogsCommand extends LoggerExtCommand{

  public function actionAddLogs($userId = "1001", $userName = "yuchao"){

    $userId = mt_rand(10000,99999)."100";
    $userName = "YuChao";

    $user = $this->mockUser($userId, $userName);


    $log = new AdminLogs();
    $logset = $log->addAdminOptLogs($user);
    var_dump($logset);
  }

  public function actionDeleteAll($userId = 0){
    Order::model()->deleteByPk($userId);

  }

  public function actionGetAdminLog($userId){

  }

  private function mockUser($userId, $userName, $ip="localhost" ){

    $params = array (
      'user_id' => $userId,
      'username'=>$userName,
      'ip'  => $ip,
      'agent' => "Mac Air",
      'status'=> 9,
      'url'=>'www.edaijia.cc',
      'created'=>date(Yii::app()->params['formatDateTime'], time())
    );
    return $params;

  }

}
