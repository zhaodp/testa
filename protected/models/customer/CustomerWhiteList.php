<?php
/**
 * 用户白名单
 * User: WangJian
 * Date: 2014-07-04
 * Time: 上午10:30
 */

class CustomerWhiteList extends CRedis {

    public $host = 'redis01n.edaijia.cn'; //10.132.17.218
    public $port = 6379;
    public $password = 'k74FkBwb7252FsbNk2M7';
    protected static $_models = array();

    private $set_key = 'CUSTOMER_WHITE_LIST';

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    public function in_whitelist($phone) {
        return $this->redis->hexists($this->set_key, $phone);
    }

    public function cache_list() {
        if(!$this->redis->exists($this->set_key)) {
            $this->reload();
	}

        $whitelist = $this->redis->hkeys($this->set_key);
	if(isset($whitelist) && !empty($whitelist)) {
            return $whitelist;
        }
	else {
	    return array();
        }
    }

    public function mysql_list($phone='') {
        $sql = "SELECT id,phone,user_id,created,remarks FROM t_customer_whitelist";
        if (!empty($phone)) {
            $sql .= " WHERE phone = :phone";
        }
        $sql .= " ORDER BY id DESC";
        $command = Yii::app()->db_readonly->createCommand($sql);
        if (!empty($phone)) {
            $command->bindParam(":phone" , $phone);
        }
        $result = $command->queryAll();
        $dataProvider = new CArrayDataProvider($result, array (
                'id'         => 'whitelist',
                'keyField'   => 'phone',
                'pagination' => array('pageSize' => 50),
            )
        );
        return $dataProvider;
    }

    public function add($data) {
        $sql = "INSERT INTO t_customer_whitelist(`phone`,`user_id`,`created`,`remarks`) VALUES(:phone,:user_id,:created,:remarks)";
        $time = date("Y-m-d H:i:s" , time());
        $count = count($data['phones']);

	$in_black_list = array();

        $i=0;
        for($i=0; $i<$count; $i++) {
            $phone = isset($data['phones'][$i]) ? $data['phones'][$i] : '';
            $phone = trim($phone);
            if(!empty($phone)){
	        if(CustomerStatus::model()->is_black($phone)) {
		    $in_black_list[] = $phone;
		    continue;
		}

                $remarks=$data['remarks'][$i];
                $sql_check = "SELECT id FROM t_customer_whitelist WHERE phone='".$phone."'";
                $result = Yii::app()->db_readonly->createCommand($sql_check)->queryRow();
                if(empty($result)) {
                    $command = Yii::app()->db->createCommand($sql);
                    $command->bindParam(":phone" , $phone);
                    $command->bindParam(":user_id" , $data['user_id']);
                    $command->bindParam(":created" , $time);
                    $command->bindParam(":remarks" , $remarks);
                    $command->execute();
                    $command->reset();
                }
            }
        }
        //reload cache
        $this->reload();

	return $in_black_list;
    }

    public function remove($phone) {
        if(!empty($phone)) {
            $sql = "DELETE FROM t_customer_whitelist WHERE phone = :phone";
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":phone" , $phone);
            $command->execute();

            // 删除时暂时不做reload操作
            $this->redis->hdel($this->set_key, $phone);
        }
    }

    public function reload() {
        $sql = "SELECT phone FROM t_customer_whitelist";
        //避免主从不同步,调用频率低,直接主库查询
        $command = Yii::app()->db->createCommand($sql);
        $result = $command->queryAll();
        if(!empty($result)) {
            $this->redis->del($this->set_key);
            foreach ($result as $val) {
                $this->redis->hset($this->set_key, $val['phone'], 1);
            }
        }
    }
}
