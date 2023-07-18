<?php

class Helper
{
	public static function fn_rad($d)
	{
		return $d*pi()/180.0;
	}
	
	
	/**
	 * 返回hello word
	 * @author sunhongjing 2013-06-11
	 */
	public static function getHelloWord()
	{
		$hello = '';

		$test_lock = dirname(dirname(__FILE__)).'/config/test.lock';
		$dev_lock = dirname(dirname(__FILE__)).'/config/dev.lock';
		if (is_file($test_lock) || is_file($dev_lock)) {
			return $hello = '注意：这是 eCenter 测试环境!';
		}
		
		$current_time = date("H");
        switch ($current_time) {
            case '22':
            case '23':
               			case '00':
               			case '01':
               			case '02':
               			case '03':
               			case '04':
               			case '05':
               			case '06':
               				$hello = '注意身体哦';
               				break;
               			case '07':
               			case '08':
               			case '09':
               			case '10':
               			case '11':
               				$hello = '新的一天加油！';
               				break;
               			case '12':
               			case '13':
               				$hello = '休息、休息一会儿';
               				break;
               			case '14':
               			case '15':
               			case '16':
               				$hello = '高效工作，看好你哦';
               				break;
               			case '17':
               			case '18':
               				$hello = '今日事，今日毕';
               				break;
               			case '19':	
               			case '20':	
               			case '21':	
							$hello = '记得吃晚饭哦';
							break;
               			default:
               				$hello = "";
               				break;
               		}
               		
    	return $hello;
	}
	
	/**
	 * 格式化json输出
	 * @author sunhongjing
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $callback
	 */
	public static function jsonOutPut($data,$callback='')
	{
		if ( !empty($callback) ) {
			return $callback.'('.json_encode($data).')';
		} else {
			return  json_encode($data);
		}
	}
	
	public static function getStreetByBaiduGPS($baidu_lat, $baidu_lng)
	{
		$cache_key = 'GPS_Baidu_Street_'.md5($baidu_lat . $baidu_lng);
		$json = Yii::app()->cache->get($cache_key);
		if($json){
			return json_decode($json,true);
		}
		
		$baidu_key = GPS::model()->getOneKey();

		$address = '';
		//查询百度地图返回地址
		$gps = $baidu_lat.','.$baidu_lng;
		$url = 'http://api.map.baidu.com/geocoder?output=json&location='.$gps.'&key='.$baidu_key;
		$snoopy = new Snoopy();
		$ret = $snoopy->fetch($url);
		if ($ret)
		{
			$location = json_decode($snoopy->results, true);
			$addressArray = $location['result']['addressComponent'];
				// 			if ($addressArray['province']==$addressArray['city'])
				// 			{
				// 				$address = $addressArray['city'].$addressArray['district'];
				// 			} else
				// 			{
				// 				$address = $addressArray['province'].$addressArray['city'].$addressArray['district'];
				// 			}
			if (isset($addressArray['district'])) {
				$address=$addressArray['district'];
			}
			if (isset($addressArray['street'])) {
				$address=$address.$addressArray['street'];
			}
			if (isset($addressArray['street_number'])) {
				$address=$address.$addressArray['street_number'];
			}
			
			$addressArray['city_id'] = Dict::code('city', preg_replace('%市$%', '', $addressArray['city']));
			$street = array(
				'name'=>$address, 
				'component'=>$addressArray);
			Yii::app()->cache->set($cache_key,json_encode($street),86400);
			return $street;
		}
		return null;
	}
	
	/**
	 * 
	 * 用城市和街道地址查询gps坐标
	 * @param string $city
	 * @param string $address
	 * @author dayuer  
	 * @editor AndyCong<congming@edaijia.cn>
	 *         2013-05-29
	 */
	public static function getBaiduGPSByAddress($city, $address)
	{
		$baidu_key = GPS::model()->getOneKey();

		$params = array(
			'output'=>'json', 
			'region'=>$city,
			'q'=>$address,
			'ak'=> $baidu_key);
		$url = 'http://api.map.baidu.com/place/v2/search?'.http_build_query($params);
		$location = @json_decode(self::HttpGet($url), true);
		if ($location&&isset($location['results']))
		{
			$distance = array();
			$position = $location['results'];
			if (!empty($position)) {
				$position[0]['name'] = rtrim($position[0]['name'], '市');
				return $position[0];
			}
		}
		return null;
	}
	
	public static function Wgs2Google($longitude, $latitude)
	{
		$url = 'http://api.map.baidu.com/ag/coord/convert?from=0&to=2&x='.$longitude.'&y='.$latitude;
		$snoopy = new Snoopy();
		$ret = $snoopy->fetch($url);
		if ($ret)
		{
			$gps = json_decode($snoopy->results, true);
			if (isset($gps['x']))
			{
				$longitude = base64_decode($gps['x']);
				$latitude = base64_decode($gps['y']);
			}
			
			if ($longitude&&$latitude)
			{
				return array(
					'longitude'=>$longitude, 
					'latitude'=>$latitude);
			} else
			{
				return null;
			}
		} else
		{
			return null;
		}
	}
	
	public static function Wgs2Baidu($longitude, $latitude)
	{
		$url = 'http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x='.$longitude.'&y='.$latitude;
		$snoopy = new Snoopy();
		$ret = $snoopy->fetch($url);
		if ($ret)
		{
			$gps = json_decode($snoopy->results, true);
			if (isset($gps['x']))
			{
				$longitude = base64_decode($gps['x']);
				$latitude = base64_decode($gps['y']);
			}
			
			if ($longitude&&$latitude)
			{
				return array(
					'longitude'=>$longitude, 
					'latitude'=>$latitude);
			} else
			{
				return null;
			}
		} else
		{
			return null;
		}
	}
	
	/**
	 * 计算两个GPS座标点之间的距离
	 */
	public static function Distance($lat1, $lng1, $lat2, $lng2)
	{
		// 纬度1,经度1 ~ 纬度2,经度2  
		$EARTH_RADIUS = 6378.137;
		$radLat1 = self::fn_rad($lat1);
		$radLat2 = self::fn_rad($lat2);
		$a = $radLat1-$radLat2;
		$b = self::fn_rad($lng1)-self::fn_rad($lng2);
		$s = 2*asin(sqrt(pow(sin($a/2), 2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2), 2)));
		$s = $s*$EARTH_RADIUS;
		$s = round($s*10000)/10000*1000;
		return intval($s); //number_format($s, 2);
	}
	
	
	/**
	 * 
	 * 计算优惠码的末尾校验码，优惠码长度不超过10位，校验位为10的忽略
	 * @param int $code 
	 */
	public static function CheckCode($code)
	{
		$step = 10;
		$validcode = 11;
		
		$code = str_split($code, 1);
		foreach($code as $item)
		{
			$tmp[] = $item*$step;
			$step--;
		}
		
		$sum = 0;
		foreach($tmp as $k=>$v)
		{
			$sum += $v;
		}
		
		$yushu = $sum%$validcode;
		
		if ($yushu!=0)
		{
			$check_code = $validcode-$sum%$validcode;
		} else
		{
			$check_code = $yushu;
		}
		
		if ($check_code==10)
		{
			return null;
		}
		
		return $check_code;
	}
	
	/**
	 * 计算用户回评的评分
	 * 
	 * @author sunhongjing 2013-08-28
	 * @param unknown_type $content
	 * @return array
     * 在原先需求基础上修改：dev_liufugang,cp_duanyongchao. 20150416
        1.解析内容，如果没解析出level，则该短信无效，不入库。
        2.针对"5{汉字}+"这样的，返回内容为，{level:5,content:"非常满意,#输入汉字#"};
        3.在第2点基础上,”5分“，作为特出处理。返回结果{level:5,content:"非常满意"};
	 */
	public static function getSmsScore($content='')
	{
		//匹配出开头的数字，如果是1位数字，则匹配。否则，再匹配文本中是否包含满意、非常好字眼，包含则处理成5。其他都直接按0算
		$pattern = "{^(\d+).*$}is";
		$starList = array(1,2,3,4,5);
		$level = 0;
		//处理字符串
		$raw_content = trim($content);
		
		if( preg_match($pattern, $raw_content, $matches) ){
			if( $matches[1]<=5){
				//评价
				$level = $matches[1];
				$raw_content = substr($raw_content, 1 );
			}
		}else{
			//简单匹配下
			$refer_arr = array('很好','非常好','满意','很满意','非常满意');
			foreach ($refer_arr as $v) {
				if( false !== strpos($raw_content,$v) ){
					$level=5;
					break;
				}
			}
		}
		
		$refer_content = array(
								'1'=>'非常不满意',
								'2'=>'不满意',
								'3'=>'一般',
								'4'=>'满意',
								'5'=>'非常满意',
							);
							
		if($level>0 && $level<6){
			if(empty($raw_content) || $raw_content=="分"){
                $raw_content = $refer_content[$level];
			}else{
                if($refer_content[$level]==$raw_content){
                    $raw_content = $refer_content[$level];
                }else{
                    $raw_content = $refer_content[$level].",".$raw_content;
                }
            }
		}
		
		return array('level'=>$level,'content'=>$raw_content);
	}

    /**
     * 获取短信回评投诉内容
     * @param string  $content
     * @return string $content_detail
     */
    public static function getSmsMoContent($content = '') {

        $refer_content = array(
            'A'=>'多收费',
            'B'=>'未展示计价器',
            'C'=>'猛踩刹车油门',
            'D'=>'态度不友好',
            'E'=>'未穿统一服装',
            'F'=>'个人卫生不好',
            'a'=>'多收费',
            'b'=>'未展示计价器',
            'c'=>'猛踩刹车油门',
            'd'=>'态度不友好',
            'e'=>'未穿统一服装',
            'f'=>'个人卫生不好',
        );
        if(empty($content)){
            return $content;
        }
        foreach ($refer_content as $key => $value){
            //逐个字母替换
            $content = str_replace($key,$refer_content[$key], $content);
        }
        return $content;
    }

	/**
	 * 
	 * 解析回评短信的内容，区分评价星级
	 * @param string $content
	 */
	public static function getSmsStar($content){
		
		$starList = array(1,2,3,4,5);
		$raw_content = $content;
		$level = 0;
		//处理字符串
		$raw_content = trim(str_replace('+', ' ', $raw_content));
		$symbols = array(',','？','\?','0','\+','，','。','！','!',':','：','\.','＋','十',',');
		foreach($symbols as $symbol)
		{
			$raw_content = preg_replace('%^'.$symbol.'%s', '', $raw_content);
		}
		
		//获取第一位
		$star = substr($raw_content, 0,	1);
		
		if(preg_match("/^\d{1}$/",$star)){
			if(in_array($star, $starList)){
				$level = $star;
				//$raw_content = str_replace($star, '', $raw_content);
				$raw_content = substr($raw_content, 1,strlen($raw_content));
			}
		}
		$raw_content = str_replace(' ', '', $raw_content);
		$raw_content = str_replace('+', '', $raw_content);
		
		//UPDATE libaiyang 2013-05-14
		if(($level==1||$level==2)&& $raw_content!=''){
			$gbk_array = array('。','？','！','，','、','；','：','〔','（','）','[',']','{','}','〕','─','《','》','〈','〉');
			$new_content = $raw_content;
			foreach($gbk_array as $item){
				$new_content = str_replace($item, '', $new_content);
			}
			$new_content = preg_replace('/\w/', '', $new_content);
			$new_content = preg_replace('/[[:punct:]]/i', '', $new_content);
			$array = array('好','很好','非常好','满意','很满意','非常满意');
			if(in_array($new_content, $array)){
				$level=5;
			}
		}
		return array(
				'level'=>$level,
				'content'=>trim(trim($raw_content),"+"));
	} 
	
	
	/**
	 * 
	 * 解析回评短信的内容，区分好评差评
	 * @param string $content
	 */
	public static function Sms($content)
	{
		$raw_content = $content;
		$level = -1;
		$content = trim(str_replace('+', ' ', $raw_content));
		
		$pattern_good = array(
			'%^0%s', 
			'%好%s', 
			'%不错%s', 
			'%满意%s', 
			'%专业%s', 
			'%优秀%s');
		$pattern_bad = array(
			'%差%s', 
			'%一般%s', 
			'%^1%s', 
			'%不满意%s', 
			'%不好%s');
		
		foreach($pattern_bad as $pattern)
		{
			$result = preg_match($pattern, $content, $matchs);
			if ($result)
			{
				$level = 1;
				break;
			}
		}
		
		if ($level==-1)
		{
			foreach($pattern_good as $pattern)
			{
				$result = preg_match($pattern, $content, $matchs);
				if ($result)
				{
					$level = 3;
					break;
				}
			}
		
		}
		
		//去掉行首的多余字符
		$symbols = array(
			'回复', 
			'好评', 
			'满意', 
			'不满意', 
			'差评', 
			'内容', 
			',', 
			'？', 
			'\?', 
			'0', 
			'1', 
			'\+', 
			'，', 
			'。', 
			'！', 
			':', 
			'：', 
			'\.', 
			'＋', 
			'十', 
			',');
		foreach($symbols as $symbol)
		{
			$content = preg_replace('%^'.$symbol.'%s', '', $content);
		}
		if (!$content)
		{
			switch ($level)
			{
				case 3 :
					$content = '满意';
					break;
				case 1 :
					$content = '不满意';
					break;
			}
		}
		return array(
			'level'=>$level, 
			'content'=>trim($content));
	}
	
	/**
	 * 
	 * 查询手机号码归属地
	 * @editor sunhongjing 2013-08-03 重构该方法
	 * @param string $mobile
	 */
	public static function PhoneLocation($phone)
	{
		$city_name = '未开通';
		$city_code = 0;

		//统一开通城市获取方法，modify by sunhongjing 2013-08-03
		$area = RCityList::model()->getOpenCityList();
		
		$location = self::_getMobileSegment_hljq($phone);
		if(empty($location['city'])){
			$location = self::_getMobileSegment_tenpay($phone);
			if(!empty($location['city'])){
                $city_name = trim($location['city']);
			}
		}else{
            $city_name = trim($location['city']);
		}

        $tmp= array_search($city_name,$area);
        if($tmp) $city_code = $tmp;
//          foreach ($area as $k => $v ) {
//               if( trim($city_name)==$v ){
//                    $city_code = $k;
//                    break;
//               }
//          }


        return $city_code;
	}
	
	public static function HttpGet($url, $second = 10)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		
		curl_close($ch);
		return $data;
	}
	
	public static function HttpPost($url, $params, $second = 15)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	/**
	 * 查询手机号归属地,淘宝接口
	 * 
	 * 
	 * @author sunhongjing 2013-08-02
	 * @param string $phone
	 * @return array
	 */
	public static function _getMobileSegment_hljq($phone='')
	{
		$ret = array();
		if(empty($phone)){
			return $ret;
		}
		$area = array(
			'010'=>'北京', 
			'028'=>'成都', 
			'021'=>'上海', 
			'0571'=>'杭州', 
			'020'=>'广州', 
			'0755'=>'深圳', 
			'023'=>'重庆',
			'025'=>'南京',
			'0731'=>'长沙',
			'027'=>'武汉',
			'029'=>'西安',
			'0574'=>'宁波',
			'0577'=>'温州',
			'0371'=>'郑州',
			'0531'=>'济南',
			'022'=>'天津',
			'0512'=>'苏州',
			'0871'=>'昆明',
			'024'=>'沈阳',
			'0532'=>'青岛',
			'0411'=>'大连',
			'0592'=>'厦门',
			'0551'=>'合肥',
	    	'0451'=>'哈尔滨',
	    	'0311'=>'石家庄',
	    	'0791'=>'南昌',
	    	'0591'=>'福州',
	    	'0757'=>'佛山',
	    	'0351'=>'太原',
			'0510'=>'无锡',
		   	'0519'=>'常州',
		   	'0769'=>'东莞',
		   	'0851'=>'贵阳',
		   	'0931'=>'兰州',
		   	'0771'=>'南宁',
		   	'0431'=>'长春',
		   	'0513'=>'南通',
		   	'0471'=>'呼和浩特',
		   	'0472'=>'包头',
		
		);
		
		$url = 'http://a2.7x24cc.com/commonInte/?flag=2&callNo='.$phone;
		$zipcode = self::HttpGet($url);
		
		//合力金桥的接口查不到的电话，转有道查询
		if ($zipcode && $zipcode!=404)
		{
			if (array_key_exists($zipcode, $area)){
				$ret['city'] = $area[$zipcode];
				//$ret['mobile'] = $phone;
			}
		}
		
		return $ret;
	}
	
	/**
	 * 查询手机号归属地,淘宝接口
	 * 
	 *
		__GetZoneResult_ = {
		    mts:'1891162',
		    province:'北京',
		    catName:'中国电信',
		    telString:'18701552183'
		}
	 * 
	 * @author sunhongjing 2013-08-02
	 * @param string $phone
	 * @return array
	 */
	public static function _getMobileSegment_taobao($phone=''){
		$ret = array();
		if(empty($phone)){
			return $ret;
		}
		
		$url = 'http://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel='.$phone;
		$data = self::HttpGet($url);
		try {
			$data = str_replace("__GetZoneResult_ = {","array(",$data);
			$data = str_replace("mts:","'mts'=>",$data);
			$data = str_replace("province:","'province'=>",$data);
			$data = str_replace("catName:","'isp'=>",$data);
			$data = str_replace("telString:","'mobile'=>",$data);
			$data = str_replace("}",")",$data);
			$ret = eval( 'return ' .$data . ';' );
			if( isset($ret['mts']) ) { unset($ret['mts']); }
			$ret['city'] = '';
		} catch (Exception $e) {
			
		}
		
		return $ret;
	}
	
	
	/**
	 * 查询手机号归属地,财富通接口
	 * 
	 * @author sunhongjing 2013-08-02
	 * @param string $phone
	 * @return array
	 */
	public static function _getMobileSegment_tenpay($phone=''){
		$ret = array();
		if(empty($phone)){
			return $ret;
		}
		
		$url = 'http://life.tenpay.com/cgi-bin/mobile/MobileQueryAttribution.cgi?chgmobile='.$phone;
		$data = self::HttpGet($url);
		try {
			if(!empty($data)){
	        	$obj = @(array)simplexml_load_string($data);//不要抑制报错，这样接口错了能发现问题
	            $ret = array();
	            $ret['mobile'] = isset($obj['chgmobile']) ? $obj['chgmobile'] : '';
	            $ret['province'] = isset($obj['province']) ? $obj['province'] : '';
	            $ret['city'] = isset($obj['city']) ? $obj['city'] : '';
	            $ret['isp'] = isset($obj['supplier']) ? $obj['supplier'] : '';
	        }
		} catch (Exception $e) {
			
		}
		
		return $ret;
	}
	
	/**
	 * 查询手机号归属地,拍拍接口
	 * try{({mobile:'18898732451',province:'广东',isp:'中国移动',stock:'1',amount:'10000',maxprice:'0',minprice:'0'});}catch(e){} 
	 * 
	 * @author sunhongjing 2013-08-02
	 * @param string $phone
	 * @return array
	 */
	public static function _getMobileSegment_paipai($phone=''){
		$ret = array();
		if(empty($phone)){
			return $ret;
		}

		$url = 'http://virtual.paipai.com/extinfo/GetMobileProductInfo?amount=10000&mobile='.$phone;
		$data = self::HttpGet($url);
		try {
			preg_match('@^try\{\([^}\)]+\}\)@i',$data, $matches);
			if( !empty($matches[0]) ){
				$data = $matches[0];
	            $data = str_replace("try{(","",$data);
	            $data = str_replace(")","",$data);
	            $data = str_replace(",stock:'1',amount:'10000',maxprice:'0',minprice:'0'","",$data);
	            $data = str_replace("{","array(",$data);
	            $data = str_replace("}",")",$data);
	            $data = str_replace("mobile:","'mobile'=>",$data);
	            $data = str_replace("province:","'province'=>",$data);
	            $data = str_replace("isp:","'isp'=>",$data);
	            $data = mb_convert_encoding($data,'utf-8','gbk');
				$ret = eval( 'return ' .$data . ';' );
			}
		} catch (Exception $e) {
			
		}
				

		return $ret;
	}
	
	/**
	 * 查询手机号归属地,拍拍接口
	 * 
	 * @author sunhongjing 2013-08-03
	 * @param string $phone
	 * @return array
	 */
	public static function _getMobileSegment_youdao($phone='')
	{
		$ret = array();
		if(empty($phone)){
			return $ret;
		}
		
		$url = 'http://www.youdao.com/smartresult-xml/search.s?type=mobile&q='.$phone;
		$data = self::HttpGet($url);
		if ($data)
		{
			$product = simplexml_load_string($data);
			$ret['city'] = $product->product->location;
		}
		return $ret;
	}
	
	
	
	/**
	 * 
	 * 短链接生成
	 * @param string $long_url
	 */
	public static function shortUrl($long_url)
	{
		$key = 'edaijia';
		$base32 = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		
		// 利用md5算法方式生成hash值  
		$hex = hash('md5', $long_url.$key);
		$hexLen = strlen($hex);
		$subHexLen = $hexLen/8;
		
		$output = array();
		for($i = 0; $i<$subHexLen; $i++)
		{
			// 将这32位分成四份，每一份8个字符，将其视作16进制串与0x3fffffff(30位1)与操作  
			$subHex = substr($hex, $i*8, 8);
			$idx = 0x3FFFFFFF&(1*('0x'.$subHex));
			
			// 这30位分成6段, 每5个一组，算出其整数值，然后映射到我们准备的62个字符  
			$out = '';
			for($j = 0; $j<6; $j++)
			{
				$val = 0x0000003D&$idx;
				$out .= $base32[$val];
				$idx = $idx>>5;
			}
			$output[$i] = $out;
		}
		
		return $output[0];
	}
	
	/**
	 * 
	 * 提交api访问数据到google
	 */
	public static function track()
	{
		$var_utmhn = 'api.edaijia.cn'; //enter your domain
		$var_utmac = 'UA-33826171-2'; //enter the new urchin code
		$var_cookie = '201990725'; //insert here the first number in your __utma cookie (visit YOUR site and check your cookies)		
		

		#On verifie de quel bot il s'agit puis on l'insert dans GA.
		$uri = $_SERVER["QUERY_STRING"]; //Resquested URI by Crawler
		

		//$uri = 'utmwv=5.3.4&utms=4&utmn=2029461433&utmhn=edaijia.cn&utme=8(Download)9(Android)&utmcs=UTF-8&utmsr=1440x900&utmvp=1440x302&utmsc=24-bit&utmul=zh-cn&utmje=1&utmfl=11.3%20r300&utmdt=e%E4%BB%A3%E9%A9%BE&utmhid=1602862978&utmr=-&utmp=%2Fdownload%2Fandroid&utmac=UA-33826171-1&utmcc=__utma%3D135055525.654375588.1343886444.1344840914.1344847406.5%3B%2B__utmz%3D135055525.1343888135.2.2.utmcsr%3Dwebio%7Cutmccn%3Dweibo_20120802%7Cutmcmd%3Dbeijing%3B&utmu=qRC~';
		parse_str($uri, $query);
		//print_r($query);die();
		

		$method = $query['method'];
		$imei = $query['imei'];
		$driver = Driver::getProfileByImei($imei);
		$userid = $driver->user;
		
		$var_utmn = rand(1000000000, 9999999999); //random request number
		$var_utmdt = ''; //urlencode( wp_title() );							//Nom de la page visitée
		$var_server = $imei; //server url => pour le crawler Remote host, nom qualifié de la machine cliente
		$var_utmp = urlencode($uri); //Page vue par le visiteur
		$var_random = rand(1000000000, 2000000000); //number under 2147483647
		$var_now = time(); //today
		

		$urchinUrl = '';
		
		$urchinUrl .= 'http://www.google-analytics.com/__utm.gif?';
		$urchinUrl .= 'utmwv=1';
		$urchinUrl .= '&utmn='.$var_utmn; //Nb au hasard
		$urchinUrl .= '&utmsr=-'; //Resolution ecran
		$urchinUrl .= '&utmsc=-'; //Qualite ecran
		$urchinUrl .= '&utmul=-'; //Langue du navigateur
		$urchinUrl .= '&utmje=0'; //Java enabled
		$urchinUrl .= '&utmfl=-'; //Flash version
		$urchinUrl .= '&utmdt='.$var_utmdt; //Nom de la page visitée
		$urchinUrl .= '&utmhn='.$var_utmhn; //Nom du site Web
		$urchinUrl .= '&utmr=-'; //pas de referer
		//$urchinUrl .= '&_trackPageview='.$method; //pas de referer
		$urchinUrl .= '&utme=8('.$method.')9('.$userid.')'; //Nombre???(Objet*Action*Label) => 5(Robots*Bot Name*Pathname)
		$urchinUrl .= '&utmp=/'.$method.'/'.$userid; //Page Vue par le visiteur
		$urchinUrl .= '&utmac='.$var_utmac; //Numero de compte analytics
		$urchinUrl .= '&utmcc=__utma%3D'.$var_cookie.'.'.$var_random.'.'.$var_now.'.'.$var_now.'.'.$var_now.'.1%3B%2B__utmb%3D'.$var_cookie.'%3B%2B__utmc%3D'.$var_cookie.'%3B%2B__utmz%3D'.$var_cookie.'.'.$var_now.'.1.1.utmccn%3D(organic)%7Cutmcsr%3D'.$method.'%7Cutmctr%3D'.$uri.'%7Cutmcmd%3Dorganic%3B%2B__utmv%3D'.$var_cookie.'.Api%20method%3A%20'.$var_server.'%3B';
		
		echo $urchinUrl;
		die();
		#Injection de la page dans Google Analytics
		$cu = curl_init();
		curl_setopt($cu, CURLOPT_HEADER, 1);
		curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($cu, CURLOPT_URL, $urchinUrl);
		$code = curl_exec($cu);
		curl_close($cu);
	}
	/*
	------------------------------------------------------
	参数：
	$sourcestr    需要截断的字符串
	$cutlength     允许字符串显示的最大长度
	
	程序功能：根据UTF-8编码规范，将3个连续的字符计为单个字符
	------------------------------------------------------
	*/
	function cut_str($sourcestr = null , $cutlength = 20)  
	{  
	   $returnstr='';  
	   $i=0;  
	   $n=0;  
	   $str_length=strlen($sourcestr);//字符串的字节数  
	   while (($n<$cutlength) and ($i<=$str_length))  
	   {  
	      $temp_str=substr($sourcestr,$i,1);  
	      $ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码  
	      if ($ascnum>=224)    //如果ASCII位高与224，  
	      {  
	$returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符          
	         $i=$i+3;            //实际Byte计为3  
	         $n++;            //字串长度计1  
	      }  
	      elseif ($ascnum>=192) //如果ASCII位高与192，  
	      {  
	         $returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符  
	         $i=$i+2;            //实际Byte计为2  
	         $n++;            //字串长度计1  
	      }  
	      elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，  
	      {  
	         $returnstr=$returnstr.substr($sourcestr,$i,1);  
	         $i=$i+1;            //实际的Byte数仍计1个  
	         $n++;            //但考虑整体美观，大写字母计成一个高位字符  
	      }  
	      else                //其他情况下，包括小写字母和半角标点符号，  
	      {  
	         $returnstr=$returnstr.substr($sourcestr,$i,1);  
	         $i=$i+1;            //实际的Byte数计1个  
	         $n=$n+0.5;        //小写字母和半角标点等与半个高位字符宽...  
	      }  
	   }  
	         if ($str_length>$i){  
	          $returnstr = $returnstr . "...";//超过长度时在尾处加上省略号  
	      }  
	    return $returnstr;  
	}


    public static function truncate_utf8_string($string, $length, $etc = '...')
    {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen) {
            $result .= $etc;
        }
        return $result;
    }
    
    /**
     * 截取城市短地址
     * 
     * @param unknown_type $address
     * @param unknown_type $city
     */
    public static function getShortAddress($address='',$city='') {
    	
		$open_city_list = array(
						    	'1'=>'北京市',
					    		'2'=>'四川省成都市',
						    	'3'=>'上海市',
					    		'4'=>'浙江省杭州市',
						    	'5'=>'广东省广州市',
						    	'6'=>'广东省深圳市',
						    	'7'=>'重庆市',
						    	'8'=>'江苏省南京市',   	
						    	'9'=>'湖南省长沙市',   	
						    	'10'=>'湖北省武汉市',
					    		'11'=>'陕西省西安市',
					    		'12'=>'浙江省宁波市',
					    		//'13'=>'温州',
						    	'14'=>'天津市',
						    	'15'=>'山东省济南市',    	
						    	'16'=>'江苏省苏州市',    	
						    	//'17'=>'昆明',    	
						    	'18'=>'河南省郑州市',
						    	//'19'=>'沈阳',
						    	'20'=>'山东省青岛市',
						    	//'21'=>'大连',
						    	'22'=>'福建省厦门市',
						    	//'23'=>'合肥',
						    	//'24'=>'哈尔滨',
						    	//'25'=>'石家庄',
						    	//'26'=>'南昌',
						    	'27'=>'福建省福州市',
						    	//'28'=>'佛山',
						    	//'29'=>'太原',
						    	//'30'=>'无锡',
						    	//'31'=>'常州',
						    	//'32'=>'东莞',
						    	//'33'=>'贵阳',
						    	//'34'=>'兰州',
						    	//'35'=>'南宁',
						    	//'36'=>'长春',
						    	//'37'=>'南通',
						    	//'38'=>'呼和浩特',
						    	//'39'=>'包头',
    					);
    	$cut_str = '';
    				
    	if(!empty($city)){
    		$cut_str = isset($open_city_list[$city]) ? $open_city_list[$city] : ''; 
    	}
    	
    	if( !empty($cut_str) ){
    		$address = str_replace($cut_str,'',$address);
    	}
    	
    	return $address;	
    	
    }
    
    /**
     * 返回订单的track信息，从又拍云获取
     * 
     * @author sunhongjing 2013-11-07
     * @param unknown_type $path
     */
     public static function getOrderTrack($path=''){
     	
     	$ret = null;
     	
     	if(empty($path)){
     		return $ret;
     	}
     	$url = 'http://etrack.b0.upaiyun.com/';
     	$order_track = self::HttpGet($url.$path);
		
     	if(empty($order_track)){
     		return $ret;
     	}
     	
     	$ret = @json_decode($order_track);
     	
		return $ret;
     }
    
    
    /**
	 * 添加百度统计(调用之前先要加载BaiduStat类)
	 * @return string
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-09-10
	 */
	public static function getBdCount()
	{
		$_hmt = new BaiduStat("e18aa2a4967dbe5551af84bddd72b45b");
		$_hmtPixel = $_hmt->trackPageView();
	  	return $_hmtPixel;
	}

  /**
   * 格式化司机信息 driver_status通过参数传入 不调用redis获取数据
   * @author wangjian 2014-04-05
   * @param array $params
   * @param array $params
   */
  public static function simple_format_driver_detail($params, $driver_redis_info) {
    $ret = array();
    $driver = $driver_redis_info;

    if(empty($params['driver_id'])) {
      return null;
    }

    $driver_id = $params['driver_id'];
    $gps_type  = isset($params['gps_type']) ? $params['gps_type'] : '';
    $distance  = isset($params['distance']) ? $params['distance'] : 0;
    $data      = isset($params['data']) ? $params['data'] : 'normal';
    $d_status  = isset($params['status']) ? $params['status'] : false;

    //传递过来的mongodb里的司机状态,如果为-1,说明该司机有问题,就剔除
    //否则比对mongodb和redis里的状态是否对
    if(-1 == $d_status){
      return null;
    }

    //比对mongodb和redis里的状态是否对
    if(false !== $d_status && $d_status != $driver['status']) {
      return null;
    }

    //如果坐标有问题，也抛掉
    if( isset($driver['position']['baidu_lng']) ){
      if( 10 > ( $driver['position']['baidu_lng']
        + $driver['position']['baidu_lat'] ) ){
        return null;
      }
    }

    $new_level = empty($driver['info']['level']) ? 0 : $driver['info']['level'];
    $id_card = isset($driver['info']['id_card']) ?
      substr_replace($driver['info']['id_card'], '******', 10, 6) : '';

    switch ($gps_type) {
      case 'wgs84':
      case 'google':
        $longitude = $driver['position']['google_lng'];
        $latitude = $driver['position']['google_lat'];
        break;
      default:
        if( isset($driver['position']['baidu_lng']) ){
          $longitude = $driver['position']['baidu_lng'];
          $latitude = $driver['position']['baidu_lat'];
        }else{
          $longitude = $driver['position']['google_lng'];
          $latitude = $driver['position']['google_lat'];
        }
        break;
    }

    //验证司机是否为皇冠 BY AndyCong 2013-08-09
    $recommand = $begin_time = $end_time = 0;
    $driver_recommand = $driver['recommand'];
    if (!empty($driver_recommand)) {
      $begin_time = isset($driver_recommand['begin_time']) ? strtotime($driver_recommand['begin_time']) : 0;
      $end_time = isset($driver_recommand['end_time']) ? strtotime($driver_recommand['end_time']) : 0;
      $current_time = time();
      if ($current_time > $begin_time && $current_time < $end_time) {
        $recommand = 1;
      }
    }
    //验证司机是否为皇冠 BY AndyCong 2013-08-09 END

    $detail=array(
      'driver_id'     => $driver_id,
      'name'          => $driver['info']['name'],
      'year'          => $driver['info']['year'],
      'state'         => $driver['status'],
      'domicile'      => $driver['info']['domicile'],
      'new_level'     => $new_level,
      'recommand'     => $recommand,
      'goback'        => $driver['goback'],
      'service_times' => !empty($driver['service']['service_times'])
        ? $driver['service']['service_times'] : 0,
      'distance'      => !isset($distance) ? '' : self::_format_distince($distance),
      'longitude'     => $longitude,
      'latitude'      => $latitude,
      'picture_small' => $driver['info']['picture_small'],
    );

    $data = empty($data) ? '' : strtolower($data);
    switch ($data) {
    case 'normal':
      $detail['phone']  = $driver['phone'];
      $detail['idCard'] = $id_card;
      $detail['picture_middle']  = $driver['info']['picture_middle'];
      $detail['picture_large']   = $driver['info']['picture_large'];
      break;
    case 'mini':
      break;
    case 'driver':
      $detail['phone'] = $driver['phone'];
      $detail['recommand_begin_time'] = $begin_time; ////2013-10-28 zhanglimin 新增皇冠时间
      $detail['recommand_end_time'] = $end_time;
      break;
    default:
      $detail['phone']  = $driver['phone'];
      $detail['idCard'] = $id_card;
      $detail['recommand_begin_time'] = $begin_time; ////2013-10-28 zhanglimin 新增皇冠时间
      $detail['recommand_end_time'] = $end_time;
      break;
    }

    return $detail;
  }
	
	/**
	 * 格式化司机信息
	 * @author sunhongjing 2013-10-10
	 * 
	 * @param string $driver_id
	 * @param string $gps_type
	 * @param string $distance
	 * 
	 */
	public static function foramt_driver_detail($driver_id, $gps_type='', $distance=0, $data='nomal',$d_status=false) {
		
		if(empty($driver_id)){
			return null;
		}
		
		//传递过来的mongodb里的司机状态，如果为-1，说明该司机有问题，就剔除，否则比对mongodb和redis里的状态是否对
		if( -1 == $d_status ){
			return null;
		}
		
		$driver = DriverStatus::model()->get($driver_id);
		
		if(empty($driver)){
			return null;
		}
		
		//比对mongodb和redis里的状态是否对
		if( false !== $d_status){
			if( $d_status != $driver->status ){
				return null;
			}
		}
		
		//如果坐标有问题，也抛掉
		if( isset($driver->position['baidu_lng']) ){
			if( 10 > ( $driver->position['baidu_lng'] + $driver->position['baidu_lat'] ) ){
				return null;
			}
		}
		
		if ( empty($driver->info['level']) ) {
			$new_level=0;
		} else {
			$new_level=$driver->info['level'];
		}
		
		$id_card=isset($driver->info['id_card']) ? substr_replace($driver->info['id_card'], '******', 10, 6) : '';
		//$car_card=isset($driver->info['car_card']) ? substr_replace($driver->info['car_card'], '******', 10, 6) : '';
		
		switch ($gps_type) {
			case 'wgs84':
			case 'google' :
				$longitude=$driver->position['google_lng'];
				$latitude=$driver->position['google_lat'];
				break;
			default :
				if( isset($driver->position['baidu_lng']) ){
					$longitude=$driver->position['baidu_lng'];
					$latitude=$driver->position['baidu_lat'];
					
				}else{
					$longitude=$driver->position['google_lng'];
					$latitude=$driver->position['google_lat'];	
				}
				break;
		}
		
		//验证司机是否为皇冠 BY AndyCong 2013-08-09 
		$recommand =  $begin_time = $end_time =  0;
		$driver_recommand = $driver->recommand;
		if (!empty($driver_recommand)) {
			$begin_time = isset($driver_recommand['begin_time']) ? strtotime($driver_recommand['begin_time']) : 0;
			$end_time = isset($driver_recommand['end_time']) ? strtotime($driver_recommand['end_time']) : 0;
			$current_time = time();
			if ($current_time > $begin_time && $current_time < $end_time) {
				$recommand = 1;
			}
		}
		//验证司机是否为皇冠 BY AndyCong 2013-08-09 END
		
		$detail=array(
					'driver_id'	=> $driver_id,
					'name'		=> $driver->info['name'],
					'year'		=> $driver->info['year'],
					'state'		=> $driver->status,
					'domicile'	=> $driver->info['domicile'],
					'new_level'	=> $new_level,
					'recommand'	=> $recommand, 
					'goback'	=> $driver->goback,
					'service_times'	=> !empty($driver->service['service_times']) ? $driver->service['service_times'] : 0,
					'distance'  => !isset($distance) ? '' : self::_format_distince($distance),
					'longitude'	=> $longitude,
					'latitude'	=> $latitude,
					'picture_small'	=> $driver->info['picture_small'],
				);
		
		$data = empty($data) ? '' : strtolower($data);
		
		switch ($data) {
			case 'nomal':
				$detail['phone']  = $driver->phone;
				$detail['idCard'] = $id_card;
				//$detail['card'] = $car_card;
				$detail['picture_middle']  = $driver->info['picture_middle'];
				$detail['picture_large']   = $driver->info['picture_large'];
				break;	
			case 'mini':break;
			case 'driver': 
				$detail['phone'] = $driver->phone;
				$detail['recommand_begin_time'] = $begin_time;////2013-10-28 zhanglimin 新增皇冠时间
				$detail['recommand_end_time'] = $end_time;
				break;
			default:
				$detail['phone']  = $driver->phone;
				$detail['idCard'] = $id_card;
				$detail['recommand_begin_time'] = $begin_time;////2013-10-28 zhanglimin 新增皇冠时间
				$detail['recommand_end_time'] = $end_time;
				break;
		}
		
		return $detail;
	}

	/**
	 * 格式化距离显示
	 * @author sunhongjing 2013-10-10
	 * 
	 * @param int $distance
	 * @return sting
	 */
	public static function _format_distince($distance) {
		$distance=intval($distance);
		
		if ($distance<=100) {
			$distance='100米内';
		} elseif ($distance>100&&$distance<=200) {
			$distance='200米内';
		} elseif ($distance>200&&$distance<=300) {
			$distance='300米内';
		} elseif ($distance>300&&$distance<=400) {
			$distance='400米内';
		} elseif ($distance>400&&$distance<=500) {
			$distance='500米内';
		} elseif ($distance>500&&$distance<=600) {
			$distance='600米内';
		} elseif ($distance>600&&$distance<=700) {
			$distance='700米内';
		} elseif ($distance>700&&$distance<=800) {
			$distance='800米内';
		} elseif ($distance>800&&$distance<=900) {
			$distance='900米内';
		} elseif ($distance>900&&$distance<=1000) {
			$distance='1公里';
		} else {
			$distance=number_format(intval($distance)/1000, 1).'公里';
		}
		
		return $distance;
	}
	
    /**
     * 通过客户位置和司机工号获取距离
     * @param string $driver_id
     * @param string $customer_lng
     * @param string $customer_lat
     * @param string $gpy_type---默认wgs84是百度坐标
     * @return int $distince
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-10-18
     */
    public static function getDriverDistance($driver_id , $customer_lng , $customer_lat , $gps_type = 'wgs84') {
    	return DriverGPS::model()->getDriverDistance($driver_id, $customer_lng, $customer_lat, $gps_type);
    }

    /**
     * 对象转数组
     * @param $e
     * @return array
     */
    public static function objectToArray($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return;
            if( gettype($v)=='object' || gettype($v)=='array' )
                    $e[$k]=(array)objectToArray($v);
        }
        return $e;
    }


    public static function alert($msg,$return_url = ''){
        header('Content-Type:text/html;charset=utf-8');
        echo '<script type="text/javascript" charset="utf-8">alert("'.$msg.'");';
        if($return_url){
            echo 'location.href="'.$return_url.'";';
        }else{
            echo 'history.go(-1);';
        }
        echo '</script>';
        return false;
    }

    /**
     * 比较版本号
     * @param $a string
     * @param $b string
     * @return boolean ($a > $b true)
     */
    public static function compareVersion($a ,$b) {
        if(empty($a)) {
	    return false;
	}
	if(empty($b)) {
	    return true;
	}

	$la = explode('.', $a);
	$lb = explode('.', $b);
	$len = count($la) > count($lb) ? count($la) : count($lb);
	for($i=0; $i<$len; $i++) {
	    $ai = isset($la[$i]) ? ((int)$la[$i]) : 0;
	    $bi = isset($lb[$i]) ? ((int)$lb[$i]) : 0;
	    if($ai > $bi) {
	        return true;
	    }
	    else if($ai < $bi) {
	        return false;
	    }
	}

	return false;
    }


    public static function syncDriverToBbs($driver_id,$password){
        $mail = $driver_id.'@edaijia-sj.cn';
        $url = 'http://bbs.edaijia.cn/insertMem.php?mod=register&username='.$driver_id.'&password='.$password.'&password2='.$password.'&email='.$mail.'&inajax=1&regsubmit=yes&formhash=d8be1872&edaijiaauth=';
        $yanzheng = md5("mod_register_username_".$driver_id."_password_".$password."_password2_".$password."_email_".$mail."_inajax_1_regsubmit_yes_formhash_d8be1872_sig_sgs346poias09!$*");
        $url .= $yanzheng;
        //echo $url;die;
        $res = self::HttpGet($url,60);
        return $res;
    }

	/**
	 * 发送报警邮件  采用异步的方式实现
	 *
	 * @param $title
	 * @param $content
	 * @param $toList
	 */
	public static  function mailAlarm($title, $content, $toList = array()){
		if(empty($toList)){
			return '';
		}
		$params = array(
			'title' 	=> $title,
			'content'	=> $content,
			'toList'	=> $toList,
		);
		//添加task队列向数据中添加
		$task = array(
			'method'=>'send_mail_alarm',
			'params'=>$params
		);
		@Queue::model()->putin($task, 'default');
	}


    /**
     * 生成二维码方法
     * @param $url
     * @param string $path
     * @param string $file_name 后续可以添加图片的大小，清晰度等。
     * @author duke
     * @return string
     */
    public function create_qrcode($url,$path = 'activity',$file_name = ''){
        Yii::import('application.extensions.qrcode.*');
        $time = time();
        $pic_url = '';
        $tmp_file = '/tmp/tmp_'.time();
        $res = QRcode::png($url, $tmp_file, 'H', 8, 2);
        if(file_exists($tmp_file)){
            $bucketname =  'edriver';
            if(!$file_name){
                $img_name = $time.'.jpg';
            }else{
                $img_name = $file_name;
            }

            $upload_model = new UpyunUpload($bucketname);
            $path = $path.'/'.substr(md5($url.$time),0,2);
            $is_upload = $upload_model->uploadFile($tmp_file, $path, $img_name);
            $is_upload['img_name'] = $img_name;
            if (is_array($is_upload) && count($is_upload) > 1) {
                $pic_url = 'http://pic.edaijia.cn/'.$path.'/'.$img_name;
            }
        }
        return $pic_url;
    }
}
