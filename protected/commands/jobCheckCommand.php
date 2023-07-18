<?php

// 0 1 * * * /usr/bin/php /sp_edaijia/www/v2/protected/yiic jobcheck

class jobCheckCommand extends LoggerExtCommand 
{

    private $failed_job_summary = array();
    
    // 这种几分钟运行一次的crontab是不会在sys_event_log表里记录事件日志的
    private function isMinuteCrontab($cron)
    {
        list($minute, $hour, $day_of_month, $month, $day_of_week) = explode(' ', $cron);    
        return !empty($minute) && strpos($minute, '/') !== false;
    }

    private function checkJobStatus($job, $logs)
    {
        foreach ($logs as $log) {
            switch ($log['event_type']) {
            case ConsoleApplicationBehavior::JOB_START:
                $job_start_time = $log['event_time']; 
                break;
            case ConsoleApplicationBehavior::JOB_STOP:
                $job_stop_time = $log['event_time'];
                break;
            case ConsoleApplicationBehavior::JOB_ERROR:
            case ConsoleApplicationBehavior::JOB_EXCEPTION:
            case ConsoleApplicationBehavior::JOB_KILLED:
                $job_error = $log['event_description'];
                $job_stop_time = $log['event_time'];
                break;
            }
        }

        if (!empty($job_error)) {
            $this->failed_job_summary[] = array(
                'cronId' => $job['cronId'],
                'task' =>  $job['task'],
                'owner' => $job['owner'],
                'start_time' => $job_start_time,
                'end_time' => $job_stop_time,
                'error' => $job_error
            );
        } 
    }

    public function actionIndex()
    {
        $yesterday = date('Y-m-d 00:00:00', strtotime("-1 day"));
        $today = date('Y-m-d 00:00:00');

        $command = Yii::app()->dbsys->createCommand("SELECT cronId, task, mhdmd, owner FROM sys_crontab WHERE active = 1");
        $jobs = $command->queryAll();
        foreach ($jobs as $job) {
            if ($this->isMinuteCrontab($job['mhdmd']) === true)
                continue;

            $sql = "SELECT event_type, event_time, event_description FROM sys_event_log WHERE event_time > '$yesterday' and event_time < '$today' and cronid = " . $job['cronId'];

            $command = Yii::app()->dbsys->createCommand($sql);
            $logs = $command->queryAll();
            if (!empty($logs)) {
                $this->checkJobStatus($job, $logs);
            }
        }

        $this->reportJobStatus();
    }

    private function reportJobStatus()
    {
        $content = $this->formatFailedJobSummary();

        if (empty($content)) {
            return;
        }

        // 等配置中心上线之后，收件人就从Redis里取，方便在线更改收件人
        $config = new Redis();
        $config->connect("redishaproxy.edaijia-inc.cn",22121); 
        $receiver = $config->get('job_check_receiver');
        if (!empty($receiver)) {
            $receiver = json_decode($receiver);
        } else {
            $receiver = array('edaijia.bu@edaijia-inc.cn', 'order@edaijia-inc.cn');
        }

        Mail::sendMail(
            $receiver,
            $content,
            date('Y-m-d')." failed job summary"
        ); 
    }

    private function formatFailedJobSummary()
    {
        if (empty($this->failed_job_summary)) {
            return "";
        }

        $content = <<<'EOD'
            <table style="text-align:left;border:1px #929292 solid;font-size:13px;" cellpadding="9" cellspacing="0" width="100%">
                <thead>
                    <tr style="background:#5577AA;color:#FFF;">
		                <td align="right" width="9%">cronId</td>
                        <td align="right" width="9%">job名称</td>
                        <td align="right" width="9%">负责人</td>
                        <td align="right" width="9%">开始时间</td>
                        <td align="right" width="9%">结束时间</td>
                        <td align="right" width="55%">错误原因</td>
                    </tr>
                </thead>
                <tbody>
EOD;

        foreach ($this->failed_job_summary as $summary) {
            $line = "";
            $line .= '<tr>';
            $line .= '<td align="right" style="font-size:12px">' . $summary['cronId']  . '</td>';
            $line .= '<td align="right" style="font-size:12px">' . $summary['task']  . '</td>';
            $line .= '<td align="right" style="font-size:12px">' . $summary['owner']  . '</td>';
            $line .= '<td align="right" style="font-size:12px">' . $summary['start_time']  . '</td>';
            $line .= '<td align="right" style="font-size:12px">' . $summary['end_time']  . '</td>';
            $line .= '<td align="right" style="font-size:12px">' . $summary['error']  . '</td>';
            $line .= '</tr>';
            $content .= $line;
        }

        $content .= '</tbody>';
        $content .= '</table>';
        return $content;
    }
}
