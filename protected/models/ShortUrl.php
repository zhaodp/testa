<?php
class ShortUrl extends CRedis {
	public $host='redis01n.edaijia.cn';
    //public $host='test01.edaijia-inc.cn';
	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';


	protected static $_models=array();
    protected $short_url_key = 'SHORT_URL_KEY';
    protected $base_url = 'http://s.edaijia.cn/';

	public static function model($className=__CLASS__) {
		$model=null;
		if (isset(self::$_models[$className]))
			$model=self::$_models[$className];
		else {
			$model=self::$_models[$className]=new $className(null);
		}
		return $model;
	}

    // 根据长的url获取短的url
    // 使用该函数的预期行为是新创建，也就是说，认为长url之前应该没有创建过短url
    // 此时函数内，首先尝试插入db，如果不成功则会出现异常log，函数内部会进一步
    // 尝试获取之前是否创建过短url，如果获取到，则返回，否则失败
    public function short_url_hash($long_url)
    {
	    $key = 'edaijai';
	    $base32 = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

	    // 利用md5算法方式生成hash值
	    $hex = hash('md5', $long_url.$key);
	    $hexLen = strlen($hex);
	    $subHexLen = $hexLen / 8;

	    $output = array();
	    for( $i = 0; $i < $subHexLen; $i++ )
	    {
	        // 将这32位分成四份，每一份8个字符，将其视作16进制串与0x3fffffff(30位1)与操作
	        $subHex = substr($hex, $i*8, 8);
	        $idx = 0x3FFFFFFF & (1 * ('0x' . $subHex));

	        // 这30位分成6段, 每5个一组，算出其整数值，然后映射到我们准备的62个字符
	        $out = '';
	        for( $j = 0; $j < 6; $j++ )
	        {
	            $val = 0x0000003D & $idx;
	            $out .= $base32[$val];
	            $idx = $idx >> 5;
	        }
	        $output[$i] = $out;
	    }

	    $shorturl = $output[0];

        EdjLog::info("ShortUrl::short_url_hash gen shorturl s:$shorturl l:$long_url");

		$sql = 'INSERT INTO t_short_url
					(hash, url)
				VALUES
					(:hash, :url)';

        $command = Yii::app()->db->createCommand($sql);
        $command->bindParam(':hash', $shorturl);
        $command->bindParam(':url', $long_url);
        try {
            $res = $command->execute();
        } catch(Exception $e) {
            $errmsg = $e->getMessage();
            EdjLog::warning("ShortUrl::short_url_hash save err: $errmsg s:$shorturl l:$long_url");

            if ($this->long_url($shorturl) == $long_url) {
                return $shorturl;
            } else {
                EdjLog::warning("ShortUrl::short_url_hash get save error s:$shorturl l:$long_url");
                return false;
            }

		}


        $this->redis->hset($this->short_url_key, $shorturl, $long_url);

        return $shorturl;

    }

    public function short_url($long_url)
    {
        return $this->base_url.$this->short_url_hash($long_url);

    }


    public function long_url($short_url_hash)
    {
        // not found reload
        $res = $this->redis->hget($this->short_url_key, $short_url_hash);

        if (!$res) {
            try {
                $long_url = Yii::app()->db_readonly->createCommand()
                    ->select('url')
                    ->from('t_short_url')
                    ->where("hash = \"$short_url_hash\"")
                    ->queryRow();

                if ($long_url) {
                    $long_url = $long_url['url'];
                    $this->redis->hset($this->short_url_key, $short_url_hash, $long_url);
                    $res = $long_url;
                } else {
                    $res = false;
                }

            } catch(Exception $e) {
                $errmsg = $e->getMessage();
                EdjLog::warning("ShortUrl::long_url read err: $errmsg s:$short_url_hash");
                $res = false;
            }

        }

        return $res;

    }


}
