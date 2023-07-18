<?php

class callConfigTestCommand extends CConsoleCommand
{

    public function actionTest($phone)
    {
        CallConfig::model()->callHandle($phone);
    }

    public function actionIvrTest($phone)
    {
        IvrConfig::model()->callHandle($phone);
    }


    public function actionTestCall($logId)
    {
        $log_flag = CallcenterLog::model()->findByPk($logId);
        if (empty($log_flag)) {
            echo "logid is not exist ";
            return;
        }
        $call_id = $log_flag->getPrimaryKey();
        //振铃(20s以上)未接听，ivr放弃
        if ($log_flag->State == 'leak') {
            echo "进入ivr放弃处理\n";
            if ((strtotime($log_flag->End) - strtotime($log_flag->Ring)) > 20) {
                $task = array(
                    'method' => 'callcenter_mobile',
                    'params' => array(
                        'id' => $call_id,
                        'state' => $log_flag->State,
                        'phone' => $log_flag->CallNo
                    )
                );
                Queue::model()->putin($task, 'dumplog');
            }
        } elseif ($log_flag->CallType && $log_flag->State == 'queueLeak') {
            echo "进入未接听处理\n";
            $task = array(
                'method' => 'callcenter_mobile',
                'params' => array(
                    'id' => $call_id,
                    'state' => $log_flag->State,
                    'phone' => $log_flag->CallNo
                )
            );
            Queue::model()->putin($task, 'dumplog');
        } else {
            echo "无需处理\n";
        }
    }

    public function actionClearCache($phone)
    {
        $cache_key = "[callhandle_sms]" . $phone;
        Yii::app()->cache->delete($cache_key);
        Putil::report($phone . " 已清除");
    }
}