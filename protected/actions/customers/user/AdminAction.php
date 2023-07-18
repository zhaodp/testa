<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-1-9
 * Time: 下午5:32
 * auther mengtianxue
 */
Yii::import("application.controllers.CustomersController");

class AdminAction extends CAction
{
    public function run()
    {
        $model = new CarCustomerMain();
		$page = 30;
		$params = array();
        if (isset($_GET['CarCustomerMain'])) {
            $params = $_GET['CarCustomerMain'];
            //搜索框默认性别为   待定 。而不是  男
            if(!isset($params['gender'])){
                $params['gender']=0;
            }
            $model->attributes = $params;
        }else{
            $model['gender']=0;
        }
		if (isset($params['page']) && !empty($params['page'])) {
			$page = trim($params['page']);
		}
        $customerList = BCustomers::model()->getCustomerList($params);
		$customerMap = $this->listToMap('id', $customerList);
		//获取账户信息
		$amountList = CarCustomerAccount::model()->getAmountList(array_keys($customerMap));
		$amountMap = $this->listToMap('user_id', $amountList);
		foreach($amountMap as $k => $v){
			$amount = $v['amount'];
			$customerMap[$k]['amount'] = $amount;
		}

		$dataProvider = new CArrayDataProvider(array_values($customerMap), array(
			'id'	=> 'id',
			'pagination'=>array(
				'pageSize' => $page,
			),
		));

        
        $this->controller->render('user/admin',
            array(
                'model' => $model,
                'dataProvider' => $dataProvider,
				'amountMap' => $amountMap,
            ));
    }

	/**
	 * 把一个二维数组,转为一个指定字段为索引的map
	 *
	 * @param $column
	 * @param array $list
	 * @return array 可能抛出undefined index异常
	 */
	public  function listToMap($column, $list = array()){
		if(empty($list)){
			return array();
		}
		$ret = array();
		foreach($list as $item){
			$index = $item[$column];
			$ret[$index]  = $item;
		}
		return $ret;
	}
} 