<?php

/**
 * This is the model class for table "{{admin_role2action}}".
 *
 * The followings are the available columns in table '{{admin_role2action}}':
 * @property integer $id
 * @property integer $role_id
 * @property integer $action_id
 * @property integer $status
 * @property string $update_time
 * @property string $create_time
 */
class AdminRole2action extends CActiveRecord
{

    //status
    CONST STATUS_NORMAL = 0;
    CONST STATUS_FORBIDEN = -1;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{admin_role2action}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('role_id,action_id', 'required'),
			array('role_id, action_id, status', 'numerical', 'integerOnly'=>true),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, role_id, action_id, status, update_time, create_time', 'safe', 'on'=>'search'),
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
			'role_id' => 'Role',
			'action_id' => 'Action',
			'status' => 'Status',
			'update_time' => 'Update Time',
			'create_time' => 'Create Time',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('role_id',$this->role_id);
		$criteria->compare('action_id',$this->action_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('create_time',$this->create_time,true);

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
	 * @return AdminRole2action the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     *
     * @param $role_id
     * @return array
     */
    public function getActionByRole($role_id){
        $action_ids = $this->findAll(array(
            'condition'=>'status = :status and role_id = :role_id',
            'params'=>array(':status'=>self::STATUS_NORMAL,':role_id'=>$role_id)));
        $res = array();
        //$this->findAll(array('order'=> 'controller desc'));
        //print_r($action_ids);die;
        if($action_ids){
            $action_id_ar = array();
            foreach($action_ids as $obj){
                $action_id_ar[] = $obj->action_id;
            }
            //print_r($action_id_ar);die;
            $res = AdminActions::model()->getInfobyIds($action_id_ar);

        }
        return $res;
    }

    /**
     * 后台导航使用。获取用户可以使用的action_id
     * @param $role_id
     * @return array
     */
    public function getActionAllByRole(array $role_id, $only_id = false, $app_id='') {
        $role_ids = implode(',',$role_id);
        $action_ids = AdminRole2action::model()->findAll(array(
            'condition'=>'status = :status and role_id in ('.$role_ids.')',
            'params'=>array(':status'=>self::STATUS_NORMAL)
        ));
        $action_id_ar = $action_info =array();
        if($action_ids){

            foreach($action_ids as $obj){
                $action_id_ar[] = $obj->action_id;
            }
        }
        $other_action_id = AdminActions::model()->getAllFreeAction(false,$app_id);
        $action_id_ar = array_merge($action_id_ar,$other_action_id);
        if($only_id) return $action_id_ar;

        $action_info = AdminActions::model()->getAllInfobyIds($action_id_ar,$app_id);
        //print_r($action_info);die;
        return $action_info;
    }


    public function getDepByAction($action_id){
        $role_ids = $this->findAll(array(
            'condition'=>'status = :status and action_id = :action_id',
            'params'=>array(':status'=>self::STATUS_NORMAL,':action_id'=>$action_id)));
        $role_info = $dep_name = $dep_id = array();
        //$this->findAll(array('order'=> 'controller desc'));
        //print_r($action_ids);die;
        if($role_ids){
            $role_id_ar = array();
            foreach($role_ids as $obj){
                $role_id_ar[] = $obj->role_id;
            }
            //print_r($role_id_ar);
            $role_info = AdminRole::model()->getInfoByid($role_id_ar,true);
            if($role_info){
                foreach($role_info as $v){

                    $dep_id[$v['department_id']] = $v['department_id'];
                }

                $dep_name = AdminDepartment::model()->getNameByIds($dep_id);

            }

        }
        return $dep_name;
    }


    /**
     * 保存，更新 用户的角色信息 用于对角色组的权限分配，更改
     * @param $user_id
     * @param $roles
     * @return bool
     * @author duke
     */
    public function saveRole2Action($role_id,$actions,$status=self::STATUS_NORMAL){
        if(is_numeric($actions)){
            $actions = array($actions);
        }
        if(!is_array($actions)){
            return false;
        }

        $old_role_action = $this->findAll('role_id=:role_id',array(':role_id'=>$role_id)); //查出该角色所有的role2action
        if($old_role_action){

            $old_arr = $old_all_info = array();
            foreach($old_role_action as $obj){
                $old_arr[$obj->id]           = $obj->action_id; //old_arr 是 该角色所拥有的所有action
                $old_all_info[$obj->action_id] = $obj->attributes;
            }
            $new_add_action_id = array_diff($actions,$old_arr); //新增的权限id
            $forbiden_role_id = array_diff($old_arr,$actions); //取消授权的权限id
            $alreday_has_role_id = array_intersect($old_arr,$actions); //原来就有的角色id 需要判断是否是禁用状态 如果是则需要改成默认状态。


            //print_r($new_add_role_id);
            if($new_add_action_id){
                foreach($new_add_action_id as $role_id_new_add){
                    $arr = array('role_id' => $role_id,'action_id' => $role_id_new_add,'status'=> $status,'create_time' => date('Y-m-d H:i:s'));
                    $this->addInfo($arr);
                }
            }
            $role_info = AdminRole::model()->findByPk($role_id);
            $need_modify_sub_role2action = false;
            if($role_info->type != AdminRole::TYPE_NORMAL){
                $need_modify_sub_role2action = true;
            }
            //print_r(array_keys($forbiden_role_id));die;
            if($forbiden_role_id){
                $arr = array('status' => self::STATUS_FORBIDEN);
                $pk = array_keys($forbiden_role_id);
                $this->updateInfo($pk,$arr);
                if($need_modify_sub_role2action){
                    $this->forbidenAuth($role_info,$pk);
                }
            }
            //print_r($alreday_has_role_id);die;

            if($alreday_has_role_id){
                $arr = array('status' => $status);
                $pk = array_keys($alreday_has_role_id);
                $this->updateInfo($pk,$arr);
                if($need_modify_sub_role2action && ($status == self::STATUS_FORBIDEN)){
                    $this->forbidenAuth($role_info,$pk);
                }
                //$this->updateInfo(array_keys($alreday_has_role_id),$arr);
            }
        }
        else{
            if($actions){
                foreach($actions as $r_id){
                    $param = array('role_id'=> $role_id,'action_id'=> $r_id,'status'=> $status,'create_time'=>date('Y-m-d H:i:s'));
                    $this->addInfo($param);
                }
            }
        }
    }

    /**
     * 在禁用部门管理员默认角色组 或 小组管理员角色组的权限时 需要同时禁用部门内或组内的其他角色组的相同权限
     * @param $role_infos  role model obj
     * @param $pk array role2action 主键
     * @param $is_action_id  是否是
     * @return bool
     */
    public function forbidenAuth($role_infos,$pk,$is_action_id = false){

        $dep_id = $role_infos->department_id;
        $type = $role_infos->type;
        //通过dep_id 查出对应的 非管理员角色组的role_id
        $ids = $role_ids = array();
        if($type == AdminRole::TYPE_DEPART) {
            $dep_ids = AdminDepartment::model()->findAll('id = :id or parent_id = :parent_id',array(':id'=>$dep_id,':parent_id'=>$dep_id));
            if($dep_ids){
                foreach($dep_ids as $obj){
                    $ids[] = $obj->id;
                }
            }
        }else {
            $ids[] = $dep_id;
        }
        $id_str = implode(',',$ids);
        //echo 'id_str'.$id_str;
        $role_info = AdminRole::model()->findAll('department_id in ('.$id_str.') and ( type = :type or type = :type1) and status = :status',
                                                    array(':type'=>AdminRole::TYPE_NORMAL,':type1'=>AdminRole::TYPE_GROUP,':status'=>AdminRole::STATUS_NORMAL));
        if($role_info){
            foreach($role_info as $k){
                $role_ids[]=$k->id;
            }
            $role_ids = implode(',',$role_ids);
            //echo 'role_ids'.$role_ids;
            foreach($pk as $pk_id){
                if(!$is_action_id){
                $role2action_info = AdminRole2action::model()->findByPk($pk_id);
                    $action_id = $role2action_info->action_id;
                }else{
                    $action_id = $pk_id;
                }
                //echo $role_ids.'action_id:'.$action_id;
                $res = AdminRole2action::model()->updateAll(array('status'=>AdminRole2action::STATUS_FORBIDEN),
                    'role_id in ('.$role_ids.') and action_id = :ac_id and status = :status',
                    array(':ac_id'=>$action_id,':status'=>AdminRole2action::STATUS_NORMAL));
            }

        }
        return true;
    }




    // 保存，更新 用户的角色信息 只对当前action 生效，不具有排他性 用于对action 分配给对应部门的管理员角色
    /**
     * 1,通过参数dep_id 查出对应的管理员role_id  -> $role_ids_new
     * 2,查询出已经拥有该 action_id 权限的 部门管理员role_id -> $role_ids_old
     * 3,如果 ￥role_ids_old 存在则
     *      判断新增role_id->新增一条记录
     *      判断出取消权限的 role_id 做禁用操作 同时 禁用该角色对应部门下得所有记录
     *      判断出交集数据 ， 更新交集数据为参数的status 状态 如果status 是禁用 则禁用部门内的角色权限
     * 否则创建对应的role2actin
     * @param $action_id
     * @param array $dep_ids
     * @param int $status
     */
    public function saveInfoByDep($action_id,array $dep_ids,$status = self::STATUS_NORMAL){
        if($dep_ids){
            $role_ids_new = AdminRole::model()->getDep2Roles($dep_ids); //分配权限的部门对应的角色id
        } else $role_ids_new = array();
        $role_ids_old = $this->getAllDepRoleByAction($action_id);

        if($role_ids_old){
            $new_add_role_id = array_diff($role_ids_new,$role_ids_old); //新增的权限id
            $forbiden_role_id = array_diff($role_ids_old,$role_ids_new); //取消授权的权限id
            $alreday_has_role_id = array_intersect($role_ids_old,$role_ids_new); //原来就有的角色id 需要判断是否是禁用状态 如果是则需要改成默认状态。


            //print_r($new_add_role_id);
            if($new_add_role_id){
                foreach($new_add_role_id as $role_id_new_add){
                    $arr = array('role_id' => $role_id_new_add,'action_id' => $action_id,'status'=> $status,'create_time' => date('Y-m-d H:i:s'));
                    $this->addInfo($arr);
                }
            }
            //print_r(array_keys($forbiden_role_id));die;
            if($forbiden_role_id){

                $arr = array('status' => self::STATUS_FORBIDEN);
                $this->updateInfo(array_keys($forbiden_role_id),$arr);

                //禁用部门内所有相关action权限
                $this->forbidenActionByRoleId($forbiden_role_id,$action_id);
            }
            //print_r($alreday_has_role_id);die;

            if($alreday_has_role_id){
                $arr = array('status' => $status);
                $this->updateInfo(array_keys($alreday_has_role_id),$arr);

                if($status == self::STATUS_FORBIDEN){
                    $this->forbidenActionByRoleId($alreday_has_role_id,$action_id);
                }
            }
        }
        else{
            if($role_ids_new){
                foreach($role_ids_new as $r_id){
                    $param = array('role_id'=> $r_id,'action_id'=> $action_id,'status'=> $status,'create_time'=>date('Y-m-d H:i:s'));
                    $this->addInfo($param);
                }
            }
        }
    }

    /**
     * insert info
     * @param $param
     * @return bool
     * @author duke
     */
    public function addInfo(  $param){
        $mod = new AdminRole2action();
        $mod->setAttributes($param);
        //print_r($mod);
        $res = $mod->save();
        //var_dump($res);die;
        return $res;
    }

    /**
     * update info
     * @param $id
     * @param $param
     * @return int
     * @author duke
     */
    public function updateInfo($id,$param){
        $mod = new AdminRole2action();
        $res = $mod -> updateByPk($id,$param);
        // print_r($res);die;
        return $res;
    }


    /**
     * 根据actionid 返回 拥有该权限的所有部门默认管理角色id
     * @param $action_id
     * @return array
     */
    public function getAllDepRoleByAction($action_id){
        $all_role_id = AdminRole::model()->findAll(
            array(
                'condition'=>'status=:status and type=:type',
                'params'=>array(
                            ':status'=>AdminRole::STATUS_NORMAL,
                            ':type'=>AdminRole::TYPE_DEPART
                            )
            )
        );
        $res = array();
        if($all_role_id){
            $role_ids = array();
            foreach($all_role_id as $obj){
                $role_ids[]=$obj->id;
            }
            $id_str = implode(',',$role_ids);
            $role2actionInfo = $this->findAll('role_id in ('.$id_str.') and action_id = :action_id',array(':action_id'=>$action_id) );
            if($role2actionInfo){
                foreach($role2actionInfo as $obj){
                    $res[$obj->id] = $obj->role_id;
                }
            }
        }
        return $res;

    }

    /**
     * 禁用role_ids （管理员默认角色组id) 内所有角色 该action_id 权限
     * @param $role_ids 管理员角色组id
     * @param $action_id action_id
     * @return bool
     */
    public function forbidenActionByRoleId( array $role_ids,$action_id){
        //echo 'action_id:'.$action_id;
        if ($role_ids){
            foreach($role_ids as $role_id){
                //echo $role_id.'----';
                $role_info = AdminRole::model()->findByPk($role_id);
                //print_r($role_info);
                $this->forbidenAuth($role_info,array($action_id),true);
            }
            //die;
            return true;
        }

        return false;
    }

//    public function getIdByRoleid($user_id){
//        $Criteria = new CDbCriteria();
//        $Criteria->condition = 'user_id = :user_id and status = :status';
//        $Criteria->params = array(':user_id'=>$user_id,':status'=>self::STATUS_NORMAL);
//        $Criteria->select = 'role_id';
//        $res = $this->findAll($Criteria); //
//        print_r($res);
//    }
}
