<?php

/**
 * This is the model class for table "{{admin_user2role}}".
 *
 * The followings are the available columns in table '{{admin_user2role}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $role_id
 * @property string $update_time
 * @property string $create_time
 */
class AdminUser2role extends CActiveRecord
{
    //status
    CONST STATUS_NORMAL = 0;
    CONST STATUS_FORBIDEN = -1;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{admin_user2role}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('user_id,role_id', 'required','on'),
			array('user_id, role_id', 'numerical', 'integerOnly'=>true),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, role_id, update_time, create_time', 'safe', 'on'=>'search'),
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
            'user'=>array(self::BELONGS_TO,'AdminUserNew','user_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'role_id' => 'Role',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('role_id',$this->role_id);
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
	 * @return AdminUser2role the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * 保存，更新 用户的角色信息
     * @param $user_id
     * @param $roles
     * @return bool
     * @author duke
     */
    public function saveUser2Role($user_id,$roles,$status = self::STATUS_NORMAL){
        if(is_numeric($roles)){
            $roles = array($roles);
        }
        if(!is_array($roles)){
            return false;
        }

        $old_user_role = $this->findAll('user_id=:user_id',array(':user_id'=>$user_id));
        if($old_user_role){

            $old_arr = $old_all_info = array();
            foreach($old_user_role as $obj){
                $old_arr[$obj->id]           = $obj->role_id;
                $old_all_info[$obj->role_id] = $obj->attributes;
            }
            $new_add_role_id = array_diff($roles,$old_arr); //新增的权限id
            $forbiden_role_id = array_diff($old_arr,$roles); //取消授权的权限id
            $alreday_has_role_id = array_intersect($old_arr,$roles); //原来就有的角色id 需要判断是否是禁用状态 如果是则需要改成默认状态。


            //print_r($new_add_role_id);
            if($new_add_role_id){
                foreach($new_add_role_id as $role_id_new_add){
                    $arr = array('user_id' => $user_id,'role_id' => $role_id_new_add,'status'=>$status, 'create_time' => date('Y-m-d H:i:s'));
                    $this->addInfo($arr);
                }
            }
            //print_r(array_keys($forbiden_role_id));die;
            if($forbiden_role_id){

                $arr = array('status' => self::STATUS_FORBIDEN);
                $this->updateInfo(array_keys($forbiden_role_id),$arr);
            }
            //print_r($alreday_has_role_id);die;

            if($alreday_has_role_id){
                $arr = array('status' => $status);
                $this->updateInfo(array_keys($alreday_has_role_id),$arr);
            }
        }
        else{
            foreach($roles as $r_id){
                $param = array('user_id'=> $user_id,'role_id'=> $r_id, 'status'=> $status,'create_time'=>date('Y-m-d H:i:s'));
                $this->addInfo($param);
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
        $mod = new AdminUser2role();
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
        $mod = new AdminUser2role();
        $res = $mod -> updateByPk($id,$param);
       // print_r($res);die;
        return $res;
    }

    /**
     * 展示用户权限的详细信息
     * @param $user_id
     * @return array|bool
     */
    public function getActionById($user_id){
        $roles = $this->findAll('user_id=:user_id and status=:status',array(':user_id'=>$user_id,':status'=> self::STATUS_NORMAL));
        //print_r($roles);
        if($roles){
            $role = array();
            $mod_role2action = AdminRole2action::model();
            foreach($roles as $obj){
                $role[$obj->role_id] = $obj->role_id;
                //获取角色对应的action
                $role_action[$obj->role_id] = $mod_role2action->getActionByRole($obj->role_id);
            }
            //print_r($role);
            //获取角色名称
            $role_info = AdminRole::model()->getInfoByid($role);
            //echo 'aaa';print_r($role_info);die;

            if($role_info){
                foreach($role_info as $k => $info) {
                    $role_info[$k]['actions'] = $role_action[$info['id']];
                }
            }
            return $role_info;



        }
        return false;
    }

    public function getActionIdByUserid($user_id, $app_id=''){
        $roles = $this->findAll('user_id=:user_id and status=:status',array(':user_id'=>$user_id,':status'=> self::STATUS_NORMAL));
        //print_r($roles);

        if($roles){
            $role = array();
            $mod_role2action = AdminRole2action::model();
            foreach($roles as $obj){
                $role[$obj->role_id] = $obj->role_id;
            }
            $actions = $mod_role2action->getActionAllByRole($role,false,$app_id);

            //print_r($role);
            //获取角色名称
        }
        else{
            $actions = AdminActions::model()->getAllFreeAction(true,$app_id);

        }
        return $actions;
        //return false;
    }


    public function getRoleInfo($user_id,$get_id_only = false){
        $roles = AdminUser2role::model()->findAll('user_id=:user_id and status=:status',array(':user_id'=>$user_id,':status'=> self::STATUS_NORMAL));

        if($roles){
            $role = array();
            foreach($roles as $obj){
                $role[$obj->role_id] = $obj->role_id;
            }
            if($get_id_only) return $role;
            //print_r($role);
            //获取角色名称
            $role_info = AdminRole::model()->getInfoByid($role);
            //echo 'aaa';print_r($role_info);die;

            return $role_info;



        }
        return false;
    }


    public function getCountByRoleId($role_id){
        $count = $this->count('role_id = :role_id and status = :status',array(':role_id'=> $role_id,':status'=>self::STATUS_NORMAL));
        return $count;
    }
}
