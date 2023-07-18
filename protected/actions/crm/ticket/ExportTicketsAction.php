<?php

/**
 * User: cuiluzhe
 * Date: 15-1-7
 * Time: 下午16:29
 */
Yii::import('application.vendors.phpexcel.*');
class ExportTicketsAction extends CAction
{
    public function run()
    {

        if (!TicketUser::model()->checkUserExist(Yii::app()->user->name)) {
            throw new CHttpException(401, '您没有工单权限，请联系后台人员添加工单权限！');
        }
        $model = new SupportTicket();
        $tickets = $model->getSupportTicketList($_GET);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->getProtection()->setSheet(false);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '工单ID')
            ->setCellValue('B1', '城市')
            ->setCellValue('C1', '类型')
            ->setCellValue('D1', '分类')
            ->setCellValue('E1', '工单内容')
            ->setCellValue('F1', '申报人')
            ->setCellValue('G1', '设备')
            ->setCellValue('H1', '操作系统版本')
            ->setCellValue('I1', '版本号')
            ->setCellValue('J1', '状态')
            ->setCellValue('K1', '创建日期')
            ->setCellValue('L1', '最后处理人')
            ->setCellValue('M1', '最后处理时间')
            ->setCellValue('N1', '结束时间');
        $i = 2;
        foreach ($tickets as $ticket) {
            $class = $ticket['class'] == '0' ? '' : SupportTicketClass::model()->findByPk($ticket["class"])->name;
            $status = $ticket['status'] == '0' ? "全部" :  SupportTicket::$statusList[$ticket['status']];
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $ticket['id'])
                ->setCellValue('B' . $i, Dict::item("city", $ticket['city_id']))
                ->setCellValue('C' . $i, Dict::item("ticket_category", $ticket['type']))
                ->setCellValue('D' . $i, $class)
                ->setCellValue('E' . $i, $ticket['content'])
                ->setCellValue('F' . $i, $ticket['driver_id'])
                ->setCellValue('G' . $i, $ticket['device'])
                ->setCellValue('H' . $i, $ticket['os'])
                ->setCellValue('I' . $i, $ticket['version'])
                ->setCellValue('J' . $i, $status)
                ->setCellValue('K' . $i, $ticket['create_time'])
                ->setCellValue('L' . $i, $ticket['last_reply_user'])
                ->setCellValue('M' . $i, $ticket['last_reply_time'])
                ->setCellValue('N' . $i, $ticket['close_time']);
            $i += 1;
        }
        $filename = 'tickets' . time() . '.csv';
        header('Content-Type : application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}
