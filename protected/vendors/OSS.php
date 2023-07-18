<?php
/**
 * 
 * Aliyun OSS接口
 * @author dayuer
 *
 */

Yii::import('application.vendors.ALIOSS.*');
require_once 'sdk.class.php';

class OSS {
	public $bucket;
	public $oss_sdk_service;
	
	public function __construct($bucket) {
		$this->bucket = $bucket;
		$this->oss_sdk_service = new ALIOSS();
		
		//设置是否打开curl调试模式
		$this->oss_sdk_service->set_debug_mode(false);
	}
	
	/**
	 * 
	 * 获取object列表
	 */
	public function list_object() {
		$options = array (
			'delimiter'=>'', 
			'prefix'=>'', 
			'max-keys'=>100); //'marker' => 'myobject-1330850469.pdf',
		

		$response = $this->oss_sdk_service->list_object($this->bucket, $options);
		self::_format($response);
	}
	
	/**
	 * 创建目录
	 * @param string $dir
	 */
	function create_directory($dir) {
		$response = $this->oss_sdk_service->create_object_dir($this->bucket, $dir);
		self::_format($response);
	}
	
	/**
	 * 
	 * 上传文件
	 * @param string object OSS的文件存放路径
	 * @param string filepath 文件路径
	 */
	function upload_by_file($object,$filepath) {
		//$object = '1/BJ0760/1.jpg';
		//$filepath = "/tmp/1.jpg";
		
		$response = $this->oss_sdk_service->upload_file_by_file($this->bucket, $object, $filepath);
		self::_format($response);
	}
	
	//格式化返回结果
	private function _format($response) {
		echo '|-----------------------Start---------------------------------------------------------------------------------------------------'."\n";
		echo '|-Status:'.$response->status."\n";
		echo '|-Body:'."\n";
		echo $response->body."\n";
		echo "|-Header:\n";
		print_r($response->header);
		echo '-----------------------End-----------------------------------------------------------------------------------------------------'."\n\n";
	}
}