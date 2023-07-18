<?php
/**
 *  屏蔽/启用工单分类
 * @author wanglonghuan
 * @date 2014/6/16
 */
class HiddenAction extends CAction
{
    public function run()
    {
	if(isset($_GET['id'])&&isset($_GET['status'])){
		$id=$_GET['id'];
		$status=$_GET['status'];
                $attr = array('status'=>$status);
		SupportTicketClass::model()->updateByPk($id, $attr);
		$this->controller->redirect(Yii::app()->createUrl('crm/ticketClassList'));
	}
    }
}
