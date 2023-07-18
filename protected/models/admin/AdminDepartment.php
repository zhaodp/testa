<?php

/**
 * This is the model class for table "{{admin_department}}".
 *
 * The followings are the available columns in table '{{admin_department}}':
 * @property integer $id
 * @property string $name
 * @property string $desc
 * @property string $update_time
 * @property string $create_time
 */
class AdminDepartment extends CActiveRecord
{
    //status
    CONST STATUS_NORMAL = 0;
    CONST STATUS_FORBIDEN = -1;

    //access auth
    CONST ACCESS_NORMAL = 0;  //需要验证的权限
    CONST ACCESS_LOGIN_AUTH = 1; // 登录即可访问的权限
    CONST ACCESS_DRIVER_AUTH = 2; //司机登录后用得权限
    CONST ACCESS_SUPER_AUTH = 3; //超级管理员的权限


    CONST IS_DEP = 0;   //部门
    CONST IS_GROUP = 1; //小组


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{admin_department}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,desc,status', 'required'),
			array('name', 'length', 'max'=>32),
			array('desc', 'length', 'max'=>255),
            array('status,parent_id', 'numerical', 'integerOnly'=>true),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('name, desc,parent_id, update_time, create_time', 'safe', 'on'=>'search'),
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
			'name' => '部门名称',
			'desc' => '描述',
            'status' => '状态',
            'parent_id'=>'父ID',
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

		//$criteria->compare('id',$this->id);
        $criteria->compare('parent_id',$this->parent_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('desc',$this->desc,true);
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
	 * @return AdminDepartment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


    public static function getStatus($status = ''){
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

    public static function getAccessAuth($access_auth = ''){
        $access_arr = array(
            self::ACCESS_NORMAL =>'需要验证权限',
            self::ACCESS_LOGIN_AUTH =>'登录即可使用权限',
            self::ACCESS_DRIVER_AUTH =>'司机使用权限',
            self::ACCESS_SUPER_AUTH =>'超级管理员权限',

        );
        if($access_auth !== ''){
            if(isset($access_arr[$access_auth])){
                return $access_arr[$access_auth];
            }
            else return false;
        }
        return $access_arr;

    }

    public function getInfoByName($department_name){
        $res = $this->find('name = :name',array(':name'=>$department_name));
        if($res){
            return $res->attributes;
        }
        return false;
    }


    /**
     * @param $ids
     * @return array
     */
    public function getInfoByIds($ids){
        if(!is_array($ids)){
            $ids = array($ids);
        }
        $ids = implode(',',$ids);

        $res = $this->find('id in( :ids) and status = :status',array(':ids'=>$ids,':status' => self::STATUS_NORMAL));
        $result = array();
        if($res){
            foreach($res as $obj){
                $result[$obj->id]= $obj->attributes;
            }
        }
        return $result;
    }

    /**
     * @param $ids
     * @return array
     */
    public function getNameByIds($ids){
        if(!is_array($ids)){
            $ids = array($ids);
        }
        $ids = implode(',',$ids);

        $res = $this->findAll('id in( '.$ids.') and status = :status',array(':status' => self::STATUS_NORMAL));
        $result = array();
        //var_dump($res);
        if($res){
            foreach($res as $obj){
                $result[$obj->id]= $obj->name;
            }
        }
        return $result;
    }


    /**
     * 获取所有部门信息
     * @param bool $show_name_only
     * @return array
     */
    public function getAll($show_name_only = false,$dep_id = 0){
        //echo $dep_id;
        $res = $this->findAll('parent_id = :p_id',array(':p_id'=>$dep_id));
        //print_r($res);
        $data = array();
        if($res){

            if($show_name_only){
                if($dep_id)
                    $data[''] = '小组';
                else
                $data[''] = '部门';
                foreach($res as  $obj){
                    $data[$obj->id] = $obj->name;
                }
            }else{
                foreach($res as $obj){
                    $data[]= $obj->attributes;
                }
            }
        }
        else{
            if($show_name_only){
                if($dep_id)
                    $data[''] = '小组';
                else
                    $data[''] = '部门';
            }
        }
        return $data;
    }



    public function getInfoByid($dep_id){
        $res = $this->findByPk($dep_id);
        if($res)
        return $res->attributes;
        return false;
    }

    public  function getDepName($dep_id){
        $res = $this->findByPk($dep_id);
        if($res)
        return $res->name;
        else return '无';
    }

    /**
     * 获取部门 角色id 对应的数组
     */
    public function getDep2Role(){
        $res = $this->findAll('status = :status',array(':status' => self::STATUS_NORMAL));
        $arr_result = array();
        if($res){
            $ids = $name = array();
            foreach($res as $obj){
                $ids[] = $obj->id;
                $name[$obj->id] = $obj->name;
            }
            $result = AdminRole::model()->getDep2Roles($ids);
            foreach($ids as $id){
                if(isset($result[$id])){
                    $arr_result[$id] = $name[$id];
//                    $role_name = $name[$id].'管理员角色组';
//                    $role_arr = array(
//                        'department_id' => $id,
//                        'name'=>$role_name,
//                        'type'=>AdminRole::TYPE_DEPART,
//                        'status'=>AdminRole::STATUS_NORMAL,
//                        'create_time'=>date('Y-m-d H:i:s')
//                    );
//                    $role_id = AdminRole::model()->createRole($role_arr);
                }


            }
        }
        return $arr_result;
    }

    public function getAllDepartment($show_name_only = false){
        //echo $dep_id;
        $res = $this->findAll();
        //print_r($res);
        $data = array();
        if($res){

            if($show_name_only){
                $data[''] = '所有';
                foreach($res as  $obj){
                    $data[$obj->id] = $obj->name;
                }
            }else{
                foreach($res as $obj){
                    $data[]= $obj->attributes;
                }
            }
        }
        else{
            if($show_name_only){
                $data[''] = '所有';
            }
        }
        return $data;
    }
}
