<?php
/**
 * 
 * API v3接入口
 * @author dayuer
 *
 */
class RestAction extends CAction
{
	public function run()
	{
		$callback = empty( $_REQUEST['callback'] ) ? '' : $_REQUEST['callback'];
		
		if ($this->controller->_ver==3)
		{
			$openApiList = Yii::app()->params['appAccessApiList'];		
			$accessAppList = Yii::app()->params['appTokenList'];
			
			$currentAppKey = $this->controller->_params['appkey'];
			if( !in_array($currentAppKey,array_keys($accessAppList)) ){
				$message = array('code'=>2002, 'message'=>'Your app key not in list.');
				//echo json_encode($message);
				echo Helper::jsonOutPut($message,$callback);
				Yii::app()->end();	
			}
			$action = str_replace('.', '/', $this->controller->_params['method']);
			
			if( isset( $accessAppList[$currentAppKey]['action'] ) && in_array( '*',$accessAppList[$currentAppKey]['action']) ){
				$this->controller->_params = Common::clean_xss($this->controller->_params);//过滤XSS，add by sunhongjing 2013-11-07
				$this->controller->render('/'.$action, array('params'=>$this->controller->_params));		
			} else {
				$m = strtolower($this->controller->_params['method']);
				if( !in_array($m,$openApiList) && !in_array($m,$accessAppList[$currentAppKey]['action']) ){
					$message = array('code'=>2003, 'message'=>'The method not opened.');
					//echo json_encode($message);
					echo Helper::jsonOutPut($message,$callback);
					Yii::app()->end();
				}
				$this->controller->_params = Common::clean_xss($this->controller->_params);//过滤XSS，add by sunhongjing 2013-11-07
				$this->controller->render('/'.$action, array('params'=>$this->controller->_params));
			}

		} else{
			$message = array('code'=>2001, 'message'=>'API version no longer support');
			//echo json_encode($message);
			echo Helper::jsonOutPut($message,$callback);
			Yii::app()->end();
		}
	}
}