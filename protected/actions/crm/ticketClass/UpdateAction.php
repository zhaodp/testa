<?php
/**
 *  修改工单分类信息
 * @author wanglonghuan
 * @date 2014/6/16
 */
class UpdateAction extends CAction
{
    public function run()
    {    
	 $id=$_GET['id'];
	 $this->controller->layout = '//layouts/main_no_nav';
	 $model = SupportTicketClass::model()->findByPk($id);
	 if(isset($_POST['SupportTicketClass'])){
		 $data = $_POST['SupportTicketClass'];
		 //$data =$_POST;
		 //$model->attributes = $data;
		$model->type_id=$data['type_id'];
		$model->name=$data['name'];
		$model->updated=date("Y-m-d H:i:s", time());
		if($model->save()){
			 Yii::app()->clientScript->registerScript('alert', 'alert("保存成功。");');
                    	//关闭弹出框   刷新父类页面
                    echo CHtml::script("window.parent.$('#tcdialog').dialog('close');window.parent.$.fn.yiiGridView.update('tc-grid');");
		}
	 }
	
	 $this->controller->render('update', array(
            'model' => $model,
         ));
    }
}
