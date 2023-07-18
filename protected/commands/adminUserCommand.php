<?php
/**
 * 系统用户相关
 * User: duke
 * Date: 14-07-02
 */

class adminUserCommand extends CConsoleCommand
{

    /**
     * 重置后台用户密码并发送短信 双因子认证用户忽略
     * @uses <int> 用户id（传递次参数后将只重置此id用户）
     * @author bidong 2013-09-11
     */
    public function actionResetUserPwd($user = null)
    {
        //$phones=array('13581855712','13241350002','15901530932','18511663962','18911893993','18701552183');

        $job_title = '重置后台用户密码';
        echo Common::jobBegin($job_title);

        $condition = 'status=:status and auth_type = :atype';
        $params = array(':status' => AdminUserNew::STATUS_NORMAL,':atype'=>AdminUserNew::AUTH_TYPE_NORMAL);
        if ($user != null) {
            $condition .= ' AND (name = :name OR id = :name)';
            $params[':name'] = $user;
        }
        $adminUserList = AdminUserNew::model()->findAll($condition, $params);
        if (!empty($adminUserList)) {
            foreach ($adminUserList as $i => $adminUser) {
                if (is_object($adminUser)) {
                    $user = $adminUser->name;
                    $user_id = $adminUser->id;
                    $admin_level = $adminUser->level;
                    if($admin_level == 2 ) {
                        continue;
                    }else{
                        $result = AdminUserNew::model()->resetPassword($user_id, 'all');
                            if ($result)
                                echo 'user：' . $user . "\r\n";
                    }
                }
                //QQ邮箱发送频率有所限制，超出一定频率后可能会被屏蔽一段时间
                if ($i % 50 == 0) {
                    sleep(60);
                }
            }
        }
        echo Common::jobEnd($job_title);
    }

    /**
     * 忘记密码，自动重置
     * @auhtor bidong 2013-09-13
     */
    public function actionForgetPassword()
    {

        //启动一个进程，随机执行3-10分钟。防止长时间不完。
        $timestamp = time();
        $quit_time = rand(2, 5) * 59;

        while (true) {
            if (time() - $timestamp > $quit_time) {
                echo "\n" . "the worker over define process time: runed {$quit_time}s\n";
                break;
            } else {
                $job_title = '自动重置密码';
                echo Common::jobBegin($job_title);

                $retSms = SmsMo::model()->getResetPwdSMS(Sms::CHANNEL_SOAP);
                if ($retSms && is_array($retSms)) {
                    foreach ($retSms as $sms) {
                        $sender = $sms['sender'];
                        //验证是否系统用户
                        $adminUser = AdminUserNew::model()->checkPhone(trim($sender));
                        if ($adminUser && is_array($adminUser)) {
                            //重置密码并发送短信
                            $ret = AdminUserNew::model()->resetPassword($adminUser['id'], 'all');
                            echo 'name: ' . $adminUser['name'];
                            if ($ret) {
                                echo "-- succ \r\n";
                            } else {
                                echo "-- error \r\n";
                            }
                        }
                    }
                } else {
                    sleep(10);
                }

                echo Common::jobEnd($job_title);
            }
        }


    }


    public function actionMailAdminLog($date = ''){ //date = 2014-09-21
        if($date){
            $start_time = date('Y-m-d 00:00:00',strtotime($date));
            $end_time = date('Y-m-d 23:59:59',strtotime($date));
        }
        else{
            $start_time = date('Y-m-d 00:00:00',strtotime('-1 day'));
            $end_time = date('Y-m-d 23:59:59',strtotime('-1 day'));
        }
        $userAll = AdminUserNew::model()->findAll();
        $data = array();
        if(!empty($userAll)){
            foreach($userAll as $obj){
                $sql = "SELECT username,count(username) as count  FROM `t_admin_logs` WHERE created >= '{$start_time}'  and created <= '{$end_time}' and user_id = '{$obj->id}' group by username order by count(username) desc limit 11;";
                $low_command = Yii::app()->dbstat_proxy->createCommand($sql);
                $data_tmp = $low_command->queryRow();
                if(isset($data_tmp['count'])){
                    $data[$data_tmp['count']] = $data_tmp;
                }
            }
            krsort($data);
            $data = array_slice($data, 0, 11);
        }

        //print_r($data);die;
        $content = $date.'后台访问量top10用户<br><br><table border="1" cellspacing="0" cellpadding="0"><tr><th style="padding:5px;">用户名</th><th>部门</th><th style="padding:5px;">当日访问次数</th></tr>';
        if($data){
            foreach($data as $v){
                $user_info = AdminUserNew::model()->getInfoByName(trim($v['username']));
                //print_r($user_info);die;
                if($user_info){
                    $dep_id = $user_info->department_id;
                    $dep_name = AdminDepartment::model()->getNameByIds($dep_id);
                    $dep_name = $dep_name  ?  $dep_name[$dep_id] : '';
                }

                else $dep_name = '';
                $content.='<tr><td style="padding:5px;">'.$v['username'].'</td><td>'.$dep_name.'</td><td style="padding:5px;">'.$v['count'].'</td>';
            }
        }
        else{
            $content.='<tr><td colspan="3">没有当日数据</td></tr>';
        }
        $content .='</table>';
        //echo $content;die;
        Mail::sendMail(array('dengxiaoming@edaijia-inc.cn','dongkun@edaijia-inc.cn'),$content,$date.'后台访问量top10用户');
        //Mail::sendMail(array('dongkun@edaijia-inc.cn'),$content,date('Y年m月d日').'后台访问量top10用户');
    }


//    /**
//     * 重置后台用户密码并发送短信
//     * @uses <int> 用户id（传递次参数后将只重置此id用户）
//     * @author bidong 2013-09-11
//     */
//    public function actionResetUserPwdNew($user = null)
//    {
//        //$phones=array('13581855712','13241350002','15901530932','18511663962','18911893993','18701552183');
//
//        $job_title = '重置后台用户密码';
//        echo Common::jobBegin($job_title);
//
//        $condition = 'status=:status ';
//        $params = array(':status' => AdminUserNew::STATUS_NORMAL);
//        if ($user != null) {
//            $condition .= ' AND (name = :name OR id = :name)';
//            $params[':name'] = $user;
//        }
//        $adminUserList = AdminUserNew::model()->findAll($condition, $params);
//
//        if (!empty($adminUserList)) {
//            foreach ($adminUserList as $i => $adminUser) {
//                if (is_object($adminUser)) {
//                    $user = $adminUser->name;
//                    $user_id = $adminUser->id;
//
//                    $result = AdminUserNew::model()->resetPassword($user_id, 'all');
//                    if ($result){
//                        if($adminUser->email){
//                            $mod = new TFA();
//                            $res = $mod->getKey($adminUser->email,$adminUser->name,$result);
//                            if(isset($res['key'])){
//                                $model = new AdminUserNew();
//                                $model->updateByPk($user_id,array('secure_key'=>$res['key']));
//                            }else{
//                                echo 'no key '.$user."\n";
//                            }
//                        }
//                        echo 'user：' . $user . "\r\n";
//                    }
//
//                }
//                //QQ邮箱发送频率有所限制，超出一定频率后可能会被屏蔽一段时间
//                if ($i % 50 == 0) {
//                    sleep(60);
//                }
//            }
//        }
//        echo Common::jobEnd($job_title);
//    }


}
