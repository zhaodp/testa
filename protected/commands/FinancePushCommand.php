<?php
/**
 * 财务下发 push 的 command
 *
 * User: tuan
 * Date: 14/12/25
 * Time: 11:36
 */

class FinancePushCommand extends  LoggerExtCommand{

    /**
     * 给司机下发红包
     *
     * @param string $flag=1,第一次推送，$flag=2,第二次推送
     */
    public function actionDispatchEnvelope($flag){
        //1.get list
        $envelopList = $this->getenvelopList($flag);
        //2.send
        $this->sendEnvelopePush($envelopList,$flag);
    }



    /**
     * 把一堆红包发出去
     *
     * @param $envelopList
     * @return bool
     */
    private function sendEnvelopePush($envelopList,$flag){
        if(empty($envelopList)){
            return true;
        }
        $totalCount = count($envelopList);
        EdjLog::info('$flag:'.$flag.';获取'.$totalCount.'行数据');
        $successCount = 0;
        $failCount    = 0;
        foreach($envelopList as $envelop){
            EdjLog::info('$envelop:'.serialize($envelop));
            $driverId = $envelop['drive_id'];
            $params = array();
            $params['name'] = isset($envelop['name']) ? $envelop['name'] : '报单红包';
            $params['type'] = intval(isset($envelop['envelope_type']) ? $envelop['envelope_type'] : '1');
            $params['sn'] = $envelop['id'];
            $params['order_id'] = intval($envelop['order_id']);
            $params['balance'] = intval($envelop['amount']);
            $status = DriverPush::model()->pushDriverEnvelope($driverId, $params);
            EdjLog::info('pushDriverEnvelope $params:'.serialize($params));
            if($status){
                $successCount += 1;
                $result=EnvelopeExtend::model()->updateEnvelopeStatus($envelop['id'],$driverId,$flag==2?2:0);
                EdjLog::info('红包更新状态:'.serialize($result));
            }else{
                $failCount    += 1;
            }
        }

        $format = '发送总数|%s|成功数目|%s|失败数目|%s|';
        $log = sprintf($format, $totalCount, $successCount, $failCount);
        EdjLog::info($log);
    }


    private function getenvelopList($flag){
        if($flag==1){
            return  EnvelopeExtend::model()->getPushList(0);
        }else{
            return  EnvelopeExtend::model()->getPushSecondList(0);
        }

    }

}