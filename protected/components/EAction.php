<?php
class EAction extends CAction {

    public function alertWindow($msg,$url=''){
        echo "<meta charset='utf-8'/>";
        echo "<script type='text/javascript'>alert('{$msg}');";
        if($url) echo 'window.location="'.$url.'"';
        else echo "history.back();";
        echo "</script>";
        Yii::app()->end();
    }
}