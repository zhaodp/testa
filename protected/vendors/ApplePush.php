<?php

 class ApplePush {
    private $socketConnet=null;
    /**
     * get connect...
     *
     */
    public function getSockConnet($isTest=false){
        $test_lock = dirname(dirname(dirname(__FILE__))).'/protected/config'.'/test.lock';
        if (is_file($test_lock)) {
            $isTest = true;
        }
        $ctx = stream_context_create();
        $pem = dirname(dirname(dirname(__FILE__))).'/config/cert/'.'ck.pem';
        if($isTest){
            $pem = dirname(dirname(dirname(__FILE__))).'/config/cert/'.'ck-enterprise.pem';
        }
        stream_context_set_option($ctx, "ssl", "local_cert", $pem);
        /**
         * 测试地址
         */
        // if($isTest){
        //     echo 'use apple test enviroment';
        //     $this->socketConnet = stream_socket_client("ssl://gateway.sandbox.push.apple.com:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        // }else{
        //测试环境使用企业证书，但是socket也是线上
            $this->socketConnet = stream_socket_client("ssl://gateway.push.apple.com:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        // }
    }

    /**
     * 推送消息
     *
     */
    public function push($deviceToken,$body){
        // $deviceToken="e78d47a44ed62a670a64ce7f6bef0e6423684abb85017e78a73973cfc42071bf";
        // echo 'deviceToken='.$deviceToken.PHP_EOL;
        echo 'body=';
        print_r($body);
        echo PHP_EOL;
        $result = '4';
        $deviceToken = str_replace('<','',$deviceToken);
        $deviceToken = str_replace('>','',$deviceToken);
        $deviceToken = str_replace(' ','',$deviceToken);
        // reconnect
        $count = 0;
        while(!$this->socketConnet && $count < 3) {
            $result = '3';
            $this->getSockConnet();
            $count ++;
            echo "conneted failed,start to reconnect :[".$count."]time";
        }
        //print "Connection OK\n";
        $msg = chr(0) . pack("n",32) . pack("H*",$deviceToken) . pack("n",strlen($body)) . $body;
        //print "sending message :" . $content . "<Br>";
        $fwrite = fwrite($this->socketConnet, $msg);
        if($fwrite){
            $result = '1';
            print "sending ok!".PHP_EOL;
        }else{
            print "fwrite failed".PHP_EOL;
            $result = '2';
        }
        return $result;
    }

    /**
    *
    *get feedback
    *
    **/
    function send_feedback_request() {
        //connect to the APNS feedback servers
        //make sure you're using the right dev/production server & cert combo!
        $pem = dirname(dirname(dirname(__FILE__))).'/config/cert/'.'ck.pem';
        $stream_context = stream_context_create();
        stream_context_set_option($stream_context, 'ssl', 'local_cert', $pem);
        $apns = stream_socket_client('ssl://feedback.push.apple.com:2196', $errcode, $errstr, 60, STREAM_CLIENT_CONNECT, $stream_context);
        if(!$apns) {
            echo "ERROR $errcode: $errstr\n";
            return;
        }


        $feedback_tokens = array();
        //and read the data on the connection:
        while(!feof($apns)) {
            $data = fread($apns, 38);
            if(strlen($data)) {
                $feedback_tokens[] = unpack("N1timestamp/n1length/H*devtoken", $data);
            }
        }
        fclose($apns);
        return $feedback_tokens;
    }


    /**
    *
    *close connection
    */
    function closeConnections(){
        if($this->socketConnet){
            fclose($this->socketConnet);
        }
    }

}