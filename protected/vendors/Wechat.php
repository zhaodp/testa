<?php

class Wechat
{
    private $url = 'http://wechat.edaijia.cn/driverapi/';
    private $url_test = 'http://wechat.d.edaijia.cn/driverapi/';


    private function getUrl(){
        $dir_name = dirname(dirname(__FILE__)).'/config/';
        $test_lock = $dir_name.'test.lock';
        $dev_lock = $dir_name.'dev.lock';
        if(file_exists($test_lock) || file_exists($dev_lock)){
            return $this->url_test;
        }   else{
            return $this->url;
        }
    }
    /**
     * 获取info
     *
     * @author duke 2015-04-02
     *
     * @param unknown_type $type
     * @param unknown_type $prefix
     * @return string
     */
    public  function getInfo($open_id)
    {
        $base_url = $this->getUrl();
        $url = $base_url.'getWechatUserInfo.do';
        $param = array('openId'=>$open_id,'sig'=>rand(10000,99999),'serviceId'=>'driver-wechat');
        $res = $this->get_contents($url,$param);

        return json_decode($res,1);
    }


    /**
     * 通过身份证 获取open_id
     *
     * @author duke 2015-04-02
     *
     * @param unknown_type $type
     * @param unknown_type $prefix
     * @return string
     */
    public  function getInfoByIdcard($id_card)
    {
        $base_url = $this->getUrl();
        $url = $base_url.'getWechatUserInfoByIdCardNum.do';
        $param = array('idCardNum'=>$id_card,'sig'=>rand(10000,99999),'serviceId'=>'driver-wechat');
        $res = $this->get_contents($url,$param);

        return json_decode($res,1);
    }


    /**
     * 服务器通过get请求获得内容
     * @author zhangtingyi
     * @param string $url       请求的url,拼接后的
     * @return string           请求返回的内容
     */
    private  function get_contents($url,$param){
        $param = "?" . http_build_query($param);
        $url = $url.$param;
        $res = $this->curl_get($url);
        return $res;
    }

    private function curl_get($url,$second = 60){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response =  curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    function encrypt($input,$key) {
        $size = mcrypt_get_block_size('des', 'ecb');
        $input = $this->pkcs5_pad($input, $size);

        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    function decrypt($encrypted) {
        $encrypted = base64_decode($encrypted);
        $key =$this->key;
        $td = mcrypt_module_open('des','','ecb','');
        //使用MCRYPT_DES算法,cbc模式
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        //初始处理
        $decrypted = mdecrypt_generic($td, $encrypted);
        //解密
        mcrypt_generic_deinit($td);
        //结束
        mcrypt_module_close($td);
        $y=$this->pkcs5_unpad($decrypted);
        return $y;
    }
    function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, -1 * $pad);
    }




}