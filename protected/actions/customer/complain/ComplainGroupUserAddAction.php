<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-15
 * Time: 下午10:06
 * To change this template use File | Settings | File Templates.
 */

class ComplainGroupUserAddAction extends CAction {

    public function run() {
        if ($_POST) {
            $res = array('succ'=>0);
            $gid = Yii::app()->request->getPost('gid');
            $ouid = Yii::app()->request->getPost('ouid');
            $uid = Yii::app()->request->getPost('user');
            $role = Yii::app()->request->getPost('role');
            $user = AdminUser::model()->getUser($uid);
            if ($user) {
                $uname = $user['name'];
                if (!$ouid) {
                    $ret = CustomerComplainGroupUser::model()->addUser($gid, $uid, $uname, $role);
                } else {
                    $ret = CustomerComplainGroupUser::model()->updateGroupUser($gid, $ouid, $uid, $uname, $role);
                }
                if ($ret) {
                    $res['succ'] = 1;
                }
            }
            echo json_encode($res);
        } else {
            $gid = Yii::app()->request->getQuery('gid');
            $uid = Yii::app()->request->getQuery('uid');
            if (!$gid) {
                echo "<meta charset='utf-8'/>";
                echo "<script type='text/javascript' charset='utf-8'>alert('投诉任务组不存在');</script>";
                Yii::app()->end();
            }
            //任务组
            $group = CustomerComplainGroup::model()->getGroupById($gid);
            $gname = $group['name'];

            //部门
            $department = AdminDepartment::model()->getAllDepartment();
            $departmentArr = array('-1'=>'全部');
            foreach ($department as $item) {
                $departmentArr[$item['id']] = $item['name'];
            }
            $did = -1;
            $role = 2;
            $userArr = array('-1'=>'---全部---');
            if ($uid) {
                //获取用户
                $user = AdminUser::model()->getUser($uid);
                $did = $user['department_id'];
                //获取部门的所有用户
                $users = AdminUser::model()->getUserByDepartment($did);
                foreach ($users as $item) {
                    $userArr[$item['id']] = $item['name'];
                }

                $groupUser = CustomerComplainGroupUser::model()->getGroupUser($gid, $uid);
                $role = $groupUser['role'];
            }


            $this->controller->renderPartial('groupuseradd',array(
                'gid'=>$gid,
                'gname'=>$gname,
                'ulist'=>$userArr,
                'uid'=>$uid,
                'did'=>$did,
                'dlist'=>$departmentArr,
                'role'=>$role,
            ));
        }
    }
}