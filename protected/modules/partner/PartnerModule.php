<?php

class PartnerModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			$this->getId().'.models.*',
			$this->getId().'.components.*',
		));

        Yii::app()->setLanguage('zh_cn');
        Yii::app()->setComponents(array(
            'errorHandler'=>array(
                'class'=>'CErrorHandler',
                'errorAction'=>$this->getId().'/default/error',
            ),
            'partner'=>array(
                'class'=>'PartnerWebUser',
                'stateKeyPrefix'=>'partnerUser',
                'loginUrl'=>Yii::app()->createUrl($this->getId().'/default/login'),
            ),
        ), false);
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
            $route=$controller->id.'/'.$action->id;
            /*
            if(!$this->allowIp(Yii::app()->request->userHostAddress) && $route!=='default/error')
                throw new CHttpException(403,"You are not allowed to access this page.");
            */
            $publicPages=array(
                'default/login',
                'default/error',
                'default/captcha'
            );

            if(Yii::app()->partner->isGuest && !in_array($route,$publicPages))
                Yii::app()->partner->loginRequired();
            else
                return true;
		}
		else
			return false;
	}
}
