<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-10-21
 * Time: 下午3:07
 * To change this template use File | Settings | File Templates.
 */
class PCaptchaAction extends CCaptchaAction {
    /*
    public function validate($input,$caseSensitive)
    {
        $code = $this->getVerifyCode();
        $valid = $caseSensitive ? ($input === $code) : strcasecmp($input,$code)===0;
        $session = Yii::app()->session;
        $session->open();
        $name = $this->getSessionKey() . 'count';
        if (!Yii::app()->request->isAjaxRequest) {
            $session[$name] = $session[$name] + 1;
        }
        if($session[$name] > $this->testLimit && $this->testLimit > 0)
            $this->getVerifyCode(true);
        return $valid;
    }
    */
    public function run()
    {
        if(isset($_GET[self::REFRESH_GET_VAR]))  // AJAX request for regenerating code
        {
            $code=$this->getVerifyCode(true);
            echo CJSON::encode(array(
                'hash1'=>$this->generateValidationHash($code),
                'hash2'=>$this->generateValidationHash(strtolower($code)),
                // we add a random 'v' parameter so that FireFox can refresh the image
                // when src attribute of image tag is changed
                'url'=>$this->getController()->createUrl($this->getId(),array('v' => uniqid())),
            ));
        }
        else
            $this->renderImage($this->getVerifyCode());
        Yii::app()->end();
    }
}