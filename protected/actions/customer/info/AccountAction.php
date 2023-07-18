<?php
/**
 * 账户信息
 * Enter description here ...
 * @author zengzhihai
 *
 */
class AccountAction extends CAction
{
	public function run(){
		//消费记录
		$dataProvider['consumption'] = new CActiveDataProvider('CustomerAccount', array(
			'criteria'=>array(
				'condition'=>'action_type=2',
				'order'=>'id DESC',
			),
		));
		//充值记录
		$dataProvider['recharge'] = new CActiveDataProvider('CustomerAccount', array(
			'criteria'=>array(
				'condition'=>'action_type=1',
				'order'=>'id DESC',
			),
		));
		$this->controller->renderPartial('info/account', array(
			'dataProvider' => $dataProvider,
		));
	}
	
	
	
}
