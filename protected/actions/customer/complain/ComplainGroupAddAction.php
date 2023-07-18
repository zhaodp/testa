<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-15
 * Time: 下午10:06
 * To change this template use File | Settings | File Templates.
 */

class ComplainGroupAddAction extends CAction {

    public function run() {
        if ($_POST) {
            $gid = Yii::app()->request->getPost('gid');
            $gname = Yii::app()->request->getPost('gname');
            if ($gid) {//更新
                $ret = CustomerComplainGroup::model()->updateGroup($gid, $gname);
            } else {//新建
                $ret = CustomerComplainGroup::model()->saveGroup($gname);
            }
            if ($ret) {
                $res['succ'] = 1;
            } else {
                $res['succ'] = 0;
            }
            echo json_encode($res);
        } else {
            $gid = Yii::app()->request->getQuery('gid');
            $gname = Yii::app()->request->getQuery('gname');

            $this->controller->renderPartial('groupadd',array(
                'gid'=>$gid,
                'gname'=>$gname,
            ));
        }
    }
}