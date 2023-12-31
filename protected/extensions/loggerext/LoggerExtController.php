<?php

class LoggerExtController extends CController
{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

	
            $actionId = $controllerId = $moduleId = $method ="";
            $actionId = $action->getId();
            $controller = $action->getController();
            if ($controller) $controllerId = $controller->getId();
            $module = $controller->getModule();
            if ($module) $moduleId = $module->getId();

            $logModules = Yii::app()->logext->modules;
            $logControllers = Yii::app()->logext->controllers;

            if ($module) {
                if ( isset($logModules[$moduleId]) ) {
                    $logControllers = $logModules[$moduleId];
                }
                else if ( isset($logModules['*']) ) {
                    $logControllers = $logModules['*'];
                }
            }
            $logController = array();
            if (isset($logControllers[$controllerId]) ) {
                $logController = $logControllers[$controllerId];
            }
            else if (isset($logControllers['*'])) {
                $logController = $logControllers['*'];
            }
            $logFile = "application.log";

            if ( isset($logController['logFilePattern']) ) {
                $pattern = $logController['logFilePattern'];
                $logFile = $pattern;
                if (
                    isset($logController['logFileUseParam']) && 
                        $logController['logFileUseParam'] &&
                    isset($logController['params']) &&
                    isset($logController['paramPattern']) &&
                    isset($logController['joinCharacter']) 
                ) {
                    $params = explode(',', $logController['params']);
                    $joinCharacter = $logController['joinCharacter'];
                    $paramPattern = $logController['paramPattern'];
                    $pp = array();
                    foreach($params as $key) {
                        if (isset($_POST[trim($key)])) {
                            $v = $_POST[trim($key)];
                        }
                        elseif (isset($_GET[trim($key)])) {
                            $v = $_GET[trim($key)];
                        }
                        else $v = NULL;
                        if (!is_null($v))
                            $pp[] = str_replace(array('%n', '%v'), array(trim($key), $v), $paramPattern);
                    }
		    $method = join($joinCharacter, $pp);
                    $logFile = str_replace('%p', $method, $pattern);
                    
                }
                $find = array('%m', '%c', '%a');
                $replace = array($moduleId, $controllerId, $actionId);
                $logFile = str_replace($find, $replace, $logFile);
            }

            $logPath = "";
            if ( isset($logController['logPathPattern'])) {
                $logPath = $logController['logPathPattern'];

                $find = array('%m', '%c', '%a');
                $replace = array($moduleId, $controllerId, $actionId);
                $logPath = str_replace($find, $replace, $logPath);

                $find = array('<', '>', '*', '?', '"', '|');
                $logPath = trim(str_replace($find, '', $logPath));

                if (PHP_OS == 'WINNT')
                    $find = '/';
                else $find = '\\';
                $logPath = trim(str_replace($find, '', $logPath));

            }

            $route = Yii::app()->logext->route;

            if ($route['class'] == 'FileDailyLogRoute') {
                $route['logFile'] = $logFile;
                $route=Yii::createComponent($route);
                $route->init();

                if ($logPath!="") {
                    $route->logPath = $route->logPath.'/'.$logPath;
                }
		
		$logPattern = $route->logPattern;

                $find = array('%m', '%c', '%a','%p');
                $replace = array($moduleId, $controllerId."Controller", $actionId."Action",$method);
                $logPattern = str_replace($find, $replace, $logPattern);

                $find = array('<', '>', '*', '?', '"', '|');
                $logPattern = trim(str_replace($find, '', $logPattern));

                if (PHP_OS == 'WINNT')
                    $find = '/';
                else $find = '\\';
                $logPattern = trim(str_replace($find, '', $logPattern));

                Yii::app()->log->setRoutes(array('controller'=>$route));
		Yii::app()->logext->route['logPattern']=$logPattern;
            } 
            return true;
        }
        return false;
    }
}
