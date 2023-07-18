<?php
/**
 * 封装又拍云SDK
 * User: zhanglimin
 * Date: 13-8-5
 * Time: 下午4:09
 */

Yii::import('application.vendors.upyun.*');
Yii::import('application.config.*');
require_once ("upyun.class.php");
require_once ("config_upyun.php");
class UpyunUpload {

    protected static $_models;
    private $_bucketname;
    private $_up_user;
    private $_up_password;
    private $_up_method;
    private $_up_host;
    private $_up_path;
    private $_up_base_url;
    private $_upyun;

    public static function model($from = "audio" , $className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className($from);
        }
        return $model;
    }

    public function __construct($from = "audio"){

        //获取配置信息
        $config=config_upyun::get_config_params($from);

        $this->_bucketname = $config['bucketname'];

        $this->_up_user = $config['up_user'];

        $this->_up_password = $config['up_password'];

        $this->_up_method = $config['up_method'];

        $this->_up_host = $config['up_host'];

        $this->_up_path = $config['up_path'];

        $this->_up_base_url = $config['up_base_url'];

        $this->_upyun = new UpYun($this->_bucketname , $this->_up_user , $this->_up_password);

    }

    /**
     * 返回URL信息
     * @param $md5_file
     * @param $filesize
     * @param $type
     */
    public function getUrlInfo($md5_file , $filesize , $type ="audio/amr" , $from="audio"){
        $ret = array(
            'flag' => false,
            'msg' => "",
            'data'=> array(),
        );
        $checkFile = $this->_checkFileSize($filesize);
        if(!$checkFile){
            $ret['msg'] = "文件超大";
            return $ret;
        }

        $filename = $this->_getFileName();
        $headers = $this->_setHeaders($md5_file, $filename ,$filesize ,$type);
        return array(
            'flag'=>true,
            'msg'=>"ok",
            'data'=>array(
                'url'=> $this->_up_base_url.$filename,
                'path'=> $this->_up_path.$filename,
                'host'=> $this->_up_host,
                'method' => $this->_up_method,
                'headers'=>$headers,
            ),
        );
    }

    /**
     * 设置header头
     * param  $md5_file
     * @param $filename
     * @param $filesize
     * @param $type
     * @return array
     */
    private function _setHeaders($md5_file, $filename , $filesize ,$type){

        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $headers = array(
            'Date' => $date,
            'Content-Type'=>$type,
            'Content-MD5'=>$md5_file,
            'Authorization'=>$this->_sign($this->_up_method , $this->_up_path.$filename , $date ,$filesize),
            'Expect'=>'',
        );

        return $headers;
    }

    /**
     * 连接签名方法
     * @param $method 请求方式 {GET, POST, PUT, DELETE}
     * return 签名字符串
     */
    private function _sign($method, $uri, $date, $length){
        $password = md5($this->_up_password);
        $sign = "{$method}&{$uri}&{$date}&{$length}&{$password}";
        return 'UpYun '.$this->_up_user.':'.md5($sign);
    }


    /**
     * 返回文件名
     * @return string
     */
    private function _getFileName(){
        $id = time() . '-' . md5(rand(1,999));
        $id = md5($id);
        return $id;
    }

    /**
     * 验证文件大小
     * @param int $fileSize
     * @return bool
     */
    private function _checkFileSize($fileSize = 0){
        $flag = true;
        $setFileSize = 1024*1024*5;
        if($fileSize  > $setFileSize){
            $flag = false;
        }
        return $flag;
    }

    public function driverPicUpload($city_id, $driver_id, $pic_address) {
        $dir = '/'.$city_id.'/'.$driver_id.'.jpg';
        $pic = fopen($pic_address, 'r');
        if ($pic) {
            $dir_list = $this->getDirList();
            if (!in_array($city_id, $dir_list)) {
                $this->_upyun->makeDir('/'.$city_id);
            }
            $result = $this->_upyun->writeFile($dir, $pic);
            fclose($pic);
            return $result;
        } else {
            return false;
        }
    }

    public function uploadFile($file, $folder, $file_name=null, $bucketname='edriver') {
        if (file_exists($file)) {
            $path = '/'.$folder.'/';
            $new_file_name = $file_name ? $path.$file_name : $path.self::getFileName($file);
            $pic = fopen($file, 'r');
            if ($pic) {
                $dir_list = $this->getDirList();
                if (!in_array($folder, $dir_list)) {
                    $this->_upyun->makeDir('/'.$folder);
                }
                $result = $this->_upyun->writeFile($new_file_name, $pic);
                fclose($pic);
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
    *   aiguoxin
    *   上传司机debug日志使用，自动创建父目录
    */
    public function uploadFileForDebugLog($file, $folder, $file_name=null, $bucketname='edriver') {
        if (file_exists($file)) {
            $path = '/'.$folder.'/';
            $new_file_name = $file_name ? $path.$file_name : $path.self::getFileName($file);
            $pic = fopen($file, 'r');
            if ($pic) {
                $dir_list = $this->getDirList();
                if (!in_array($folder, $dir_list)) {
                    $this->_upyun->makeDir('/'.$folder,true);
                }
                $result = $this->_upyun->writeFile($new_file_name, $pic);
                fclose($pic);
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getFileName($file) {
        $file_info = pathinfo($file);
        return $file_info['basename'];
    }

    public function getDirList(){
        $data = $this->_upyun->getList();
        $dir_list = array();
        if (is_array($data) && count($data)) {
            foreach ($data as $v) {
                if ($v['type'] == 'folder') {
                    $dir_list[] = $v['name'];
                }
            }
        }
        return $dir_list;
    }

    public function updateDriverPicCache($driver_id, $city_id) {
        $status = true;
        $size_list = array(
            Driver::PICTURE_SMALL,
            Driver::PICTURE_NORMAL,
            Driver::PICTURE_MIDDLE,
            Driver::PICTURE_BOX,
            Driver::PICTURE_SBOX,
            Driver::PICTURE_SMALLA,
        );
        foreach($size_list as $size) {
            $purge = Driver::getPictureUrl($driver_id, $city_id, $size);
            $status = $status && $this->updateCache($purge);
        }
        return $status;
    }

    public function updateCache($purge) {
        $config=config_upyun::get_config_params('edriver');
        $user = $config['up_user'];
        $password = $config['up_password'];
        $bucket = $config['bucketname'];
        $date = gmdate('D, d M Y H:i:s \G\M\T'); // 取得世界时间
        $sign = md5($purge."&".$bucket."&".$date."&".md5($password)); // 计算签名
        $process = curl_init('http://purge.upyun.com/purge/');

        // 设置表头参数
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_HTTPHEADER, array("Expect:",
            "Authorization: UpYun {$bucket}:{$user}:{$sign}", "DATE:{$date}"));

        curl_setopt($process, CURLOPT_POSTFIELDS, "purge=".urlencode($purge));
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);

        $content = curl_exec($process);
        $ret_code = curl_getinfo($process, CURLINFO_HTTP_CODE);

        curl_close($process);
        if($ret_code != 200)
            return var_export($content, 1);
        else
            return var_export(json_decode($content), 1);

    }

    /**
     *  后台上传upyun图片 wanglonghuan 2013.11.05
     * @param string $pic_cat=knowledge 目录类型 /edaijia/knowledge/
     * @param $pic_name 图片名称
     * @param $pic_address 图片地址
     * @param null $opts
     * @return bool|mixed
     */
    public function edaijiaPicUpload($pic_cat, $pic_name, $pic_address, $opts = NULL) {
        $dir = '/'.$pic_cat .  '/' . $pic_name . '.jpg';
        $pic = fopen($pic_address, 'r');
        if ($pic) {
            $dir_list = $this->getDirList();
            if (!in_array($pic_cat, $dir_list)) {
                $this->_upyun->makeDir('/'.$pic_cat);
            }
            $result = $this->_upyun->writeFile($dir, $pic, false ,$opts);
            fclose($pic);
            return $result;
        } else {
            return false;
        }
    }

    /*
     * 上传静态页面 长文章 公告 知识库 目录规则  /$cat/日期/abc.html
     * @param string $cat=knowledge 目录类型 /edaijia/knowledge/
     * @param $type 文件目录名称 /notice /knowledge
     * @param $name html名称
     * @param $path html地址
     * @return bool|mixed
     */
    public function edaijiaHtmlUpload($cat,$day, $name, $path, $opts = NULL)
    {
        $dir = '/' . $cat . '/' . $day . '/' . $name . '.html';
        $file = fopen($path, 'r');
        if ($file) {
            $dir_list = $this->getDirList();
            if (!in_array($cat, $dir_list)) {
                $this->_upyun->makeDir('/'.$cat);
            }
            $day_list = $this->_upyun->getList('/'.$cat);
            if (!in_array($day, $day_list)) {
                $this->_upyun->makeDir('/' . $cat . '/'.$day);
            }
            $result = $this->_upyun->writeFile($dir, $file, false ,$opts);
            fclose($file);
            return $result;
        } else {
            return false;
        }
    }

}
