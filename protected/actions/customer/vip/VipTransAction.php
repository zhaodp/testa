<?php
Yii::import("application.controllers.VipController");

class VipTransAction extends CAction
{
	public function run()
	{
		$magic_number = 10000007;
		$params = array();
		$customer_id = '';
		if ($_GET) {
			$getData = $_GET;
			$phone = isset($_GET['phone']) ? htmlspecialchars(trim($_GET['phone'])) : '';
			$params['type'] = $getData['type'] = isset($_GET['type']) ? trim($_GET['type']) : $magic_number;
			$params['order_id'] = isset($_GET['order_id']) ? htmlspecialchars(trim($_GET['order_id'])) : '';
			if (isset($_GET['start_time']) && !empty($_GET['start_time'])) {
				$params['start_time'] = $getData['start_time'] = strtotime(trim($_GET['start_time']));
			}
			if (isset($_GET['end_time']) && !empty($_GET['end_time'])) {
				$params['end_time'] = $getData['end_time'] = strtotime(trim($_GET['end_time']));
			}

			if (!empty($phone)) {
				$params['vipcard'] = Vip::model()->getCardByPhone($phone);
				if (empty($params['vipcard'])) {
					$params['vipcard'] = '--';
				}
			}
			if (isset($_GET['vip_type'])) {
				$params['vip_type'] = $_GET['vip_type'];
				$getData['vip_type'] = $_GET['vip_type'];
			}
		}

		if (!isset($params['start_time'])) {
			$params['start_time'] = $getData['start_time'] = strtotime(date('Y-m', time()) . '-01');
		}
		if (!isset($params['end_time'])) {
			$params['end_time'] = $getData['end_time'] = strtotime(date('Y-m-d', time()));
		}
		//$params['start_time']=strtotime($params['start_time']);
		//$params['end_time']=strtotime($params['end_time']);
		if (!isset(VipTrade::$trans_type[$params['type']])) {
			unset($params['type']);
		}
		$dataProvider = VipTrade::model()->V2VipTradeList($params);
		$req = array();
		$req['start_time'] = $params['start_time'];
		$req['end_time'] = $params['end_time'];
		$vipData = array();
		foreach (VipTrade::$trans_type as $key => $value) {
			$req['type'] = $key;
			$staticData = VipTrade::model()->getVipStaticData($req);
			$vipData[$key] = isset($staticData[0]['total_amount']) ? $staticData[0]['total_amount'] : 0;
		}
		$req['type'] = VipTrade::TYPE_INCOME;
		$payCnt = VipTrade::model()->getVipPayTypeCnt($req);
		$vipData['type_income'] = isset($payCnt[0]['pay_cnt']) ? $payCnt[0]['pay_cnt'] : 0;
		$req['type'] = VipTrade::TYPE_CARD_INCOME;
		$payCnt = VipTrade::model()->getVipPayTypeCnt($req);
		$vipData['type_card_income'] = isset($payCnt[0]['pay_cnt']) ? $payCnt[0]['pay_cnt'] : 0;
		$req['type'] = VipTrade::TYPE_PAY;
		$payCnt = VipTrade::model()->getVipPayTypeCnt($req);
		$vipData['type_pay'] = isset($payCnt[0]['pay_cnt']) ? $payCnt[0]['pay_cnt'] : 0;
		$consumeCnt = VipTrade::model()->getVipConsumeCnt($req);
		$vipData['consume_cnt'] = isset($consumeCnt[0]['consume_cnt']) ? $consumeCnt[0]['consume_cnt'] : 0;
		$this->controller->render('user/vip_trans_list',
			array(
				'model' => $getData,
				'dataProvider' => $dataProvider,
				'statistics' => $vipData,
			));
	}
}







