<?php
/**
 * This is the model class for table "{{admin_user}}".
 *
 * The followings are the available columns in table '{{admin_user}}':
 * @property integer $user_id
 * @property string $name
 * @property string $pass
 * @property string $phone
 * @property string $email
 * @property integer $permissions
 * @property integer $first_login
 * @property string $roles
 * @property integer $city
 * @property string $department
 * @property string $access_begin
 * @property string $access_end
 * @property string $expiration_time
 * @property string $admin_level
 * @property string $status
 * @property string $type
 */
class AdminUser extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return AdminUser the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

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
            array('phone, roles', 'required'),
            array('permissions,city,phone', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 20),
            array('pass', 'length', 'max' => 32),
            array('phone', 'length','min'=>11, 'max' => 11),
            array('email', 'email'),
            array('roles', 'length', 'max' => 3000),
            array('department', 'length', 'max' => 30),
            array('admin_level, status, type', 'length', 'max' => 1),
            array('access_begin, access_end, expiration_time,update_time', 'safe'),

            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('user_id, name, pass, phone, email, permissions, first_login, roles, city, department, access_begin, access_end, expiration_time, admin_level, status, type', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'user_id' => 'User',
            'name' => 'Name',
            'pass' => 'Pass',
            'phone' => 'Phone',
            'email' => 'Email',
            'permissions' => '所属组',
            'roles' => '所属组',
            'city' => '城市',
            'department' => '部门',
            'access_begin' => 'Access Begin',
            'access_end' => 'Access End',
            'expiration_time' => 'Expiration Time',
            'admin_level' => 'Admin Level',
            'status' => 'Status',
            'type' => 'Type',
	    'update_time' => '更新时间',
	    'create_time' => '创建时间',
        );
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
        $command=Yii::app()->db_readonly->createCommand();
        $param=array(':phone' => $phone,':status'=>1,':permissions'=>1);
        $ret = $command->select('*')
            ->from('{{admin_user}}')
            ->where('phone=:phone and status=:status and permissions=:permissions')
            ->queryRow(true,$param);

        return $ret;
    }

    public function getName($user_id)
    {
        $user = self::find('user_id=:user_id', array(
            ':user_id' => $user_id));
        if ($user) {
            return $user->name;
        }
        return '';
    }

    /**
     *
     * 所有管理员用户的数组
     * @param int $roles 只选择拥有此权限的用户
     */
    public function getUsers($roles = null)
    {
        $user_array = null;

        $criteria = new CDbCriteria();
        $criteria->condition = 'permissions=1 and user_id !=1';
        $criteria->order = 'name';

        $users = self::model()->findAll($criteria);
        foreach ($users as $user) {
            if (isset($roles)) {
                if (in_array($roles, explode(',', $user->roles))) {
                    $user_array[$user->user_id] = $user->name;
                }
            } else {
                $user_array[$user->user_id] = $user->name;
            }
        }

        return $user_array;
    }

    /*
     * 获取坐席分配人员
     *
     * */
    public function getAgentUsers($roles = array())
    {
        $user_array = null;

        $criteria = new CDbCriteria();
        $criteria->condition = 'permissions=1 and user_id !=1';
        $criteria->order = 'name';

        $users = self::model()->findAll($criteria);
        foreach ($users as $user) {
            if (isset($roles) && !empty($roles)) {
                foreach ($roles as $item) {
                    if (in_array($item, explode(',', $user->roles))) {
                        $user_array[$user->user_id] = $user->name;
                    }
                }
            } else {
                $user_array[$user->user_id] = $user->name;
            }
        }

        return $user_array;
    }


    /**
     * 得到一个员工的角色列表（带出对应d角色下的功能），以便重新定义角色,如果$user_id 为空则取出所有人的角色功能列表
     *
     * @author sunhongjing 2013-04-05
     * @param string $user_id
     */
    public function getUserGroupModsList($user_id = '')
    {
        /*

                $user_group_mods_list = array();

                $key_fix = '';
                if( !empty($user_id) ){
                    $key_fix = md5($user_id);
                }

                $cache_key = 'CACHE_USER_GROUP_MODS_LIST_'.$key_fix;

                $user_group_mods_list = Yii::app()->cache->get($cache_key);

                if( empty( $user_group_mods_list ) ){

                    $user_group_list = array();
                    $where_str = array('AND', 'permissions=:permissions' );
                    $where_param = array(':permissions'=>1 ) ;

                    //赋值
                    if( !empty($user_id) ){
                        $where_str[] = 'user_id=:user_id';
                        $where_param[':user_id'] = trim($user_id);
                    }

                    $user_group_list = Yii::app()->db_readonly->createCommand()
                            ->select('*')
                            ->from('t_admin_user')
                            ->where($where_str, $where_param)
                            ->queryAll();

                    if( !empty( $user_group_list ) ){

                        $group_mods_list = AdminRoles::model()->getAllGroupModsList();

                        foreach ($user_group_list as & $user ) {

                            $user['groups'] = array();
                            $user_groups =  @explode(",",$user['roles'] );

                            foreach ($user_groups as $g) {
                                if( !empty($g) ){
                                    $tmp[$g]['name'] = AdminGroup::model()->getName($g);
                                    $tmp[$g]['mods'] = empty($group_mods_list[$g]) ? array() : $group_mods_list[$g];
                                    $user['groups'][] = $tmp;
                                }
                            }
                        }
                        $user_group_mods_list ＝ $user_group_list；
                        //缓存1小时
                        Yii::app()->cache->set($cache_key, $user_group_mods_list, 3600);
                    }

                }

                return $user_group_mods_list;
                */

    }

    /**
     *
     * 将roles处理成数组
     */
    public function getRoles()
    {
        $user_group = array();
        if (!$this->roles) {
            return array();
        }

        $user_roles = AdminGroup::model()->findAll('id in (' . $this->roles . ')');

        if ($user_roles) {
            foreach ($user_roles as $item) {
                $user_group[$item->id] = $item->name;
            }
        }

        return $user_group;
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
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $criteria = new CDbCriteria();
        if (Yii::app()->user->city != 0) {
            $criteria->condition = 'name != "administrator" and city=' . Yii::app()->user->city;
        } else {

            if ($this->city != 0) {
                $criteria->compare('city', $this->city);
            }

            if ($this->department) {
                $criteria->compare('department', $this->department);
            }
        }

        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('pass', $this->pass, true);
        $criteria->compare('permissions', $this->permissions);



        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 30)));
    }


    /**
     * 获取全部客户用户名
     * @author mengtianxue 2013-05-28
     * @return array
     */
    public function getCallUserList()
    {
        $call_user = Yii::app()->db_readonly->createCommand()
            ->select("user_id,name")
            ->from("t_admin_user")
            ->where("department = :department and status = :status",
                array(':department' => '呼叫中心', ':status' => 1))
            ->queryAll();
        return $call_user;
    }

    /**
     * 获取部门下的用户
     * @param $did 部门id
     * @return mixed
     */
    public function getUserByDepartment($did)
    {
        $users = Yii::app()->dbadmin_readonly->createCommand()
            ->select("id,name")
            ->from(self::tableName())
            ->where("department_id = :did and status = :status",
                array(':did' => $did, ':status' => 1))
            ->queryAll();
        return $users;
    }

    /**
     * 获取一个用户信息
     * @param $uid
     * @return mixed
     */
    public function getUser($uid)
    {
        $user = Yii::app()->dbadmin_readonly->createCommand()
            ->select("id,name,department_id")
            ->from(self::tableName())
            ->where("id=:uid and status = :status",
                array(':uid' => $uid, ':status' => 1))
            ->queryRow();
        return $user;
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
        $adminUserModel=self::model()->findByPk($user_id);
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
            $adminUserModel->pass=md5($new_pwd);
            $ret=$adminUserModel->save(TRUE, array('pass'));
            if($ret){
                $sms_content= SmsTemplate::model()->getContentBySubject('user_new_password',array('$user$'=>$user,'$password$'=>$new_pwd));
                $sms_content=$sms_content['content'];
                //发送密码短信
                if((1 == $method || 'all' == $method) && Common::checkPhone($phone)){
                    try{
                        $result=Sms::SendSMS($phone,$sms_content,Sms::CHANNEL_ZCYZ);//使用单独通道 modify by sunhongjing
//                    	echo $result ? '-短信发送成功-' : '-短信发送失败-';             //model中输出会破坏前台的展示  modify by 刘晓波
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
        $model = AdminUser::model()->findByPk($id);
        if($model){
            $model->pass = md5('11223344a');
            $save = $model->save(TRUE, array('pass'));
            if($save){
                return TRUE;
            }
        }
        return FALSE;
    }

}
