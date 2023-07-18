<?php

/**
 * This is the model class for table "{{admin_user}}".
 *
 * The followings are the available columns in table '{{admin_user}}':
 * @property integer $id
 * @property string $name
 * @property string $password
 * @property string $email
 * @property string $phone
 * @property integer $department_id
 * @property integer $city_id
 * @property integer $first_login
 * @property integer $level
 * @property string $access_begin
 * @property string $access_end
 * @property string $expiration_time
 * @property integer $type
 * @property integer $status
 * @property string $update_time
 * @property string $create_time
 */
class AdminUserNew extends ActiveRecord
{
    //STATUS
    CONST STATUS_NORMAL = 1; //正常状态
    CONST STATUS_FORBIDEN = 0; //禁用状态

    //TYPE
    CONST TYPE_NORMAL = 1; //正式员工
    CONST TYPE_TEMP = 0; //兼职员工

    //FIRST LOGIN
    CONST IS_FIRSTLOIGIN = 1; //是首次登录
    CONST NOT_FIRSTLOGIN = 0; // 不是首次登录

    //LEVEL
    CONST LEVEL_GROUP_ADMIN = 3; //小组管理员
    CONST LEVEL_ADMIN = 2; //超级管理员
    CONST LEVEL_DEPARTMENT_ADMIN = 1; //部门管理员
    CONST LEVEL_NORMAL = 0; //普通用户

    CONST USER_TYPE_DRIVER = 1;
    CONST USER_TYPE_ADMIN = 2;


    CONST FIRST_LOGIN_TRUE = 0; //首次登录
    CONST FIRST_LOGIN_MODIFY_PASS = 1; //密码已经修改
    CONST FIRST_LOGIN_AUTH = 2; //已经经过双因子认证
    CONST FIRST_LOGIN_FALSE = 3; //即修改过密码也认证过双因子


    CONST AUTH_TYPE_NORMAL = 1;
    CONST AUTH_TYPE_GOOGLE = 2;


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{admin_user}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id,level,type,status,department_id,name,email,phone,auth_type', 'required'),
            array('id, department_id, group_id, city_id, first_login, level, type, status, organization_id', 'numerical', 'integerOnly'=>true),
            array('name, password', 'length', 'max'=>32),
            array('name', 'unique' ,'message'=>'该用户已存在'),
            array('email', 'length', 'max'=>128),
            array('auth_qrcode_pic', 'length', 'max'=>255),
            array('email', 'email'),
            array('phone', 'length', 'max'=>15),
            array('access_begin, access_end, expiration_time, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('name,  email, phone, department_id, city_id, first_login, level,  type, status, create_time', 'safe', 'on'=>'search'),
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
            'name' => '用户名',
            'password' => 'Password',
            'email' => '邮箱',
            'phone' => '电话',
            'department_id' => '部门',
            'group_id'=>'小组',
            'city_id' => '城市',
            'first_login' => '首次登录',
            'level' => '用户等级',
            'access_begin' => 'Access Begin',
            'access_end' => 'Access End',
            'expiration_time' => 'Expiration Time',
            'type' => '用户类型',
            'auth_type'=>'认证类型',
            'status' => '用户状态',
            'auth_qrcode_pic' => '认证二维码',
            'update_time' => '更新时间',
            'create_time' => '创建时间',
            'organization_id'=> '所属机构'
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
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $criteria = new CDbCriteria();
        //$criteria->select = '*,user2role.role_id';
        if($this->city_id != 0){
            $criteria->compare('city_id', $this->city_id);
        }
        $criteria->compare('department_id',$this->department_id);
        $criteria->compare('group_id',$this->group_id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('level',$this->level,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('t.status',$this->status);

        if(isset($_GET['AdminUserNew']['role_id']) && $_GET['AdminUserNew']['role_id']){
            $criteria->join = 'left join t_admin_user2role  user2role on t.id=user2role .user_id ';
            $criteria->compare('user2role.role_id',(int)$_GET['AdminUserNew']['role_id']);
            $criteria->compare('user2role.status',AdminUser2role::STATUS_NORMAL);
        }

        return new CActiveDataProvider($this, array(
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => 30)
            )
        );
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
     * @return AdminUserNew the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
     * 获取用户状态列表
     * @param string $status
     * @return array|bool
     */
    public static function getUserStatus($status = '',$isSearch = false){
        $status_array = array();
        if($isSearch){
            $status_array = array(''=>'全部');
        }
        $status_array[self::STATUS_NORMAL] = '正常';
        $status_array[self::STATUS_FORBIDEN] = '禁用';
        if($status !== ''){
            if(isset($status_array[$status]))
                return $status_array[$status];
            else return false;

        }
        return $status_array;
    }


    /**
     * 根据字段获取员工类型 正式，兼职
     * @param string $type
     * @return array|bool
     */
    public static function getUserType($type = '',$isSearch = false){
        $type_arr = array(
            self::TYPE_NORMAL =>'正式',
            self::TYPE_TEMP => '兼职',
        );
        if($isSearch){
            $type_arr = array_merge(array(''=>'全部'),$type_arr);
        }
        if($type !== ''){
            if(isset($type_arr[$type])){
                return $type_arr[$type];
            }
            else return false;
        }
        return $type_arr;
    }


    /**
     * 根据字段获取员工管理等级  普通 部门管理员 超级管理员
     * @param string $level
     * @return array|bool
     */
    public static function getUserLevel($level = '',$isSearch = false, $showDepadminLevel = false){
        $level_arr = array();
        if($isSearch){
            $level_arr = array_merge(array(''=>'全部'),$level_arr);
        }
        $level_arr[self::LEVEL_NORMAL] = '普通员工';
        $level_arr[self::LEVEL_GROUP_ADMIN] = '小组管理员';
        if(!$showDepadminLevel){
            $level_arr[self::LEVEL_DEPARTMENT_ADMIN] = '部门管理员';
            $level_arr[self::LEVEL_ADMIN] = '超级管理员';
        }


        if($level !== ''){
            if(isset($level_arr[$level])){
                return $level_arr[$level];
            }
            else return false;
        }
        return $level_arr;
    }



    /**
     * 获取用户登录类型
     * @param string $type
     * @return array|bool
     */
    public static function getAuthType($type = ''){
        $type_arr = array(
            self::AUTH_TYPE_GOOGLE => 'google 双因子认证',
            self::AUTH_TYPE_NORMAL =>'默认，每周更新密码',
        );

        if($type !== ''){
            if(isset($type_arr[$type])){
                return $type_arr[$type];
            }
            else return false;
        }
        return $type_arr;
    }


    /**
     * 密码强度
     * @param type $value
     * @return int
     */
    public function pwdLevel($value){
        $pattern_1 = "/^.*([\W_])+.*$/i";
        $pattern_2 = "/^.*([a-zA-Z])+.*$/i";
        $pattern_3 = "/^.*([0-9])+.*$/i";
        $level = 0;
//        if (isset($value[9])) {
//            $level++;
//        }
        if (preg_match($pattern_1,$value)) {
            $level++;
        }
        if (preg_match($pattern_2,$value)) {
            $level++;
        }
        if (preg_match($pattern_3,$value)) {
            $level++;
        }
        if ($level > 3) {
            $level = 3;
        }
        if (!isset($value['7'])) {
            $level = 1;
        }
        return $level;
    }

    public static function checkName($name)
    {
        $ret = self::model()->find('name=:name', array(
            ':name' => $name));
        if ($ret) {
            return true;
        }
        return false;
    }

    /**
     * 检查手机是否系统用户
     * @param $phone
     * @return bool
     */
    public function checkPhone($phone)
    {
        $command=Yii::app()->dbadmin_readonly->createCommand();
        $param=array(':phone' => $phone,':status'=>self::STATUS_NORMAL);
        $ret = $command->select('*')
            ->from('{{admin_user}}')
            ->where('phone=:phone and status=:status ')
            ->queryRow(true,$param);
        return $ret;
    }

    public function getName($user_id)
    {
        $user = self::find('id=:user_id', array(
            ':user_id' => $user_id));
        if ($user) {
            return $user->name;
        }
        return '';
    }

    public function getInfoByName($name){
        $user = self::find('name = :name', array(
            ':name' => $name));
        if ($user) {
            return $user;
        }
        return false;
    }

    /**
     * 获取全部呼叫中心客户用户名
     * @author duke 2014-06-10
     * @return array
     */
    public function getUserById($user_id)
    {
        $user = Yii::app()->dbadmin_readonly->createCommand()
            ->select("*")
            ->from(self::tableName())
            ->where("id=:uid and status=:status",
                array(':uid' => $user_id, ':status' => self::STATUS_NORMAL))
            ->queryRow();

        return $user;
    }


    /*
     * 获取坐席分配人员
     *
     * */
    public function getAgentUsers($dep_id=0)
    {
        $user_array = null;

        $criteria = new CDbCriteria();
        $criteria->condition = 'status ='.self::STATUS_NORMAL;
        if($dep_id != 0) {
            $criteria->condition .= ' and department_id = '.$dep_id;
        }

        $users = self::model()->findAll($criteria);
        $tmp = array();
        foreach ($users as $user) {
            $user_array[$user->id] =  iconv('UTF-8', 'GB2312//TRANSLIT//IGNORE', $user->name);
            $tmp[$user->id] = $user->name;
        }

        //汉字排序
        asort($user_array);
        $ret_array = array();
        foreach($user_array as $k=>$v) {
            $ret_array[$k] = $tmp[$k];
        }

        return $ret_array;
    }

    /**
     *
     * 随机密码生成
     * @param 长度 $password_length
     * @param unknown_type $generated_password
     */
    function gen_random_password($password_length)
    {
        $generated_password='';
        mt_srand((double)microtime() * 1000000);
        $valid_characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $chars_length = strlen($valid_characters) - 1;
        for ($i = $password_length; $i--;) {
            $generated_password .= substr($valid_characters, (mt_rand() % (strlen($valid_characters))), 1);
        }
        return $generated_password;
    }


    /**
     * 获取全部呼叫中心客户用户名
     * @author duke 2014-06-10
     * @return array
     */
    public function getCallUserList()
    {
        $department_info = AdminDepartment::model()->getInfoByName('呼叫中心');

        if($department_info){
            $call_user = Yii::app()->dbadmin_readonly->createCommand()
                ->select("id,name")
                ->from("t_admin_user")
                ->where("department_id = :department and status = :status",
                    array(':department' => $department_info['id'], ':status' => self::STATUS_NORMAL))
                ->queryAll();

            return $call_user;
        }
        return false;
    }

    /**
     * 重置后台用户密码，并返回新密码
     * @param $user_id  主键
     * @param $method   途径（1=>短信，2=>邮箱, all=>全部）
     * @return string   新密码
     * @author bidong   2013-09-11
     */
    public function resetPassword($user_id, $method = 1){
        $new_pwd='';
        $adminUserModel=$this->findByPk($user_id);
        if($adminUserModel){
            //生成新密码
            $new_pwd=Common::makeRandCode(3);

            $user=$adminUserModel->name;
            $phone=  str_replace(' ', '', $adminUserModel->phone);
            $email = $adminUserModel->email;
            //手机号码为‘1’的是特殊账户;  特殊账户和没有联系方式的用户不重置密码
            if($phone == '1' || (!(Common::checkPhone($phone) && !isset($phone[11])) && !strpos($email,'@'))){
                return FALSE;
            }
            //更新密码
            $adminUserModel->password=md5($new_pwd);
            $ret=$adminUserModel->save(TRUE, array('password'));
            if($ret){
                $sms_content= SmsTemplate::model()->getContentBySubject('user_new_password',array('$user$'=>$user,'$password$'=>$new_pwd));
                $sms_content=$sms_content['content'];
                //发送密码短信
                if((1 == $method || 'all' == $method) && Common::checkPhone($phone)){
                    try{
                        $result=Sms::SendSMS($phone,$sms_content,Sms::CHANNEL_ZCYZ);//使用单独通道 modify by sunhongjing
//                        echo $result ? '-短信发送成功-' : '-短信发送失败-';             //model中输出会破坏前台的展示  modify by 刘晓波
                    }  catch (Exception $e){
//                        print_r($e);
                    }
                }
                if((2 == $method && !empty($email)) || 'all' == $method){
                    try{
                        $result=Mail::sendMail(array($email), $sms_content, 'E代驾 - 重置后台密码');
//                        echo $result ? '-邮件发送成功-' : '-邮件发送失败-';
                    }catch (Exception $e){
//                        print_r($e);
                    }
                }
//                echo "\n";
            }
        }

        return $new_pwd;
    }

    /**
     * 重置为默认密码
     * @param <int> $id
     */
    public function initPasswd($id){
        $model = $this->findByPk($id);
        if($model){
            $model->password = md5('11223344a');
            $save = $model->save(TRUE, array('password'));
            if($save){
                return TRUE;
            }
        }
        return FALSE;
    }


    public function haveViewPermission($controller,$action,$user_id){
        $viewer_user_info = Yii::app()->user->admin_level;
        $view_dep = Yii::app()->user->department;
        $haveper = AdminActions::model()->havepermission($controller, $action);
        if($viewer_user_info != AdminUserNew::LEVEL_ADMIN){
            $user_info = AdminUserNew::model()->findByPk($user_id);
            $in_one_dep = ($user_info->department_id == $view_dep || $user_info->group_id == $view_dep )? true : false;
            return $in_one_dep && $haveper;
        }
        return true;


    }

    public function updateStatus($id,$status){
        $mod = new AdminUserNew();
        $res = $mod->updateByPk($id,array('status'=>$status));
        return $res;
    }


    public function checkIsSupperAdmin($user_name){
        $res = $this->find('name = :name and status = :st and  auth_type = :atype',
            array(':name'=>$user_name,':st'=>self::STATUS_NORMAL,':atype'=>self::AUTH_TYPE_GOOGLE));
        //if($res && ($res['level'] == self::LEVEL_ADMIN || $res['department_id'] == 5)){
        if($res ){
            if($res['first_login'] == self::FIRST_LOGIN_TRUE || $res['first_login'] == self::FIRST_LOGIN_MODIFY_PASS){
                $ret = array('first_login'=>1,'value'=>$res['first_login']);
            }
            else {
                $ret = array('first_login'=>0,'value'=>$res['first_login']);
            }
            return $this->returnMsg(1,'yes',$ret);
        }
        return $this->returnMsg(0,'no','');
    }


    public function bindGoogleAuth($username,$password,$smscode){
        $userInfo = $this->find('name = :name and status = :st',array(':name'=>$username,':st'=>self::STATUS_NORMAL));
        if($userInfo){
            if(md5($password) == $userInfo->password){
                $mod_login = new LoginForm();
                $checkcode = $mod_login->checkSmsCode($userInfo->phone,$smscode);
                if(isset($checkcode['code']) && $checkcode['code']== 1){
                    $mod = new TFA();
                    if(!$userInfo->email) $userInfo->email = 'tmp@edaijia-inc.cn';
                    if($userInfo->first_login == self::FIRST_LOGIN_FALSE || $userInfo->first_login == self::FIRST_LOGIN_AUTH){
                        $res = $mod->getKey($userInfo->email,$userInfo->name,$userInfo->password);
                        if(isset($res['key'])){
                            //if(!isset($res['qrCode'])) $res['qrCode'] = 'http://pic.baike.soso.com/p/20120813/20120813160608-1787136401.jpg';
                            $login_first = in_array($userInfo->first_login,array(self::FIRST_LOGIN_TRUE,self::FIRST_LOGIN_MODIFY_PASS)) ? $userInfo->first_login + 2 : $userInfo->first_login;
                            $model = new AdminUserNew();
                            $res_sub = $model->updateByPk($userInfo->id,array('secure_key'=>$res['key'],'first_login'=>$login_first));
                            if($res_sub){
                                $this->sendKeyToVpn($userInfo->department_id, $res['key'], $userInfo->name, $userInfo->email);
                                return $this->returnMsg(1,'成功',$res['qrCode']);
                            }else{
                                return $this->returnMsg(10,'绑定失败 101');
                            }

                        }else{
                            EdjLog::error('用户绑定手机号码错误 id:'.$userInfo->id.' name:'.$userInfo->name.'return res:'.json_encode($res));
                            return $this->returnMsg(9,'认证失败请联系技术 错误码9');
                        }
                    }else{
                        $login_first =  $userInfo->first_login + 2;
                        $model = new AdminUserNew();
                        $res_sub = $model->updateByPk($userInfo->id,array('first_login'=>$login_first));
                        return $this->returnMsg(1,'成功',$userInfo['auth_qrcode_pic']);
                    }
                }
                else return $this->returnMsg($checkcode['code'],$checkcode['message'],$checkcode['data']);
            }else{
                return $this->returnMsg(4,'用户名或密码不正确','');
            }
        }
        else{
            return $this->returnMsg(6,'用户名或密码不正确','');
        }
    }


    /**
     * @param $dep_id
     * @param $key 解密前的key
     * @param $name
     * @param $email
     */
    public function sendKeyToVpn($dep_id,$key,$name,$email){
        $dir_name = dirname(dirname(dirname(__FILE__))).'/config/';
        $test_lock = $dir_name.'test.lock';
        $dev_lock = $dir_name.'dev.lock';
        if(file_exists($test_lock) || file_exists($dev_lock)){
            EdjLog::info('mail to vpn testenv'.$dep_id.$key.$name.$email);
        }   else{
            $content = '';
            $mod = new TFA();

            //同步更新VPN服务器上的key
            //$content .= $this->updateKeyInVpn($email, $mod->decrypt($key), '172.16.171.81');
            //$content .= $this->updateKeyInVpn($email, $mod->decrypt($key), '172.16.171.71');
            $content .= $this->updateKeyInVpn($email, $mod->decrypt($key), '111.204.119.130');
            $content .= $this->updateKeyInVpn($email, $mod->decrypt($key), '111.204.119.189');
            //绑定同时通知vpn 也同事绑定
            $dep_name = AdminDepartment::model()->getNameByIds($dep_id);
            $dep_name = $dep_name[$dep_id];
            $content .= $name.','.$mod->decrypt($key).','.$email.','.$dep_name;
            EdjLog::info('mail to vpn'.$content);
            Mail::sendMail(array('vpn@edaijia-inc.cn'), $content, '用户绑定Google auth');
        }

    }

    public function updateKeyInVpn($email, $key, $ip){
        if(!extension_loaded('ssh2')){
            $host = gethostname();
            $msg = "加载 ssh2 扩展失败. host: $host";
            return $msg;
        }

        $connection = ssh2_connect($ip, 22);
        ssh2_auth_password($connection, 'root', 'Edaijia123@');

        $stream = true;
        list($username, $dumy) = explode("@", $email);

        $msg = "$ip:\n";

        $stream = ssh2_exec($connection, "tmsh modify ltm data-group internal google_auth_keys records delete{ $username} ");
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $msg .= stream_get_contents($stream_out);

        $stream = ssh2_exec($connection, "tmsh modify ltm data-group internal google_auth_keys records add { $username{ data $key } } ");
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $msg .= stream_get_contents($stream_out);

        $stream = ssh2_exec($connection, 'tmsh save sys config ');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $msg .= stream_get_contents($stream_out);

        ssh2_exec($connection, 'exit');

        return $msg;
    }
}
