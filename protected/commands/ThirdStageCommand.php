<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/3/30
 * Time: 17:17
 */
Yii::import('application.models.third_stage.*');
Yii::import('application.models.third_stage.bill.*');
class ThirdStageCommand extends LoggerExtCommand{

    /**
     * 建立 from 和名称对应
     *
     * @param $fileName
     */
    public function actionInsertFrom($fileName){
        $dictName = ThirdDict::DICT_NAME_FROM_NAME;
        $content = file_get_contents($fileName);
        $contentArr = preg_split('/[\r\n]+/', $content);
        foreach ($contentArr as $item) {
            $arr = preg_split('/[\s]+/', $item);
            $from = trim($arr[0]);
            $name = trim($arr[1]);
            $this->actionInitDict($dictName, $from, $name);
        }
    }

    /**
     * 字典里面插入key/value
     *
     * @param $dictName
     * @param $key
     * @param $value
     */
    public function actionInitDict($dictName, $key, $value){
        ThirdDict::model()->createInstance($dictName, $key, $value);
    }

    public function actionDateSummary($channel){
        $dateArray = array(
        );
        for($i = 0 ; $i < 60; $i ++){
            $date = date('Y-m-d', strtotime('-'.$i.' days'));
            $dateArray[] = $date;
        }

        foreach($dateArray as $date){
            $orderCount = rand(10, 20);
            $customerCount = rand(5, 10);
            $inviteCount = rand(0, 4);
            $tmp = array(
                'orderCount' => $orderCount,
                'customerCount' => $customerCount,
                'inviteCount' => $inviteCount,
                'date'        => $date,
                'channel'     => $channel,
            );
            DataSummary::model()->createInstance($tmp);
        }
    }

    /**
     * 生成每月的账单
     *
     * @param $channel
     * @param $month
     */
    public function actionMonth($channel, $month){
        $dataSummary = DataSummary::model()->queryByTime(strtotime('2015-04-01'), strtotime('2015-04-20'), true, $channel);
        $userId = ThirdUser::model()->getUserIdByChannel($channel);
        $billInstance = UserBillInstance::model()->getLatestActiveInstance($userId);
        $model = new BillProcess($billInstance, $dataSummary);
        $model->calculator($month);
        ThirdBillStatus::model()->createInstance($channel, $month);
    }

    public function actionBillStatus($channel, $month){
        ThirdBillStatus::model()->createInstance($channel, $month);
    }



    public function actionDateBill($userId, $type, $cast, $month, $created = 0){
        ThirdMonthBill::model()->createInstance($userId, $type, $cast, $month, $created);
    }

    private function getDataSummaryChannel(){
        $sql = 'select distinct channel from data_summary';
        return Yii::app()->db_third->createCommand()->queryAll();
    }


}