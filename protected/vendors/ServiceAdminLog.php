<?php
/**
 * @file			ServiceAdminLog.php
 * @CopyRight		(C)1996-2099 Edaijia Inc.
 * @Project		service model	
 * @Author		  yuchao@edaijia-inc.cn	
 * @Create Date:	2015-02-01
 * @Modified By:	yuchao/2015-02-03
 * @Brief		调用服务化日志接口	
 */

class ServiceAdminLog{

  private $traceIdObject = null;
  private $traceLogObject = null;
  private $config = array();

  private $traceId = "";
  private $service = "";
  private $method = "";
  private $api = "";
  private $ip = "127.0.0.1";
  private $localIP = '10.0.0.1';

  public function __construct($config){
    $this->traceIdObject =  new TraceId(1);
    $this->service = $config["service"];
    $this->method = $config["method"];
    $this->api = $config["api"];
    $this->traceLogObject = new TraceLog('1',$config['tracelogpath'],true);
    $this->config = $config;
    $this->ip = $config['ip'];
    $this->localIP = self::getLocalIp();
    self::getId($config['ip']);

  }

  public function getLocalIp(){
    $host = gethostname(); 
    $localip =  gethostbyname($host);
    return $localip;
  }

  public function getId($ip){
    $traceid = $this->traceIdObject->getId(1,$ip);
    $this->traceId = $traceid;
    return $traceid;
  } 

  public function getTraceLogdata($type="S",$cost=0){ 
    $traceid = $this->traceId; 
    $service = $this->service;
    $method = $this->method;
    $api = $this->api;
    $status = 0;
    if($type=="S"){
      $typeflag = "Start";
    }elseif($type=="E"){
      $typeflag = "End";
    }else{
      $typeflag = "Exception";
      $status = 2;
    }
    $logdata = array(
      'time' => date('c'), 
      'trace_id' => $traceid,
      'span_id' => "1",
      'service' => $service,
      'method' => $method.'|'.$api.'|'.$typeflag,
      'ip' => $this->localIP,
      'cost' => $cost,
      'status' => $status,
    );
    return $logdata;
  }

  public function pushAdminLog($adminlog){
    $this->ip = $adminlog['ip'];
    $startlogdata = self::getTraceLogdata();

    $this->traceLogObject->traceLog($startlogdata);

    $config = $this->config;
    $iboolee_header = "Iboolee-TraceId:".$this->traceId;
    $config['http_header'] = $iboolee_header;

    $start_exe_time = microtime(true);
    $http = new ServiceHttpClient();
    $http->adp_init($config);
    $http->setUrl($config['url'].$this->api);
    $http->setHeader("Iboolee-TraceId",$this->traceId);
    $http->setData(array("adminLog"=>json_encode($adminlog)));

    $result = $http->request('post');
    $code = $http->getState();

    $end_exe_time = microtime(true);
    $cost = $end_exe_time - $start_exe_time;
    $cost = round($cost*1000);

    if($code == 200){
      $endlogdata = self::getTraceLogdata("E",$cost);
      $this->traceLogObject->traceLog($endlogdata);
    }else{
      $exceptionlogdata = self::getTraceLogdata("ex",$cost);
      $this->traceLogObject->traceLog($exceptionlogdata);
      return false;
    }
    return true;
  }

}
?>
