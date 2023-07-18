<?php
set_time_limit(500);
class RechargeBatchAction extends CAction
{

    public function run(){
        $message = '';
        $i = $j = 0;
        if(isset($_POST) && isset($_FILES['vip'])){

            $array = file($_FILES['vip']['tmp_name']);
            //print_r($array);die;
            if(is_array($array)){
                //比较admin 用户 看是否有遗漏的用户
                $adminAllInfo = AdminUserNew::model()->findAll('status = :status',array(':status'=>AdminUserNew::STATUS_NORMAL));
                foreach($adminAllInfo as $v){
                    $adminAll[$v->id] = $v->name;
                    $adminInfoById[$v->id]=$v->attributes;
                }


                //先检测电话号码格式是否正确
                foreach($array as $line) {
                    //$item = preg_split('/\s+/', $line);
                    $item = explode(',',$line);
                    if(!empty($item[0]) && !empty($item[1])) {
                        $user_name = trim($item[0]);
                        $name_encoding = mb_detect_encoding($user_name);

                        if($name_encoding != 'UTF-8'){
                            $user_name = mb_convert_encoding($user_name,'UTF-8',array('GBK','gb2312'));
                        }

                        $checkFirst = mb_substr($user_name,0,1,'utf-8');

                        $a = rawurlencode($checkFirst);

                        if($a == '%EF%BB%BF'){
                            $user_name = mb_substr($user_name,1,20,'utf-8');
                        }

                        $phone = $s = trim($item[1]);
                        $phone = trim(str_replace(' ','',$phone));
                        $phone = str_replace(chr(0xC2).chr(0xA0),'',$phone);
                        if(strlen($phone) > 11 || !is_numeric($phone)){
                            throw new CHttpException('404','电话号码只能是数字和空格。且是11位数字 电话：'.$phone);
                        }
                        else {
                            $exist_keys = array_search($user_name,$adminAll);
                            if($exist_keys) unset($adminAll[$exist_keys]);
                        }
                    }
                }

                foreach($array as $line) {
                    $item = explode(',',$line);
                    if(!empty($item[0]) && !empty($item[1])) {
                        $user_name = trim($item[0]);
                        $name_encoding = mb_detect_encoding($user_name);
                        if($name_encoding != 'UTF-8'){
                            $user_name = mb_convert_encoding($user_name,'UTF-8',array('GBK','gb2312'));
                        }
                        $checkFirst = mb_substr($user_name,0,1,'utf-8');

                        $a = rawurlencode($checkFirst);

                        if($a == '%EF%BB%BF'){
                            $user_name = mb_substr($user_name,1,20,'utf-8');
                        }
                        $phone = trim($item[1]);
                        $phone = str_replace(' ','',trim($phone));
                        $phone = str_replace(chr(0xC2).chr(0xA0),'',$phone);
                        if(strlen($phone) > 11){
                            throw new CHttpException('404','电话号码只能是数字和空格。且是11位数字 电话：'.$phone);
                        }

                        $res = $this->charge($user_name,$phone,100);
                        if($res) {
                            $message .= $res;
                            $j ++;
                        }
                        else $i ++;


                    }
                    else {
                        if(isset($item[0]) || isset($item[1])) {
                            $j ++;
                            $message .= "用户或电话缺少内容：<br>".(isset($item[0]) ? '缺少电话'.$item[0] : (isset($item[1]) ? '缺少用户名'.$item[1] : '空行'));
                        }
                    }
                }
                $mail_content = '操作人：'.Yii::app()->user->name.'<br>';
                $mail_content .= '日期：'.date('Y-m-d H:i:s').'<br>';
                $mail_content .= '成功充值'.$i.'人,失败'.$j.'人<br>';
                $mail_content .= $message;
                if(!empty($adminAll)){
                    $mail_content.='<hr><h3>以下用户没有在EXCEL中出现请留意。</h3><br>';
                    $dep_info = AdminDepartment::model()->getAll(1);
                    foreach($adminAll as $user_id => $user_name){
                        if($user_id && $adminInfoById[$user_id]['department_id'] && isset($dep_info[$adminInfoById[$user_id]['department_id']])){
                            $dep_name = $dep_info[$adminInfoById[$user_id]['department_id']];
                            $mail_content .= "用户ID :{$user_id}-- 姓名：{$user_name} -- 部门：{$dep_name}<br>";
                        }
                        else {
                            //echo  $user_id; print_r($adminInfoById);die;
                        }
                    }
                }

                Mail::sendMail(array('dengxiaoming@edaijia-inc.cn','dongkun@edaijia-inc.cn'),$mail_content,date('Y-m-d').'VIP批量充值状态');
            }
            //echo 'empty';
            $message .= '成功充值'.$i.'人,失败'.$j.'人<br>';
        }
        //echo $message;

        $this->controller->render('rechargeBatch',array('msg'=>$message));
    }

    public function charge($username,$phone,$money = 100){
        $msg = '';
        $model = Vip::model()->getPrimaryPhone($phone);

        $model_pri = Vip::model()->getPrimary($phone);
        if($model_pri){
            if(Vip::model()->vipIncome($model_pri, $money)) {
                //echo "充值".$money."元:".$username." ".$phone."\n";
            }
            else {
                $msg =  "充值失败:".$username." ".$phone."<br>";
            }
        }else
        if($model) {
            if(Vip::model()->vipIncome($model, $money)) {
                //echo "充值".$money."元:".$username." ".$phone."\n";
            }
            else {
                $msg =  "充值失败:".$username." ".$phone."<br>";
            }
        }
        else {
            if(VipPhone::model()->getPrimary($phone)) {
                $msg = "已经是VIP副卡:".$username." ".$phone."没有充值<br>";
            }
            else{
                $data['id'] = $phone;
                $data['name'] = $username;
                $data['company'] = 'e代驾';
                $data['phone'] = $phone;
                $data['send_phone'] = $phone;
                $data['type'] = 0;
                $data['city_id'] = 0;
                $data['status'] = 1;
                $data['send_type'] = 0;
                $data['email'] = '';
                $data['totelamount'] = 0.00;
                $data['credit'] = 0;
                $data['channel'] = '1';
                $data['commercial_invoice'] = '';
                $data['remarks'] = 'e代驾福利';
                $data['balance'] = $data['totelamount'];
                $data['invoiced'] = 0;
                $data['contact'] = '';
                $data['address'] = '';
                $data['telephone'] = '';
	            $data['operator'] = Yii::app()->user->getId();
                $model = new Vip();
                if($model->insertVip($data)) {
                    //$msg = "VIP创建成功:".$username." ".$phone."\n";
                    sleep(1);
                    $model = Vip::model()->getPrimaryPhone($phone);
                    if(Vip::model()->vipIncome($model, $money,'0','员工内部福利')) {
                        //echo "充值".$money."元:".$username." ".$phone."\n";
                    }
                    else {
                        $msg = "充值失败:".$username." ".$phone."<br>";
                    }
                }else{
                    $msg = "vip 创建失败{$username} {$phone}<br>";
                }


            }

        }
        return $msg;
    }
}