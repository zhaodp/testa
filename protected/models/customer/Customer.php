<?php

/**
 * This is the model class for table "{{customer}}".
 *
 * The followings are the available columns in table '{{customer}}':
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $comments
 * @property string $insert_time
 */
class Customer extends CActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Customer the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{customer}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array (
            array (
                'name, phone',
                'required'),
            array (
                'name, phone',
                'length',
                'max'=>20),
            array (
                'comments',
                'length',
                'max'=>1024),
            array (
                'insert_time',
                'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array (
                'id, name, phone, comments, insert_time',
                'safe',
                'on'=>'search'));
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array (
            'cars'=>array (
                self::HAS_MANY,
                'CustomerCar',
                'user_id'));
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array (
            'id'=>'ID',
            'name'=>'Name',
            'phone'=>'Phone',
            'comments'=>'Comments',
            'insert_time'=>'Insert Time');
    }

    /**
     *
     * 根据电话号码查找是否有此客户
     * @param string $phone
     */
    public static function getCustomer($phone) {
        $criteria = new CDbCriteria(array (
            'condition'=>'phone=:phone',
            'params'=>array (
                ':phone'=>$phone)));
        //CustomerMain::$db = Yii::app ()->db_readonly;
        return CustomerMain::model()->find($criteria);
    }

    /**
     * 根据电话号码返回客户ID
     * @param string $phone
     */
    public static function getId($phone) {
        $criteria = new CDbCriteria(array (
            'condition'=>'phone=:phone',
            'params'=>array (
                ':phone'=>$phone)));

        $customer = self::model()->find($criteria);
        if($customer){
            return $customer->id;
        }
    }

    public function afterSave() {
        parent::afterSave();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('comments', $this->comments, true);
        $criteria->compare('insert_time', $this->insert_time, true);

        return new CActiveDataProvider($this, array (
            'criteria'=>$criteria));
    }

    private function curlTransmission($url, $postData, $connection, $table, $keyvalue) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($ch);
        curl_close($ch);
        $return = json_decode($output);
        print_r($output);
        print_r($postData);
        die();
        if (isset($return->error_response->code)) {
            $sql = "INSERT INTO t_transmission_error(`table`, `keyvalue`, `error` ) VALUES (:table, :keyvalue, :error)";
            $command = $connection->createCommand($sql);
            $command->bindParam(":table", $table);
            $command->bindParam(":keyvalue", $keyvalue);
            $command->bindParam(":error", $return->code);
            $command->execute();
            $command->reset();
        }
    }

    /**
     * 新增黑名单电话号
     * @param array $data
     * @return boolean
     * @author AndyCong<congming@edaijia.cn> 2013-04-17
     * @editor AndyCong<congming@edaijia.cn> 2013-05-14
     */
    public function insertBlackCustomer($data) {
        $sql = "INSERT INTO t_customer_blacklist(`phone` , `user_id` , `expire_time` , `created`, `remarks`) VALUES(:phone , :user_id , :expire_time , :created,:remarks)";
        $time = date("Y-m-d H:i:s" , time());
//        print_r($data);exit;
        $arr = array();
        $count=count($data['phones']);

	$in_whitelist = array();

        $i=0;
        for($i=0;$i<$count;$i++) {
            $phone=isset($data['phones'][$i])?$data['phones'][$i]:'';
            $phone=trim($phone);
            if(!empty($phone)){
                $remarks     = $data['remarks'][$i];
		$expire_time = $data['expire_times'][$i];
		if($expire_time == '1'){
		    $ex = date('Y-m-d H:i:s', strtotime('7 days'));
		}else if($expire_time == '2'){
		    $ex = date('Y-m-d H:i:s', strtotime('1 months'));
		}else if($expire_time == '3'){
                    $ex = date('Y-m-d H:i:s', strtotime('3 months'));
                }else if($expire_time == '4'){
                    $ex = date('Y-m-d H:i:s', strtotime('6 months'));
                }else if($expire_time == '5'){
                    $ex = date('Y-m-d H:i:s', strtotime('1 years'));
                }else if($expire_time == '6'){
                    $ex = date('Y-m-d H:i:s', strtotime('3 years'));
                }else if($expire_time == '7'){
                    $ex = date('Y-m-d H:i:s', strtotime('5 minute'));
                }

	        if(CustomerWhiteList::model()->in_whitelist($phone)) {
                    $in_whitelist[] = $phone;
		    continue;
                }

                $sql_check = "SELECT id FROM t_customer_blacklist WHERE phone='".$phone."'";
                $result = Yii::app()->db_readonly->createCommand($sql_check)->queryRow();
                if (empty($result) && !empty($phone)) {
                    $arr[] = $phone;
                    $command = Yii::app()->db->createCommand($sql);
                    $command->bindParam(":phone" , $phone);
                    $command->bindParam(":user_id" , $data['user_id']);
		    $command->bindParam(":expire_time" , $ex);
                    $command->bindParam(":created" , $time);
                    $command->bindParam(":remarks" , $remarks);
                    $command->execute();
                    EdjLog::info("phone=".$phone.',add db ok');
                    $command->reset();
                    // update cache
                    CustomerStatus::model()->add_black($phone);
                    EdjLog::info("phone=".$phone.',add redis ok');
                }
            }
        }

        return $in_whitelist ;
    }

    /**
     * 获取黑名单列表
     * @param string $phone
     * @return object $dataProvider
     * @author AndyCong<congming@edaijia.cn> 2013-04-17
     */
    public function getBlackCustomerList($phone = '') {
        $sql = "SELECT * FROM t_customer_blacklist WHERE 1=1";
        if (!empty($phone)) {
            $sql .= " AND phone = :phone";
        }
        $sql .= " ORDER BY id DESC";
        $command = Yii::app()->db_readonly->createCommand($sql);
        if (!empty($phone)) {
            $command->bindParam(":phone" , $phone);
        }
        $result = $command->queryAll();
        $dataProvider = new CArrayDataProvider($result, array (
            'id'=>'blacklist',
            'keyField'=>'phone',
            'pagination'=>array (
                'pageSize'=>50)
            )
        );
        return $dataProvider;
    }

    /**
     * 删除黑名单记录
     * @param intval $id
     * @return boolean
     * @author AndyCong<congming@edaijia.cn> 2013-04-17
     * @editor AndyCong<congming@edaijia.cn> 2013-05-14
     */
    public function delBlackCustomer($id = 0) {
        if ($id == 0) {
            return false;
        }
        $info = Yii::app()->db_readonly->createCommand()
                ->select('phone')
                ->from('t_customer_blacklist')
                ->where('id = :id', array(
                        ':id'=>$id,
                ))->queryRow();
        if (!empty($info)) {
            //添加task队列推送司机黑名单
            //zhangtongkai 2014-4-2
            $data = array('drivers'=>'','phone' => $info['phone'], 'city_id'=>1,'mark' => 0);
            $task = array(
                'method' => 'customer_blacklist_push',
                'params' => $data,
            );
            //Queue::model()->putin($task, 'customer_blacklist');
            //$result = PushMessage::push_list('' , $info['phone'] , 1 , 0);
            $result=true;
            if ($result) {
                $sql = "DELETE FROM t_customer_blacklist WHERE id = :id";
                $command = Yii::app()->db->createCommand($sql);
                $command->bindParam(":id" , $id);
                $command->execute();

                CustomerStatus::model()->rm_black($info['phone']);
            }
        }
        return true;
    }
   /**
    *删除超过屏蔽时间的黑名单
    *@author clz 20140909
   **/
    public function delExpireTimeBlackCustomer() {
	$current_time = date('Y-m-d H:i:s',time());
        $infos = Yii::app()->db_readonly->createCommand()
                ->select('id,phone')
                ->from('t_customer_blacklist')
                ->where('expire_time <= :expire_time', array(
                        ':expire_time'=>$current_time,
                ))->query();

        if (!empty($infos)) {
	   foreach($infos as $info){
		    echo $info['phone'];echo "<br/>";
                    $sql = "DELETE FROM t_customer_blacklist WHERE id = :id";
                    $command = Yii::app()->db->createCommand($sql);
                    $command->bindParam(":id" , $info['id']);
                    $command->execute();
                    CustomerStatus::model()->rm_black($info['phone']);
	    }
        }
    }
    /**
     * 获取黑名单信息
     * @return string
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-02
     */
    public function getBlackCustomerInfo() {
        //添加黑名单，就会添加到redis中 aiguoxin 2014-10-13
        $blackArray = CustomerStatus::model()->getBlackList();
        if (!empty($blackArray)) {
            return implode(",",$blackArray);
        } else {
            $sql = "SELECT phone FROM t_customer_blacklist";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $result = $command->queryAll();
            $str_phone = '';
            foreach ($result as $val) {
                $str_phone .= $val['phone'].",";
                //加载到缓存
                CustomerStatus::model()->add_black($val['phone']);
            }
            $str_phone = substr($str_phone , 0 , strlen($str_phone)-1);
            return $str_phone;
        }
    }

    /**
     * 通过电话号获取黑名单
     * @param string $phone
     * @return array
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-08-12
     */
    public function getBlackByPhone($phone = '') {
        if (empty($phone)) {
            return '';
        }
        $result = Yii::app()->db->createCommand()
                     ->select('*')
                     ->from('t_customer_blacklist')
                     ->where('phone = :phone' , array(':phone' => $phone))
                     ->queryRow();
        return $result;
    }


    /**
     * 删除手动加入的某个城市的司机黑名单
     * @param $city_id
     */
    public function delCityDriver($city_id)
    {
        $remarks = 'city_id=' . $city_id;
        $infos = Yii::app()->db_readonly->createCommand()
            ->select('id,phone')
            ->from('t_customer_blacklist')
            ->where('remarks =:remarks', array(':remarks' => $remarks))->query();

        if (!empty($infos)) {
            foreach ($infos as $info) {
                EdjLog::info('开始移除city_id=' . $city_id . ',phone=' . $info['phone'] . '的手机');
                echo '开始移除city_id=' . $city_id . ',phone=' . $info['phone'] . '的手机'.PHP_EOL;
                $sql = "DELETE FROM t_customer_blacklist WHERE id = :id";
                $command = Yii::app()->db->createCommand($sql);
                $command->bindParam(":id", $info['id']);
                $command->execute();
                CustomerStatus::model()->rm_black($info['phone']);
                EdjLog::info('已经将city_id=' . $city_id . ',phone=' . $info['phone'] . '的手机移除黑名单');
                echo '已经将city_id=' . $city_id . ',phone=' . $info['phone'] . '的手机移除黑名单'.PHP_EOL;
            }
        }
    }

    /**
     * 将司机加入黑名单
     * @param $data
     */
    public function insertDriverToBlackCustomer($data)
    {

        $phone = $data['phone'];
        if (CustomerWhiteList::model()->in_whitelist($phone)) {
            EdjLog::info('phone=' . $phone . '在白名单');
            echo 'phone=' . $phone . '在白名单' . PHP_EOL;
            return;
        }

        $sql = "INSERT INTO t_customer_blacklist(`phone` , `user_id` , `expire_time` , `created`, `status`, `remarks`) VALUES(:phone , :user_id , :expire_time , :created, :status,:remarks)";
        $time = date("Y-m-d H:i:s", time());
        $sql_check = "SELECT id FROM t_customer_blacklist WHERE phone='" . $phone . "'";
        $result = Yii::app()->db_readonly->createCommand($sql_check)->queryRow();
        $status = 1;
        if (empty($result) && !empty($phone)) {
            $command = Yii::app()->db->createCommand($sql);
            $command->bindParam(":phone", $phone);
            $command->bindParam(":user_id", $data['user_id']);
            $command->bindParam(":expire_time", $data['expire_time']);
            $command->bindParam(":created", $time);
            $command->bindParam(":status", $status);
            $command->bindParam(":remarks", $data['remarks']);
            $command->execute();
            EdjLog::info("phone=" . $phone . ',add db ok');
            echo "phone=" . $phone . ',add db ok'.PHP_EOL;
            $command->reset();
            CustomerStatus::model()->add_black($phone);
            EdjLog::info("phone=" . $phone . ',add redis ok');
            echo "phone=" . $phone . ',add redis ok'.PHP_EOL;
        }

    }
}
