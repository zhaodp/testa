<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-10-10
 * Time: 上午11:01
 * To change this template use File | Settings | File Templates.
 */
class Controller extends LoggerExtController {
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout='/layouts/main';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array ();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array ();

    /**
     * 调用edaijia api server的key
     * @var string
     */
    public $api_key;

    /**
     * render输出的格式定义，支持html,json
     * @var string
     */
    public $format;

    public function init() {
        parent::init();

        $host = $_SERVER['HTTP_HOST'];
        if ($host=='www.edaijia.cn'||$host=='www.edaijia.cc') {
            if (Yii::app()->isMobile->isMobileBrowser()==true) {
                Yii::app()->theme = 'mobile';
            }
        }
    }

    public function beforeAction($action) {
        return parent::beforeAction($action);
        /*
        $controller_id = $this->getUniqueId();
        $action_id = $this->defaultAction;

        if (Yii::app()->user->getIsGuest()) {
            Yii::app()->user->setState('roles', array (
                AdminGroup::model()->getID('*')));
        }


        if ($action!==null) {
            $action_id = $action->getId();
        }

        if( defined ( "CURRENT_ENV" ) && 'dev' == CURRENT_ENV ){
            $is_allow = 1;
        }else{
            $is_allow = AdminActions::model()->havepermission($controller_id, $action_id);
        }

        //echo $controller_id;echo  $action_id;echo $is_allow;die();
        if ($is_allow==1) {
            return parent::beforeAction($action);
        }

        $url = $this->createAbsoluteUrl('site/login', array (
            'controller'=>$controller_id,
            'action'=>$action_id));
        $this->redirect($url);
        */
        //		$this->render('//site/error', array (
        //		'code'=>-9000,
        //		'message'=>"未被授权访问此功能，请联系管理员。$controller_id, $action_id"));
        //		throw new Exception("未被授权访问此功能，请联系管理员。$controller_id, $action_id", -9001, null);
    }

    /**
     * 对www.edaijia.cn/v2 的请求纪录访问日志
     *
     * @author sunhongjing 2013-05-30
     * @param unknown_type $action
     */
    public function afterAction($action){

        /*
        $server = Yii::app()->request->getServerName();
        if( 0 == strcasecmp($server, 'www.edaijia.cn') ){
            $url = Yii::app()->request->getUrl();
            if( 0===strpos($url, '/v2') ){

                //对某些方法不纪录访问日志
                $controller = Yii::app()->controller->getId();  // controller
                $method = $action->getId(); // action

                if( 'notice' == $controller ){
                    return parent::afterAction($action);
                }

                if( 'order' == $controller  && 'dispatch'==$method ){
                    return parent::afterAction($action);
                }

                $params = array (
                    'username'=>Yii::app()->user->getUserName(),
                    'ip'	=> Yii::app()->request->getUserHostAddress(),
                    'agent' => Yii::app()->request->getUserAgent(),
                    'status'=> 2,
                    'url'   => Yii::app()->request->getUrl(),
                    'created'=>date(Yii::app()->params['formatDateTime'], time())
                );
                //纪录访问日志
                //访问人的ip,user_id,访问url,访问时间
                $task=array(
                    'method'=>'admin_opt_log',
                    'params'=>$params
                );
                Queue::model()->dumplog($task);
            }
        }
        */
        return parent::afterAction($action);
    }

    /**
     * 重载父类过滤方法
     * @author sunhongjing 2013-05-12
     * @return array
     */
    public function filters()
    {
        //只针对www.edaijia.cn域名增加兼职登陆限制
        /*
        $server = Yii::app()->request->getServerName();
        if( 0 == strcasecmp($server, 'www.edaijia.cn') ){
            return array(
                //'checkPartTimeUserIp',
            );
        }else{
            return array();
        }
        */
    }

    /**
     * 用户过滤器方法，对兼职用户的访问ip进行过滤
     *
     * @param unknown_type $filterChain  action object
     * @author sunhongjing 2013-05-11
     */
    public function FilterCheckPartTimeUserIp($filterChain){

        //1得到当前登录用户信息,判断是否兼职，如果兼职，只允许在固定ip登陆 add by sunhongjing 2013-05-11
        //如果是guest访问，那么放过让beforeAction去处理，如果是登陆用户，就直接判断了
        if( ! Yii::app()->user->getIsGuest()  ){
            $userType = Yii::app()->user->getUserType() ;
            //var_dump($userType);
            //公司员工 $userType = 2
            if( 2 == $userType ){
                //0为兼职，1为全职，-1为非登陆用户
                $callCenterUserType = Yii::app()->user->getCallCenterUserType();
                //var_dump($callCenterUserType);
                if( 0==$callCenterUserType ){
                    //58.30.38.132  124.204.239.0/24 124.204.203.40
                    $user_host_ip = Yii::app()->request->getUserHostAddress();
                    if( '127.0.0.1'!=$user_host_ip && '58.30.38.132'!=$user_host_ip
                        && '124.204.239' != substr($user_host_ip,0,strrpos($user_host_ip,'.') )
                        && '124.204.203' != substr($user_host_ip,0,strrpos($user_host_ip,'.') )
                    ){
                        Yii::app()->user->logout(true);
                        echo <<<eof
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta charset="UTF-8" />
</head>
<body>
<div>抱歉，您当前的位置禁止登陆。</div>
</body>
</html>
eof;
                        exit;
                    }
                }
            }
        }

        $filterChain->run();

    }


    public function render($view, $data = null, $return = false) {
        if ($this->format=='json') {
            $this->layout = false;
            header('Content-Type: application/json; charset=utf-8');

            $ret = parent::render($view, $data, true);
            if (isset($_REQUEST['callback'])) {
                $callback = $_REQUEST['callback'];
                echo $callback.'('.$ret.')';
            } else {
                echo $ret;
            }
            Yii::app()->end();
        } else {
            parent::render($view, $data, $return);
        }
    }

    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }
}
