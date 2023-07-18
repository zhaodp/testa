<?php
/**
 * 
 * 封装短信发送接口
 * @author dayuer
 * @version 2012-04-21
 */

class Sms {
	/**
	 * @var 定时发送
	 */
	const TYPE_SCHEDULE = 1;
	/**
	 * 
	 * @var 立刻发送
	 */
	const TYPE_RIGHTOFF = 0;
	
	const CHANNEL_SOAP = 'soap';//E达信
	const CHANNEL_ZCYZ = 'zcyz';//E达信 新通道
	const CHANNEL_GSMS = 'gsms';//33易9
	const CHANNEL_ZLZX = 'qxt100'; //指联在线
	const CHANNEL_MT   = 'mt'; //E达信mt方法发送
	const CHANNEL_WNSMS= 'wnsms'; //维纳斯
	const CHANNEL_PINCHE= 'pinche'; //拼车发送短信通道
	const CHANNEL_GUODU = 'guodu';	// 北京国都互联科技有限公司
	const CHANNEL_GUODU_MARKET= 'guodu_market';//国都营销短信通道，可批量发送




    // 按照业务类型发送短信的接口

    // 订单相关
	public static function SendForOrder($mobile, $content)
    {
        return self::SendSMS($mobile, $content, self::CHANNEL_GUODU);
    }

    // 支付相关
	public static function SendForPayment($mobile, $content)
    {
        return self::SendSMS($mobile, $content, self::CHANNEL_GUODU);
    }

    //订单评价相关
    public static function SendForOrderComment($mobile, $content, $subcode){
    	return self::SendSMS($mobile,$content,self::CHANNEL_GUODU,$subcode);
    }

    //国都营销通道，用来发送批量相同信息内容短信 aiguoxin
    public static function SendForBatchMsg($mobile, $content){
    	return self::SendSMS($mobile, $content, self::CHANNEL_GUODU_MARKET);
    }

    //提供给活动专用，发送失败就不再发了，量比较大，上百万
    public static function SendForActive($mobile, $content){
    	$result=self::_send_market_guoduhttp($mobile, $content, null);
    	return empty($result['success']) ? false : true;
    }

	/**
	 * 
	 * 普通短信发送
	 * @param $mobile |接收号码
	 * @param $content |短信内容
	 */
    //	public static function SendSMS($mobile, $content, $channel=self::CHANNEL_GUODU, $ext=null, $presend=null) {
	public static function SendSMS($mobile, $content, $channel=self::CHANNEL_MT, $ext=null, $presend=null) {
		$result=false;
		
		if( empty($mobile) || empty($content) ){
			return $result;
		}
		
		$is_phone = Common::checkPhone($mobile);
		if(!$is_phone){
			echo('phone='.$mobile.',content='.$content.',发送失败,手机号格式不对');
			EdjLog::info('phone='.$mobile.',content='.$content.',发送失败,手机号格式不对');	
			return $result;	
		}
		
		// close 33e9
		if ( $channel==self::CHANNEL_GSMS ) {
			$channel=self::CHANNEL_SOAP;
		}
        $appkey = Yii::app()->params['edj_api_key'];
        $api = new Api($appkey);
        $result = $api->sp_send_sms($mobile, $content, $channel);
        if (!empty($result) && $result['message'] == 'success') {
            return true;
        } else {
            return false;
        }

//		switch ($channel) {
//			case self::CHANNEL_GUODU:
//				$result=self::_send_guoduhttp($mobile, $content, $presend);
//				break;
//			case self::CHANNEL_GUODU_MARKET:
//				$result=self::_send_market_guoduhttp($mobile, $content, $presend);
//				break;
//			case self::CHANNEL_GSMS :
//				$result=self::_send_gsmshttp($mobile, $content, $presend);
//				break;
//			case self::CHANNEL_MT :
//				$result=self::mtSms($mobile, $content, $ext, $presend);
//				break;
//			case self::CHANNEL_SOAP :
//				$result=self::_send_soap($mobile, $content, $presend);
//				break;
//			case self::CHANNEL_ZCYZ :
//				$result=self::_send_zcyz($mobile, $content, $presend);
//				break;
//			case self::CHANNEL_PINCHE :
//				$result=self::_send_pinchesms($mobile, $content, $presend);
//				break;
//			default:
//				$result=self::_send_gsmshttp($mobile, $content, $presend);
//				break;
//
//		}
//
//        //var_dump($result);
//		//检查短信发送状态，发送失败，则用备用通道再发送一次
//        //var_dump($result);
//
//		if( empty($result['success']) ){
//			$result = self::_send_soap($mobile, $content,$presend);
//		}
//
//
// 		if( empty($result['success']) ){
// 			$result = self::_send_gsmshttp($mobile, $content , $ext);
// 		}
//
//		//扔队列处理
//		$columns = array (
//			'receiver'=>$mobile,
//			'message'=>$content,
//			'result'=>json_encode($result),
//			'created'=>date('Y-m-d H:i:s', time())
//		);
//		//Yii::app()->db->createCommand()->insert('t_sms_log', $columns);
//		$task=array(
//				'method'=>'mark_sms_send_log',
//				'params'=>$columns,
//		);
//		Queue::model()->putin($task,'dumpsmslog');
//
//		return empty($result['success']) ? false : true;
	}


	/**
	 * 查询余额
	 * @param string $channel
	 * @return string $result
	 */
	public static function Balance($channel = self::CHANNEL_SOAP) {
		$result = '';
		switch ($channel) {
			case self::CHANNEL_SOAP :
				$result = self::GetBalance($channel);
				break;
			case self::CHANNEL_GSMS :
				$result = self::_getbalance_gsmssoap();
				break;
			case self::CHANNEL_ZLZX :
				$result = self::_getbalance_zlzxhttp();
				break;
		}
		return $result;
	}
	
	protected static function _send_zcyz($mobile, $content,$presend=null) {
		
		$url 		= Yii::app()->params['sms_config']['zcyz']['url_soap'];
		$username 	= Yii::app()->params['sms_config']['zcyz']['sn'];
		$password 	= Yii::app()->params['sms_config']['zcyz']['pwd'];
		$sign 		= Yii::app()->params['sms_config']['zcyz']['sign'];
		
		try {
			$content = $content.$sign;
			$soap = new SoapClient($url);
			$soap->decode_utf8 = true;
			$params = array ( 'sn'=>$username, 'pwd'=>$password, 'mobile'=>$mobile, 'content'=>$content);
			
			//增加预计发送时的定时发送参数
			if( $presend ){
				//$params['stime'] = trim($presend);
			}
			
			$result = $soap->__soapCall('SendSMS', array (
				'parameters'=>$params));
			
			////统一短信返回值 modify by sunhongjing 2013-05-08
			if ( !empty($result->SendSMSResult) && '0 成功'==$result->SendSMSResult ) {
				return array('success'=>true , 'channel' => self::CHANNEL_ZCYZ , 'msg'=>$result->SendSMSResult);
				
			}else{
				return array('success'=>false , 'channel' => self::CHANNEL_ZCYZ ,'msg'=>$result->SendSMSResult);
			}
			
		} catch (Exception $e) {
			return array('success'=>false , 'channel' => self::CHANNEL_ZCYZ , 'msg'=>"异常");
		}
	}
	
	protected static function _send_soap($mobile, $content,$presend=null) {
		
		$url 		= Yii::app()->params['sms_config']['soap']['url_soap'];
		$username 	= Yii::app()->params['sms_config']['soap']['sn'];
		$password 	= Yii::app()->params['sms_config']['soap']['pwd'];
		
		try {
			$content = $content.'【e代驾】';
			$soap = new SoapClient($url);
			$soap->decode_utf8 = true;
			$params = array ( 'sn'=>$username, 'pwd'=>$password, 'mobile'=>$mobile, 'content'=>$content);
			
			//增加预计发送时的定时发送参数
			if( $presend ){
				//$params['stime'] = trim($presend);
			}
			
			$result = $soap->__soapCall('SendSMS', array (
				'parameters'=>$params));
			
			////统一短信返回值 modify by sunhongjing 2013-05-08
			if ( !empty($result->SendSMSResult) && '0 成功'==$result->SendSMSResult ) {
				return array('success'=>true , 'channel' => self::CHANNEL_SOAP , 'msg'=>$result->SendSMSResult);
				
			}else{
				return array('success'=>false , 'channel' => self::CHANNEL_SOAP ,'msg'=>$result->SendSMSResult);
			}
			
		} catch (Exception $e) {
			return array('success'=>false , 'channel' => self::CHANNEL_SOAP , 'msg'=>"异常");
		}
	}

	protected static function _send_guoduhttp($mobile, $content, $presend=0, $timeout = 5) {
		$url 		= Yii::app()->params['sms_config']['guodusms']['url_http'];
		$username 	= Yii::app()->params['sms_config']['guodusms']['sn'];
		$password 	= Yii::app()->params['sms_config']['guodusms']['pwd'];
		
		try {		
			$content = mb_convert_encoding($content.'【e代驾】','gbk','utf-8');	
			$params = array ('OperID'=>$username,'OperPass'=>$password, 'DesMobile'=>$mobile, 'Content'=>$content, 'ContentType'=>'8');
			//if( $presend ){
				//$params['presendTime'] = trim($presend);
			//}
			
			$url = $url.'?'.http_build_query($params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 0);
			$data = curl_exec($ch);
			curl_close($ch);
            $ret  = simplexml_load_string(iconv('utf-8','gbk',$data));
            //var_dump($ret);

            if (!isset($ret->code)) {
				return array('success'=>false , 'channel' => self::CHANNEL_GUODU , 'msg'=>$ret);
            }

            $code = $ret->code;

			if($code == '03'){
				return array('success'=>true , 'channel' => self::CHANNEL_GUODU , 'msg'=>$ret);
			}else{
				return array('success'=>false , 'channel' => self::CHANNEL_GUODU , 'msg'=>$ret);
			}


		} catch (Exception $e) {
			return array('success'=>false , 'channel' => self::CHANNEL_GUODU , 'msg'=>"异常");
		}
	}
	
	/**
	*	国都市场营销短信
	*/
	protected static function _send_market_guoduhttp($mobile, $content, $presend=0, $timeout = 5) {
		$url 		= Yii::app()->params['sms_config']['guodumarket']['url_http'];
		$username 	= Yii::app()->params['sms_config']['guodumarket']['sn'];
		$password 	= Yii::app()->params['sms_config']['guodumarket']['pwd'];
		
		try {		
			$content_origin=$content;
			$content = mb_convert_encoding($content.'【e代驾】','gbk','utf-8');	
			$params = array ('OperID'=>$username,'OperPass'=>$password, 'DesMobile'=>$mobile, 'Content'=>$content, 'ContentType'=>'8');
			//if( $presend ){
				//$params['presendTime'] = trim($presend);
			//}
			
			$url = $url.'?'.http_build_query($params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 0);
			$data = curl_exec($ch);
			curl_close($ch);
            $ret  = simplexml_load_string(iconv('utf-8','gbk',$data));
            // var_dump($ret);

            if (!isset($ret->code)) {
				EdjLog::info('phone='.$mobile.',content='.$content_origin.',发送失败'.PHP_EOL);
				EdjLog::info('phone='.$mobile.',content='.$content_origin.',发送失败');
				return array('success'=>false , 'channel' => self::CHANNEL_GUODU_MARKET , 'msg'=>$ret);
            }

            $code = $ret->code;
			if($code == '03'){
            	EdjLog::info('phone='.$mobile.',content='.$content_origin.',发送成功'.PHP_EOL);
            	EdjLog::info('phone='.$mobile.',content='.$content_origin.',发送成功');
				return array('success'=>true , 'channel' => self::CHANNEL_GUODU_MARKET , 'msg'=>$ret);
			}else{
				EdjLog::info('phone='.$mobile.',content='.$content_origin.',发送失败'.',code='.$code.PHP_EOL);
				EdjLog::info('phone='.$mobile.',content='.$content_origin.',发送失败'.',code='.$code);
				return array('success'=>false , 'channel' => self::CHANNEL_GUODU_MARKET , 'msg'=>$ret);
			}


		} catch (Exception $e) {
			echo 'phone='.$mobile.',content='.$content_origin.',发送失败，异常'.PHP_EOL;
			EdjLog::info('phone='.$mobile.',content='.$content_origin.',发送失败,异常');
			return array('success'=>false , 'channel' => self::CHANNEL_GUODU_MARKET , 'msg'=>"异常");
		}
	}
	

	/**
	 * 33e9短信通道，修改超时时间为5秒
	 * 
	 * @author sunhongjing
	 * @param unknown_type $mobile
	 * @param unknown_type $content
	 * @param unknown_type $presend
	 * @param unknown_type $timeout 默认设置5秒
	 */
	protected static function _send_gsmshttp($mobile, $content, $presend=0, $timeout = 5) {
		//$url = 'http://GATEWAY.IEMS.NET.CN/GsmsHttp';
	
		$url 		= Yii::app()->params['sms_config']['gsms']['url_http'];
		$username 	= Yii::app()->params['sms_config']['gsms']['sn'];
		$password 	= Yii::app()->params['sms_config']['gsms']['pwd'];
		
		try {		
			$content = mb_convert_encoding($content,'gbk','utf-8');	
			$params = array ('username'=>$username,'password'=>$password, 'to'=>$mobile, 'content'=>$content);
			if( $presend ){
				$params['presendTime'] = trim($presend);
			}
			
			$url = $url.'?'.http_build_query($params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 0);
			$data = curl_exec($ch);
			curl_close($ch);
			$ret = explode(':',$data);
			
			//统一短信返回值 modify by sunhongjing 2013-05-08
			if( !empty($ret['0']) && 'OK'==$ret['0']){
				return array('success'=>true , 'channel' => self::CHANNEL_GSMS , 'msg'=>$ret);
			}else{
				return array('success'=>false , 'channel' => self::CHANNEL_GSMS , 'msg'=>$ret);
			}

		} catch (Exception $e) {
			return array('success'=>false , 'channel' => self::CHANNEL_GSMS , 'msg'=>"异常");
		}
	}
	
	/**
	 * 为拼车单独设立的新通道
	 * 
	 * @author sunhongjing
	 * @param unknown_type $mobile
	 * @param unknown_type $content
	 * @param unknown_type $presend
	 * @param unknown_type $timeout 默认设置5秒
	 */
	protected static function _send_pinchesms($mobile, $content, $presend=0, $timeout = 5) {
		//$url = 'http://GATEWAY.IEMS.NET.CN/GsmsHttp';
		$url 		= Yii::app()->params['sms_config']['pinche']['url_http'];
		$username 	= Yii::app()->params['sms_config']['pinche']['sn'];
		$password 	= Yii::app()->params['sms_config']['pinche']['pwd'];
		
		try {		
			$content = mb_convert_encoding($content,'gbk','utf-8');	
			$params = array ('username'=>$username,'password'=>$password, 'to'=>$mobile, 'content'=>$content);
			if( $presend ){
				$params['presendTime'] = trim($presend);
			}
			
			$url = $url.'?'.http_build_query($params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 0);
			$data = curl_exec($ch);
			curl_close($ch);
			$ret = explode(':',$data);
			
			//统一短信返回值 modify by sunhongjing 2013-05-08
			if( !empty($ret['0']) && 'OK'==$ret['0']){
				return array('success'=>true , 'channel' => self::CHANNEL_PINCHE , 'msg'=>$ret);
			}else{
				return array('success'=>false , 'channel' => self::CHANNEL_PINCHE , 'msg'=>$ret);
			}

		} catch (Exception $e) {
			return array('success'=>false , 'channel' => self::CHANNEL_PINCHE , 'msg'=>"异常");
		}
	}
	
	
	/**
	 * 下行接口：http://hysmsapi.qxt100.com/dapi/send_simple.php
	 * 余额查询：http://hysmsapi.qxt100.com/dapi/balance.php
	 * 
	 * 	//测试账号
	 *  //用户名为：edaijia
	 *  //密码为：4dj787py
	 * 
	 * 下行测试URL
	 * http://hysmsapi.qxt100.com/dapi/send_simple.php?name=****&pwd=****&dest=****&content=******
	 * 上行测试URL
	 * http://客户的上行接收程序?name=****&src=13800138000&dest=10690123456&content=%C9%CF%D0%D0%B2%E2%CA%D4&time=2009-12-03%2011:05:38
	 * 状态报告测试URL
	 * http://客户的状态接收程序?name=****&report=1203110100547341956,13882768136,DELIVRD,2009-12-03%2011:05:38;%201203110100547341956,13124569889,%0ADELIVRD,2009-12-03%2011:05:38
	 * 余额查询URL
	 * http://hysmsapi.qxt100.com/dapi/ balance.php?name=****&pwd=****
	 * 
	 * @author sunhongjing
	 * @param unknown_type $mobile
	 * @param unknown_type $content
	 * @param unknown_type $ext
	 * @param unknown_type $presend
	 * @param unknown_type $second
	 */
	protected static function _send_zlzxhttp($mobile, $content, $ext = null , $presend = 0 , $second = 5) {
		$url = 'http://hysmsapi.qxt100.com/dapi/send_simple.php';

		$content = $content."【e代驾】";
		try {
			if( !mb_check_encoding($content,'gbk') ){
				$content = mb_convert_encoding($content,'gbk','utf-8');
			}
			$params = array ('name'=>'edaijia','pwd'=>'vtklmypl', 'dest'=>$mobile, 'content'=>$content);	
			if ($ext != null) {
				$params['ext'] = $ext;
			}
			if( $presend ){
				$params['time'] = trim($presend);
			}
			
			$url = $url.'?'.http_build_query($params);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, $second);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			$ret = curl_exec($ch);
			curl_close($ch);
			$ret = explode(':',$ret);
			
			//统一短信返回值 modify by sunhongjing 2013-05-08
			if( !empty($ret['0']) && 'success'==$ret['0']){
				return array('success'=>true , 'channel' => self::CHANNEL_ZLZX , 'msg'=>$ret);
				
			}else{
				return array('success'=>false , 'channel' => self::CHANNEL_ZLZX , 'msg'=>$ret);
				
			}
			
		} catch (Exception $e) {
			return array('success'=>false , 'channel' => self::CHANNEL_ZLZX , 'msg'=>"异常");
		}
		
	}
	
	
	/**
	 * 维纳短信通道，修改超时时间为5秒
	 * 	
			具体示例	http://121.52.221.108/send/gsend.aspx?name=company&pwd=1515&dst=1393710***4,1393710***5&msg=你好吗&sequeid=12345
			返回结果	num=2&success=1393710***4,1393710***5&faile=&err=发送成功&errid=0

			//测试账号
			//用户名为：edaijia
			//密码为：edaijia12345

参数名	含义	           是否必填	示例 	备注
name 	企业用户登陆名称 	是 	 	　 
pwd 	企业用户登录密码 	是 	 	　 
dst 	群发目标手机号 	是 	 	手机（联通移动）、小灵通必须分开单独为一组进行提交，号码之间必须用英文逗号分割,最后一个手机号后不加逗号, 必填,请少于70个号码。 
msg 	发送短信内容 	    否 	 	普通短信字数不能超过60字，长短信字数不能超过300字。超过的字符将直接返回失败。“字”解释：1个数字或字母或符号为1字，1个汉字为1字，以字为计，非字节。 
time	定时时间	        否	200505241713表示此条短信定时在2005年5月24日17点13分发出。	默认为空，为立即发送。定时发送格式: YYYYMMDDHHMM；15位时间表示，不符合规则的将立即进行发送。
sender 	长号码 	        否 	您的特服代码为0888008，想让此条短信的发送者为088800800，则sender=00即可；可填,值为空则默认为您的特服代码。	纯数字的字符串，长度<6,完整的特服号码长度<20，在短信分配的特服代码后附加的数字。
sequeid	序列号 		一对一审批 和状态报告使用	

参数名	含义	            类型	     示例	备注
num	    已成功提交短信条数	数字	 	=0，为发送失败；>0，为正确结果，调用者应该根据此值唯一判断短信提交成功与否。
success	成功提交的手机号	字符	 	用英文逗号分割
faile	发送失败的手机号	字符	 	用英文逗号分割
err	    发送错误原因	    字符	 	仅供参考
errid	具体错误编码	    数字	 	　
	 * 
	 * @author sunhongjing
	 * @param unknown_type $mobile
	 * @param unknown_type $content
	 * @param unknown_type $presend
	 * @param unknown_type $timeout 默认设置5秒
	 */
	public static function _send_wnshttp($mobile, $content,$subcode=null, $sendtime=0, $timeout = 5) 
	{
		$url = 'http://121.52.221.108/send/gsend.aspx';
		
		//判断是手机号	
		$subcode = trim($subcode);
		
		if(empty($subcode)){
			$subcode = '';
		}else{
			if( ! preg_match('{^\d{0,5}$}',$subcode) ){
				$subcode = '';
			}
		}
		
		$presend = '';
		if( ! empty($sendtime)  && $sendtime > time() ){		
			$presend  = date("Y-m-d H:i:s",trim($sendtime) );
		}
		
		try {
			
			$content = mb_convert_encoding($content,'gbk','utf-8');
		
			$params = array ('name'=>'edaijia','pwd'=>'edaijia12345', 'dst'=>$mobile, 'msg'=>$content);
				
			if( !empty($presend) ){
				$params['time'] = $presend;
			}
			
			if( !empty($subcode) ){
				$params['sender'] = $subcode;
			}
			
			$params['sequeid'] = microtime(true);
			
			$url = $url.'?'.http_build_query($params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			$data = curl_exec($ch);
			curl_close($ch);
			//print_r($data);
			$data = mb_convert_encoding($data,'utf-8','gbk');
			$ret = parse_str($data);
			return $ret;
			
			
			//统一短信返回值 modify by sunhongjing 2013-05-08
			if( !empty($ret['0']) && 'OK'==$ret['0']){
				return array('success'=>true , 'channel' => self::CHANNEL_WNSMS , 'msg'=>$data);
			}else{
				return array('success'=>false , 'channel' => self::CHANNEL_WNSMS , 'msg'=>$data);
			}

		} catch (Exception $e) {
			return array('success'=>false , 'channel' => self::CHANNEL_WNSMS , 'msg'=>"异常");
		}
	}
	
	
	protected static function _send_http($mobile, $content, $second = 30) {
		$url = 'http://119.254.104.137:8080/ystSms/sms_message2tk2.jsp';
		
		$params = array (
			'user'=>'zdj', 
			'pass'=>'123456', 
			'mobile'=>$mobile, 
			'msg'=>$content);
		
		$url = $url.'?'.http_build_query($params);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 0);
		$data = curl_exec($ch);
		print_r($data);
		curl_close($ch);
		
		return $data;
	}
	
	/**
	 * 
	 * 带扩展码的短信发送
	 * @param 接收号码 $mobile
	 * @param 短信内容 $content
	 * @param 扩展码 $subcode
	 * @param 定时发送 $stime
	 */
	public static function SendSMSEx($mobile, $content, $subcode,$stime=null) {
		
		$url 		= Yii::app()->params['sms_config']['soap']['url_soap'];
		$username 	= Yii::app()->params['sms_config']['soap']['sn'];
		$password 	= Yii::app()->params['sms_config']['soap']['pwd'];
		
		
		$soap = new SoapClient($url);
		$soap->decode_utf8 = true;
		$params = array ( 	'sn'=>$username, 
							'pwd'=>$password, 
							'mobile'=>$mobile, 
							'content'=>$content."【e代驾】", 
							'subcode'=>$subcode,
					);
		
		if( $stime ){
			//$params['stime'] = trim($stime);
		}
		
		$result = $soap->__soapCall('SendSMSEx', array (
			'parameters'=>$params));
		
		$columns = array (
			'receiver'=>$mobile, 
			'message'=>$content, 
			'result'=>json_encode($result), 
			'created'=>date('Y-m-d H:i:s', time()));
		//Yii::app()->db->createCommand()->insert('t_sms_log', $columns);
		
		$task=array(
				'method'=>'mark_sms_send_log',
				'params'=>$columns,
		);
		Queue::model()->putin($task,'dumpsmslog');
		
		return $result;
	}
	
	/**
	 * 个性短信：sn软件序列号;pwd加密密码md5(sn+password);
	 * 
	 * @param unknown_type $mobile   mobile手机号列表，以逗号,隔开;
	 * @param unknown_type $content  content发送内容,GB2312编码,以逗号,隔开;
	 * @param unknown_type $subcode  ext扩展子号;
	 * @param unknown_type $stime    stime定时时间,格式如2009-09-01 18:21:00;
	 * @param unknown_type $rrid     rrid唯一标识,全数字.返回:唯一标识
	 */
	public static function gxmt($mobile, $content, $subcode = null, $stime = null, $rrid = null) {
		$soap = new SoapClient(Yii::app()->params['sms_soap']);
		$soap->decode_utf8 = true;
		$params = array (
			'sn'=>Yii::app()->params['sms_sn'], 
			'pwd'=>strtoupper(md5(Yii::app()->params['sms_sn'].Yii::app()->params['sms_password'])), 
			'mobile'=>$mobile, 
			'content'=>$content, 
			'ext'=>$subcode, 
			'stime'=>$stime, 
			'rrid'=>$rrid);
		$result = $soap->__soapCall('gxmt', array (
			'parameters'=>$params));
		
		return $result;
	}
	
	
	/**
	 * e达信短信通道取余额，modify by sunhongjing 2013-10-03 统一配置
	 * 
	 * @return array
	 */
	public static function GetBalance($channel=self::CHANNEL_SOAP) {
		
		$channel = trim($channel);
		
		if( !in_array( $channel, array(self::CHANNEL_SOAP,self::CHANNEL_ZCYZ) ) ){
			$channel = self::CHANNEL_SOAP;
		}
		
		$url 		= Yii::app()->params['sms_config'][$channel]['url_soap'];
		$username 	= Yii::app()->params['sms_config'][$channel]['sn'];
		$password 	= Yii::app()->params['sms_config'][$channel]['pwd'];
			
		
		$soap = new SoapClient($url);
		$soap->decode_utf8 = true;
		$params = array ( 	'sn'=>$username,  'pwd'=>$password, );
		
		$result = $soap->__soapCall('GetBalance', array ( 'parameters'=>$params ) );
		return $result;
	}
	
	/**
	 * 33易9短信余额查询
	 * @return int $count
	 * @author AndyCong<congming@edaijia.cn>
	 * @editor sunhongjing 2013-10-03 修改配置读取配置文件
	 * @version 2013-05-03
	 */
	protected static function _getbalance_gsmssoap() {
		
		$url 		= Yii::app()->params['sms_config']['gsms']['url_soap'];
		$username 	= Yii::app()->params['sms_config']['gsms']['sn'];
		$password 	= Yii::app()->params['sms_config']['gsms']['pwd'];
		
		$soap = new SoapClient($url);
		$soap->soap_defencoding = 'utf-8';   
		$soap->decode_utf8 = false;   
		$soap->xml_encoding = 'utf-8'; 
	    $params	= array('username'=>$username,'password'=>$password);
		$result = $soap->__soapCall('getUserInfo', $params);
		
		$ret  = simplexml_load_string(iconv('utf-8','gb2312',$result));
		
		$count = doubleval($ret->balance) / doubleval($ret->smsPrice);
		return intval($count);
	}
	
	/**
	 * 获取余额-指联在线
	 * @return string $ret
	 * @author AndyCong<congming@edaijia.cn>
	 */
	protected static function _getbalance_zlzxhttp() {
		$url = 'http://hysmsapi.qxt100.com/dapi/balance.php';
		$params = array ('name'=>'edaijia','pwd'=>'vtklmypl');
		$url = $url.'?'.http_build_query($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;
	}
	
	public static function SendGPRS($message) {
		$message = urlencode($message);
		$url = "http://www.edaijia.cn:22322/sms?imei=353419036320567&message=".$message;
		//http://www.edaijia.cn:22322/sms?imei=353419036320567&message=SERVER,1,www.gookey.net,8841,0#
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$content = curl_exec($curl);
		curl_close($curl);
		return $content;
	}
	
	public static function RecSMS() {
		
		$url 		= Yii::app()->params['sms_config']['soap']['url_soap'];
		$username 	= Yii::app()->params['sms_config']['soap']['sn'];
		$password 	= Yii::app()->params['sms_config']['soap']['pwd'];
		
		$sms = array();
		$soap = new SoapClient($url);
		$soap->decode_utf8 = true;
		$params = array ( 'sn'=>$username, 'pwd'=>$password );
		
		$result = $soap->__soapCall('RECSMS', array ( 'parameters'=>$params ) );
		
		$sms_body = $result->RECSMSResult->MOBody;
		//echo json_encode($sms_body);
		
		//$json = '{"total_num":"1","this_num":"1","recvtel":"300118","sender":"15011553373","content":"9\u8bfb\u901f\u5ea6\u57fa\u672c\u4e0dh\u54c8\u54c8\u54c8\u54c8\u5c31\u54c8\u54c8\u54c8\u54c8","recdate":"2012\/7\/24 0:19:21"}';
		//$json = '[{"total_num":"2","this_num":"2","recvtel":"300118","sender":"18311476212","content":"\u548c\u6df1\u5316\u6539\u9769","recdate":"2012\/7\/24 0:47:55"},{"total_num":"2","this_num":"2","recvtel":"300118","sender":"18311476212","content":"hello","recdate":"2012\/7\/24 0:48:46"}]';
		//$sms_body = json_decode($sms_body);
		$sms_body = (array)$sms_body;
		//print_r($sms_body);
		
		//只有一条的情况
		if (isset($sms_body['total_num'])) {
			$ret = self::toSms($sms_body);
			if ($ret) {
				$sms[] = $ret;
			}
		} else {
			//多条的情况
			foreach($sms_body as $item) {
				$item = (array)$item;
				$sms[] = self::toSms($item);
			}
		}
		return $sms;
	}
	
	private static function toSms($item) {
		if ($item['total_num']!=-1) {
			return array (
				'recvtel'=>$item['recvtel'], 
				'sender'=>$item['sender'], 
				'content'=>$item['content'], 
				'recdate'=>$item['recdate']);
		}
		return null;
	}
	
	public static function RecSMSEx($subcode = '') {
		
		$url 		= Yii::app()->params['sms_config']['soap']['url_soap'];
		$username 	= Yii::app()->params['sms_config']['soap']['sn'];
		$password 	= Yii::app()->params['sms_config']['soap']['pwd'];
		
		$soap = new SoapClient($url);
		$soap->decode_utf8 = true;
		$params = array ( 'sn'=>$username, 'pwd'=>$password, 'subcode'=>$subcode );
		
		$result = $soap->__soapCall( 'RECSMSEx', array ('parameters'=>$params) );
		
		$sms_body = $result->RECSMSExResult->MOBody;
		$json = json_encode($sms_body);
		
		//echo $json;die();
		//$json = '[{"total_num":"2","this_num":"2","recvtel":"30011812345","sender":"18911883373","content":"good","recdate":"2012\/9\/29 23:10:01"},{"total_num":"2","this_num":"2","recvtel":"30011800457","sender":"18911883373","content":"0+  \u5f88\u597d\u7684\u53f8\u673a\uff0c\u5de5\u4f5c\u5f88\u8ba4\u771f","recdate":"2012\/9\/30 22:22:04"}]';
		//$json = '[{"total_num":"1","this_num":"1","recvtel":"30011812345","sender":"18911883373","content":"\u5361\u83b1\u62c9\u5361","recdate":"2012\/9\/28 16:13:24"}]';
		//$json = '{"total_num":"-1","this_num":"-1","recvtel":"-1","sender":"-1","content":"\u64cd\u4f5c\u5931\u8d25","recdate":"-1"}';
		

		$sms_body = json_decode($json, true);
		
		$sms = null;
		if (isset($sms_body['total_num'])) {
			$ret = self::toSms($sms_body);
			if ($ret) {
				$sms[] = $ret;
			}
		} else {
			foreach($sms_body as $item) {
				$sms[] = self::toSms($item);
			}
		}
		return $sms;
	}
	
	
	/**
	 * 个性短信：sn软件序列号;pwd加密密码md5(sn+password);
	 * 
	 * @author sunhongjing 2013-08-08
	 * @param unknown_type $mobile   mobile手机号列表，以逗号,隔开;
	 * @param unknown_type $content  content发送内容;
	 * @param unknown_type $subcode  ext扩展子号，5位数字
	 * @param unknown_type $stime    $sendtime 定时时间,时间戳，time()+30 或strtotime('2013-08-08 12:12:12')
	 * @param unknown_type $rrid     rrid唯一标识,全数字.返回:唯一标识
	 * 
	 * @return array
	 */
	public static function mtSms($mobile, $content, $subcode = '' , $sendtime = '') {
		
		if(empty($mobile) || empty($content)){
			return array('success'=>false , 'channel' => self::CHANNEL_MT ,'msg'=>$result->SendSMSResult);
		}
		
		$url 		= Yii::app()->params['sms_config']['soap']['url_soap'];
		$username 	= Yii::app()->params['sms_config']['soap']['sn'];
		$password 	= Yii::app()->params['sms_config']['soap']['pwd'];
		
		//判断是手机号	
		$subcode = trim($subcode);
		
		if(empty($subcode)){
			$subcode = '';
		}else{
			if( ! preg_match('{^\d{0,5}$}',$subcode) ){
				$subcode = '';
			}
		}	
		
		$stime = '';
		if( ! empty($sendtime)  && $sendtime > time() ){		
			$stime  = date("Y-m-d H:i:s",$sendtime);
		}
		
		try {
			$content = $content.'【e代驾】';	
			
			$soap = new SoapClient($url);
			$soap->decode_utf8 = true;
			$params = array (
				'sn'=>$username, 
				'pwd'=> strtoupper( md5( $username.$password ) ), 
				'mobile'=>$mobile, 
				'content'=>$content, 
				'ext'=>$subcode, 
				'stime'=>$stime, 
				'rrid'=>''
			);
			$result = $soap->__soapCall('mt', array ('parameters'=>$params));
			
			//print_r($result);
			//stdClass Object
			//(
			//    [mtResult] => 081751573867423832
			//)
			
			////统一短信返回值 modify by sunhongjing 2013-05-08
			if ( 1 < $result->mtResult   ) {
				return array('success'=>true , 'channel' => self::CHANNEL_MT ,'subcode'=>$subcode, 'msg'=>$result->mtResult);
				
			}else{
				return array('success'=>false , 'channel' => self::CHANNEL_MT ,'subcode'=>$subcode,'msg'=>$result->mtResult);
			}
			
		} catch (Exception $e) {
			return array('success'=>false , 'channel' => self::CHANNEL_MT ,'subcode'=>$subcode, 'msg'=>"异常");
		}
	}
	
	
	/**
	 * 维纳通道接收上行短信
	 * 
	 * 具体示例	http://121.52.221.108/send/readxmlsms.aspx?name=company&pwd=153
	 * 返回结果	id=1&err=成功&src=13761300***&msg=第一条上行记录&dst=&time=2012-04-10 08:56:38|||| id=2&err=成功&src=1337025***&msg=你好吗&dst=&time=2012-04-10 08:56:38
	 * 
	 * @author sunhongjing 2013-08-14
	 * @return array
	 */
	public static function moWnSms()
	{
		$timeout = 5;
		$url = 'http://121.52.221.108/send/readxmlsms.aspx';
		
		try {
		
			$params = array ('name'=>'edaijia','pwd'=>'edaijia12345');
			
			$url = $url.'?'.http_build_query($params);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			$data = curl_exec($ch);
			curl_close($ch);
			print_r($data);
			//id=1&err=成功&src=13761300***&msg=第一条上行记录&dst=&time=2012-04-10 08:56:38|||| id=2&err=成功&src=1337025***&msg=你好吗&dst=&time=2012-04-10 08:56:38
			$data = mb_convert_encoding($data,'utf-8','gbk');
			$ret = parse_str($data);
			return $ret;

		} catch (Exception $e) {
			return array();
		}
	}
	
	
	
	/**
	 * 接收短信：sn软件序列号;pwd加密密码md5(sn+password).[优先使用本接口] 
	 * 
	 * @author sunhongjing 2013-08-07
	 * @return array
	 */
	public static function moSms()
	{
		$url 		= Yii::app()->params['sms_config']['soap']['url_soap'];
		$username 	= Yii::app()->params['sms_config']['soap']['sn'];
		$password 	= Yii::app()->params['sms_config']['soap']['pwd'];
		
		$sms = array();
		$soap = new SoapClient($url);
		$soap->decode_utf8 = true;
		$params = array (
			'sn'=>$username, 
			'pwd'=> strtoupper( md5($username.$password) ), 
		);
		
		$result = $soap->__soapCall('mo', array ('parameters'=>$params));
//		print_r($result);
//		stdClass Object
//		(
//		    [moResult] => 158903823,161219,15321858155,%b2%e2%ca%d4%c9%cf%d0%d024,2013-08-07 15:08:40
//		158903842,161219,15321858155,%b2%e2%ca%d4%c9%cf%d0%d025,2013-08-07 15:08:44
//		158903863,161219,15321858155,%b2%e2%ca%d4%c9%cf%d0%d026,2013-08-07 15:08:48
//		)
			
		$sms_body = $result->moResult;
		
		if(is_numeric ( $sms_body )){
			if( 1== $sms_body ){
				return $sms;//没取到任何值
			}else{
				//这里是报错了,需要记录log
				return $sms;
			}
		}else{
			//正常的数据
			$sms = self::moStr2SmsArr($sms_body);
		}
		
		return $sms;
	}
	
	/**
	 * 格式化mo方法返回的数据为数组
	 * 
	 * @author sunhongjing 2013-08-08
	 * @param string $sms_str
	 * @return array
	 */
	protected static function moStr2SmsArr($sms_str='')
	{
		$ret = array();
    	
    	if(empty($sms_str)){
    		return $ret;
    	}
    	$sms_str = preg_split('/[\r\n]+/', $sms_str);
		if(!empty($sms_str)){
			foreach ($sms_str as $str) {		
				$sms = explode(',',$str);
				$tmp = array();
				$tmp['recvtel']=$sms[1]; 
				$tmp['sender'] =$sms[2]; 
				$tmp['content']=empty($sms[3]) ? '' : mb_convert_encoding (urldecode($sms[3]),'utf-8','gbk');
				$tmp['recdate']=$sms[4];
				$ret[] = $tmp;
			}	
		}
    	
    	return $ret;
	}
	
	
	
	
	
}
