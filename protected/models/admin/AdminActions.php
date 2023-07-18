<?php

/**
 * This is the model class for table "{{admin_action}}".
 *
 * The followings are the available columns in table '{{admin_action}}':
 * @property integer $id
 * @property string $controller
 * @property string $action
 * @property string $name
 * @property string $desc
 * @property integer $access_auth
 * @property integer $status
 * @property string $update_time
 * @property string $create_time
 * @property integer $audit_status
 */
class AdminActions extends CActiveRecord
{
    //status
    CONST STATUS_NORMAL = 0; //moren 默认
    CONST STATUS_FORBIDEN = -1; //禁用

    //access_auth
    CONST ACCESS_NORMAL = 0; // 默认 需要分配才可以访问的权限
    CONST ACCESS_LOGIN  = 1; // 登录用户权限
    CONST ACCESS_GUEST = 2; // 访客权限

    //driver_access_auth
    CONST DRIVER_ACCESS_FORBIDEN = 0; // 不是司机权限
    CONST DRIVER_ACCESS_ALLOW = 1; // 是司机权限

    //can_allocate
    CONST CAN_ALLOCATE = 1; // 部门管理员可以分配给其他人
    CONST CAN_NOT_ALLOCATE = 0; // 部门管理员不可以分配给其他人

    //审核配置状态
    public static $audit_status = array(
        '0'=>'全部',
        '1'=>'已配置',
        '2'=>'未配置',
    );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{admin_action}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('app_id,controller,action,action_url,name,desc', 'required'),
			array('access_auth,driver_access_auth, status,can_allocate,audit_status', 'numerical', 'integerOnly'=>true),
			array('controller, action, name', 'length', 'max'=>32),
			array('desc', 'length', 'max'=>255),
			array('app_id, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('app_id, controller, action, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'app_id' => '应用系统',
			'controller' => '资源(Controller)',
			'action' => '操作(Action)',
			'action_url' => '操作地址',
			'name' => '名称',
			'desc' => '描述',
			'access_auth' => '访问权限',
            'driver_access_auth'=>'司机权限',
            'can_allocate' =>'是否允许再分配',
			'status' => '权限状态',
			'update_time' => '更新时间',
			'create_time' => '创建时间',
            'audit_status' => '审核配置状态',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		if(!empty($this->app_id)){
			$criteria->compare('app_id',$this->app_id);
		}
		$criteria->compare('controller',$this->controller,true);
		$criteria->compare('action',$this->action,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('access_auth',$this->access_auth);
		$criteria->compare('status',$this->status);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('audit_status',$this->audit_status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->dbadmin;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AdminAction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


    public function getInfobyIds($id){
        if(is_array($id)){
            $ids = implode(',',$id);
        }
        else{
            $ids = $id;
        }
        //echo $ids;die;
        $res = $this->findAll(array('condition'=>'id in ('.$ids.') and status = :status and access_auth = :access_auth',
            'params'=>array(':status'=>self::STATUS_NORMAL,':access_auth'=> self::ACCESS_NORMAL), 'order'=> 'controller asc ,action asc'));
        $result = array();
        if($res){
            foreach($res as $v){
		$app_name = AdminApp::model()->getAppName($v->app_id);
		$result[$app_name . '-' . $v->controller][] = $v->attributes;
            }
        }
        return $result;
    }

    /**
     * 获取多个id的action
     * @param $id 多个或一个id
     * @param string $app_id 应用ID
     * @author zys
     * @return array
     */
    public function getAllInfobyIds($id, $app_id='') {
        if(is_array($id)){
            $ids = implode(',',$id);
        }
        else{
            $ids = $id;
        }
        $condition = 'id in ('.$ids.') and status = :status';
        $params = array(':status'=>self::STATUS_NORMAL);
        if (!empty($app_id)) {
            $condition .= ' and app_id=:app_id';
            $params[':app_id'] = $app_id;
        }

        //echo $ids;die;
        $res = $this->findAll(array('condition'=>$condition,
            'params'=>$params, 'order'=> 'controller asc ,action asc'));
        $result = array();
        if($res){
            foreach($res as $obj){
                $result[$obj->id] = $obj->attributes;
            }
        }
        return $result;
    }

    /**
     * 获取用户状态列表
     * @param string $status
     * @return array|bool
     */
    public static function getActionStatus($status = ''){
        $status_array = array(
            self::STATUS_NORMAL=>'正常',
            self::STATUS_FORBIDEN => '禁用'
        );
        if($status !== ''){
            if(isset($status_array[$status]))
                return $status_array[$status];
            else return false;

        }
        return $status_array;
    }

    /**
     * 获取用户状态列表
     * @param string $status
     * @return array|bool
     */
    public static function getActionAccessAuth($access = ''){
        $access_array = array(

            self::ACCESS_NORMAL => '需要分配权限',
            self::ACCESS_GUEST => '访客权限',
            self::ACCESS_LOGIN => '登录用户权限',

        );
        if($access !== ''){
            if(isset($access_array[$access]))
                return $access_array[$access];
            else return false;

        }
        return $access_array;
    }


    /**
     * 获取用户状态列表
     * @param string $status
     * @return array|bool
     */
    public static function getDriverAccessAuth($access = ''){
        $access_array = array(

            self::DRIVER_ACCESS_FORBIDEN => '不是司机权限',
            self::DRIVER_ACCESS_ALLOW => '是司机权限'
        );
        if($access !== ''){
            if(isset($access_array[$access]))
                return $access_array[$access];
            else return false;

        }
        return $access_array;
    }


    /**
     * 获取用户状态列表
     * @param string $status
     * @return array|bool
     */
    public static function getCanAllocate($allocation = ''){
        $allocation_array = array(
            self::CAN_ALLOCATE=>'允许',
            self::CAN_NOT_ALLOCATE => '禁止'
        );
        if($allocation !== ''){
            if(isset($allocation_array[$allocation]))
                return $allocation_array[$allocation];
            else return false;

        }
        return $allocation_array;
    }

    /**
     * 检测用户是否有访问权限
     * @param $controller
     * @param $action
     * @param string $authUrl
     * @param string $app_id
     * @return bool
     */
    public function havepermission($controller, $action, $authUrl = "", $app_id = "")
    {
        $res = $access = false;
        $is_guest = Yii::app()->user->getIsGuest();

        if (!$is_guest) {
            $user_id = Yii::app()->user->user_id;
        }
        $app_id = !empty($app_id) ? $app_id : (empty($_REQUEST['appid']) ? 1 : intval($_REQUEST['appid']));
        //因为v2跟权限系统耦合比较紧所以要区别查询 1, 2 为应用系统id
        if ($app_id > 2) {
            $action_info = $this->model()->find('app_id=:app_id and controller=:controller and action=:action and status=:status',
                array(':app_id' => $app_id, ':controller' => $controller, ':action' => $action, ':status' => self::STATUS_NORMAL));
        } else {
            if ($controller == "webAdmin" && $authUrl) {
                if ($action == "openBrowerPage") {
                    $action_info = $this->model()->find('app_id <=2 and controller=:controller and action=:action and status=:status',
                        array(':controller' => $controller, ':action' => $action, ':status' => self::STATUS_NORMAL));
                } else {
                    //需要校验具体的url是否有权限
                    $authUrl = base64_decode($authUrl);
                    $authArr = str_replace("/v2/", "", $authUrl);
                    $action_info = $this->model()->find('app_id <=2 and controller=:controller and action=:action and status=:status and action_url=:url',
                        array(':controller' => $controller, ':action' => $action, ':status' => self::STATUS_NORMAL, ":url" => $authArr));
                }
            } else {
                $action_info = $this->model()->find('app_id <=2 and controller=:controller and action=:action and status=:status',
                    array(':controller' => $controller, ':action' => $action, ':status' => self::STATUS_NORMAL));
            }
        }
        //print_r($action_info);die;
        if ($action_info) {
            if (!$is_guest) { //登录用户
                if (Yii::app()->user->type == AdminUserNew::USER_TYPE_ADMIN) { //后台用户

                    $user_level = isset(Yii::app()->user->admin_level) ? Yii::app()->user->admin_level : '';
                    if ($user_level == AdminUserNew::LEVEL_ADMIN) { //超级管理员

                        return true;
                    }

                    switch ($action_info->access_auth) {
                        case self::ACCESS_NORMAL:
                            $access = true;
                            break;
                        case self::ACCESS_LOGIN:
                        case self::ACCESS_GUEST:
                            $access = true;
                            $res = true;
                            break;
                    }
                    //echo $access;
                } //司机
                else if (Yii::app()->user->type == AdminUserNew::USER_TYPE_DRIVER && $action_info->driver_access_auth == self::DRIVER_ACCESS_ALLOW) {
                    $access = true;
                    $res = true;
                    //ECHO 'AAA';DIE;
                }
                if ($res && $access) { //有权限，返回true
                    return $res;
                }
                //var_dump($access);die;
                if (!$access) return $access; //如果 access 为 false 则说明权限不够 直接返回false
            } else { //访客

                if ($action_info->access_auth == self::ACCESS_GUEST)
                    return true;
                return false;
            }
        } else return $res; //如果连action 信息都没有 则返回false;
        $roles = AdminUser2role::model()->getRoleInfo($user_id, 1);
        if ($roles) {
            $role_str = implode(',', $roles);
            $action_exist = AdminRole2action::model()->find('role_id in (' . $role_str . ') and action_id = :action_id and status = :status',
                array(':action_id' => $action_info->id, ':status' => AdminRole2action::STATUS_NORMAL));
            if ($action_exist) return true;
            return false;
        }
        return false;
    }

    /**
     * 根据标识获取的权限列表
     * @param $params
     * @return array
     * @author ztk
     */
    public function getPermissionByParams($params)
    {

        $res = array();
        $controllers = $params['resources'];
        $app_id = $params['appid'];
        AdminActions::$db = Yii::app()->dbadmin_readonly;
        $criteria = new CDbCriteria();
        $criteria->select = 'name,controller,action,app_id';
        $criteria->order = 'id asc';
        $controllersArr = explode(",", $controllers);
        if (!empty($controllersArr)) {
            $criteria->addInCondition('controller', $controllersArr);
        }
        if (!empty($app_id)) {
            $criteria->addCondition('app_id=' . $app_id);
        }
        $listAdminActions = AdminActions::model()->findAll($criteria);
        if (!empty($listAdminActions)) {
            foreach ($listAdminActions as $item) {
                $is_allow = $this->havepermission($item['controller'], $item['action'], "", $item['app_id']);
                $res[] = ['name' => $item['name'], 'controller' => $item['controller'], 'action' => $item['action'], 'is_allow' => $is_allow];
            }
        }
        return $res;
    }

    /**
     * 获取所有的能够分配的权限
     * @return array
     */
    public function getAllNormalAction(){
        $result = array();
        $res = $this->findAll(array('condition'=>' status = :status and access_auth = :access_auth',
            'params'=>array(':status'=>self::STATUS_NORMAL,':access_auth'=> self::ACCESS_NORMAL), 'order'=> 'controller asc ,action asc'));
        //$res = $this->findAll('status = :status and access_auth= :access_auth',array(':status'=>self::STATUS_NORMAL,':access_auth'=>self::ACCESS_NORMAL));
        if($res){
            foreach($res as $v){
                $result[$v->controller][] = $v->attributes;
            }
        }
        return $result;
    }


    /**
     * 获取所有的权限
     * @param $key
     * @param $app_id
     * @author zys
     * @return array
     */
    public function getAllAction($key = 'controller', $app_id=''){
        $result = array();
        $condition = ' status = :status ';
        $params = array(':status'=>self::STATUS_NORMAL);
        if (!empty($app_id)) {
            $condition .= ' and app_id=:app_id';
            $params[':app_id'] = $app_id;
        }
        $res = $this->findAll(array('condition'=>$condition,
            'params'=>$params, 'order'=> 'controller asc ,action asc'));
        //$res = $this->findAll('status = :status and access_auth= :access_auth',array(':status'=>self::STATUS_NORMAL,':access_auth'=>self::ACCESS_NORMAL));
        if($res){
            if($key === 'controller'){
                foreach($res as $v){
		            $app_name = AdminApp::model()->getAppName($v->app_id);
                    $result[$app_name . '-' . $v->controller][] = $v->attributes;
                }
            }
            if($key === 'id'){
                foreach($res as $v){
                    $result[$v->id] = $v->attributes;
                }
            }
        }
        return $result;
    }


    /**
     * 获取所有的不用分配的权限 导航分配使用的
     * @param $show_all
     * @param $app_id
     * @return array
     */
    public function getAllFreeAction($show_all = false, $app_id=''){
        $result = array();
        $condition = ' status = :status and (access_auth = :access_auth or access_auth = :access_auth2) ';
        $params = array(':status'=>self::STATUS_NORMAL,':access_auth'=> self::ACCESS_GUEST,':access_auth2'=>self::ACCESS_LOGIN);
        if (!empty($app_id)) {
            $condition .= ' and app_id=:app_id';
            $params[':app_id'] = $app_id;
        }
        $res = $this->findAll(array('condition'=>$condition,
            'params'=>$params, 'order'=> 'controller asc ,action asc'));
        //$res = $this->findAll('status = :status and access_auth= :access_auth',array(':status'=>self::STATUS_NORMAL,':access_auth'=>self::ACCESS_NORMAL));
        if($res){
            if($show_all == false){
                foreach($res as $v){
                    $result[$v->id] = $v->id;
                }
            }
            else {
                foreach($res as $v){
                    $result[$v->id] = $v->attributes;
                }
            }
        }
        return $result;
    }


    public function getInfo($id){
        $res = AdminActions::model()->findByPk($id);
        return $res;
    }


}
