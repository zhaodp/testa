<?php

/**
 * This is the model class for table "{{menu}}".
 *
 * The followings are the available columns in table '{{menu}}':
 * @property integer $id
 * @property integer $parentid
 * @property string $name
 * @property integer $is_show
 * @property integer $roles_id
 * @property integer $position
 * @property string $description
 * @property string $operator
 * @property string $create_time
 */
class Menu extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Menu the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{menu}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, operator, create_time', 'required'),
            array('parentid, is_show, is_target, roles_id, position', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>25),
            array('description', 'length', 'max'=>255),
            array('operator', 'length', 'max'=>50),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, parentid, name, is_show, is_target, roles_id, position, description, operator, create_time', 'safe', 'on'=>'search'),
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
            'parentid' => '父ID',
            'name' => '菜单名称',
            'is_show' => '是否展示',
            'is_target' => '新窗口打开',
            'roles_id' => '角色ID',
            'position' => '排序',
            'description' => '描述',
            'operator' => '操作人',
            'create_time' => 'Create Time',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('parentid',$this->parentid);
        $criteria->compare('name',$this->name);
        $criteria->compare('is_show',$this->is_show);
        $criteria->compare('is_target',$this->is_target);
        $criteria->compare('roles_id',$this->roles_id);
        $criteria->compare('position',$this->position);
        $criteria->compare('description',$this->description);
        $criteria->compare('operator',$this->operator);
        $criteria->compare('create_time',$this->create_time);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function afterSave(){
       self::removeAllCache();
       return parent::afterSave();
    }

    public function afterDelete(){
        self::removeAllCache();
        return parent::afterDelete();
    }

    /**
     * 更改菜单排序
     * @param $id   菜单ID
     * @param $pid  父ID
     * @param $old_sqe  当前排序
     * @param $new_sqe  新排序
     * @author bidong  2013-05-22
     */
    public function changeSequence($pid,$old_sqe,$new_sqe){

        $attributes=array('position'=>$new_sqe);
        $condition='parentid=:pid and position=:old_sqe';
        $params=array(':pid'=>$pid,':old_sqe'=>$old_sqe);
        $ret = $this->updateAll($attributes,$condition,$params);

        if($ret>0)
            return true;
        else
            return false;

    }

    /**
     * 获取某一级菜单的最大排序
     * @param $id
     * @param $pid
     * @author bidong  2013-05-22
     */
    public function getMaxSequence($pid){
        $max=false;
        $menuSqe=Yii::app()->db_readonly->CreateCommand()
            ->select('MAX(position) as maxsqe')
            ->from($this->tableName())
            ->where('parentid=:pid', array(':pid'=>$pid))
            ->queryRow();

        if($menuSqe){
            $max=$menuSqe['maxsqe'];
        }else{
            $max=1;
        }


        return $max;
    }

    /**
     * 获取某类菜单
     * @param $pid
     * @author bidong  2013-05-22
     */
    public function getMenuListByPid($pid){
        $menuInfo=Yii::app()->db_readonly->CreateCommand()
            ->select('id,parentid,name,is_show,is_target,roles_id,position')
            ->from($this->tableName())
            ->where('parentid=:pid', array(':pid'=>$pid))
            ->order('position asc')
            ->queryAll();

       return $menuInfo;
    }

    /**
     * 获取菜单列表，arrayList
     * @author bidong 2013-5-28
     * @return array
     */
    public function getMenuList(){
        $retMenu=array();
        $menuMain=$this->getMenuListByPid(0);
        foreach($menuMain as $menu){
            $menuArr=array();
            $id=$menu['id'];
            $menuArr['id']=$id;
            $menuArr['parentid']=$menu['parentid'];
            $menuArr['name']=$menu['name'];
            $menuArr['is_show']=$menu['is_show'];
            $menuArr['roles_id']=$menu['roles_id'];
            $menuArr['position']=$menu['position'];
            $retMenu[]=$menuArr;
            $menuSub=$this->getMenuListByPid($id);
            if($menuSub){
                foreach($menuSub as $m){
                    $temp=array();
                    $temp['id']=$m['id'];
                    $temp['parentid']=$m['parentid'];
                    $temp['name']=$m['name'];
                    $temp['is_show']=$m['is_show'];
                    $temp['roles_id']=$m['roles_id'];
                    $temp['position']=$m['position'];

                    $retMenu[]=$temp;
                    $menuThird=$this->getMenuListByPid($m['id']);
                    if($menuThird){
                        foreach($menuThird as $sub){
                            $temp=array();
                            $temp['id']=$sub['id'];
                            $temp['parentid']=$sub['parentid'];
                            $temp['name']=$sub['name'];
                            $temp['is_show']=$sub['is_show'];
                            $temp['roles_id']=$sub['roles_id'];
                            $temp['position']=$sub['position'];

                            $retMenu[]=$temp;
                        }
                    }

                }
            }

        }
        return $retMenu;
    }

    /**
     * 获取菜单列表，array
     *  @author zys 2015-11-30
     */
    public function getMenuArr($user_id, $app_id='') {
        if (!empty($app_id)) {
            $key = $user_id.'_'.$app_id;
        } else {
            $key = $user_id;
        }
        //取缓存
        $cacheData = AdminCache::model()->get(AdminCache::CACHE_MENU,$key);
        if(empty($cacheData) || (! unserialize($cacheData))) {
            $cacheData = self::reloadMenuCache($user_id,$app_id);
            if (!$cacheData) {
                return false;
            }
            //$cacheData=AdminCache::model()->get(AdminCache::CACHE_MENU,$user_id);
        }
        //print_r( unserialize($cacheData));
        return unserialize($cacheData);
    }

    /**
     * 重新构建用户菜单缓存
     * @param $user_id 用户ID
     * @param $app_id 应用ID
     * @author zys
     */
    public function reloadMenuCache($user_id, $app_id='') {
        $retArr = $userMods = array();
        if (isset(Yii::app()->user) && !empty(Yii::app()->user->admin_level)) {
            $admin_level = Yii::app()->user->admin_level;
        } else {
            $user = AdminUserNew::model()->getUserById($user_id);
            if (!empty($user) && isset($user['level'])) {
                $admin_level = $user['level'];
            } else {
                return false;
            }
        }
        //获取用户有权限的模块
        $userMods =  $admin_level == AdminUserNew::LEVEL_ADMIN ? AdminActions::model()->getAllAction('id',$app_id) : AdminUser2role::model()->getActionIdByUserid($user_id,$app_id);
        //print_r($userMods);die;

	    $apps = AdminApp::model()->getAllToArray();

        $menuMain=$this->getMenuListByPid(0);
        foreach($menuMain as $menu){
            $menuArr=array();
            $id=$menu['id'];
            $menuArr['id']=$id;
            $menuArr['parentid']=$menu['parentid'];
            $menuArr['name']=$menu['name'];

            $menuSub=$this->getMenuListByPid($id);
            if($menuSub){
                foreach($menuSub as $m){
                    if(($admin_level == AdminUserNew::LEVEL_ADMIN || array_key_exists($m['roles_id'],$userMods)) && $m['is_show'] && isset($userMods[$m['roles_id']]) ){

                        $temp=array();
                        $temp['id']=$m['id'];
                        $temp['parentid']=$m['parentid'];
                        $temp['name']=$m['name'];
                        $temp['is_show']=$m['is_show'];
                        $temp['is_target']=$m['is_target'];
                        $temp['roles_id']=$m['roles_id'];
                        $temp['position']=$m['position'];
                        $temp['controller']=$userMods[$m['roles_id']]['controller'];
                        $temp['action']=$userMods[$m['roles_id']]['action'];
                        $temp['action_url']=$userMods[$m['roles_id']]['action_url'];
                        $temp['app_url']=$apps[$userMods[$m['roles_id']]['app_id']]['url'];

                        $menuThird=$this->getMenuListByPid($m['id']);
                        if($menuThird){
                            foreach($menuThird as  $t){
                                if(($admin_level == AdminUserNew::LEVEL_ADMIN || array_key_exists($t['roles_id'],$userMods)) && $t['is_show'] && isset($userMods[$t['roles_id']]) ){
                                    $temps=array();
                                    $temps['id']=$t['id'];
                                    $temps['parentid']=$t['parentid'];
                                    $temps['name']=$t['name'];
                                    $temps['is_show']=$t['is_show'];
                                    $temps['is_target']=$t['is_target'];
                                    $temps['roles_id']=$t['roles_id'];
                                    $temps['position']=$t['position'];
                                    $temps['controller']=$userMods[$t['roles_id']]['controller'];
                                    $temps['action']=$userMods[$t['roles_id']]['action'];
                        	    $temps['action_url']=$userMods[$t['roles_id']]['action_url'];
                        	    $temps['app_url']=$apps[$userMods[$t['roles_id']]['app_id']]['url'];
                                    $temp['third'][]=$temps;
                                }
                            }
                        }
                        $menuArr['sub'][]=$temp;
                    }
                }
            }
            if(!empty($menuArr['sub']) || $menu['is_show'])
                $retArr[]=$menuArr;
        }
        //写缓存
        $res = serialize($retArr);
        if (!empty($app_id)) {
            $key = $user_id.'_'.$app_id;
        } else {
            $key = $user_id;
        }
        AdminCache::model()->set(AdminCache::CACHE_MENU, $key, $res);

        return $res;
    }


    public function removeAllCache(){
        $users=Yii::app()->dbadmin_readonly->CreateCommand()
            ->select('id')
            ->from('t_admin_user')
            ->where('status=:status', array(':status'=>AdminUserNew::STATUS_NORMAL))
            ->queryAll();

        if($users){
            foreach($users as $u){
                AdminCache::model()->delete(AdminCache::CACHE_MENU,$u['id']);
            }
        }
    }

    public static function getClassNameByLabel($name){
        switch($name){
            case '公告': $class = 'edj-v2-ico-notice'; break;
            case '运营': $class = 'edj-v2-ico-operate'; break;
            case '呼叫中心': $class = 'edj-v2-ico-call'; break;
            case '客户': $class = 'edj-v2-ico-client'; break;
            case '品质监控': $class = 'edj-v2-ico-brand'; break;
            case '司机管理': $class = 'edj-v2-ico-driverm'; break;
            case '市场': $class = 'edj-v2-ico-market'; break;
            case '财务': $class = 'edj-v2-ico-finance'; break;
            case '系统': $class = 'edj-v2-ico-sys'; break;
            default: $class = 'edj-v2-ico-notice'; break;
        }
        return $class;
    }

    public static function initMenu($label, $labelId, $className, $link = '', $is_target = '0'){
        $menu = array();
        $menu['label'] = $label;
        $menu['labelId'] = $labelId;
        if($link){
            $menu['link'] = $link;
            $menu['is_target'] = $is_target;
        }
        if($className){
            $menu['className'] = $className;
        }
        $menu['hasSub'] = false;

        return $menu;
    }

} 
