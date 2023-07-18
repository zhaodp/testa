<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-16
 * Time: 上午10:36
 * To change this template use File | Settings | File Templates.
 */

class ComplainGroupUserDelAction extends CAction {

    public function run() {
        $res = array('succ'=>0);
        $gid = Yii::app()->request->getQuery('gid');
        $uid = Yii::app()->request->getQuery('uid');
        if ($gid && $uid) {
            $ret = CustomerComplainGroupUser::model()->deleteUser($gid, $uid);
            if ($ret) {
                $res['succ'] = 1;
            }
        }
        echo json_encode($res);
    }
}