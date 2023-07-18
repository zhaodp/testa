<?php
/**
 * Created by JetBrains PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-15
 * Time: 上午10:06
 * To change this template use File | Settings | File Templates.
 */

class ComplainDispatchAction extends CAction {

    public function run() {

        $groups = CustomerComplainGroup::model()->getAllGroup();

        foreach ($groups as $k=>$v) {
            $user = CustomerComplainGroupUser::model()->getAllGroupUser($v['id']);
            $groups[$k]['user'] = $user;
        }

        //var_dump($groups);die;

        $this->controller->render('dispatch',array(
            'groups'=>$groups,
        ));
    }
}