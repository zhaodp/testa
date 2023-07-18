<?php
/**
 *
 *发邮件功能
 *调用方法
 *$emailList =array(    'libaiyang123@qq.com','xxxxxxxx@xx.com');
 *$emailContent = '邮件内容';
 *$emailSubject = 'e代驾发来的邮件主题';
 *Mail::sendMail($emailList, $emailContent, $emailSubject);
 */


class Mail
{
    /**
     *
     * 邮件发送
     * @param 接收邮件 $emails
     * @param 邮件内容 $emailContent
     * @param 邮件主题 $emailSubject
     * 优先使用edaijia.cn 邮箱发送，如果失败，选择edaijia-staff.cn 再次发送
     */
    public static function sendMail($emails, $emailContent, $emailSubject,$server_use = 'cn')
    {

        if($server_use == 'cn'){
            $emailHost = Yii::app()->params['emailHost'];
            $emailAccount = Yii::app()->params['emailAccount'];
            $emailPassword = Yii::app()->params['emailPassword'];
            $emailFrom = Yii::app()->params['emailFrom'];
        }elseif( $server_use == 'staff' ){
            $emailHost = Yii::app()->params['emailStaffHost'];
            $emailAccount = Yii::app()->params['emailStaffAccount'];
            $emailPassword = Yii::app()->params['emailStaffPassword'];
            $emailFrom = Yii::app()->params['emailStaffFrom'];
        }

        $mailer = Yii::createComponent('application.extensions.mailer.EMailer');
        $mailer->CharSet = 'utf-8'; //防止发出的utf8邮件出现乱码。
        $mailer->Encoding = 'base64'; //防止发出的utf8邮件出现乱码。
        $mailer->IsSMTP();
        $mailer->Host = $emailHost;
        $mailer->SMTPAuth = true;

        //邮件服务器账户。
        $mailer->Username = $emailAccount;
        $mailer->Password = $emailPassword;

        //邮件属性和内容。
        $mailer->From = $emailFrom;

        $repeat = false;
        foreach ($emails as $email) {
            $email = trim($email);
            $atIndex = explode("@", $email);
            //根据目标用户邮箱更换，只针对内部使用
            //如果内部邮件用inc 邮箱发送失败，则用staff尝试发送一次
            if ($server_use == 'cn' && !empty($atIndex) && count($atIndex)==2 && in_array(strtolower($atIndex[1]) ,array('edaijia-inc.cn','edaijia-staff.cn','edaijia.cn') )) {
                $mailer->Host = Yii::app()->params['emailIncHost'];
                $mailer->Username = Yii::app()->params['emailIncAccount'];
                $mailer->Password = Yii::app()->params['emailIncPassword'];
                $mailer->From = Yii::app()->params['emailIncFrom'];
                $mailer->Port = Yii::app()->params['emailIncPort'];
                $mailer->SMTPSecure = Yii::app()->params['emailIncSMTPSecure'];
            }
            else{
                $repeat = true;
            }
            $mailer->AddAddress($email);
        }

        $mailer->Subject = $emailSubject;
        $mailer->Body = $emailContent;
        $mailer->IsHTML();

        $res = $mailer->Send();

        if(!$res && $server_use == 'cn'){
            $res = self::sendMail( $emails, $emailContent, $emailSubject,'staff');
        }

        if($repeat)//如果是外部邮件（非edaijia-inc邮件）睡眠一秒后再发
        {
            sleep(1);
        }
        return $res;

    }

}
