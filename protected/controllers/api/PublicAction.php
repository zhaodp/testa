<?php
/**
 * 
 * 公共API接口，无需认证
 * @author dayuer
 *
 */
class PublicAction extends CAction {

	public function run() {
		$action=str_replace('.', '/', $this->controller->_params['method']);
		switch ($this->controller->_params['method']) {
			case 'driver.define.timestamp' :
			case 'gps.location' :
			//case 'sms.zlzx.uplink' :
			case 'callcenter.redlist' :
			case 'callcenter.blacklist' :
			case 'callcenter.getdriver' :
			case 'driver.twocode.info':
			case 'driver.twocode.bind':
			case 'driver.signup.upload':
			case 'driver.log.debug':
            case 'open.qget':
				$this->controller->render('/'.$action, array(
						'params'=>$this->controller->_params
				));
				break;
			default :
				echo "welcome access edaijia api.";
				break;
		}
	}
}
