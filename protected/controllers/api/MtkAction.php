<?php
/**
 * 
 * MTK手机服务端post司机手机上报数据包
 * @author dayuer
 *
 */
class MtkAction extends CAction
{
	public function run()
	{
		$method = isset($this->controller->_params['method']) ? $this->controller->_params['method'] : '';
		switch ($method)
		{
			case 'lbs' :
			case 'call' :
			case 'status' :
			case 'location' :
				$action = '/mtk/'.$method;
				$this->controller->render($action, array(
					'params'=>$this->controller->_params));
				break;
			default :
				break;
		}
	}
}