<?php
/**
 * 品监补偿
 * User: Bidong
 * Date: 13-7-18
 * Time: 下午11:33
 * To change this template use File | Settings | File Templates.
 */

class ComplainDoRecoupAction extends CAction
{
    public function run()
    {

	if ($_GET && !empty($_GET['id'])) {
	    $recoup_id = $_GET['id'];
	    $complain_id = $_GET['cid'];
	    $urlReferrer = $_GET['re'];

	    $command = Yii::app()->db_readonly->createCommand();
	    $lastMark = $command->select('mark')
		->from('t_customer_complain_log')
		->where('complain_id=:cid', array(':cid' => $complain_id))
		->order('create_time DESC')
		->queryRow();

	    $lastMark = $lastMark['mark'];
	    $recoupModel = CustomerComplainRecoup::model()->findByPk($recoup_id);

	    $this->controller->render('dorecoup', array(
			'mark' => $lastMark,
			'model' => $recoupModel,
			're' => $urlReferrer,
			'cid' => $complain_id,
			'id' => $recoup_id,
			));
	}


	if ($_POST && !empty($_POST['cid'])) {

		$data['mark'] = $_POST['mark'];
	    $process_time = date('Y-m-d H:i:s', time());
	    $operator = Yii::app()->user->id;
	    $complain_id = $_POST['cid'];
	    $complain = CustomerComplain::model()->findByPk($complain_id);

	    if (!empty($complain)) {
		//先判断用户是否存在 不存在则直接返回错误
		$cus_pro_type =isset($_POST['cus_process_type'])? intval($_POST['cus_process_type']):'';
		$binding_phone=isset($_POST['binding_phone'])?trim($_POST['binding_phone']):'';
		$vip_cash=isset($_POST['vip_cash'])?trim($_POST['vip_cash']):0;
		//补偿最多 1000元
		if(($cus_pro_type == 2 || $cus_pro_type == 3) && $vip_cash > 1000){

		    Helper::alert('金额最多1000元');
		    Yii::app()->end();
		}

		//如果是补偿或者扣款则需要判断用户是否存在
		if ($cus_pro_type > 1 )
		{
		    $vipModel = Vip::model()->getPrimary($binding_phone);
		    //desc
		    if (!$vipModel)
		    {
			$is_vip = false;
		    }
		    else $is_vip = true;
		}

		$process_result = intval($vip_cash) > 0 ? CustomerComplain::SP_PROCESS_T2 : (intval($_POST['bonus']) > 0 ? CustomerComplain::SP_PROCESS_T1 : '');
		//changed by aiguoxin 2014-07-08
		$complain->status = CustomerComplain::STATUS_EFFECT;

		$complain->sp_process = $process_result; //品监处理结果

		// $complain->operator = $operator;
		// $complain->update_time = $process_time;

		if ($complain->save()) {
            EdjLog::info('complain='.$complain_id.'状态更改成功...');
            $pk = $_POST['id'];
		    $recoup = CustomerComplainRecoup::model()->findByPk($pk);
		    //确认投诉后 扣除司机对应的代价分 add by duke
		    $driver_id = $complain->driver_id; //司机工号
		    $order_id = $complain->order_id;
		    $complain_type_id = $complain->complain_type;
		    $driver_info = Driver::model()->getProfile($driver_id); //
		    if($complain_id && $driver_info){ //先查询是否该城市已经配置了扣分功能
			//$complainModel = $complainModel->findByPk($complain_id);

			$driver_city_id = $driver_info->city_id;

			//如果投诉有订单则查询订单ID 否则查询投诉创建时间
			$order_info = Order::model()->getOrdersById($order_id);
			if($order_info){
			    $compare_time = $order_info['created'] ;
			}else {
			    $compare_time = strtotime($complain->create_time);
			}
			if(Common::checkOpenScore($driver_city_id,$compare_time)){
                EdjLog::info('complain='.$complain_id.'开始扣分...');
                //var_dump($_POST['sub_type']);die;
			    $complain_type = CustomerComplainType::model()->findByPk($complain_type_id);
			    //print_r($complain_type);die;
			    $deduct_score = $complain_type->score;
			    if($deduct_score){ //如果对应的投诉有扣分项
				$driver_ext_mod = new DriverExt(); //扣除司机对应分数 、 查看扣分后是否应该屏蔽司机、 发送扣分短信，屏蔽短信
				$res = $driver_ext_mod->scoreDeduct($driver_id,-$deduct_score,$complain_type_id);
				$block_day = $res['update_res'] && $res['had_punished'] ? $res['block_day'] : 0; //司机是否被屏蔽了
				$comment_sms_id = 0;
				if($order_id){
				    $complain_mod = CommentSms::model()->getCommandSmsByOrderId($order_id);
				    if(!empty($complain_mod)) {
					$comment_sms_id = $complain_mod['id'];
				    }
				}

				//print_r($complain_mod);die;


				$param = array(
					'driver_id' => $driver_id,
					'customer_complain_id' => $complain_id,
					'complain_type_id' => $complain_type_id,
					'operator' => Yii::app()->user->id,
					'driver_score'=>-$deduct_score,
					'block_day' =>$block_day,
					'comment_sms_id' => $comment_sms_id,
					'city_id'=> $driver_city_id,
					'create_time' => date('Y-m-d H:i:s'),
					'deduct_reason' => $complain_type->name,
					'revert'=> DriverPunishLog::REVERT_NO_EXECUTE,

					);
				$driver_punish_log_res = DriverPunishLog::model()->addData($param);
                    EdjLog::info('complain='.$complain_id.'扣分成功...');
				if($res['had_punished'] == false){

				    $message = $driver_id.' 师傅,您由于 '.$complain_type->name.',被扣 '.$deduct_score.' 分。';
				    if($order_id){
					$message.='订单号：'.$order_id;
				    }
				    $message.='(三日内可申述)';//http://jira.edaijia.cn/browse/UPDATE-2831
				    //echo $message;

				    $i_phone = ($driver_info->ext_phone) ? $driver_info->ext_phone : $driver_info->phone;
				    $res = Sms::SendSMS($i_phone, $message);
				    //var_dump($res);die;
				}
			    }
			}
		    }
		    //添加处理日志
		    //VIP 补偿cus_process_type
		    if($recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE2 
			    || $recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE3
			    || $recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND3
			    || $recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND4
			    || $recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND3
			    || $recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND4){
			switch($cus_pro_type){
			    case 1:
				//不补偿
				$data['mark']='品监管理员,取消用户补偿';
				$this->addProcessLog($complain_id, $process_result, 0, $data);
				break;
			    case 2:
				//补偿用户
				if (intval($vip_cash) > 0 && !empty($binding_phone)) {
				    $data['vip_cash'] = $vip_cash;
				    $data['binding_phone'] = $binding_phone;
				    $data['mark']='品监管理员,补偿用户 '.$vip_cash.' 元';
				    $this->addProcessLog($complain_id, $process_result, 1, $data);

				    //desc
				    if ($is_vip)
				    {
					//VIP 充值
					$this->recoupVip($binding_phone, $vip_cash);
				    }else {
					$this->recharge($binding_phone, $vip_cash);
				    }

				}
				break;
			    case 3:
				//扣款
				if (intval($_POST['vip_cash']) > 0 && !empty($_POST['binding_phone'])) {
				    $data['vip_cash'] = $vip_cash;
				    $data['binding_phone'] = $binding_phone;
				    $data['mark']='品监管理员,扣除用户 '.$vip_cash.' 元';
				    $this->addProcessLog($complain_id, $process_result, 1, $data);

				    //desc
				    if ($is_vip)
				    {
					//VIP 充值
					$this->recoupVip($binding_phone, -$vip_cash);
				    }else {
					$this->recharge($binding_phone, -$vip_cash);
				    }
				}
				break;
			    default:
				break;
			}
			//更新补偿记录
			if (!empty($recoup)) {
			    if($binding_phone)
				$recoup->recoup_customer = $binding_phone;
			    if($vip_cash)
				$recoup->amount_customer = $vip_cash;
			    $recoup->operator = $operator;
			    $recoup->update_time = $process_time;
			    $recoup->status = 1;
			    $recoup->mark=$data['mark'];

			    $recoup->save();
			}
		    }

		    if($recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE4 
			    || $recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE5
			    || $recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND3
			    || $recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE1AND4
			    || $recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND3
			    || $recoup->process_type==CustomerComplainRecoup::PROCESS_TYPE2AND4){                            
			if (isset($_POST['dri_process_type'])) {
			    $dri_pro_type = intval($_POST['dri_process_type']);
			    $new_driver_id=trim($_POST['new_driver_id']);
			    $driver_cash=trim($_POST['driver_cash']);

			    $params['user']=strtoupper($new_driver_id);
			    //$city_pre=substr($params['user'],0,2);
			    //$params['city_id']=Common::getCity($city_pre);
                $params['city_id'] = DriverStatus::model()->getItem($new_driver_id,'city_id');
			    $params['channel']='22';
				$params['order_date'] = time();
				$params['type'] = EmployeeAccount::TYPE_FORFEIT;
				$user = Yii::app()->user->getID();
				$params['operator'] = $user;
				$params['order_id'] = 0;
				if ($dri_pro_type == 1){
					$data['mark']='不对司机做处理';
			    }
			    //司机补偿
			    if ($dri_pro_type == 2) {
				//补偿司机
				$comment='因投诉补偿司机信息费:'.$driver_cash.'元';
				$params['cast']=$driver_cash * -1;//因为insert里面会给* -1,这里先变成负数
				$params['comment']=$comment;
				OrderSettlement::model()->insertAccount($params);
				$data['mark']='品监管理员,补偿司机信息费 '.$driver_cash.' 元';
				$this->addProcessLog($complain_id, $process_result, 0, $data);
			    }
			    //司机扣款
			    if ($dri_pro_type == 3) {
				$params['cast']= $driver_cash;//因为insert里面会给* -1
				$params['comment']='因投诉扣除司机信息费:'.$driver_cash.'元';
				OrderSettlement::model()->insertAccount($params);
				$data['mark']='品监管理员,扣除司机信息费 '.$driver_cash.' 元';
				$this->addProcessLog($complain_id, $process_result, 0, $data);
			    }

			    //更新补偿记录
			    if (!empty($recoup)) {
				$recoup->recoup_driver=$new_driver_id;
				$recoup->amount_driver=$driver_cash;
				$recoup->operator = $operator;
				$recoup->update_time = $process_time;
				$recoup->status = 1;
				$recoup->mark=$recoup->mark.$data['mark'];

				$recoup->save();
			    }
			    // 更新司机补偿扣款记录到driver_punish_log  用于记录，以便司机申诉退还
			    if($dri_pro_type == 2 or $dri_pro_type ==3){
				//判断该城市是否开通该规则
				$driver_id = $new_driver_id; //司机工号
				$driver_info = Driver::model()->getProfile($driver_id); //
				if($driver_info){
				$driver_city_id = $driver_info->city_id;
				$driver_cash = ($dri_pro_type == 2) ? $driver_cash : -$driver_cash;

				//如果投诉有订单则查询订单ID 否则查询投诉创建时间
				if($complain->order_id){
				    $order_info = Order::model()->getOrdersById($complain->order_id);

				}
				//如果投诉有订单则查询订单ID 否则查询投诉创建时间
				if($complain->order_id){
				    $order_info = Order::model()->getOrdersById($complain->order_id);
				    $compare_time = $order_info['created'] ;
				}else {
				    $compare_time = strtotime($complain->create_time);
				}
				if(Common::checkOpenScore($driver_city_id,$compare_time)){
				    $mod = DriverPunishLog::model();
				    $res = $mod->updateAll(array('driver_money'=>$driver_cash),
					    'customer_complain_id = :ccid',array(':ccid'=>$complain_id));
				}
				}
			    }
			}


		    }
		    $this->controller->redirect($_POST['re']);
		}
	    }
	}
    }

    //添加处理日志
    protected function addProcessLog($complain_id, $process_result, $recoup_type, $data)
    {
	$complainLog = new CustomerComplainLog();
	$recoup_user = $recoup_amount = '';

	//补偿用户
	if ($recoup_type == 1) {
	    $recoup_user = $data['binding_phone']; //绑定手机号
	    $recoup_amount = $data['vip_cash'];
	}
	if ($recoup_type == 2) {
	    $recoup_user = $data['driver_id']; //绑定手机号
	    $recoup_amount = $data['driver_cash'];
	}
	$complainLog->recoup_amount = $recoup_amount;
	$complainLog->recoup_user = $recoup_user;
	$complainLog->result = $process_result;
	$complainLog->mark = $data['mark'];
	$complainLog->complain_id = $complain_id;
	$complainLog->process_type = $complainLog::PROCESS_ONE; //品监处理
	$complainLog->operator = Yii::app()->user->id;
	$complainLog->create_time = date('Y-m-d H:i:s', time());
	$complainLog->recoup_type = $process_result; //补偿方式
	$complainLog->payer = 1;

	$complainLog->insert();


    }

    protected function recoupVip($vip_card, $money)
    {
	//vip 充值
	$vipModel = Vip::model()->getPrimary($vip_card);
	//手机号
	if ($vipModel) {
	    //找到主卡，充值
	    if (Vip::model()->vipIncome($vipModel, $money)) {
		$content = $money . '元代驾费已充值到您的手机号,使用此号码拨打4006913939预约代驾,或下载安装e代驾应用软件直接
		    呼叫最近司机>>http://t.cn/S7Ta29';
		Sms::SendSMS($vipModel->send_phone, $content);
	    }
	}
    }

    /**
     * 补偿用户款直接进入客户充值
     * @param   type    $varname    description
     * @param   type    $varname    description
     * @param   type    $varname    description
     * @return  type    description
     * @access  public or private
     * @static  makes the class property accessible without needing an instantiation of the class
     */
    protected function recharge($phone,$money)
    {
	$customerMod = new BCustomers();

	$customer =  $customerMod-> getCustomerInfo(array('phone' => $phone));

	//desc
	if ($customer['code'] == 0)
	{
	    $res = $customerMod->income($customer['data']->id,$money,array('remark'=>'投诉补偿','trans_type'=> CarCustomerTrans::TRANS_TYPE_FV,'source'=>CarCustomerTrans::TRANS_SOURCE_RE));
	}
	else {
	    $param = array('name'=>$phone,'phone'=>$phone,'operator'=>'系统补偿');
	    $customer = $customerMod->addCustomer($param);

	    if($customer['code'] == 0) {
		$res = $customerMod->income($customer['data']->id,$money,array('remark'=>'投诉补偿','trans_type'=> CarCustomerTrans::TRANS_TYPE_FV,'source'=>CarCustomerTrans::TRANS_SOURCE_RE));
	    }else {
		throw new exception('创建用户失败');
	    }
	}

    } // end func
}
