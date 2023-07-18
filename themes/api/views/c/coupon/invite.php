<?php

//邀请码
//这个接口需要客户端配合检查是否还在使用，如果使用中，则需要优化，增加缓存。add by sunhongjing

$invite_count = 0;
$invite_code = '';

$invite = CustomerInvite::model()->getInviteByMacaddress($params['macaddress']);
if ($invite)
{
	$invite_code = $invite->bonus_sn;	
	$parityBit = substr($invite_code, -1);
	$bonus = substr($invite_code, 0, 6);
	$bindCount = CustomerBonus::model()->getCountBonusUsed($bonus, $parityBit);
	if ($bindCount < CustomerInvite::MAX_INVITE_COUNT)
		$invite_count = CustomerInvite::MAX_INVITE_COUNT - $bindCount;	
}
else
{
	$bonusCode = getBonusCode();
	if (!empty($bonusCode))
	{
		$bonus_sn = $bonusCode['bonus_sn'];
		$parityBit = $bonusCode['parityBit'];
		
		$dataInvite = array(); 
		$dataInvite['macaddress'] = $params['macaddress'];
		$dataInvite['source'] = $params['from'];		
		$dataInvite['bonus_sn'] = $bonus_sn . $parityBit;
		$dataInvite['created'] = time();		
		
		$model = new CustomerInvite();
		$model->attributes = $dataInvite;
		if ($model->save())
		{
			$invite_code = $bonus_sn . $parityBit;
			$invite_count = CustomerInvite::MAX_INVITE_COUNT;						
		}
	}
}

if (!empty($invite_code))
{
	$inviteTextTop = MessageText::INVITE_TEXT_TOP;
	$inviteText = MessageText::INVITE_TEXT;
	$shareTextWeibo = sprintf(MessageText::INVITE_WEIBO_SHARE_TEXT, $invite_code); 
	$shareTextSms = sprintf(MessageText::INVITE_SMS_SHARE_TEXT, $invite_code);
	
	$ret = array (
		'code' => '0',
		'message' => '',
		'invite_code' => $invite_code,
		'invite_count' => strval($invite_count),
		'invite_text_top' => $inviteTextTop,
		'invite_text' => $inviteText,
		'weibo_invite_text' => $shareTextWeibo,
		'sms_invite_text' =>$shareTextSms
	);
}
else
{
	$ret = array (
		'code' => '-1',
		'message' => '邀请码获取失败'
	);		
}
echo json_encode($ret);

function getBonusCode()
{
	$bonus_code = array();
	$count = 1;
	$id = CustomerInvite::model()->getNextId();
	if (!empty($id))
	{
		$bonus = BonusType::getBonusType(CustomerInvite::BONUS_TYPE_ID);		
		if ($bonus)
		{				
			do 
			{
				if ($count > 10)
					break;

				$bonus_sn = $bonus->sn_start + $id * 10 + rand(0, 9);
				$parityBit = Helper::CheckCode($bonus_sn);
				if ($parityBit)
				{
					$bonus_code['bonus_sn'] = $bonus_sn;
					$bonus_code['parityBit'] = $parityBit;
				}
				$count ++;
			}
			while(!$parityBit);
		}
	}	
	
	return $bonus_code;
}