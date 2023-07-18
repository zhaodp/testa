<?php
/**
 * 副卡信息
 * @author zengzhihai
 *
 */
class SubAdminAction extends CAction
{
	public function run(){
		if ($_GET&&!empty($_GET['id'])){
			$id=$_GET['id'];
			$sql1 = "SELECT vip_card FROM {{customer_main}} WHERE vip_main=1 AND id=:id";
			$command1 = Yii::app ()->db_readonly->createCommand ($sql1)->bindValue(':id',$id);
			$otherCard = $command1->queryScalar();
			$sql2 = "SELECT name,phone,status,id FROM {{customer_main}} WHERE vip_main=0 AND vip_card=:otherCard";
			$command2 = Yii::app ()->db_readonly->createCommand ($sql2)->bindValue(':otherCard',$otherCard);
			$models = $command2->queryAll();
			$this->controller->render('info/sub', array(
				'models' => $models,
				'vip_card_'=>$otherCard,
			));
		}
	}
	
	
	
}