<?php

class ApplePushEx
{
    const COMMAND_PUSH = 1;
    const DEVICE_BINARY_SIZE = 32; 
    const CONNECT_RETRY_TIME = 3;
    const CONNECT_RETRY_INTERVAL = 1000000;
    // 200000 出自PHP文档 http://php.net/manual/en/function.stream-select.php
    const STREAM_SELECT_INTERVAL = 200000;

    const STREAM_SELECT_ERROR = 100;
    const SEND_ERROR = 101;
    const RECV_ERROR = 102;
    const CONNECTION_CLOSED = 103;

    private $stream_socket=null;

    function __destruct() 
    {
        EdjLog::info("close connection in destructor", 'console');
        $this->close();
    }

    function __construct()
    {
        $this->verboseConnect();
    }

    private function verboseConnect()
    {
        $connected = false;
        for ($i = 0; $i < self::CONNECT_RETRY_TIME; $i++) {
            if ($this->connect()) {
                $connected = true;
                EdjLog::info("successful connected to APNS", 'console');
                break;
            }
            usleep(self::CONNECT_RETRY_INTERVAL);
        }

        // 其实这里应该抛出一个异常，简单返回一个错误不够——曾坤 2015/4/16
        if ($connected === false) {
            EdjLog::info("failed to connect to APNS for ".self::CONNECT_RETRY_TIME." times", 'console');
            return false;
        } else {
            return true;
        }
    }

    private function connect()
    {
        $test_lock = dirname(dirname(dirname(__FILE__))).'/protected/config'.'/test.lock';
        if (is_file($test_lock)) {
            $pem = dirname(dirname(dirname(__FILE__))).'/config/cert/'.'ck-enterprise.pem';
        } else {
            $pem = dirname(dirname(dirname(__FILE__))).'/config/cert/'.'ck.pem';
        }

        $ctx = stream_context_create();
        stream_context_set_option($ctx, "ssl", "local_cert", $pem);
        $this->stream_socket = @stream_socket_client("ssl://gateway.push.apple.com:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        return is_resource($this->stream_socket);
    }

    private function checkToken($token)
    {
        return preg_match('~^[a-f0-9]{64}$~i', $token) === 1;
    }

    private function checkPayload($payload)
    {
        return json_encode($payload) !== false;
    }

    private function checkAppleMessage($token, $payload)
    {
        return $this->checkToken($token) && $this->checkPayload($payload);
    }

    private function prepareAppleMessage($token, $payload, $message_id, $expire = 864000)
    {
        if ($this->checkAppleMessage($token, $payload) === false) {
            return false;
        }

        $message  = pack('CNNnH*', self::COMMAND_PUSH, $message_id, time() + $expire, self::DEVICE_BINARY_SIZE, $token);
        $message .= pack('n', strlen($payload));
        $message .= $payload;

        return $message;
    }

    private function send($message)
    {
        $write_bytes = @fwrite($this->stream_socket, $message);
        $expected_bytes = strlen($message);
        if ($expected_bytes == $write_bytes) {
            return true;
        } else {
            EdjLog::info("send error! only $write_bytes/$expected_bytes send", 'console');
            return self::SEND_ERROR;
        }
    }

    private function recv()
    {
        $str = @fread($this->stream_socket, 6);
        if (strlen($str) == 0) {
            return self::CONNECTION_CLOSED;
        } elseif (strlen($str) == 6) {
            return unpack('Ccommand/CstatusCode/Nidentifier', $str);
        } else {
            return self::RECV_ERROR;
        }
    }

    public function push($token, $payload, $message_id)
    {
        $message = $this->prepareAppleMessage($token, $payload, $message_id);
        if ($message === false) {
            EdjLog::info("invalid Apple Push msg, token:$token, payload:$payload", 'console');
            return false;
        }

        if (!is_resource($this->stream_socket)) {
            EdjLog::info("connection lost, reconnect when push", 'console');
            $this->verboseConnect();
        }

        // 每次发送之前，先检查一下连接上有没有发生错误——曾坤 2015/4/16
        $last_error = $this->getLastError();
        if ($last_error !== null) {
            EdjLog::info("getLastError returned ".json_encode($last_error).' close connection now', 'console');
            $this->close();
            return $last_error;
        }

        if (($send_result = $this->send($message)) !== true) {
            EdjLog::info("send failed, close connection now", 'console');
            $this->close();
            return $send_result;
        }

        return true;
    }

    public function getLastError()
    {
        $read = array($this->stream_socket);
        $write = null;
        $except = null;
        $last_error = null;

        $select = stream_select($read, $write, $except, 0, self::STREAM_SELECT_INTERVAL);
        if ($select > 0 && in_array($this->stream_socket, $read)) {
            $last_error = $this->recv();
        }

        return $last_error;
    }

    public function sendFeedbackRequest() 
    {
        //connect to the APNS feedback servers
        //make sure you're using the right dev/production server & cert combo!
        $test_lock = dirname(dirname(dirname(__FILE__))).'/protected/config'.'/test.lock';
        if (is_file($test_lock)) {
            $pem = dirname(dirname(dirname(__FILE__))).'/config/cert/'.'ck-enterprise.pem';
        } else {
            $pem = dirname(dirname(dirname(__FILE__))).'/config/cert/'.'ck.pem';
        }
        
        $stream_context = stream_context_create();
        stream_context_set_option($stream_context, 'ssl', 'local_cert', $pem);
        $apns = stream_socket_client('ssl://feedback.push.apple.com:2196', $errcode, $errstr, 60, STREAM_CLIENT_CONNECT, $stream_context);
        if(!$apns) {
            EdjLog::info("ERROR $errcode: $errstr\n", 'console');
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

    private function close()
    {
        if (is_resource($this->stream_socket)) {
            fclose($this->stream_socket);
            $this->stream_socket = null;
        }
    }
}

