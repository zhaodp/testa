<?php

class AdminuserNewController extends Controller {
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    public function init(){
        //var_dump( Yii::app()->user->admin_level);die;
        if(isset(Yii::app()->user->admin_level) && !in_array(Yii::app()->user->admin_level ,array( AdminUserNew::LEVEL_ADMIN ,AdminUserNew::LEVEL_DEPARTMENT_ADMIN,AdminUserNew::LEVEL_GROUP_ADMIN))){
            $this->alertWindow('您没有访问权限');
        }
    }
    public function actions()
    {
        return array(



//            'allmodsmap' => 'application.controllers.admin.priv.getAllModsListAction',
//            'menu' => 'application.controllers.admin.group.GetUserFunAction',
//            'runsql'=>'application.controllers.admin.group.RunSqlAction',

            'dict'=>'application.actions.admin.dict.DictAction',
            'dictCreate' => 'application.actions.admin.dict.DictCreateAction',
            'getDictCode'=> 'application.actions.admin.dict.GetDictCodeAction',

            //new
            'getActionByUserid'=>'application.actions.admin.action.getInfoAction',
            'getActionByRoleid'=> 'application.actions.admin.action.getInfoByidAction',
            'rolecreate'=>  'application.actions.admin.role.createAction',
            'roleupdate'=>  'application.actions.admin.role.updateAction',
            'rolecopy'=>  'application.actions.admin.role.copyAction',
            'roleadmin' =>  'application.actions.admin.role.adminAction',
            'actionadmin' =>  'application.actions.admin.action.adminsAction',
            'actioncreate' =>  'application.actions.admin.action.createAction',
            'actionupdate' =>  'application.actions.admin.action.updateAction',
            'actioneditaudit' =>  'application.actions.admin.action.auditEditAction',
            'actiondeleteaudit' =>  'application.actions.admin.action.auditDelAction',
            'depadmin'      =>  'application.actions.admin.department.adminAction',
            'depcreate' =>  'application.actions.admin.department.createAction',
            'depupdate' =>  'application.actions.admin.department.updateAction',
            'specialAuth' =>  'application.actions.admin.special.adminAction',
            'groupadmin'=> 'application.actions.admin.group.adminAction',
            'groupcreate'=> 'application.actions.admin.group.createAction',
            'groupupdate'=> 'application.actions.admin.group.updateAction',
	    //new @date 2015-05-19
            'appadmin' =>  'application.actions.admin.app.adminAction',
            'appcreate' =>  'application.actions.admin.app.createAction',
            'appupdate' =>  'application.actions.admin.app.updateAction',

        );
    }


    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new AdminUserNew();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['AdminUserNew'])) {
            //print_r($_POST);die;
            if(!isset($_POST['role_id'])){
                $url = Yii::app()->createUrl('adminuserNew/create');
                $this->alertWindow('请给用户分配角色',$url);
            }

            if( !empty( $_POST['access_begin'] ) ){
                $check_format = count(explode(':', $_POST['access_begin'])) == 2 ? true : false;
                $_POST['AdminUserNew']['access_begin'] = $check_format ? $_POST['access_begin'].":00" : $_POST['access_begin'];
            }

            if( !empty( $_POST['access_end']) ){
                $check_format = count(explode(':', $_POST['access_end'])) == 2 ? true : false;
                //var_dump($check_format);die;
                $_POST['AdminUserNew']['access_end'] = $check_format ? $_POST['access_end'].":00" : $_POST['access_end'];
            }

            if( !empty($_POST['expiration_time']) ){
                $check_format = count(explode(':', $_POST['expiration_time'])) == 2 ? true : false;
                $_POST['AdminUserNew']['expiration_time'] = $check_format ? $_POST['expiration_time'].":00" :$_POST['expiration_time'] ;
            }
            $_POST['AdminUserNew']['create_time'] = date('Y-m-d H:i:s');

            $model->attributes = $_POST['AdminUserNew'];
            $mod = new TFA();
            $code = $mod->getKey($model->name, $model->email, '11223344a');
            if(isset($code['key']) && $code['key']){
                $model->secure_key = $code['key'];
                $model->auth_qrcode_pic = $code['qrCode'];
            }
            if ($model->save()){
                $user_id = $model->primaryKey;
                AdminUserNew::model()->sendKeyToVpn($model->department_id,$code['key'],$model->name, $model->email);

                //保存成功后 保存用户角色
                $mod = AdminUser2role::model();
                $res = $mod->saveUser2Role($user_id,$_POST['role_id']);

                //然后保存用户和城市的对应关系
                if (isset($_POST['AdminUserNew']['city_list']) && !empty($_POST['AdminUserNew']['city_list'])) {
                    UserCity::model()->insertUserCityList($model->id, explode(',', $_POST['AdminUserNew']['city_list']));
                } else {
                    // 否则选择city_id来填充city_list
                    UserCity::model()->insertUserCityList($model->id, array($_POST['AdminUserNew']['city_id']));
                }

                $this->redirect((isset($_POST['back_url'])&&!empty($_POST['back_url'])) ? $_POST['back_url'] : array ('admin'));
            }
        }
        $action_info = '';

        //得到当前登录用户信息
        $currentUserInfo =  Yii::app()->user->getCurrentUserInfo();
        //echo $currentUserInfo['admin_level']; echo '---';echo AdminUserNew::LEVEL_DEPARTMENT_ADMIN;die;
        //部门管理员可以分配自己有得权限 、、 超级管理员需要进入页面后选择部门后分配
        if($currentUserInfo['admin_level'] == AdminUserNew::LEVEL_DEPARTMENT_ADMIN){
            $model_role = new AdminRole('search');
            $model_role->unsetAttributes(); // clear any default values

            $model_role->setAttribute('department_id',$currentUserInfo['department']);
            $model_role->setAttribute('status',AdminRole::STATUS_NORMAL);

           //echo 'aaa'; print_r($role_id) ;die;
            //$action_info = AdminRole2action::model()->getActionByRole($role_id['id']);
        }elseif($currentUserInfo['admin_level'] == AdminUserNew::LEVEL_GROUP_ADMIN){
            $model_role = new AdminRole('search');
            $model_role->unsetAttributes(); // clear any default values
            $user_info = AdminUserNew::model()->findByPk($currentUserInfo['user_id']);
            $model_role->setAttribute('department_id',$user_info->group_id);
            $model_role->setAttribute('status',AdminRole::STATUS_NORMAL);
        }else  $model_role = '';
        //print_r($model_role);die;
        $organization = [];




        $this->render('create', array (
            'model'=>$model,
            'role_info'=>$model_role,
            'admin_info' => $currentUserInfo,
            'organization'=>$organization));
    }


    public function actionCheckname() {
        if (Yii::app()->request->isAjaxRequest) {
            $name = (isset($_GET['name'])) ? $_GET['name'] : null;
            if (AdminUserNew::checkName($name)) {
                echo json_encode(1);
            } else {
                echo json_encode(0);
            }
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        // $this->performAjaxValidation($model);


        if (isset($_POST['AdminUserNew'])) {

            if(!isset($_POST['role_id'])){
                $url = Yii::app()->createUrl('adminuserNew/update',array('id'=>$id));
                $this->alertWindow('请给用户分配角色',$url);
            }
            $role_ids = isset($_POST['role_id']) ? $_POST['role_id'] : array();

            if( !empty( $_POST['access_begin'] ) ){
                $check_format = count(explode(':', $_POST['access_begin'])) == 2 ? true : false;
                $_POST['AdminUserNew']['access_begin'] = $check_format ? $_POST['access_begin'].":00" : $_POST['access_begin'];
            }

            if( !empty( $_POST['access_end']) ){
                $check_format = count(explode(':', $_POST['access_end'])) == 2 ? true : false;
                $_POST['AdminUserNew']['access_end'] = $check_format ? $_POST['access_end'].":00" : $_POST['access_end'];
            }

            if( !empty($_POST['expiration_time']) ){
                $check_format = count(explode(':', $_POST['expiration_time'])) == 2 ? true : false;
                $_POST['AdminUserNew']['expiration_time'] = $check_format ? $_POST['expiration_time'].":00" :$_POST['expiration_time'] ;
            }

            // Updated and the kicked out
            $kick_key = 'ECENTER_KICK_KEY_'.$model->name;
            Yii::app()->cache->set($kick_key, 'kicked', 18000);
            $old_status = $model->status;
            $model->attributes = $_POST['AdminUserNew'];

            if ($model->save()){

                $user_id = $model->primaryKey;
                //保存成功后 保存用户角色
                $mod = AdminUser2role::model();
                $status = $model->status == AdminUserNew::STATUS_NORMAL ? AdminUser2role::STATUS_NORMAL : AdminUser2role::STATUS_FORBIDEN;
                $res = $mod->saveUser2Role($user_id,$role_ids,$status);

                // 然后更新用户和城市的对应关系
                if (isset($_POST['AdminUserNew']['city_list'])
                    && (!empty($_POST['AdminUserNew']['city_list'])
                    || $_POST['AdminUserNew']['city_list'] == 0)
                ) {
                    UserCity::model()->updateUserCityList($user_id, explode(',', $_POST['AdminUserNew']['city_list']));
                } else {
                    // 否则选择city_id来填充city_list
                    UserCity::model()->updateUserCityList($user_id, array($_POST['AdminUserNew']['city_id']));
                }

                //如果更新用户状态为正常，更新当前用户访问限制缓存
                if($old_status != AdminUserNew::STATUS_NORMAL && $model->status == AdminUserNew::STATUS_NORMAL){
                    $visitLimitKey = 'visitCount'.date('Ymd').$id;
                    Yii::app()->cache->set($visitLimitKey,array('visitTime'=>1, 'dayCount'=>0 , 'startTime' => time()),86400);
                }
                //更新当前用户MENU缓存
                $check_self = $user_id == Yii::app()->user->user_id;
                Menu::model()->removeAllCache();
                if($check_self){
                    $url = Yii::app()->createUrl('site/logout');
                }else{
                    $url = isset($_POST['back_url']) ? $_POST['back_url'] : Yii::app()->createUrl('adminuserNew/admin');

                }
                $this->redirect($url);
            }
        }

        //得到当前登录用户信息
        $currentUserInfo =  Yii::app()->user->getCurrentUserInfo();
        //echo $currentUserInfo['admin_level']; echo '---';echo AdminUserNew::LEVEL_DEPARTMENT_ADMIN;die;
        //部门管理员可以分配自己有得权限 、、 超级管理员需要进入页面后选择部门后分配
        $action_info = '';
        $model_role = new AdminRole('search');
        $model_role->unsetAttributes(); // clear any default values
        if($model->group_id){
            $dep_id = $model->group_id;
        }
        else $dep_id = $model->department_id;
        $model_role->setAttribute('department_id',$dep_id);
        $model_role->setAttribute('status',AdminRole::STATUS_NORMAL);

        $user_city_list = UserCity::model()->getUserCityList($model->id);
        $city_list_dict = array();
        if (!empty($user_city_list)) {
            $city_arr =  Dict::items('city');
            foreach ($user_city_list as $x_city) {
                if (isset($city_arr[$x_city])) {
                    $city_list_dict[] = array(
                        'id' => $x_city,
                        'name' => $city_arr[$x_city],
                    );
                }
            }
        }
        $city_list_dict = json_encode($city_list_dict);

        //print_r($role_info);die;
        $user_have_role_info = AdminUser2role::model()->getRoleInfo($id);

        $user_have_roles = $user_have_role_info ? array_keys($user_have_role_info) : array();
        $organization = OrganizationService::getInstance()->getOrganizationByCity($model['city_id']);
        if(isset($organization['code']) && $organization['code'] == 0 && $organization['data']){
            $organization = $organization['data'];
        }else{
            $organization = [];
        }

        $this->render(
            'update',
             array (
                'model'=>$model,
                'role_info'=>$model_role,
                'my_role_info' => $user_have_roles,
                'admin_info' => $currentUserInfo,
                'city_list_dict' => $city_list_dict,
                'organization'   => $organization,
            )
        );
    }

    public function actionAuth(){
        $level = Yii::app()->user->admin_level;
        if($level == AdminUserNew::LEVEL_ADMIN) $this->redirect(array('adminuserNew/depadmin'));
        if($level == AdminUserNew::LEVEL_DEPARTMENT_ADMIN) {
            $url = Yii::app()->createUrl('adminuserNew/admin',array('dep_id'=>Yii::app()->user->department));
            //echo $url;die;
            $this->redirect($url); //.'&dep_id='.Yii::app()->user->department
        }
        if($level == AdminUserNew::LEVEL_GROUP_ADMIN) {

            $user_info = AdminUserNew::model()->findByPk(Yii::app()->user->user_id);
            if($user_info->group_id){
                $url = Yii::app()->createUrl('adminuserNew/admin',array('parent_id'=>Yii::app()->user->department,'dep_id'=>$user_info->group_id));
                $this->redirect($url);
            }else{
                $this->alertWindow('用户没有被指定小组 请联系部门管理员或超管');
            }
        }
        echo 'no auth';
    }

    /**
     *
     * 用户管理
     */
    public function actionAdmin() {
        //echo Yii::app()->user->user_id;
        $model = new AdminUserNew('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['AdminUserNew'])){
            $model->attributes = $_GET['AdminUserNew'];
        }

        $group_id = $group_info = $dep_info = '';
        $dep_id = isset($_GET['dep_id']) && $_GET['dep_id'] ? $_GET['dep_id'] : '';
        if(isset($_GET['parent_id']) && $_GET['parent_id'] && isset($_GET['dep_id'])){
            $dep_id = $_GET['parent_id'];
            if(!$dep_id){$this->alertWindow('参数错误');}
            $group_id = $_GET['dep_id'];
            $group_info = AdminDepartment::model()->findByPk($group_id);
            if($group_info->parent_id != $dep_id){
                $this->alertWindow('参数错误');
            }
        }
        //echo $dep_id.'---'.$group_id;
        $dep_id && $dep_info = AdminDepartment::model()->findByPk($dep_id);

        switch(Yii::app()->user->admin_level){
            case AdminUserNew::LEVEL_ADMIN :
                if($dep_id){
                    $model->department_id = $dep_id;
                }
                $group_id && $model->group_id = $group_id;
                break;
            case AdminUserNew::LEVEL_DEPARTMENT_ADMIN :
                if($dep_id){
                    if($dep_id != Yii::app()->user->department){
                        $this->alertWindow('参数错误');
                    }
                    $model->department_id = $dep_id;
                    $group_id && $model->group_id = $group_id;
                }else{
                    $model->department_id = Yii::app()->user->department;
                }

                break;
            case AdminUserNew::LEVEL_GROUP_ADMIN :
                $user_info = AdminUserNew::model()->findByPk(Yii::app()->user->user_id);
                $model->group_id = $user_info->group_id;
                break;
        }


        $dep_id = isset($_GET['dep_id']) && !empty($_GET['dep_id']) ? $_GET['dep_id'] : Yii::app()->user->department;

        $this->render('admin', array (
            'model'=>$model,
            'dep_id'=>$dep_id,
            'dep_info'=>$dep_info,
            'group_info'=>$group_info
        ));

    }


    public function actionGetRoleByDep($dep_id,$user_id = ''){

        //$role_info = AdminRole::model()->getRolesByDepid($dep_id); //array 部门对应的所有角色
        $model_role = new AdminRole('search');
        $model_role->unsetAttributes(); // clear any default values

        $model_role->setAttribute('department_id',$dep_id);
        $model_role->setAttribute('status',AdminRole::STATUS_NORMAL);

        $user_have_roles = array();
        if($user_id){
            $user_have_role_info = AdminUser2role::model()->getRoleInfo($user_id);
            $user_have_roles = $user_have_role_info ? array_keys($user_have_role_info) : array();
        }
        $str = '';
        if($model_role){
            $this->widget('zii.widgets.grid.CGridView',
                array (
                    'id'=>'admin-usernew-role-grid',
                    'itemsCssClass'=>'table table-striped',
                    'dataProvider'=>$model_role->search(),
                    'selectableRows'=>2,
                    'columns'=>array (
                        array(
                            'class' => 'CCheckBoxColumn',
                            'checkBoxHtmlOptions' => array(
                                'name' => 'role_id[]',
                                'value'=> '$data->id',
                            ),
                            'checked'=>function ($data) use ($user_have_roles) {
                                    return in_array($data->id, $user_have_roles);
                                },
                        ),
                        'id',
                        'name',
                        'desc',
                        'create_time',
                        array(
                            'name'=>'查看功能',
                            'type'=>'raw',
                            'value' => 'CHtml::link("查看功能", "javascript:void(0);", array (
						    "onclick"=>"{showRoles($data->id);}"));'
                        ),

                    ),
                )
            );

            }
            else
            {
                $str = '没有对应角色组';
                echo $str;
            }


    }


    public function GetGroupByDep($dep_id,$isSelect = false){

        $group_info = AdminDepartment::model()->findAll(
            array('select'=>'id,name',
                'condition'=>'parent_id = :p_id and status = :status',
                'params'=>array(':p_id'=>$dep_id,':status'=>AdminDepartment::STATUS_NORMAL))
        );
        if($group_info){
            if($isSelect) $tmp[''] = '小组';
            foreach($group_info as $obj){
                $tmp[$obj->id] = $obj->name;
            }
            return $tmp;
        }
        return array();
    }

    public function actionGetGroupHtml($dep_id,$group_id='') {
        $data = $this->GetGroupByDep($dep_id,1);
        if(!$data){
            $data = array(''=>'小组');
        }
        echo CHtml::dropDownList('AdminUserNew[group_id]',$group_id,$data,array('id'=>'AdminUserNew_group_id','onchange'=>'changeGroup()'));

    }




    /**
     *
     * 客服坐席一览
     */
    public function actionAgent() {
        $model = new AdminAgent();
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['AdminAgent']))
            $model->attributes = $_GET['AdminAgent'];

        $this->render('agent', array (
            'model'=>$model));

    }

    /**
     * 客服坐席分配设定
     * @param int $agent_num
     */
    public function actionAgentAllot($agent_num) {
        $model = AdminAgent::model()->findByPk($agent_num);

        if (isset($_POST['AdminAgent'])) {
            unset($model->attributes);
            $model->attributes = $_POST['AdminAgent'];

            //清除以前的选择
            $sql = 'update t_admin_agent set user_id =0 where user_id =:user_id;';
            Yii::app()->db->createCommand($sql)->execute(array (
                'user_id'=>$model->user_id));

            if ($model->save()) {
                if (!empty($_GET['asDialog'])) {
                    //Close the dialog, reset the iframe and update the grid
                    echo CHtml::script("window.parent.$('#cru-dialog').dialog('close');window.parent.$('#cru-frame').attr('src','');window.parent.$.fn.yiiGridView.update('{$_GET['gridId']}');");
                    Yii::app()->end();
                }
            }
        }
        $this->layout = '//layouts/main_no_nav';
        $this->render('agent_allot', array (
            'model'=>$model));
    }

// 	protected function showUserRoles($data) {
// 		return CHtml::dropDownList('roles', '', $data->getRoles());
// 	}

    /**
     * 查看用户有哪些权限
     * @param object user $data
     */
    protected function getUserRolesToStr($data){

        $user_id = $data->id;

        $str=CHtml::link("查看", "javascript:void(0);", array ("onclick"=>"{showRoles('".$user_id."')}"));
        //$str.='&nbsp;&nbsp;'.CHtml::link('功能',Yii::app()->createUrl('adminuser/menu',array('user_id'=>$data->user_id)),array('target'=>'_blank'));
        echo $str;

    }

    /**
     * 查看、编辑 用户的查看电话号码权限和 优惠券权限
     * @param $data
     */
    protected function getSpecialBtnStr($data){

        $user_id = $data->id;

        $str=CHtml::link("编辑", "javascript:void(0);", array ("onclick"=>"{showSpecial('".$user_id."')}"));
        //$str.='&nbsp;&nbsp;'.CHtml::link('功能',Yii::app()->createUrl('adminuser/menu',array('user_id'=>$data->user_id)),array('target'=>'_blank'));
        echo $str;

    }


    /**
     * 查看action 都对应哪些部门
     * @param $data
     */
    protected function showGroupName($data) { //获取action 对应的部门
        $dep_names = '';
        $mod = AdminRole2action::model();
        $dep_name = $mod->getDepByAction($data->id);
        foreach($dep_name as $v) {
            $dep_names .= $v.'<br/>';
        }
        echo trim($dep_names, '<br/>');
    }


    /**
     * 重置用户密码
     * @param $id
     */
    public function actionResetPwd($id, $method = 2){

        $result=array('succ'=>0,'msg'=>'失败');
        if($method == 'init'){
            $initPass = AdminUserNew::model()->initPasswd($id);
            if($initPass){
                $result=array('succ'=>1,'msg'=>'密码已重置为默认密码：11223344a');
            }
            echo json_encode($result);
            Yii::app()->end();
        }
        $ret= AdminUserNew::model()->resetPassword($id, $method);
        if($ret){
            $receive = array();
            if(1 == $method){
                $receive[] = '手机';
            }
            if(2 == $method){
                $receive[] = '邮箱';
            }
            $result=array('succ'=>1,'msg'=>'密码已更新，新密码已发送至你的'.(implode('、', $receive)));
        }

        echo json_encode($result);
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = AdminUserNew::model()->findByPk($id);
        if ($model===null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax'])&&$_POST['ajax']==='admin-user-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * 展示用户角色组管理链接
     * @param $data
     */
    public function getRoleAdminUrl($data){
        $dep_id = $data->id;
        if($data->status == AdminDepartment::STATUS_NORMAL){
            $param = $data->parent_id ? array('dep_id'=>$dep_id,'parent_id'=>$data->parent_id) : array('dep_id'=>$dep_id);
            $url = Yii::app()->createUrl('adminuserNew/roleadmin',$param);
            $str=CHtml::link($data->name, $url);
        }else {
            $str = $data->name;
        }

        //$str.='&nbsp;&nbsp;'.CHtml::link('功能',Yii::app()->createUrl('adminuser/menu',array('user_id'=>$data->user_id)),array('target'=>'_blank'));
        echo $str;
    }


    /**
     * 显示部门内有多少用户 并支持链接跳转
     * @param $data
     */
    public function getDepUserNum($data){

        $dep_id = $data->id;
        if($data->parent_id){
            $dep_id = $data->parent_id;
            $group_id = $data->id;
            //echo $dep_id.'----'.$group_id;
            $count = AdminUserNew::model()->count('department_id = :dep_id and group_id = :group_id and status = :status',array(':dep_id'=>$dep_id,':group_id'=>$group_id,':status'=>AdminUserNew::STATUS_NORMAL));

        } else {
            $count = AdminUserNew::model()->count('department_id = :dep_id and status = :status',array(':dep_id'=>$dep_id,':status'=>AdminUserNew::STATUS_NORMAL));
        }
        if($data->status == AdminDepartment::STATUS_NORMAL){
            $param = $data->parent_id ? array('dep_id'=>$data->id,'parent_id'=>$data->parent_id) : array('dep_id'=>$dep_id);
            $url = Yii::app()->createUrl('adminuserNew/admin',$param);
            $str=CHtml::link($count, $url);
        } else {
            $str = $count;
        }
        //$str.='&nbsp;&nbsp;'.CHtml::link('功能',Yii::app()->createUrl('adminuser/menu',array('user_id'=>$data->user_id)),array('target'=>'_blank'));
        echo $str;
    }

    /**
     * 用户列表页显示用户拥有哪个角色组
     * @param $data
     */
    public  function showUserRole($data){
        //print_r($data);
        $st1 = AdminUser2role::STATUS_NORMAL;
        $st2 = AdminRole::STATUS_NORMAL;
        $sql = "select ur.id,r.name from t_admin_user2role as ur ,t_admin_role as r where ur.role_id = r.id and ur.user_id = {$data->id} and ur.status = {$st1} and r.status = {$st2}";
        $connection = Yii::app()->dbadmin;
        $command = $connection->createCommand($sql);
        $data = $command->queryAll();
        $res = array();
        if($data) {
            foreach($data as $v){
                $res[]=$v['name'];
            }
            echo implode('<br>',$res);
        }else
        echo '无';
    }

    /**
     * 显示角色组内有多少用户 角色组列表页使用
     * @param $data
     */
    public function getRoleUserNum($data){
        $count = AdminUser2role::model()->getCountByRoleId($data->id);
        //echo $count;
        if($count > 0 && isset($_GET['dep_id']) && $_GET['dep_id']){
            $param = array('dep_id'=>$_GET['dep_id']);
            if( isset($_GET['parent_id']) && $_GET['parent_id']){
                $param['parent_id'] = $_GET['parent_id'];
            }
            $param['AdminUserNew']['role_id'] = $data->id;

            $url = Yii::app()->createUrl('adminuserNew/admin',$param);
            echo CHtml::link($count,$url);
        }else echo $count;
    }


     public function actionGetOrganizaByCity($city_id){
         $result = OrganizationService::getInstance()->getOrganizationByCity($city_id);
         //$result = ['3'=>'机构3','5'=>'机构5'];

         //$result=array('code'=>0,'data'=>$result);

         echo json_encode($result);
         Yii::app()->end();
     }




}
