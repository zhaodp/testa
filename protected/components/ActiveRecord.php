<?php
/**
 * ActiveRecord
 *
 * @author duke 基础ar 类 添加一些基础方法
 */
class ActiveRecord extends CActiveRecord {

    
    /**
     *  model 统一返回数据格式
     * @param string $dbHandlerName
     */
    public function returnMsg($code = 1, $msg='', $data=array() ){
        return array('code'=>$code,'data'=>$data, 'message'=>$msg);
    }
}
