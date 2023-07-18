<?php
declare(ticks=1);
abstract class MultipleProcessCommand extends CConsoleCommand
{
    protected $_maxProcesses = 2; 
    protected $_jobsStarted = 0; 
    protected $_currentJobs = array(); 
    protected $_signalQueue=array();   
    protected $_parentPID;
	//是否为daemon 模式，daemon模式，会一直保持子进程数量
	protected $_daemon = false;
	//为了降低cpu使用率，可以调整主进程的休息时间。
	protected $_mainUsleepTime = 10;

	//fork子进程频率
	protected $_forkUsleepTime = 1;

	//信号检测频率
	protected $_signalUsleepTime = 1;

	public function init() {
		parent::init();
		if (! function_exists('pcntl_fork')) 
			$this->error('PCNTL functions not available on this PHP installation');
        $this->_parentPID = getmypid(); 
        pcntl_signal(SIGCHLD, array($this, "childSignalHandler")); 
	}

	public function run($args)
	{
		list($action, $options, $args)=$this->resolveRequest($args);
		$methodName='action'.$action;
		if(!preg_match('/^\w+$/',$action) || !method_exists($this,$methodName))
			$this->usageError("Unknown action: ".$action);

		$method=new ReflectionMethod($this,$methodName);
		$params=array();
		// named and unnamed options
		foreach($method->getParameters() as $i=>$param)
		{
			$name=$param->getName();
			if(isset($options[$name]))
			{
				if($param->isArray())
					$params[]=is_array($options[$name]) ? $options[$name] : array($options[$name]);
				elseif(!is_array($options[$name]))
					$params[]=$options[$name];
				else
					$this->usageError("Option --$name requires a scalar. Array is given.");
			}
			elseif($name==='args')
				$params[]=$args;
			elseif($param->isDefaultValueAvailable())
				$params[]=$param->getDefaultValue();
			else
				$this->usageError("Missing required option --$name.");
			unset($options[$name]);
		}

		// try global options
		if(!empty($options))
		{
			$class=new ReflectionClass(get_class($this));
			foreach($options as $name=>$value)
			{
				if($class->hasProperty($name))
				{
					$property=$class->getProperty($name);
					if($property->isPublic() && !$property->isStatic())
					{
						$this->$name=$value;
						unset($options[$name]);
					}
				}
			}
		}

		if(!empty($options))
			$this->usageError("Unknown options: ".implode(', ',array_keys($options)));

		$exitCode=0;
		if($this->beforeAction($action,$params))
		{
			//如果启动了daemon模式，主程序会一直保持设置的子进程数量，如果不有子进程退出，就会启动一个
			if ($this->_daemon == true && count($this->_currentJobs) < $this->_maxProcesses ) { 
				while(true) {
					while(count($this->_currentJobs) < $this->_maxProcesses) {
						$this->start($method, $params);
						if ($this->_forkUsleepTime > 0) usleep($this->_forkUsleepTime);
					}
					if ($this->_mainUsleepTime > 0) usleep($this->_mainUsleepTime);
				}
				$exitCode=$this->afterAction($action,$params,is_int($exitCode)?$exitCode:0);
			}
			else {
				//开始启动子进程
				while(count($this->_currentJobs) < $this->_maxProcesses) {
					$this->start($method, $params);
					if ($this->_forkUsleepTime > 0) usleep($this->_forkUsleepTime);
				}
				$exitCode=$this->afterAction($action,$params,is_int($exitCode)?$exitCode:0);
			}
		}
		return $exitCode;
	}

	protected function start($method, $params) {
		$jobID = rand(0,10000000000000);
		$pid = pcntl_fork(); 
		if($pid == -1){ 
			$this->error('Could not run, exiting');  
		} 
		else if ($pid){ //主线程
			$this->_currentJobs[$pid] = $jobID; 
			if(isset($this->_signalQueue[$pid])){ 
				$this->childSignalHandler(SIGCHLD, $pid, $this->_signalQueue[$pid]); 
				unset($this->_signalQueue[$pid]); 
			}
		}
		else{ 
			$exitStatus = 0; //Error code if you need to or whatever 
			$exitCode=$method->invokeArgs($this,$params);
			exit($exitStatus); 
		}
	}

	protected function error($message) {
		echo trim($message)."\n";
		exit(-1);
	}

	protected function output($message, $hasTime=true, $format="Y-m-d H:i:s") {
		if ($hasTime) echo "[". date($format) ."] ";
		echo trim($message)."\n";
	}

	protected function setMaxProcessNum($maxNum=10) {
		$this->_maxProcesses = intval($maxNum);
	}

	protected function setMainUsleepTime($time=10) {
		$this->_mainUsleepTime = $time;
	}

	protected function setSignalUsleepTime($time=1) {
		$this->_signalUsleepTime = $time;
	}

	protected function setForkUsleepTime($time=1) {
		$this->_forkUsleepTime = $time;
	}

	protected function getMaxProcessNum() {
		return $this->_maxProcesses;
	}

	protected function getCurrentProcessNum() {
		return count($this->_currentJobs);
	}

	protected function beforeAction($action,$params)
	{
		if (parent::beforeAction($action,$params)) {
			$methodName = $this->getBeforeMethodName($action);
			call_user_func_array(array($this, $methodName), $params);
			return true;
		}
	}


	protected function afterAction($action,$params,$exitCode=0)
	{
		parent::afterAction($action,$params,$exitCode);

		$methodName = $this->getAfterMethodName($action);
		$method=new ReflectionMethod($this,$methodName);

		//主进程进入等待，所有子进程结束;
        while(count($this->_currentJobs)){ 
			usleep($this->_mainUsleepTime); 
        }

		call_user_func_array(array($this, $methodName), $params);
		return $exitCode;
	}

	protected function getNewDbInstance(CDbConnection $db) {
		$config  = get_object_vars($db);
		if (!isset($config['class'])) $config['class']="CDbConnection";
		return Yii::createComponent($config);
	}
	
	private function getBeforeMethodName($action) {
		$methodName='before'.$action;
		if(!method_exists($this,$methodName)) return false;
		return $methodName;
	}

	private function getAfterMethodName($action) {
		$methodName='after'.$action;
		if(!method_exists($this,$methodName)) return false;
		return $methodName;
	}

	protected function setDaemon($flag=false) {
		$this->_daemon = $flag;
	}
	
    public function childSignalHandler($signo, $pid=null, $status=null){ 
        if(!$pid){ 
        	//非主进程
            $pid = pcntl_waitpid(-1, $status, WNOHANG); 
        } 
       
        while($pid > 0){ //主进程
            if($pid && isset($this->_currentJobs[$pid])){ 
                $exitCode = pcntl_wexitstatus($status); 
                if($exitCode != 0){ 
                    echo "$pid exited with status ".$exitCode."\n"; 
                } 
                unset($this->_currentJobs[$pid]); 
            } 
            else if($pid) {
                $this->_signalQueue[$pid] = $status; 
            } 

            $pid = pcntl_waitpid(-1, $status, WNOHANG); 

			if ($this->_signalUsleepTime > 0) usleep($this->_signalUsleepTime);
        } 
        return true; 
    } 

}