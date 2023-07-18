<?php
/**
 * 
 * API v4接入口
 * @author syang
 *
 */
class V4Action extends CAction
{
	public function run()
	{
		$callback = empty( $_REQUEST['callback'] ) ? '' : $_REQUEST['callback'];
		
		$method = str_replace('.', '/', $this->controller->_params['method']);
		$this->controller->render('/v4/'.$method, array('params'=>$this->controller->_params));
		
	}
}