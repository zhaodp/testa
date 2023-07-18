<?php
/**配置审核参数
 * Created by PhpStorm.
 * User: xujiandong
 * Date: 2015/12/28
 * Time: 17:05
 */
class auditDelAction extends CAction{


    public function run($action_id){
        $model = AuditAction::model()->find('action_id=:action_id',array(':action_id'=>$action_id));
        $result = array('code'=>1,'mes'=>'fail');
        if( $model ){
            AuditAction::model()->deleteAll('action_id=:action_id',array(':action_id'=>$action_id));
            AuditAuditor::model()->deleteAll('audit_id=:audit_id',array(':audit_id'=>$model->id));
            $admionAction = AdminActions::model()->findByPk($action_id);
            $admionAction->audit_status = 0;
            //删除缓存
            $key = AuditAction::$redisKeyPre.strtoupper($admionAction->controller).'_'.strtoupper($admionAction->action);
            $redis_mod  = RedisHAProxy02::model();
            $count = $redis_mod->del($key);
            if( $admionAction->update() ){
                $result = array('code'=>0,'mes'=>'success');
            }
        }
        $this->controller->layout = false;
        echo json_encode($result);
        Yii::app()->end();
    }
}