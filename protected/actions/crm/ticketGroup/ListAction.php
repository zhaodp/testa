<?php
/**
 *  工单 操作用户 list wanglonghuan 2013.12.23
 */
class ListAction extends CAction
{
    public function run()
    {
        $users = new TicketUser('search');
        $users->unsetAttributes();

        $this->controller->render('ticket_group_list',array(
            'userModels' => $users,
        ));
    }
}