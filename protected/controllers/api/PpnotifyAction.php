<?php
/**
 * 
 * pp钱包回调接口，无需认证
 * @author tuanwang,liu
 *
 */
class PpnotifyAction extends CAction {

	public function run() {
		$action='common/pay/pp/ppnotify';
		$this->controller->render('/'.$action,array('params'=>$this->controller->_params));
	}
}
