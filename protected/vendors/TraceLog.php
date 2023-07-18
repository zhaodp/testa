<?php
/**
 * @file			TraceLog.php
 * @CopyRight		(C)1996-2099 Edaijia Inc.
 * @Project		service model	
 * @Author		  yuchao@edaijia-inc.cn	
 * @Create Date:	2015-02-01
 * @Modified By:	yuchao/2015-02-03
 * @Brief			日志记录类
 */

/**
 * Static logger class.
 * Stack will be written to file once destructor is initialized by php.
 */
class TraceLog{
	
	/**
	 * Warning message
	 * @var int
	 */
	const E_WARN = 1;
	/**
	 * Debug message
	 * @var int
	 */
	const E_DEBUG = 2;
  /**
   * Trace Log Message
   * @var int
   */
  const E_TRACE = 3;
	/**
	 * Info message
	 * @var int
	 */
	const E_INFO = 4;
	/**
	 * Error message
	 * @var int
	 */
	const E_ERROR = 8;
	
	/**
	 * Keeps flags for what too log.
	 * @var array
	 */
	private $logFlags;
	/**
	 * Stack of log entries
	 * @var array
	 */
	private $stack = array();
	/**
	 * Log directory.
	 * @var string
	 */
	private $logLocation;

  /**
   * Trace Log entries
   */
  private $tracelog=true;
	
	/**
	 * Creates a new logger instance.
	 * @param int $logFlags Flags of what to log.
	 * @param string $logLocation Log location ( must be a directory )
	 */
	public function __construct($logFlags, $logLocation,$tracelog = false){
		$this->logFlags = $logFlags;
		$this->logLocation = $logLocation;
    $this->tracelog = $tracelog;
	}
	
	/**
	 * Gets what method / controller that called the method.
	 */
	protected function getTraceString(){
		return "Edj::Edj";
	}
  
	/**
	 * Logs a trace log message.
	 * @param string $msg The message to log.
	 * @param int $flags Additional flags.
	 */
	public function traceLog($data){
    /**
     * time 事件发生时间  2015-01-07T14:10:00.735+0800 必须是这种格式
     * trace_d  全局id 分布式唯一 9776398419886080
     * span_id  用于描述服务和方法间的层级关系  1.1.3 现在默认打印1
     * service  服务的名称  iboolee-demo
     * method 方法的名称  GET|/customer|Start，GET|/customer|End， GET|/customer|End|Exception
     * ip 本机ip地址  10.3.28.183
     * cost 一个调用完成的耗时 毫秒级 89
     * status 调用状态  0正常 1调用异常 2业务异常
     */
    //time::2015-01-07T14:10:00.735+0800 #trace_id::9776398419886080 #span_id::1 #service::iboolee-demo #method::GET|/customer|End #ip::127.0.0.1 #cost::2 #status::ok
		if ($this->logFlags & self::E_TRACE)$this->stack[] = $data;
	}
	
	/**
	 * Logs a debug message.
	 * @param string $msg The message to log.
	 * @param int $flags Additional flags.
	 */
	public function trace($msg, $flags=null){
		if ($this->logFlags & self::E_TRACE)$this->stack[] = array('TRACE', time(), $msg, $flags, $this->getTraceString());
	}
	/**
	 * Logs a debug message.
	 * @param string $msg The message to log.
	 * @param int $flags Additional flags.
	 */
	public function debug($msg, $flags=null){
		if ($this->logFlags & self::E_DEBUG)$this->stack[] = array('DEBUG', time(), $msg, $flags, $this->getTraceString());
	}
	
	/**
	 * Logs a warning message.
	 * @param string $msg The message to log.
	 * @param int $flags Additional flags.
	 */
	public function warn($msg, $flags=null){
		if ($this->logFlags & self::E_WARN)$this->stack[] = array('WARNING', time(), $msg, $flags, $this->getTraceString());
	}
	/**
	 * Logs a error message.
	 * @param string $msg The message to log.
	 * @param int $flags Additional flags.
	 */
	public function error($msg, $flags=null){
		if ($this->logFlags & self::E_ERROR)$this->stack[] = array('ERROR', time(), $msg, $flags, $this->getTraceString());
	}
	/**
	 * Logs a info message.
	 * @param string $msg The message to log.
	 * @param int $flags Additional flags.
	 */
	public function info($msg, $flags=null){
		if ($this->logFlags & self::E_INFO)$this->stack[] = array('INFO', time(), $msg, $flags, $this->getTraceString());
	}
	
	/**
	 * Builds a message string from a stack entry.
	 * @param array $data array of elements in the stack
	 */
	protected function buildTraceMsg($data){
		$msg =  "";

    foreach($data as $key => $value){
      $msg .= $key."::".$value." #";
    }
    $msg .="\n";
		return $msg;
	}
	/**
	 * Builds a message string from a stack entry.
	 * @param array $data array of elements in the stack
	 */
	protected function buildMsg($data){
		$msg =  "[{$data[0]}] ".'['.date('r', $data[1]).'] '."[{$data[4]}]".$data[2]."\n";
		return $msg;
	}
	/**
	 * Writes the stack to file and cleans the stack.
	 */
	protected function writeStack(){
		$s = '';
		foreach($this->stack as $d){
      if($this->tracelog){
       $s .= $this->buildTraceMsg($d);
      }else{
			 $s .= $this->buildMsg($d);
      }
		}
    if($this->tracelog){
      $fp = $this->logLocation .DIRECTORY_SEPARATOR . 'trace_id.log';
    }else{
		  $fp = $this->logLocation . DIRECTORY_SEPARATOR . date('D-d-m-Y', time()) . '.txt';
    }
		if ($s)file_put_contents($fp,$s, FILE_APPEND);
		$this->stack = array();
	}
	
	/**
	 * Writes the log stack to file.
	 */
	public function __destruct(){
		$this->writeStack();
	}
	
}
