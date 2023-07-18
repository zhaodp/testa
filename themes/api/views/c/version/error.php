<?php
/**
 * 全局的app 错误返回view。 传入$message
 * editor sunhongjing 
 */
$message = empty($message) ? array('code'=>1,'message'=>'发生错误啦') : $message;
echo json_encode($message);
//print_r($_REQUEST);