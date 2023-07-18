<?php
//获得注册用户的交易历史列表 
//这个接口需要客户端配合检查是否还在使用，如果使用中，则需要优化，增加缓存。add by sunhongjing
//此接口在司机客户端智能计费版本上线后废弃。 add by sunhongjing 2013-06-06

$token = $params['token'];
$pageNo = $params['pageNo'];
$pageSize = $params['pageSize'];


$ret = array( 'code'=>1, 'message'=>'获取失败');
echo json_encode($ret);return;


//需要封装customerToken，采用缓存方式验证。add by sunhongjing
$validate = CustomerToken::validateToken($token);

if ($validate){
	
	$account_deal_list = array();
	$account_info = VipPhone::getPrimary($validate->phone);
	if( !empty($account_info) ){
		if( $account_info['type']==1 ){
			$condition['vipcard'] = $account_info['vipid'];
			$account_deal_list = VipTrade::AccountTradeList($condition,$pageNo,$pageSize);
			$account_deal_total  = VipTrade::getAccountTradeCount($condition);
			if( !empty($account_deal_list) ){
				foreach($account_deal_list as &$v)
				{
					$v['created'] = date('Y-m-d',$v['created']);
					$v['comment'] = $v['type']==1 ? '消费' : '充值';
				}
			}
		}
	}
	
	$ret = array(
		'code'=>0,
		'accountDealList'=>$account_deal_list,
		'accountDealTotal' =>$account_deal_total[0]['total_rs'],
		'message'=>'获取成功');
} else {
	$ret = array(
		'code'=>1,
		'message'=>'获取失败');
}

echo json_encode($ret);