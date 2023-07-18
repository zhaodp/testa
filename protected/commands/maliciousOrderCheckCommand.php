<?php

// 0 1 * * * /usr/bin/php /sp_edaijia/www/v2/protected/yiic maliciousOrderCheck

class maliciousOrderCheckCommand extends LoggerExtCommand 
{

    public function actionIndex()
    {
        $summary_table = $this->createSummaryTable();

        $malicious_table = $this->createMaliciousTable();

        $this->report($summary_table, $malicious_table);
    }

    private function createMaliciousTable()
    {
        $malicious_table = <<<'EOD'
            <table style="text-align:left;border:1px #929292 solid;font-size:13px;" cellpadding="9" cellspacing="0" width="50%">
                <thead>
                    <tr style="background:#5577AA;color:#FFF;">
                        <td align="right" width="25%">用户/司机 ID</td>
                        <td align="right" width="25%">销单时间</td>
                    </tr>
                </thead>
                <tbody>
EOD;

        $yesterday = date('Y-m-d 00:00:00', strtotime("-1 day"));
        $today = date('Y-m-d 00:00:00');

        $command = Yii::app()->dborder->createCommand("SELECT * FROM t_malicious_order WHERE record_time > '$yesterday' and record_time < '$today'");
        $logs = $command->queryAll();

        foreach ($logs as $log) {
            $start_of_the_week = date('Y-m-d H:i:s', strtotime("-7 day", strtotime($log['record_time'])));
            $log_time = $log['record_time'];
            $user_id = $log['user_id'];

            $sql = "SELECT count(*) as c FROM t_malicious_order WHERE user_id = '$user_id' and record_time > '$start_of_the_week' and record_time < '$log_time'";
            $command = Yii::app()->dborder->createCommand($sql);
            $count = $command->queryAll();

            // 在这条销单记录之前的一周之内如果有3条以上的记录，认为是一次恶意销单
            if (!empty($count) && intval($count[0]['c']) >= 2) {
                $line = "";
                $line .= '<tr>';
                $line .= '<td align="right" style="font-size:12px">' . $log['user_id']  . '</td>';
                $line .= '<td align="right" style="font-size:12px">' . $log['record_time']  . '</td>';
                $line .= '</tr>';
                $malicious_table .= $line;
            }
        }

        $malicious_table .= '</tbody>';
        $malicious_table .= '</table>';
        return $malicious_table;
    }

    private function createSummaryTable()
    {
        $yesterday = date('Y-m-d 00:00:00', strtotime("-1 day"));
        $today = date('Y-m-d 00:00:00');
        $command = Yii::app()->dborder->createCommand("SELECT count(*) as c, user_id FROM t_malicious_order WHERE record_time > '$yesterday' and record_time < '$today' group by user_id");
        $summary = $command->queryAll();

        $summary_table = <<<'EOD'
            <table style="text-align:left;border:1px #929292 solid;font-size:13px;" cellpadding="9" cellspacing="0" width="50%">
                <thead>
                    <tr style="background:#5577AA;color:#FFF;">
                        <td align="right" width="25%">用户/司机 ID</td>
                        <td align="right" width="25%">销单次数</td>
                    </tr>
                </thead>
                <tbody>
EOD;

        foreach ($summary as $s) {
            $line = "";
            $line .= '<tr>';
            $line .= '<td align="right" style="font-size:12px">' . $s['user_id']  . '</td>';
            $line .= '<td align="right" style="font-size:12px">' . $s['c']  . '</td>';
            $line .= '</tr>';
            $summary_table .= $line;
        }

        $summary_table .= '</tbody>';
        $summary_table .= '</table>';
        return $summary_table;
    }

    private function report($summary_table, $malicious_table)
    {
        $yesterday = date('Y-m-d', strtotime("-1 day"));
        $content = "<h1>$yesterday"." 发起恶意销单的用户/司机</h1>".$malicious_table;
        $content .= "<h1>$yesterday"." 在司机就位后发起的销单记录</h1>".$summary_table;

        Mail::sendMail(
            array('shida@edaijia-inc.cn', 'wangzichao@edaijia-inc.cn', 'zengkun@edaijia-inc.cn',),
            $content,
            date('Y-m-d')." malicious order report"
        ); 
    }

}
