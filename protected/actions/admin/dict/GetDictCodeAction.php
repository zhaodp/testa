<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-9-2
 * Time: 下午5:48
 */
class GetDictCodeAction extends CAction
{

    public function run()
    {
        $array = array('status' => 0,'msg' => 'error！');
        if(isset($_GET['dictname'])){
            $dictname = trim($_GET['dictname']);
            $result = Dict::model()->findAll('dictname=:dictname ORDER BY postion DESC', array(':dictname'=>$dictname));
            if($result){
                $array['status'] = 1;
                $array['msg'] = 'success!';
                $array['code']= $result[0]['postion'] + 1;
                echo CJSON::encode($array);
            }else{
                echo CJSON::encode($array);
            }
        }else{
            echo CJSON::encode($array);
        }


    }
}