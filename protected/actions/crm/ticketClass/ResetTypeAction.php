<?php
/**
 *  修改工单分类信息
 * @author wanglonghuan
 * @date 2014/6/16
 */
class ResetTypeAction extends CAction
{
    public function run()
    {    
	 $id=$_GET['id'];
         $type_id=$_GET['type_id'];
	 $tclass=$_GET['class'];
	
	 $model = SupportTicket::model()->findByPk($id);	 
	 $model->type=$type_id;
 	 $model->class=$tclass;
	 if($model->save()){
		echo '1';
	 }else{
		echo '0';
	}
    }
}
