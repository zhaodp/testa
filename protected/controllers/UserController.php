<?php
/**
 * 用户接口API
 */
class UserController extends ApiBaseController {

	public function actionGetInfoBySsid() {
		parent::baseVerify();

		//参数校验
		$params = $this->initParams();
		if(empty($params)){
			$this->result(ErrorCode::ERROR_PARAMS);
		}

		if(!isset(Yii::app()->user->user_id)){
			$this->result(ErrorCode::ERROR_NOT_LOGIN);
		}

		$this->kickUser();

		$this->result(ErrorCode::SUCCESS);
	}

    public function actionAuth(){
        $username = isset($_GET['username']) ? $_GET['username'] : '';
        $password = isset($_GET['password']) ? $_GET['password'] : '';
        $token = isset($_GET['token']) ? $_GET['token'] : '';
        $identity = new UserIdentity($username, $password);
        if (!$identity->authenticate()){
            $this->result(ErrorCode::ERROR_PWD);
        }

        $user_info = AdminUserNew::model()->find('name = :name',array(':name'=>$username));
        if(!$user_info || !$user_info->secure_key) {
            $this->result(ErrorCode::ERROR_NO_SECURE_KEY);
        }
        $tfa = new TFA();
        $secure_key = $tfa->decrypt($user_info->secure_key);
        $res = GoogleAuthenticator::checkCode($secure_key, $token);
        if (!$res){
            EdjLog::error('双因子认证返回错误 id:'.$user_info->id.' name:'.$user_info->name.' secure_key: '. $user_info->secure_key. ' decrypted key: '. $secure_key);
            $this->result(ErrorCode::ERROR_PWD);
        }
        $user = $user_info->attributes;
        unset($user['password']);
        unset($user['secure_key']);
        echo json_encode(array(
            'code' => ErrorCode::SUCCESS,
            'message' => ErrorCode::getDesc(ErrorCode::SUCCESS),
            'user' => $user,
        ));
    }

	private function initParams(){
		$params = array();
		$params_init = array('ssid');
		foreach($_REQUEST as $k=>$v){
			if(in_array($k, $params_init)){
				$params[$k] = $v;
			}
		}
		if(empty($params['ssid'])){
			return false;
		}
		return $params;
	}

    public function actionGetAuthList()
    {
        static $DEFAULT_PAGE_SIZE = 100;
        static $DEFAULT_PAGE_NO = 1;
        static $DEFAULT_APP_ID = -1;
        static $DEFAULT_USER_ID = -1;

        // 接口安全性检查
        $user_id = isset($_GET['userid']) ? intval($_GET['userid']) : $DEFAULT_USER_ID;
        $page_no = isset($_GET['pageno']) ? intval($_GET['pageno']) : $DEFAULT_PAGE_NO;
        $page_size = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $DEFAULT_PAGE_SIZE;
        $app_id = isset($_GET['appid']) ? intval($_GET['appid']) : $DEFAULT_APP_ID;

        /*
		if (Yii::app()->user->getIsGuest()) {
			$this->result(ErrorCode::ERROR_NOT_LOGIN);
		}
        */

        if ($user_id < 0) {
            $this->result(ErrorCode::ERROR_PARAMS);
        }

        if ($page_no < 1) {
            $page_no = $DEFAULT_PAGE_NO;
        }

        if ($page_size < 1) {
            $page_size = $DEFAULT_PAGE_SIZE;
        }

        $offset = ($page_no - 1) * $page_size;

        $this->kickUser();

        // 查询MySQL
        $sql = "select SQL_CALC_FOUND_ROWS C.app_id, C.controller, C.action from t_admin_user2role as A inner join 
            t_admin_role2action as B on (A.role_id = B.role_id) inner join 
            t_admin_action as C on (B.action_id = C.id) where A.user_id = $user_id ";

        if ($app_id > 0) {
            $sql .= "and C.app_id = $app_id ";
        }

        $sql .= "limit $offset, $page_size";

        $auth_list = Yii::app()->dbadmin_readonly->createcommand($sql)->queryAll();

        $rows = Yii::app()->dbadmin_readonly->createcommand('select found_rows() as total')->queryRow();

        // 返回查询结果
        if (empty($rows)) {
            echo json_encode(array('code' => -1, 'total' => -1, 'pageno' => $page_no, 'pagesize' => $page_size, 'data' => array()));
        } else {
            echo json_encode(array('code' => 1, 'total' => intval($rows['total']), 'pageno' => $page_no, 'pagesize' => $page_size, 'data' => $auth_list));
        }

        return;
    }

    /**
     * 获取用户的权限目录（某个或全部系统的），提供给服务端的接口
     * @param $userid 用户ID
     * @param $appid 系统ID
     * @return json
     *  {
            "label": "\u7cfb\u7edf",
            "labelId": "80",
            "className": "edj-v2-ico-sys",
            "hasSub": true,
            "navList": [
                {
                    "label": "\u8ba2\u5355\u72b6\u6001\u67e5\u8be2",
                    "labelId": "80.196",
                    "link": "http:\/\/www.edaijia.cn\/v2\/index.php?r=order\/status",
                    "is_target": "0",
                    "hasSub": false
                },
                {
                    "label": "\u6d41\u91cf\u7edf\u8ba1",
                    "labelId": "80.130",
                    "link": "http:\/\/www.edaijia.cn\/v2\/index.php?r=driver\/appTraffic",
                    "is_target": "0",
                    "hasSub": false
                },
            ]
        }
     */
    public function actionGetAuthMenu() {
        $res = array('code'=>1,'msg'=>'','data'=>array());
        $user_id = isset($_GET['userid']) ? intval($_GET['userid']) : '';
        $app_id = isset($_GET['appid']) ? intval($_GET['appid']) : '';

        if (empty($user_id)) {
            $res['code'] = 0;
            $res['msg'] = '参数错误';
            echo json_encode($res);
            Yii::app()->end();
        }

//        $this->kickUser();
        $appv2 = AdminApp::model()->findByPk(2);
        $v2url = $appv2->url;
        // 菜单集合
        $menus = array();
        $data = array();

        $menuList = Menu::model()->getMenuArr($user_id,$app_id);//获取menulist
        //print_r($menuList);die;
        if ($menuList && is_array($menuList)) {
            foreach ($menuList as $m) {
                $menu = Menu::initMenu($m['name'], $m['id'], Menu::getClassNameByLabel($m['name']));

                if(!empty($m['sub'])){
                    //构建二级菜单
                    $navList = array();
                    foreach ($m['sub'] as $s) {
                        $menuSub = Menu::initMenu($s['name'], $m['id'].'.'.$s['id'], '', $s['app_url'].'/'.$s['action_url'], $s['is_target']);
                        if(!empty($s['third'])){
                            $navListSub = array();
                            foreach($s['third'] as $third){
                                $navListSub[] = Menu::initMenu($third['name'],$m['id'].'.'.$s['id'].'.'.$third['id'], '', $third['app_url'].'/'.$third['action_url'], $third['is_target']);
                            }
                            $menuSub['hasSub'] = true;
                            $menuSub['navList'] = $navListSub;
                        }
                        $navList[] = $menuSub;
                    }
                } else {
                    $navList = array();
                }
                $menu['hasSub'] = true;
                $menu['navList'] = $navList;

                $menus[] = $menu;
            }
        }

        $data['menus'] = $menus;
        $data['v2url'] = $v2url;
        $res['data'] = $data;
        echo json_encode($res);
    }
}
