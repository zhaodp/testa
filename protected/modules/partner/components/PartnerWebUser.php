<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-9-20
 * Time: 下午12:00
 * To change this template use File | Settings | File Templates.
 */
class PartnerWebUser extends CWebUser
{
    public $allowAutoLogin = true;

    public function __get($name)
    {
        if ($this->hasState('__userInfo')) {
            $user=$this->getState('__userInfo',array());
            if (isset($user[$name])) {
                return $user[$name];
            }
        }

        return parent::__get($name);
    }

    public function login($identity, $duration=0) {
        $this->setState('__userInfo', $identity->getUser());
        parent::login($identity, $duration);
    }

}
