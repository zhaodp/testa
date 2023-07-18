<?php
/**
 *  工单列表
 * @author wanglonghuan
 * @date 2013/12/24
 */
class ListAction extends CAction
{
    public function run()
    {
        $model = new SupportTicket('search');
        $model->unsetAttributes();

        if(isset($_GET['SupportTicket'])){


            $model->attributes=$_GET['SupportTicket'];
        }
        else{
            $model->status = 1;
        }
        if(!TicketUser::model()->checkUserExist(Yii::app()->user->name))
        {
            throw new CHttpException(401, '您没有工单权限，请联系后台人员添加工单权限！');
        }
        $dataProvider = $model->search();
        $this->controller->render('ticket_list',array(
            'model' =>$model,
            'dataProvider' => $dataProvider,
        ));
    }
}