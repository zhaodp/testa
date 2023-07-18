<?php
/**
 *  添加工单分类
 * @author wanglonghuan
 * @date 2014/6/16
 */
class CreateAction extends CAction
{
    public function run()
    {
	$type_id = $_GET['Dict']['code'];
	$name = $_GET['class'];
	
	$supportTicketClass = new SupportTicketClass();
	$supportTicketClass->type_id=$type_id;
	$supportTicketClass->name=$name;
	$supportTicketClass->created=date("Y-m-d H:i:s", time());
	$supportTicketClass->updated=date("Y-m-d H:i:s", time());

	if($supportTicketClass->save()){
	//	 Yii::app()->clientScript->registerScript('alert', 'alert("创建成功");');	
        	$this->controller->redirect(Yii::app()->createUrl('crm/ticketClassList'));
	}
    }
}
