<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-15
 * Time: 下午19:06
 * To change this template use File | Settings | File Templates.
 */

class ComplainManualDispatchAction extends CAction {

    public function run() {

        if ($_POST) {
            $id = Yii::app()->request->getPost('id');
            $gid = Yii::app()->request->getPost('group');
            $uid = Yii::app()->request->getPost('user');
            $ret = CustomerComplain::model()->setComplainUser($id, $gid, $uid);
            if ($ret) {
                $res['succ'] = 1;
            } else {
                $res['succ'] = 0;
            }
            echo json_encode($res);
            Yii::app()->end();
        } else {
            $id = Yii::app()->request->getQuery('id');

            $model = new CustomerComplain();
            $data = $model->getComplainById($id);
            if ($data['complain_type']>0) {
                $subtype = $data['complain_type'];
                $type = CustomerComplainType::model()->getTypeById($subtype);
                $data['second_type'] = $type['name'];
                $parentType = CustomerComplainType::model()->getTypeById($type['parent_id']);
                $data['top_type'] = $parentType['name'];

            } else {
                $data['top_type'] = '';
                $data['second_type'] = '';
            }

            //获取投诉任务组
            $complainGroup = CustomerComplainGroup::model()->getAllGroup();
            $groupArr = array('-1' => '全部');
            foreach ($complainGroup as $item) {
                $groupArr[$item['id']] = $item['name'];
            }

            $gid = -1;
            $uid = -1;
            $userArr = array('-1' => '---全部---');
            if ($data['user_id'] && $data['group_id']) {
                $gid = $data['group_id'];
                $uid = $data['user_id'];
                //查询任务组中的人
                $users = CustomerComplainGroupUser::model()->getAllGroupUser($gid);
                if (!empty($users)) {
                    foreach($users as $v) {
                        $userArr[$v['uid']] = $v['uname'];
                    }
                }
            }

            $this->controller->renderPartial('manual_dispatch',array(
                'data'=>$data,
                'grouplist'=>$groupArr,
                'userlist'=>$userArr,
                'gid'=>$gid,
                'uid'=>$uid
            ));
        }
    }
}