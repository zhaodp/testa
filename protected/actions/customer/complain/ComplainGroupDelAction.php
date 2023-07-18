<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-16
 * Time: 上午10:36
 * To change this template use File | Settings | File Templates.
 */

class ComplainGroupDelAction extends CAction {

    public function run() {
        $res = array('succ'=>0);
        $gid = Yii::app()->request->getQuery('gid');
        if ($gid) {
            $ret = CustomerComplainGroup::model()->deleteGroup($gid);
            if ($ret) {
                $res['succ'] = 1;
            }
        }
        echo json_encode($res);
    }
}