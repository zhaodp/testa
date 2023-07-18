<?php

class BankCustomerImportCommand extends LoggerExtCommand
{

    /**
     *
     * php yiic.php BankCustomerImport Import
     */
    public function actionImport($fileName, $flag = 1)
    {
        $success_num = 0;
        $error_num = 0;
        $re_num = 0;
        $content = file_get_contents($fileName);
        $contentArr = preg_split('/[\r\n]+/', $content);

        foreach ($contentArr as $item) {
            EdjLog::info('记录:' . $item);
            $arr = explode('	', $item);
            if(count($arr)>4){
                $model = new BankCustomerBonus();
                $model->name=trim($arr[0]);
                $model->phone = trim($arr[1]);
                $model->card_id = trim($arr[2]);
                $isExit = $model->checkCardIsExit($model->card_id,$model->name,$model->phone);
                if ($isExit == 0) {
                    $model->card_number = trim($arr[3]);
                    $model->club_number = trim($arr[4]);
                    $model->create_date = date('Y-m-d H:m;s');
                    $model->last_changed_date = date('Y-m-d H:m;s');
                    $model->is_vip =VipPhone::model()-> getPrimary($model->phone) ? 1 : 0;
                    if ($model->save()) {
                        $success_num++;
                    } else {
                        $error_num++;
                        EdjLog::error(json_encode($model->getErrors()));
                    }
                } elseif ($isExit == 1) {
                    $re_num++;
                    EdjLog::info('记录:' . $item . ';重复.');
                    continue;
                } else {
                    EdjLog::error('记录:' . $item . ';error.');
                }
            }else{
                EdjLog::error('记录:' . $item . ';error.数组spilt error:');
            }
        }
        EdjLog::info($flag . '号进程共有数据:' . count($contentArr) . '条记录;成功:' . $success_num . ';失败:' . $error_num. ';重复:'.$re_num);
    }
}