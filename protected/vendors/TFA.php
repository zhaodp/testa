<?php

class TFA
{
    private $key = '6MvIfktcaa1';
    //http://open.api.edaijia.cn/
    private $url = 'http://open.api.edaijia-inc.cn/inner/auth/';

    /**
     * 生成$uniq_id
     *
     * @author sunhongjing  2013-12-30
     *
     * @param unknown_type $type
     * @param unknown_type $prefix
     * @return string
     */
    public  function getKey($email,$user_name,$password)
    {
        $url = $this->url.'sendmailsecret';
        $param = array('toMailAddress'=>$email,'userName'=>$user_name,'password'=>$password);
        $res = $this->get_contents($url,$param);
        return json_decode($res,1);
    }


    public function checkCode($code,$key){
        //{"code":"284099","key":"FUFo5R2RPjawkkESQBqYODELqr6y5ODU"}
        $url = $this->url.'checkgauthcode';
        $param = array('code'=>$code,'key'=>$key);
        //print_r($param);die;
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
        $data = $this->encrypt(json_encode($param),$this->key);
        $url = $url.'?data='.urlencode($data);
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
        //$a = curl_getinfo($ch);
        curl_close($ch);
        //print_r($a);
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
