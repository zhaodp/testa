<?php

class LoggerExtCommandBehavior extends CConsoleCommandBehavior
{
    public function beforeAction($event)
    {
	    $actionId = strtolower($event->action);
	    $commandId = strtolower($event->sender->getName());
	    $queueName = "";
        $logCommands = Yii::app()->logext->commands;

        $logCommand = array();
	    if (isset($logCommands[$commandId]) ) {
	        $logCommand = $logCommands[$commandId];
	    }
	    else if (isset($logCommands['*'])) {
	        $logCommand = $logCommands['*'];
	    }
	    
	    $logFile = "console.log";
        if ( isset($logCommand['logFilePattern']) ) {
	        $pattern = $logCommand['logFilePattern'];
	        $logFile = $pattern;
	        if ( isset($logCommand['logFileUseParam']) && $logCommand['logFileUseParam'] &&
		         isset($logCommand['params']) && isset($logCommand['paramPattern']) &&
		         isset($logCommand['joinCharacter'])) 
	        {
		        $params = explode(',', $logCommand['params']);
		        $joinCharacter = $logCommand['joinCharacter'];
		        $paramPattern = $logCommand['paramPattern'];
		        $pp = array();
		
		        $methodName = 'action'.$actionId;
		        $method=new ReflectionMethod($event->sender,$methodName);
		        $paramsName = array();
	            $paramsNow = array();
		        foreach($method->getParameters() as $i=>$param)
		        {
		            $name=$param->getName();
		            array_push($paramsName, $name);
	            }
		        for($i=0; $i<count($paramsName); $i++) {
		            $paramsNow[$paramsName[$i]] = $event->params[$i];
		        }
	
	        	foreach($params as $key ) {
		            if (isset($paramsNow[trim($key)])) {
		            	$v = $paramsNow[trim($key)];
	            	}
	            	else 
	            		$v = NULL;
	            	if (!is_null($v)) {
	            		$pp[] = str_replace(array('%n', '%v'), array(trim($key), $v), $paramPattern);
	           		}
	            }
	        	$queueName = join($joinCharacter, $pp);
	            $logFile = str_replace('%p', $queueName, $pattern);
	        }
        
	        $find = array('%c', '%a');
	        $replace = array($commandId, $actionId);
	        $logFile = str_replace($find, $replace, $logFile);
	    }

    	$logPath = "";
	    if ( isset($logCommand['logPathPattern'])) {
	        $logPath = $logCommand['logPathPattern'];
	
	        $find = array('%c', '%a');
	        $replace = array($commandId, $actionId);
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
	        $find = array('%c', '%a', '%p');
	        $replace = array($commandId."Command", "action".$actionId,$queueName);
	        $logPattern = str_replace($find, $replace, $logPattern);
	        $find = array('<', '>', '*', '?', '"', '|');
	        $logPattern = trim(str_replace($find, '', $logPattern));
	
	        if (PHP_OS == 'WINNT')
	            $find = '/';
	        else 
	        	$find = '\\';
	        $logPattern = trim(str_replace($find, '', $logPattern));
	        
	        //为达到不影响原有日志的情况下，还能多记录一份汇总日志，所以增加一个开关
	        //当logInOne＝true时会读取相关的配置，执行记录汇总日志的动作
	        if(isset($logCommand['logInOne'])){
	        	$route->setLogInOne($logCommand['logInOne']);
	        }
	
	        Yii::app()->log->setRoutes(array('controller'=>$route));
	    	Yii::app()->logext->route['logPattern'] = $logPattern;
	    }
	    
        //开始打印相应的开始与结束日志
        EdjLog::info("job $commandId $actionId with args ".serialize($event->params)." start");
        global $cronId;
        if($cronId){
            Yii::app()->dbsys->createCommand()->update('sys_crontab',array('runAt'=>time(),'endAt'=>0),"cronId=$cronId");
        }
    }

    public function afterAction($event){
        if ($event->exitCode < 0) {
            $this->jobErrorHandler($event);
        }

        EdjLog::info("job ".$event->sender->getName()." ".$event->action." with args ".serialize($event->params)." end");
        global $cronId;
        if($cronId){
            Yii::app()->dbsys->createCommand()->update('sys_crontab',array('endAt'=>time()),"cronId=$cronId");
        }
    }

    private function jobErrorHandler($event){
        $receiver = array('dengxiaoming@edaijia-inc.cn','yuchao@edaijia-inc.cn','zengkun@edaijia-inc.cn');
        $content = 'action'.$event->action." failed!";
        $title = "job failed!";
        Mail::sendMail($receiver, $content, $title); 
        EdjLog::info($content);
    }
}
