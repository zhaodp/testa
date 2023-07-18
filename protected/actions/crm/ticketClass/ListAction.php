<?php
/**
 *  工单分类列表
 * @author wanglonghuan
 * @date 2013/12/24
 */
class ListAction extends CAction
{
    public function run()
    {
	$model = new Dict();
        $model->unsetAttributes();
	
	$classModel = new SupportTicketClass();
	$dataProvider = $classModel->search();

        $this->controller->render('ticket_class_list',array(
            'model' =>$model,
	    'dataProvider' =>$dataProvider,
        ));
    }
}
