<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Bidong
 * Date: 13-12-25
 * Time: 上午11:42
 * To change this template use File | Settings | File Templates.
 */


Yii::import('application.models.schema.customer.*');

class BCustomers extends CarCustomerMain
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 组织条件
     * @param $items 表attributes
     * @param $params 要查询的参数
     * @return CDbCriteria
     * @auther mengtianxue
     */
    public function conditions($items, $params)
    {
        $criteria = new CDbCriteria;
        $criteria->condition = 'id > 0';
        foreach ($params as $k => $v) {
            if (array_key_exists($k, $items) && !empty($v)) {
                $criteria->compare($k, $v);
            }
        }
        return $criteria;
    }

    /**
     * 用户查询
     * @param array $params
     * @return array
     * @auther mengtianxue
     */
    public function getCustomerInfo($params = array())
    {
        $back = array('code' => 1, 'message' => '用户不存在');
        $model = new CarCustomerMain();
        if (!empty($params)) {
            $items = $model->attributes;
            $conditions = $this->conditions($items, $params);
            $customer_info = $model->find($conditions);
            if (!empty($customer_info)) {
                if (isset($params['field']) && !empty($params['field'])) {
                    $field = trim($params['field']);
                    $customer_info = $customer_info->$field;
                }

                //用户金额
                $account = $this->getAccount(array('user_id' => $customer_info->id));
                if ($account['code'] === 0) {
                    $account = $account['data'];
                } else {
                    $account = array();
                }

                $back['code'] = 0;
                $back['message'] = '查询成功';
                $back['data'] = $customer_info;
                //用户金额
                $back['user_account'] = $account;
            }
        }
        return $back;
    }

    /**
     * 获取用户列表
     * @param array $params
     * @return CActiveDataProvider
     * @auther mengtianxue
     */
    public function getCustomerList($params = array(), $limit = 30)
    {
        $model = new CarCustomerMain();
        $criteria = new CDbCriteria;
		$criteria->limit = $limit;
        if (!empty($params)) {
            $items = $model->attributes;
            foreach ($params as $k => $v) {
                if (array_key_exists($k, $items) && !empty($v)) {
                    $criteria->compare($k, $v);
                }
            }
        }

        $criteria->order = 'type asc, id desc';
		CarCustomerMain::$db  = Yii::app()->db_readonly;
		$rowData = CarCustomerMain::model()->findAll($criteria);
		CarCustomerMain::$db  = Yii::app()->db;
		return $rowData;
    }

    /**
     * 添加用户
     * @param array $params
     * @return array
     * @auther mengtianxue
     */
    public function addCustomer($params = array())
    {
        $back = array('code' => 1, 'message' => '参数不能为空');
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $params[$k] = trim($v);
            }
            $params['update_time'] = $params['create_time'] = date('Y-m-d H:i:s');
            $customer_main = new CarCustomerMain();
            $customer_main->attributes = $params;
            $add_customer = $customer_main->insert();
            if ($add_customer) {
                $back['code'] = 0;
                $back['message'] = '添加成功';
                $back['data'] = $customer_main;
            } else {
                $back['message'] = '添加失败';
            }
        }
        return $back;
    }

    /**
     * 修改用户信息
     * @param array $params
     * @return array
     * @auther mengtianxue
     */
    public function updateCustomer($params = array())
    {
        $customer = new CarCustomerMain();
        $back = array('code' => 1, 'message' => '参数不能为空');
        if (!empty($params)) {
            $id = intval($params['id']);
            $where = array('id' => $id);
            $check_customer = $this->getCustomerInfo($where);
            if ($check_customer['code'] === 0) {
                //过滤空格
                foreach ($params as $k => $v) {
                    $params[$k] = trim($v);
                }
                $params['update_time'] = date('Y-m-d H:i:s');
                $update_customer = $customer->updateAll($params, 'id = :id', array(':id' => $id));
                if ($update_customer) {
                    $back['code'] = 0;
                    $back['message'] = '添加成功';
                    $back['date'] = $check_customer['data'];
                } else {
                    $back['message'] = '添加失败';
                }
            } else {
                $back['message'] = '用户不存在';
            }
        }
        return $back;
    }

    /**
     * 查询余额信息
     * @param array $params
     * @return array     如果传field,返回date值为字符串，否则为对象
     * @auther mengtianxue
     */
    public function getAccount($params = array())
    {
        $customer_account = new CarCustomerAccount();
        $items = $customer_account->attributes;

        $back = array('code' => 1, 'message' => '信息不存在');
        if (!empty($params)) {
            $criteria = $this->conditions($items, $params);
            $account = CarCustomerAccount::model()->find($criteria);
            if ($account) {
                if (isset($params['field']) && !empty($params['field'])) {
                    $field = trim($params['field']);
                    $account = $account->$field;
                }

                $back['code'] = 0;
                $back['message'] = '余额';
                $back['data'] = $account;
            }
        }
        return $back;
    }

    /**
     * 根据手机号获取帐户用户余额
     * @param $phone
     * @return array
     * @auther mengtianxue
     */
    public function getUserAmount($phone)
    {
        $back = array('code' => 0, 'data' => '', 'amount' => 0);
        $where = array('phone' => trim($phone));
        $customer = BCustomers::model()->getCustomerInfo($where);

        if ($customer['code'] === 0) {
            $data_user = $customer['data'];
            $user = $this->getAccount(array('user_id' => $data_user->id));
            $back['user_id'] = $data_user->id;
            $back['amount']  = 0;
            if ($user['code'] === 0) {
                $data_account = $user['data'];
				$back['id']   = $data_user->id;
				$back['user_id']   = $data_user->id;
                $back['data_user'] = $data_user;
                $back['amount'] = $data_account->amount;
            }
        }
        return $back;
    }


    /**
     * 修改余额
     * @param array $params
     * @return array
     * @auther mengtianxue
     */
    public function updateAccount($params = array())
    {
        $back = array('code' => 1, 'message' => '修改失败');
        if (!empty($params) && isset($params['user_id'])) {
            $update_params['amount'] = isset($params['amount']) ? trim($params['amount']) : 0;

            $account = CarCustomerAccount::model()->updateCounters($update_params, 'user_id = :user_id',
                array(':user_id' => trim($params['user_id'])));
            if ($account) {
                $back['code'] = 0;
                $back['message'] = '修改成功';
            }
        }
        return $back;
    }

    /**
     * 添加余额信息
     * @param array $params
     * @return array
     * @auther mengtianxue
     */
    public function addAccount($params = array())
    {
        $customer_account = new CarCustomerAccount();
        $back = array('code' => 1, 'message' => '充值失败');
        if (!empty($params) && isset($params['user_id'])) {
            //检查用户是否充值
            $check_Account = $this->getAccount(array('user_id' => $params['user_id']));
            if ($check_Account['code'] === 0) {
                $account = $check_Account['data'];
                $balance = $params['amount'] + $account->amount;
                $up_account = $this->updateAccount(array('user_id' => $params['user_id'], 'amount' => $params['amount']));
                if ($up_account['code'] === 0) {
                    $back['code'] = 0;
                    $back['message'] = '充值成功';
                    $back['balance'] = $balance;
                }
            } else {
                $params['user_id'] = isset($params['user_id']) ? trim($params['user_id']) : 0;
                $params['city_id'] = isset($params['city_id']) ? trim($params['city_id']) : 0;
                $params['type'] = isset($params['type']) ? trim($params['type']) : 0;
                $params['amount'] = isset($params['amount']) ? trim($params['amount']) : 0;
                $params['vip_card'] = isset($params['vip_card']) ? trim($params['vip_card']) : 0;
                $params['update_time'] = isset($params['update_time']) ? trim($params['update_time']) : date('Y-m-d H:i:s');
                $customer_account->attributes = $params;
                if ($customer_account->insert()) {
                    $back['code'] = 0;
                    $back['message'] = '充值成功';
                    $back['balance'] = $params['amount'];
                }
            }
        }
        return $back;
    }


    /**
     * 添加销费列表
     * @param array $params
     * @return array
     * @auther mengtianxue
     */
    public function addCustomerTrade($params = array())
    {

        $back = array('code' => 1, 'message' => '参数不能为空');
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $params[$k] = trim($v);
            }
            $params['create_time'] = date('Y-m-d H:i:s');
            $model = new CarCustomerTrans();
            $model->attributes = $params;

            $add_customer = $model->insert();
            if ($add_customer) {
                $back['code'] = 0;
                $back['message'] = '添加成功';
                $back['date'] = $params;
            } else {
                $back['message'] = '添加失败';
            }
        }
        return $back;
    }


    /**
     * 获取销费账单列表
     * @param array $params
     * @return CActiveDataProvider
     * @auther mengtianxue
     */
    public function getCustomerTradeList($params = array())
    {
        $page = 30;
        $model = new CarCustomerTrans();
        $criteria = new CDbCriteria;
        if (!empty($params)) {
            $items = $model->attributes;
            foreach ($params as $k => $v) {
                if (array_key_exists($k, $items) && !empty($v)) {
                    $criteria->compare($k, $v);
                }
            }
        }
        if(isset($params['start_time']) && isset($params['end_time'])){
            $criteria->addBetweenCondition('create_time',trim($params['start_time']),trim($params['end_time']));
        }

        if (isset($params['page']) && !empty($params['page'])) {
            $page = trim($params['page']);
        }

        $criteria->order = 'id desc';
        return new CActiveDataProvider($model, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $page
            ),
        ));
    }
    /**
     * 客户退款获取客户消费账单列表 只需要充值记录不需要消费记录
     * @param array $params
     */
    public function getCustomerTradeListByRefund($params = array())
    {
        $page = 30;
        $model = new CarCustomerTrans();
        $criteria = new CDbCriteria;
        if (!empty($params)) {
            $items = $model->attributes;
            foreach ($params as $k => $v) {
                if (array_key_exists($k, $items) && !empty($v)) {
                    $criteria->compare($k, $v);
                }
            }
        }
        $criteria->addCondition('trans_type !=:trans_type');
        $criteria->params[':trans_type'] = CarCustomerTrans::TRANS_TYPE_F;//不能是订单消费的记录
        if(isset($params['start_time']) && isset($params['end_time'])){
            $criteria->addBetweenCondition('create_time',trim($params['start_time']),trim($params['end_time']));
        }

        if (isset($params['page']) && !empty($params['page'])) {
            $page = trim($params['page']);
        }

        $criteria->order = 'id desc';
        return new CActiveDataProvider($model, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $page
            ),
        ));
    }



    protected  function makeCustomerTransCriteria($params=array()){
        $criteria = new CDbCriteria;
        if (!empty($params)) {
            $items = CarCustomerTrans::model()->attributes;
            foreach ($params as $k => $v) {
                if (array_key_exists($k, $items) && !empty($v)) {
                    $criteria->compare($k, $v);
                }
            }
        }
        return $criteria;
    }

    /**
     * 获取用户交易流水数量
     * @param array $params
     * @return int
     */
    public function getCustomerTransCount($params=array()){
        $criteria =$this->makeCustomerTransCriteria($params);
        $ret=CarCustomerTrans::model()->count($criteria);

        return $ret;
    }


    /**
     * api调用销费明细
     * @param $phone
     * @param $min_id
     * @param $count
     * @return array
     * @auther mengtianxue
     */
    public function getCustomerTradeListApi($user_id, $min_id = 0, $count = 10, $type = 0)
    {
        if (empty($user_id)) {
            return false;
        }

        //加缓存 一天
        $cache_key = 'CUSTOMER_TRANS_LIST_' . $user_id . $min_id;
//        $data = Yii::app()->cache->get($cache_key);
        $data = array();
        if (!$data) {
            $model = new CarCustomerTrans();
            $criteria = new CDbCriteria;
            $params = array();
            if (!empty($min_id)) {
                $criteria->addCondition('id < :min_id');
                $params[':min_id'] = $min_id;
            }

            if(!empty($type)){
                $criteria->addCondition('source = :source');
                $params[':source'] = $type;
            }

            $criteria->addCondition('user_id = :user_id');
            $params[':user_id'] = $user_id;

            $criteria->params = $params;
            $criteria->limit = $count;
            $criteria->order = 'id desc';

            $trans_list = $model->findAll($criteria);
            foreach ($trans_list as $v) {
                $attributes = $v->attributes;
                foreach($attributes as $k => $val){
                    if($k == 'trans_type'){
                        $trans_type = CarCustomerTrans::$trans_type;
                        $val = $trans_type[$val];
                    }

                    if($k == 'source'){
                        $trans_source = CarCustomerTrans::$trans_source;
                        $val = $trans_source[$val];
                    }
                    $attributes[$k] = $val;
                }
                $description='';
                if(!empty($attributes['trans_order_id'])){
                    $description="订单号：".$attributes['trans_order_id'];
                }
                $attributes['description'] = $description;
                $data[] = $attributes;
            }
//            Yii::app()->cache->set($cache_key, $data, 600);
        }
        return $data;
    }

    /**
     * 用户充值
     * @param $user_id
     * @param $amount
     * @param array $params
     * @return array
     * @auther mengtianxue
     */
    public function income($user_id, $amount, $params = array())
    {
        $back = array('code' => 1, 'message' => '充值失败');
        if (empty($user_id)) {
            $back['message'] = '用户ID不能为空';
            return $back;
        }
        $amount = isset($amount) ? trim($amount) : 0;
        $vip_card = isset($params['vip_card']) ? trim($params['vip_card']) : 0;
        $trans_order_id = isset($params['trans_order_id']) ? $params['trans_order_id'] : 0;
        $trans_card = isset($params['trans_card']) ? $params['trans_card'] : 0;
        $operator = isset($params['operator']) ? $params['operator'] : '系统';
        $remark = isset($params['remark']) ? $params['remark'] : '充值';
        $trans_type = isset($params['trans_type']) ? $params['trans_type'] : CarCustomerTrans::TRANS_TYPE_S;
        $source = isset($params['source']) ? $params['source'] : CarCustomerTrans::TRANS_SOURCE_F;
        $type = isset($params['type']) ? trim($params['type']) : CarCustomerAccount::ACCOUNT_TYPE_S;

        $customer = $this->getCustomerInfo(array('id' => $user_id));
        if ($customer['code'] === 0) {
            $customer_data = $customer['data'];
            $params_account = array();
            $params_account['user_id'] = $user_id;
            $params_account['city_id'] = $customer_data->city_id;
            $params_account['type'] = $type;
            $params_account['amount'] = $amount;
            $params_account['vip_card'] = $vip_card;
            $add_account = $this->addAccount($params_account);
            if ($add_account['code'] === 0) {
                $balance = $add_account['balance'];
                $trans = array();
                $trans['user_id'] = $params_account['user_id'];
                $trans['trans_order_id'] = $trans_order_id;
                $trans['trans_card'] = $trans_card;
                $trans['trans_type'] = $trans_type;
                $trans['amount'] = $amount;
                $trans['balance'] = $balance;
                $trans['source'] = $source;
                $trans['operator'] = $operator;
                $trans['remark'] = $remark;
                $add_trans = $this->addCustomerTrade($trans);
                if ($add_trans['code'] === 0) {
                    $back['code'] = 0;
                    $back['message'] = '充值成功';
                } else {
                    $back['message'] = '添加充值记录失败';
                }
            } else {
                $back['message'] = '充值失败';
            }
            return $back;
        }
    }

    /**
     * 获取用户手机
     * @param $user_id
     * @return mixed
     */
    public function getUserPhoneById($user_id){
        $customerInfo=CarCustomerMain::model()->findByPk($user_id);
	$phone='';
	if(!empty($customerInfo)){
            $phone=$customerInfo->phone;
            $phone=Common::parseCustomerPhone($phone);
	}
        return $phone;
    }

    public function getUserTransStatistics($start_time,$end_time,$user_id=''){

        $sql = 'SELECT
                    SUM(IF((trans_type = :cz), amount, 0)) AS cz,
                    SUM(IF((trans_type = :xf), amount, 0)) AS xf,
                    SUM(IF((trans_type = :bc), amount, 0)) AS bc
              FROM {{customer_trans}} WHERE (create_time BETWEEN :stime AND :etime) ';
        $param=array(':cz'=>CarCustomerTrans::TRANS_TYPE_S,
            ':xf'=>CarCustomerTrans::TRANS_TYPE_F,
            ':bc'=>CarCustomerTrans::TRANS_TYPE_FV,
            ':stime'=>$start_time,
            ':etime'=>$end_time
        );
        if(!empty($user_id)){
            $sql.=' AND user_id=:user_id';
            $param[':user_id']=$user_id;
        }
        $result = Yii::app()->db_finance->createCommand($sql)->queryRow(true, $param);

        return $result;
    }

	/**
	 * 返还底单里面的钱
	 *
	 * @param $userId
	 * @param string $vipCard
	 * @param $orderId
	 * @param $phone 用户电话
	 */
	public function refundOrderCost($userId = 0, $orderId, $vipCard = '0', $phone)
	{
		if(empty($userId) && empty($phone)){
			return;
		}
		if(empty($userId)){
			$customer	= CustomerMain::model()->getCustomerInfo($phone);
			$userId		= $customer->id;
		}
		//查交易记录，普通用户是否从余额有扣款
		$criteria = new CDbCriteria;
		$criteria->compare('user_id', $userId);
		$criteria->compare('trans_order_id', $orderId, true);
		$criteria->compare('trans_type', CarCustomerTrans::TRANS_TYPE_F);
		$criteria->compare('source', CarCustomerTrans::TRANS_SOURCE_S);
		$criteria->order = ' id desc ';
		$trans = CarCustomerTrans::model()->find($criteria);
		if (empty($trans)) {
			$format = '没有找到普通用户的交易记录|userId|%s|orderId|%s|time|%s';
			EdjLog::info(sprintf($format, $userId, $orderId, date('Y-m-d H:i:s')));
			return;
		}
		$customerIncomeParams = array(
			'vip_card' => $vipCard,
			'trans_type' => CarCustomerTrans::TRANS_TYPE_F,
			'source' => CarCustomerTrans::TRANS_SOURCE_S,
		);
		$ret = $this->income($userId, abs($trans->amount), $customerIncomeParams);
		$format = '客户 充值返回值|%s|应回退金额|%s|userId|%s|orderId|%s|time|%s';
		EdjLog::info(sprintf($format, json_encode($ret), $trans->amount, $userId, $orderId, date('Y-m-d H:i:s')));
	}

}
