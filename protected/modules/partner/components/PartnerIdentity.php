<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-9-20
 * Time: 下午12:09
 * To change this template use File | Settings | File Templates.
 */
class PartnerIdentity extends CUserIdentity
{
    public $id;

    public $name;

    public $user;

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        $user = PartnerUsers::model()->find('username=:username', array(':username'=>$this->username));

        $partner_common = new PartnerCommon();

        if(!$user) {
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        } elseif ($user->password!==$partner_common->passwordEncrypt($this->password)) {
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        } else {

            $partner_model = Partner::model()->findByPk($user->partner_id);
            if ($partner_model && $partner_model->status==Partner::PARTNER_STATUS_ENABLE) {
                $this->setUser($user);
                $this->setState('info', $partner_model->attributes);
                $this->id = $user->id;
                $this->name = $user->username;
                $this->errorCode=self::ERROR_NONE;
            } else {
                $this->errorCode=self::ERROR_PASSWORD_INVALID;
            }
        }
        unset($user);
        return !$this->errorCode;
    }

    public function getId(){
        return $this->id;
    }

    public function getName()
    {
        return $this->username;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(CActiveRecord $user)
    {
        $this->user=$user->attributes;
    }

}