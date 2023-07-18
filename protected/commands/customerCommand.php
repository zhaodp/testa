<?php
Yii::import('application.models.redis.*');
class CustomerCommand extends CConsoleCommand {
    /**
     * 通过订单表反查用户的城市(临时倒数据，仅执行一次)
     * author zhangtingyi
     */
	public function actionSetCustomerCity() {
		$sql = "SELECT id,phone FROM t_customer_main WHERE city_id is null OR city_id = 0";
		$command = Yii::app ()->db_readonly->createCommand ($sql);
		$all_user = $command->queryAll();
		if (is_array($all_user) && count($all_user)) {
			foreach ($all_user as $val) {
				$phone = $val['phone'];
				$customer_id = $val['id'];
                                $city_id = Order::model()->getCityByPhone($phone);
				if ($city_id>0) {
					$customer_model = CustomerMain::model()->findByPk($customer_id);
					$customer_model->city_id = $city_id;
					echo 'customer:'.$customer_id."  city_id:".$city_id.' success:'.intval($customer_model->save())."\n";
				}
			}
		}
	}


    /**
     * @auther mengtianxue
     * php yiic.php customer LoadAll
     */
    public function ActionLoadAll(){
        RCustomerInfo::model()->loadAll();
    }

    /**
     * @auther mengtianxue
     * php yiic.php customer Load --id=414502
     */
    public function ActionLoad($id = 0){
        RCustomerInfo::model()->load($id);
    }


    /**
     * @auther mengtianxue
     * php yiic.php customer GetCustomerSmsPasswd --phone=18511663962
     */
    public function ActionGetCustomerSmsPasswd($phone = 0){
        $customer_info = RCustomerInfo::model()->getCustomerSmsPasswd($phone);
        print_r($customer_info);
    }

    /**
     * @auther mengtianxue
     * php yiic.php customer DeletePassCodeCache --phone=18511663962
     */
    public function ActionDeletePassCodeCache($phone = 0){
        $customer_info = RCustomerInfo::model()->deletePassCodeCache($phone);
        print_r($customer_info);
    }


    /**
    *   完善客户城市信息
    *   @author aiguoxin
    */
    public function ActionFillCity(){
        echo '------------start to fill customer city_id...'.PHP_EOL;
        $max=0;
        
        while (true) {
            $sql = "SELECT id,phone FROM t_customer_main WHERE id>:max and city_id is NULL LIMIT 5000";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $customer_list = $command->queryAll();
            if ($customer_list) {
                foreach ($customer_list as $customer) {
                    $max = $customer['id'];
                    $phone = $customer['phone'];
                    if($phone){
                        $city_id = Helper::PhoneLocation($phone);
                        $attr = array('city_id'=>$city_id);
                        $res = CustomerMain::model()->updateByPk($customer['id'], $attr);
                        if($res){
                            echo 'phone='.$phone.'更新城市信息成功city='.$city_id.PHP_EOL;
                        }else{
                            echo 'phone='.$phone.'更新城市信息失败city='.$city_id.PHP_EOL;
                        }
                    }
                }
            }else{
                break;
            }
        }
    }

    /**
    *   完善客户黑名单数据库---缓存
    *
    */
    public function ActionBlacklist(){
        echo '------------start to fill black list...'.PHP_EOL;
        $max=0;
        
        while (true) {
            $sql = "SELECT id,phone FROM t_customer_blacklist WHERE id>:max limit 1000";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $customer_list = $command->queryAll();
            if ($customer_list) {
                foreach ($customer_list as $customer) {
                    $max = $customer['id'];
                    $phone = trim($customer['phone']);
                    $black_customer = CustomerStatus::model()->is_black($phone);  //此处调用个黑名单验证方法(走缓存)
                    if(empty($black_customer)){
                        CustomerStatus::model()->add_black($phone);
                        echo 'phone='.$phone.'更新到redis成功'.PHP_EOL;
                    }else{
                        echo 'phone='.$phone.'已经在redis中，不用更新'.PHP_EOL;
                    }
                }
            }else{
                break;
            }
        }
        echo '------------fill black list end...'.PHP_EOL;
    }

}
