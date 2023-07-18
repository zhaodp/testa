<?php
/**
 * customer_black_list
 * User: clz
 * Date: 14-09-09
 */

class customerBlackListCommand extends CConsoleCommand
{

    public function actionClearExpireTimeBlackList()
    {

        $job_title = '删除超过屏蔽期限的黑名单';
        echo Common::jobBegin($job_title);
        $customer = new Customer();
	$customer->delExpireTimeBlackCustomer();
        echo Common::jobEnd($job_title);
    }




}
