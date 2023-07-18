<?php
/**配置审核参数
 * Created by PhpStorm.
 * User: xujiandong
 * Date: 2015/12/28
 * Time: 17:05
 */
class auditEditAction extends CAction{


    public function run($action_id){
        $model = AuditAction::model()->find('action_id=:action_id',array(':action_id'=>$action_id));
        $auditorStr = '';
        $totalAmount = isset($_GET['totalAmount'])?$_GET['totalAmount']:'';
        if( $model ){
            $auditorList = AuditAuditor::model()->findAll('audit_id=:audit_id',array(':audit_id'=>$model->id));
            foreach( $auditorList as $auditor ){
                $auditorStr .= $auditor->auditor.',';
            }
            if( strlen($auditorStr) > 0 ){
                $auditorStr = substr($auditorStr,0,-1);
            }
            $totalAmount = $model->total_amount;
        }
        //更新t_admin_action状态
        $admin_action = AdminActions::model()->findByPk($action_id);
        if( !empty($_GET['params']) && !empty($_GET['auditor']) ){
            $admin_action->audit_status = 1;
            $auditors = explode(',',$_GET['auditor']);
            $data = array();
            $data['action_id'] = $action_id;
            $data['status'] = 1;//1正常，0禁用
            $data['params'] = $_GET['params'];
            $data['operator'] = Yii::app()->user->name;
            $data['update_time'] = date('Y-m-d H:i:s');
            if( !$model ){
                $model = new AuditAction();
                $data['create_time'] = date('Y-m-d H:i:s');
            }else{
                $data['create_time'] = $model->create_time;
            }
            $transaction = $model->getDbConnection()->beginTransaction();
            $model->attributes = $data;
            $model->total_amount = $_GET['totalAmount'];
            $flag = false;
            $result = array('code'=>1,'mes'=>'系统错误');
            $admin_action->audit_status = 1;
            try{
                if( $model->save() && $admin_action->update() ){
                    $flag = true;
                    //删除原先的审核员
                    AuditAuditor::model()->deleteAll('audit_id=:audit_id',array(':audit_id'=>$model->id));
                    //保存当前的审核员
                    foreach( $auditors as $auditor ){
                        $user = AdminUserNew::model()->find('name=:name',array(':name'=>$auditor));
                        if( !$user ){
                            $result['code'] = 1;
                            $result['mes'] = '审核员:'.$auditor.' 不存在';
                            $flag = false;
                            break;
                        }
                        $auditAuditor = new AuditAuditor();
                        $auditAuditor->attributes = array('audit_id'=>$model->id,'auditor'=>$auditor);
                        if( !$auditAuditor->save() ){
                            $result['code'] = 1;
                            $result['mes'] = '保存审核员失败';
                            $flag = false;
                            break;
                        }
                    }
                }
                //加入缓存
                $key = AuditAction::$redisKeyPre.strtoupper($admin_action->controller).'_'.strtoupper($admin_action->action);
                $redis_mod  = RedisHAProxy02::model();
                $count = $redis_mod->set($key,$model->id);
                if( $flag && $count ){
                    $transaction->commit();
                    $result['code'] = 0;
                    $result['mes'] = '保存成功';
                }else{
                    $transaction->rollback();//如果操作失败, 数据回滚
                }
            }catch(Exception $e){
                EdjLog::error('orderChangeBonusSn  error:' . $e->getMessage());
                $transaction->rollback(); //如果操作失败, 数据回滚
            }
            $this->controller->layout = false;
            echo json_encode($result);
            Yii::app()->end();
        }
        $this->controller->layout = '//layouts/main_no_nav';
        echo $this->controller->render('action_edit_audit',array(
            'model'=>$model,
            'totalAmount'=>$totalAmount,
            'auditorStr'=>$auditorStr,
            'id'=>$action_id,
        ));
    }
}