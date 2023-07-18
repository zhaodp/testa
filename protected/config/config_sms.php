<?php
return array(
	
	'soap'=> array(
					'sn'		=> 'SDK-WKS-010-00884', 
					'pwd'		=> '992504', 
					'url_soap'	=> 'http://sdk2.entinfo.cn:8060/webservice.asmx?WSDL', 
					'sign' 		=> '【e代驾】',
				),
	'zcyz'=>array(
                                'sn'            => 'SDK-WKS-010-00971',
                                'pwd'           => '332498',
                                'url_soap'      => 'http://sdk2.entinfo.cn:8060/webservice.asmx?WSDL',
                                'sign'          => '【e代驾】',
                                ),
	'gsms'	=> array(
					'sn'		=> '66892:admin',
					'pwd'		=> '25244076', 
					//'url_soap'=> 'http://ws.iems.net.cn/GeneralSMS/ws/SmsInterface?wsdl', 
					//'url_http'=> 'http://GATEWAY.IEMS.NET.CN/GsmsHttp',
					'url_soap'	=> 'http://219.133.59.101/GeneralSMS/ws/SmsInterface?wsdl', 
					'url_http' 	=> 'http://219.133.59.101/GsmsHttp',
					'sign' 		=> '【e代驾】',
				),

	// 北京国都互联科技有限公司
	'guodusms'	=> array(
					#'sn'		=> 'ywceshi', 
					#'pwd'		=> 'ljy0225', 
					'sn'		=> 'edaijia', 
					'pwd'		=> 'edaijia', 
					'url_http' 	=> 'http://221.179.180.158:9007/QxtSms/QxtFirewall',
					'sign' 		=> '【e代驾】',
				),

	'guodumarket'	=> array(
					#'sn'		=> 'ywceshi', 
					#'pwd'		=> 'ljy0225', 
					'sn'		=> 'daijia', 
					'pwd'		=> 'daijia', 
					'url_http' 	=> 'http://221.179.180.158:9007/QxtSms/QxtFirewall',
					'sign' 		=> '【e代驾】',
				),


				
	//http://GATEWAY.IEMS.NET.CN/GsmsHttp?username=66892:admin&password=25244076&to=18701552183&content=测试123test
	'pinche'	=> array(
					//'sn'		=> '66961:admin', 
					//'pwd'		=> '78252381', 
					'sn'		=> '66892:admin', 
					'pwd'		=> '25244076', 
					//'url_soap'=> 'http://ws.iems.net.cn/GeneralSMS/ws/SmsInterface?wsdl', 
					//'url_http'=> 'http://GATEWAY.IEMS.NET.CN/GsmsHttp',
					'url_soap'	=> 'http://219.133.59.101/GeneralSMS/ws/SmsInterface?wsdl', 
					'url_http' 	=> 'http://219.133.59.101/GsmsHttp',
					'sign' 		=> '【蚂蚁拼车】',
				),

//EMAIL
//	'emailHost'=>'smtp.qq.com',
//	'emailAccount'=>'service@edaijia.cn',
//	'emailPassword'=>'edaijia@123',
//	'emailFrom'=>'service@edaijia.cn',
        
);
            
            
            
            
            
