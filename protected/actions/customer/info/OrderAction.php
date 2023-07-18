<?php
/**
 * 
 * 代驾信息
 * @author zengzhihai
 *
 */
class OrderAction extends CAction
{
	public function run(){
		$id=$_GET['id'];
		$criteria = new CDbCriteria();
		$criteria->compare('user_id', $id);
		//这是是搜索，现在隐藏了
		/*
		if (Yii::app()->request->isAjaxRequest){
			if ($_GET['s']&&$_GET['e']){
				$criteria->addBetweenCondition('created', strtotime($_REQUEST['s']),strtotime($_REQUEST['e']));
			}
		}
		*/
		$order_model = new Order('search');
		$this->controller->render('info/order', array(
			'model' => $order_model,
			'criteria' => $criteria,
		));
	}
	
	
	
}
