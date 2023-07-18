<?php

class DownloadTempletAction extends CAction
{

    public function run(){
        $filename = 'VIP批量充值模板.txt';
        header('Content-Type: text/plain; charset=UTF-8');
        Header('Accept-Ranges: bytes');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        echo '张三,13800138000';
        Yii::app()->end();
    }
}