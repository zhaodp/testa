<?php
set_time_limit(300); //设置超时时间300秒
/**
 * 删除订单时间线的缓存（redis）信息
 * @author liuxiaobo
 */
class StatusAction extends CAction {

    public function run() {
		$res = array();
		$order_id = isset($_POST['order_id']) && is_numeric($_POST['order_id']) ?  $_POST['order_id'] : 0;

		//如果是请求状态
		if ($order_id) {

			$model = new OrderProcess();
			$res = $model->getOrderProcessesById($order_id); //629387\

        }

        $this->controller->render('status' , array(
            'order' => $res,
        ));
	}


	/**
	 * 获取url返回值，curl方法
	 * @param string $url	请求url地址
	 * @param int $timeout	超时时间
	 * @param string $userpwd	请求密码
	 * @param string $host_ip	指定ip
	 * @return string	请求结果
	 */
	public function getUrl($url, $timeout = 300, $userpwd = '', $host_ip = null) {
	    $ch = curl_init();
		if (!is_null($host_ip))
		{
			$urldata = parse_url($url);
			if (!empty($urldata['query']))
			{
				$urldata['path'] .= "?" . $urldata['query'];
			}
			$headers = array("Host: " . $urldata['host']);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$url = $urldata['scheme'] . "://" . $host_ip . $urldata['path'];
		}
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome/33.0.1750.146 Safari/537.36');
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	    if ($userpwd != '') {
	        curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
	    }
	    $ret = curl_exec($ch);
	    curl_close($ch);
	    return $ret;
	}


	/**
	 * 提交post请求，curl方法
	 *
	 * @param string $url	请求url地址
	 * @param array $post_fields	变量数组
	 * @param int $timeout	超时时间
	 * @param string $host_ip	指定ip
	 * @return string	请求结果
	 */
	public static function postUrl($url, $post_fields, $timeout = 3, $host_ip = null) {
	    $post_data = $post_fields;
	    $ch = curl_init();
		if (!is_null($host_ip))
		{
			$urldata = parse_url($url);
			if (!empty($urldata['query']))
			{
				$urldata['path'] .= "?" . $urldata['query'];
			}
			$headers = array("Host: " . $urldata['host']);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$url = $urldata['scheme'] . "://" . $host_ip . $urldata['path'];
		}
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Chrome/33.0.1750.146 Safari/537.36');
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	    $ret = curl_exec($ch);

	    curl_close($ch);
	    return $ret;
	}


}
