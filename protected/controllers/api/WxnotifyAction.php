<?php
/**
 *
 * 微信回调接口，无需认证
 * @author tuanwang,liu
 *
 */
class WxnotifyAction extends CAction {

    public function run() {
        $action='common/pay/wechat/wxnotify';
        $this->controller->render('/'.$action,array('params'=>$this->controller->_params));
    }
}
