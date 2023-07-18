<?php

/**
 * This is the model class for table "{{admin_role}}".
 *
 * The followings are the available columns in table '{{admin_role}}':
 * @property integer $id
 * @property integer $department_id
 * @property string $name
 * @property integer $type
 * @property string $update_time
 * @property string $create_time
 */
class AdminRole extends CActiveRecord
{

    //type
    CONST TYPE_NORMAL = 0; // 普通角色组
    CONST TYPE_DEPART = 1; //该角色是部门管理员角色组
    CONST TYPE_GROUP  = 2; //小组管理员角色组

    //status
    CONST STATUS_NORMAL = 0; //默认正常
    CONST STATUS_FORBIDEN = -1; //禁用
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{admin_role}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,department_id', 'required'),
			array('department_id, type,status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>32),
            array('desc','length','max'=>255),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, department_id, name, type, status, update_time, create_time', 'safe', 'on'=>'search'),
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
			'department_id' => '部门id',
			'name' => '角色名称',
            'desc' => '角色描述',
			'type' => '是否是部门默认角色',
            'status'=> '状态',
			'update_time' => '更新时间',
			'create_time' => '创建时间',
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
		$criteria->compare('department_id',$this->department_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('status',$this->status);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array('pageSize'=>800),
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
	 * @return AdminRole the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


    public static function getRoleTypeList($type = ''){
        $type_arr = array(
            self::TYPE_NORMAL =>'普通员工',
            self::TYPE_DEPART => '部门管理员',
            self::TYPE_GROUP  => '小组管理员',
        );
        if($type !== ''){
            if(isset($type_arr[$type])){
                return $type_arr[$type];
            }
            else return false;
        }
        return $type_arr;
    }


    public static function getRoleStatusList($status = ''){
        $status_arr = array(
            self::STATUS_NORMAL =>'正常',
            self::STATUS_FORBIDEN => '禁用',
        );
        if($status !== ''){
            if(isset($status_arr[$status])){
                return $status_arr[$status];
            }
            else return false;
        }
        return $status_arr;
    }

    /**
     * 获取部门管理员,小组管理员 对应的角色
     * @param $department_id
     * @return array|bool|mixed|null
     */
    public function getDepartmentRoles($department_id,$type = self::TYPE_DEPART){
        $res = $this->find('department_id = :dep_id and type = :type and status = :status',array(':dep_id'=>$department_id,':type' => $type,':status'=>self::STATUS_NORMAL));
        if($res){
            return $res->attributes;
        }
        return false;
    }

    /**
     * 通过部门id 获取该部门对应的角色id
     * @param $dep_ids
     * @return array
     */
    public function getDep2Roles($dep_ids ){
        if(!is_array($dep_ids)){
            $dep_ids = array($dep_ids);
        }
        $dep_ids = implode(',',$dep_ids);
        $role_info = $this->findAll(array('condition'=>'status = :status and type = :type and department_id in ('.$dep_ids.')',
            'params'=>array(':status'=> self::STATUS_NORMAL,':type'=>self::TYPE_DEPART)));

        $res = array();
        if($role_info){

            foreach($role_info as $obj){
                $res[$obj->department_id]=$obj->id;
            }

        }
        return $res;
    }


    /**
     * 获取部门内的所有角色信息
     * @param $department_id
     * @return array|bool|mixed|null
     */
    public function getRolesByDepid($department_id,$nameonly = false){
        $res = $this->findAll('department_id = :dep_id  and status = :status',array(':dep_id'=>$department_id,':status'=>self::STATUS_NORMAL));
        if($res){
            $result = array();
            if(!$nameonly){
                foreach($res as $obj){
                    $result[$obj->id] = $obj->attributes;
                }
            }
            else{
                $result['']= '全部';
                foreach($res as $obj){
                    $result[$obj->id] = $obj->name;
                }
            }
            return $result;
        }
        return false;
    }

    /**
     * 根据角色id 获取角色信息 type : 是否只获取部门管理员角色信息
     * @param $roles_id 角色id int | array
     * @param bool $type 是否需要管理员默认角色 true 是  false 否
     * @author duke
     * @return array
     */

    public function getInfoByid($roles_id,$type = false){
        if(!is_array($roles_id)){
            $roles_id = array($roles_id);
        }
        $roles_id = implode(',',$roles_id);
        if($type == true){
            $condition = 'id in ( '.$roles_id.' ) and status = :status and type = :type';
            $params = array(':status'=>self::STATUS_NORMAL,':type'=>self::TYPE_DEPART);
        }
        else {
            $condition = 'id in ( '.$roles_id.' ) and status = :status';
            $params = array(':status'=>self::STATUS_NORMAL);
        }

        $res = $this->findAll($condition,$params);

        $result = array();
        if($res){
            foreach($res as $obj){
                $result[$obj->id]= $obj->attributes;
            }
        }
        return $result;
    }

    /** 获取角色详细信息 包括对应的action
     * @param $role_id
     * @return array
     */
    public function getRoleDetail($role_id){

        $mod_role2action = AdminRole2action::model();

        //获取角色对应的action
        $role_action[$role_id] = $mod_role2action->getActionByRole($role_id);

        //print_r($role);
        //获取角色名称
        $role_info = $this->getInfoByid($role_id);
        //echo 'aaa';print_r($role_info);die;

        if($role_info){
            foreach($role_info as $k => $info) {
                $role_info[$k]['actions'] = $role_action[$info['id']];
            }
        }
        return $role_info;
    }


    /**
     * 单纯创建一个角色
     * @param $param array('department_id','name','type','status','create_time')
     */
    public function createRole($param){
        $mod = new AdminRole();

        $mod->attributes = $param;
        $mod->save();
        return $mod->primaryKey;
    }

    public function haveEditPermission($type){
        $admin_level = Yii::app()->user->admin_level;
        //$admin_level = 1;
        if($admin_level == AdminUserNew::LEVEL_ADMIN){
            //echo '2';
            return true;
        }
        if($admin_level == AdminUserNew::LEVEL_DEPARTMENT_ADMIN && $type == self::TYPE_DEPART){
            //echo '1';
            return false;
        }
        if($admin_level == AdminUserNew::LEVEL_GROUP_ADMIN && $type == self::TYPE_GROUP){
            return false;
        }
        //echo '3';
        return true;

    }


}
