<?php
class FileDailyLogRoute extends CLogRoute
{
    /**
     * The 'datePattern' parameter.
     * Determines how date will be formatted in file name.
     * @var string
     */
    private $_dayPattern="Ymd";

    /**
     * @var string directory storing log files
     */
    private $_logPath;
    /**
     * @var string log file name
     */
    private $_logFile='application.log';

    /**
     * @var int Keep Days
    */
    private $_keepDays = 7;


    public $newDirMode=0777;

    public $newFileMode=0666;

    public $logPattern="application_default";

	private $logInOne=false;
	
	private $logInOnePath;
	
    /**
     * Initializes the route.
     * This method is invoked after the route is created by the route manager.
     */
    public function init()
    {
        parent::init();
        if($this->getLogPath()===null)
            $this->setLogPath(Yii::app()->getRuntimePath());
    }
    
    public function setLogInOnePath($value){
    	$this->logInOnePath = $value;
    }
    
    public function getLogInOnePath(){
    	return $this->logInOnePath;
    }
    
    public function setLogInOne($value){
    	$this->logInOne = $value;
    }
    
    public function getLogInOne(){
    	return $this->logInOne;
    }

    /**
     * @return string directory storing log files. Defaults to application runtime path.
     */
    public function getLogPath()
    {
        return $this->_logPath;
    }

    /**
     * @param string $value directory for storing log files.
     * @throws CException if the path is invalid
     */
    public function setLogPath($value)
    {
        if(!file_exists($value)){
            @mkdir($value,$this->newDirMode,true);
            @chmod($value,$this->newDirMode);
        }
        $this->_logPath=realpath($value);
        if($this->_logPath===false || !is_dir($this->_logPath) || !is_writable($this->_logPath))
            throw new CException(Yii::t('yii','CFileLogRoute.logPath "{path}" does not point to a valid directory. Make sure the directory exists and is writable by the Web server process.',
                array('{path}'=>$value)));
    }

    /**
     * @return string log file name. Defaults to 'application.log'.
     */
    public function getLogFile()
    {
        return $this->_logFile;
    }

    /**
     * @param string $value log file name
     */
    public function setLogFile($value)
    {
        
        $this->_logFile=$this->toFileName($value);
    }

    public function getKeepDays() {
        return $this->_keepDays;
    }
    
    public function setKeepDays($value)
    {
        if(($this->_keepDays=(int)$value)<1)
            $this->_keepDays=7;
    }
    
    public function getDatePattern() {
        return $this->_dayPattern;
    }
    
    public function setDatePattern($value) {
        if (''==trim($value)) $this->_dayPattern = 'Ymd';
        else $this->_dayPattern = trim($value);
    }
    
    protected function resolveLogFile($date="") {
        $logFile = $this->getLogFile();
        $ext = pathinfo($logFile, PATHINFO_EXTENSION);
        $main = pathinfo($logFile, PATHINFO_FILENAME);
        if (''==$date) $date = $this->getDate();
        $main .= "." . $date;
        if (''!=$ext) $main .= '.'. $ext;
        return $main;
    }

    protected function formatLogMessage($message,$level,$category,$time)
    {
        $pid = getmypid();
        return @date('Y/m/d H:i:s',$time)." [$pid] [$level] [$category] $message\n";
    }

    /**
     * Saves log messages in files.
     * @param array $logs list of log messages
     */
    protected function processLogs($logs)
    {
        $text='';
        foreach($logs as $log)
            $text.=$this->formatLogMessage($log[0],$log[1],$log[2],$log[3]);

        $logFile=$this->getLogPath().DIRECTORY_SEPARATOR.$this->resolveLogFile();

        //check file run keepDays
        if (!@is_file($logFile)) {
            $timestamp = strtotime($this->getDate());
            $timestamp = $timestamp - ($this->getKeepDays() * 60 * 60 * 24);
            $removeDateLogFile = $this->resolveLogfile(date($this->getDatePattern(), $timestamp));
            $removeDateLogFile = $this->getLogPath().DIRECTORY_SEPARATOR.$removeDateLogFile;
            @unlink($removeDateLogFile);
        }
        
        $fp=@fopen($logFile,'a');
        @flock($fp,LOCK_EX);
        @fwrite($fp,$text);
        @flock($fp,LOCK_UN);
        @fclose($fp);
        
        //额外写入一个汇总文件
        if($this->logInOne){
        	$logInOneFile=$this->getLogPath().DIRECTORY_SEPARATOR.$this->logInOnePath;
        	
        	//比较文件最后修改日期和当前日期，假如不相等，则备份这个文件，文件名+".bak"
        	//重新写一份新文件
        	if(@is_file($logInOneFile)){
        		$lastTime=@filemtime($logInOneFile);
        		$lastDay = date($this->getDatePattern(), $lastTime);
        		$today = $this->getDate();
        		
        		if($lastDay != $today){
        			$logInOneBakFile = $logInOneFile.".bak";
        			@rename($logInOneFile, $logInOneBakFile);
        		}
        	}
        	
        	$newFp=@fopen($logInOneFile,'a');
        	@flock($newFp,LOCK_EX);
        	@fwrite($newFp,$text);
        	@flock($newFp,LOCK_UN);
        	@fclose($newFp);
        }
    }

    protected function getDate() {
        return date($this->getDatePattern(), time());
    }

    protected function toFileName($filename) {
        $find = array('<', '>', '*', '?', '/', '\\', '"', '|');
        $filename = str_replace($find, '', $filename);
        $left_one = substr($filename, 0, 1);
        if ($left_one == '.' && PHP_OS == 'WINNT') {
            $filename = '_'.substr($filename, 1);
        }
        return $filename;
    }
}
