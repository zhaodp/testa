<?php
/**
 * 
 * API文档页面
 * @author dayuer
 *
 */
class DocsAction extends CAction
{
	
	public function run()
	{
		$params = Yii::app()->getController()->getActionParams();
		$this->layout = '//layouts/main';
		
		switch ($params['cat'])
		{
			case 'test' :
				$this->render('//docs/test');
				break;
			default :
				$ver = isset($params['ver']) ? $params['ver'] : 3;
				$this->render('//docs/index', array(
					'ver'=>$ver));
				break;
		}
	
	}
}