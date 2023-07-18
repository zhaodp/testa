<?php

/**
 * 统计每日开票情况数据到统计表
 * @author cuiluzhe 2014-12-09
 */
class invoiceReportCommand extends LoggerExtCommand
{
    public function actionInvoiceReport()
    {
        if ($this->invoiceReportProcessed() !== false) {
            EdjLog::info('job already processed today');
            return;
        }

        $error_occured = false;
        $begin_time = strtotime(date('Y-m-d 07:00:00', strtotime("-1 day")));
        $end_time = strtotime(date('Y-m-d 07:00:00'));
        $apply_data = CustomerInvoice::model()->getInvoiceApplyStatics($begin_time, $end_time);
        $wait_data = CustomerInvoice::model()->getInvoiceNotDealStatics();
        $wait_data_vip = CustomerInvoice::model()->getVipInvoiceStatics();
        $seven_day_in = CustomerInvoice::model()->getSevenDaysStatics('in');
        $seven_day_in_vip = CustomerInvoice::model()->getVipSevenDaysStatics('in');
        $seven_day_out = CustomerInvoice::model()->getSevenDaysStatics('out');
        $seven_day_out_vip = CustomerInvoice::model()->getVipSevenDaysStatics('out');
        $report = new CustomerInvoiceReport();
        $report->web = $apply_data['web'];
        $report->app = $apply_data['app'];
        $report->vip = $apply_data['vip'];
        $report->confirm = $apply_data['confirm'];
        $report->finance_confirm = $apply_data['finance_confirm'];
        $report->not_confirm = $wait_data['not_confirm'] - $wait_data_vip;//客服未确认数为:未确认总数-vip手机号申请的发票数
        $report->finance_not_confirm = $wait_data['finance_not_confirm'];
        $report->not_complate_in_sevenday = $seven_day_in['finance_not_confirm'] - $seven_day_in_vip;//未确认总数-vip手机号申请的发票数
        $report->not_complate_out_sevenday = $seven_day_out['finance_not_confirm'] - $seven_day_out_vip;
        $report->cancel = $wait_data['cancel'];
        $report->created = time();
        $res = $report->save();
        if ($res) {
            EdjLog::info('统计完毕');
            if ($this->markInvoiceReportProcessed() !== true) {
                EdjLog::info('markInvoiceReportProcessed failed');
            }
        } else {
            EdjLog::info('error:' . var_dump($report));
            EdjLog::info(json_encode($report->getErrors()));
            $error_occured = true;
        }

        return $error_occured ? -1 : 0;
    }

    private function getInvoiceReportCheckingKey()
    {
        $namespace = 'INVOICEREPORT';
        $prefix = date('Y-m-d');
        return "$namespace|$prefix|invoicereport";
    }

    private function markInvoiceReportProcessed()
    {
        return RedisHAProxy::model()->set($this->getInvoiceReportCheckingKey(), 1, 24 * 60 * 60);
    }

    private function invoiceReportProcessed()
    {
        return RedisHAProxy::model()->get($this->getInvoiceReportCheckingKey()) !== false;
    }
}
