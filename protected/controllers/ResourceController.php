<?php
class ResourceController extends ApiBaseController {

	public function actionVerify() {
		parent::baseVerify();

		//参数校验
		$params = $this->initParams();
		if(empty($params)){
			$this->result(ErrorCode::ERROR_PARAMS);
		}

		//if(!isset(Yii::app()->user->user_id)){
		//	$this->result(ErrorCode::ERROR_NOT_LOGIN);
		//}

		$this->kickUser();

		$action_mod = new AdminActions();
        $authUrl = $params["url"] ?? "";
		$is_allow = $action_mod->havepermission($params['resource'], $params['action'],$authUrl);
		if(!$is_allow){
			$this->result(ErrorCode::ERROR_AUTH);
		}
        $params_log = [
            'url'=> isset($params['url']) ? base64_decode($params['url']) : $params['resource'].'/'.$params['action'],
            'ip' => isset($params['ip']) ? $params['ip'] : 1,
            'ssid'  => $params['ssid'],
            'status'=> isset($params['appid']) ?  $params['appid'] : 0,
            'agent' => isset($params['agent']) ? base64_decode($params['agent']) : '',
            'created'   => isset($params['created']) ? $params['created'] : date(Yii::app()->params['formatDateTime'], time()),
        ];

		$this->addLog($params_log);

		$this->result(ErrorCode::SUCCESS);
	}

    /**
     * 获取权限列表
     * resources (例:"/ticket/api/web/v1/")
     * appid (例:52)
     */
    public function actionGetPermission()
    {
        parent::baseVerify();
        //参数校验
        $params = $this->initGetVerifyParams();
        if (empty($params)) {
            $this->result(ErrorCode::ERROR_PARAMS);
        }
        $this->kickUser();
        $action_mod = new AdminActions();
        $list = $action_mod->getPermissionByParams($params);
        $code = ErrorCode::SUCCESS;
        $message = ErrorCode::getDesc($code);
        $data['list'] = $list;
        echo json_encode(['code' => $code, 'message' => $message, 'data' => $data]);
        Yii::app()->end();
    }

    private function initGetVerifyParams()
    {
        $params = array();
        $params_init = array('resources', 'ssid', 'ip', 'appid');
        foreach ($_REQUEST as $k => $v) {
            if (in_array($k, $params_init)) {
                $params[$k] = $v;
            }
        }
        if (empty($params['resources']) || empty($params['ssid'])
        ) {
            return false;
        }
        return $params;
    }

    private function initParams(){
        $params = array();
        $params_init = array('resource', 'action', 'ssid', 'url', 'ip', 'appid', 'agent','created');
        foreach($_REQUEST as $k=>$v){
            if(in_array($k, $params_init)){
                $params[$k] = $v;
            }
        }
        if(empty($params['resource']) ||
            empty($params['action']) ||
            empty($params['ssid'])
        ){
            return false;
        }
        return $params;
    }

    public function addLog($params){
        $is_guest  = Yii::app()->user->getIsGuest();
        $user_id = '';
        if(!$is_guest){
            $user_id = Yii::app()->user->user_id;
        }

        if(!is_numeric($user_id) || !$user_id){
            $user_id = 0;
        }


        $params = array(
            'user_id' => $user_id,
            'username' => Yii::app()->user->getUserName(),
            'ip'	=> $params['ip'],
            'agent' => $params['agent'],
            'status'=> $params['status'],
            'url'   => $params['url'],
            'created'=>$params['created'],
            'ssid'  => $params['ssid']
        );
        EdjLog::info('after action log:'.json_encode($params));
        //纪录访问日志
        //访问人的ip,user_id,访问url,访问时间
        $task = array(
            'method'=>'admin_opt_log',
            'params'=>$params
        );

        Queue::model()->putin($task,'admin_log_queue_682146');
    }
}
