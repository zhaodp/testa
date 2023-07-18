<?php
/**
 * This is the model class for table "{{admin_roles}}".
 *
 * The followings are the available columns in table '{{admin_roles}}':
 * @property integer $id
 * @property string $controller
 * @property string $action
 * @property string $name
 * @property string $access
 * @property string $roles
 * @property integer $position
 * @property string $created
 */
class AdminRoles extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AdminRoles the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{admin_roles}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'controller, action, name, roles', 
				'required'), 
			array (
				'position', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'controller, action, name', 
				'length', 
				'max'=>20), 
			array (
				'access', 
				'length', 
				'max'=>10), 
			array (
				'roles', 
				'length', 
				'max'=>1024), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, controller, action, access, roles, position, created', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'controller'=>'Controller', 
			'action'=>'Action', 
			'access'=>'Access', 
			'roles'=>'Roles', 
			'position'=>'Position', 
			'created'=>'Created');
	}
	
	public function beforeSave() {
		if (parent::beforeSave()) {
			$this->access = 1;
            return true;
		}
	}
    public function afterSave(){
        //更改用户权限，清空用户个人缓存
        Menu::model()->removeAllCache();
        //清空MODS缓存
        $cache_key = 'CACHE_ALL_GROUP_MODS_LIST';
        Yii::app()->cache->delete($cache_key);
        //清空功能列表缓存
        $cache_key = 'CACHE_GROUP_MODS_TREE_';
        Yii::app()->cache->delete($cache_key);

        return parent::afterSave();
    }
	
	public function afterFind() {
		$tmp = @unserialize($this->roles);
		$this->roles =  ( $tmp!==false ) ?  $tmp : @explode(",",$this->roles);
		//$this->roles = @unserialize($this->roles);
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('controller', $this->controller, true);
		$criteria->compare('action', $this->action, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('access', $this->access, true);
		$criteria->compare('roles', $this->roles, true);
		$criteria->order = 'controller,action';
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
	
	/**
	 * 取得当前可用功能模块
	 * @param $condition
	 * @return unknown_type
	 */
	public function getValidMods($condition=array()){

		$where_str = array('AND', 'access=:access' );
		$where_param = array(':access'=>1) ;
		
		if( !empty($condition['controller']) ){
			$where_str[] = 'controller=:controller';
			$where_param[':controller'] = $condition['controller'];
		}
		if( !empty($condition['action']) ){
			$where_str[] = 'action=:action';
			$where_param[':action'] = $condition['action'];
		}
		
		$ret = Yii::app()->db_readonly->createCommand()
				->select('*')
				->from('t_admin_roles')
				->where($where_str, $where_param)
				->order('controller asc')
				->queryAll();
		return $ret;
		
	}
	
	/**
	 * 检查当前用户是否有访问此功能的权限
	 */
	public function havingPermissions($controller, $action) {
		$access = 0;
		
		$roles = self::checkPermissions($controller, $action);
		if ($roles&&isset(Yii::app()->user->roles)) {
			foreach($roles['roles'] as $role) {
				$role_name = AdminGroup::model()->getName($role);
				switch ($role_name) {
					case '*' :
					case 'guest' :
						$access = 1;
						break;
					case '@' :
						if (!Yii::app()->user->getIsGuest()) {
							$access = 1;
						}
						break;
					default :
						if (in_array($role, Yii::app()->user->roles)) {
							$access = 1;
						}
						break;
				}
			}
		}
		return $access;
	}
	
	
	/**
	 * 取得组（角色）的功能列表树
	 * 
	 * @author sunhongjing 2013-04-04
	 * 
	 * @param max $group_id 角色组列表id
	 * @return array
	 */
	public function getGroupModsTree()
	{
		$group_mods_tree = array();
		
		$cache_key = 'CACHE_GROUP_MODS_TREE_';

		$group_mods_tree =Yii::app()->cache->get($cache_key);
		
		if( empty( $group_mods_tree ) ){
			
			$group_tree = AdminGroup::model()->getGroups() ;
			
			if( !empty($group_tree) ){
				
				$group_mods_list = self::getAllGroupModsList();		
	
				foreach ($group_tree as $parent_group_key =>$group_item ) {
					
					if( !empty( $group_mods_list[$parent_group_key]['mods']) ){
						$group_item['mods'] = $group_mods_list[$parent_group_key]['mods'];
					}
					$group_mods_tree[$parent_group_key] = $group_item;
					foreach ($group_item['children'] as $k => $v ) {
						if( !empty( $group_mods_list[$k]['mods']) ){
							$v['mods'] = $group_mods_list[$k]['mods'];
						}
						$group_mods_tree[$parent_group_key]['children'][] = $v;
					}
				}
				
			}
				
			Yii::app()->cache->set($cache_key, $group_mods_tree, 600);
		}
		
		return $group_mods_tree;
		
	}
	
	/**
	 * 从admin_group_roles表得到全部的group mod list
	 * 
	 * @author sunhongjing 2013-04-04
	 */
	public function getAllGroupModsList()
	{
		$group_mods_list = array();
		
		$cache_key = 'CACHE_ALL_GROUP_MODS_LIST';

		$group_mods_list = '';//Yii::app()->cache->get($cache_key);
		
		if( empty($group_mods_list) ){
			$where_str = array('AND', 'access=:access' );
			$where_param = array(':access'=>1) ;		
			
			$ret = Yii::app()->db_readonly->createCommand()
					->select('*')
					->from('t_admin_roles')
					->where($where_str, $where_param)
					->order('controller asc')
					->queryAll();
			
			if( !empty( $ret ) ){
				
				foreach ($ret as $mods ) {		
					$groups =  @unserialize($mods['roles']);
					$groups =  ( $groups!==false ) ?  $groups : @explode(",",$mods['roles'] );
					unset($mods['roles']);
					unset($mods['access']);
					unset($mods['created']);	
					foreach ($groups as $g) {
						$group_mods_list[$g]['mods'][] = $mods;
					}
				}
				
				//缓存1小时
				Yii::app()->cache->set($cache_key, $group_mods_list, 3600);
			}
		}
		
		return $group_mods_list;
		
	}
	
	
	/**
	 * 
	 * 检查controller/action的许可角色
	 * @param string $controller
	 * @param string $action
	 * @return array
	 */
	public function checkPermissions($controller, $action) {
		$roles = null;
		
		//$cache_key = 'cache_admin_roles_'.md5($controller.$action);
		//$allow_roles = Yii::app()->cache->get($cache_key);
		//if (!$allow_roles) {
			$allow_roles = $this->model()->find('controller=:controller and action=:action', 
											array (':controller'=>$controller, ':action'=>$action) );
			//Yii::app()->cache->set($cache_key, $allow_roles, 300);
		//}
		
		if ($allow_roles) {
			$roles = array (
				'access'=>$allow_roles->access, 
				'roles'=>$allow_roles->roles);
		}
		return $roles;
	}

    /**
     * 获取user Roles 信息
     * @param $id
     * @return mixed
     */
    public function getRolesInfo($id){
        $ret = Yii::app()->db_readonly->createCommand()
            ->select('*')
            ->from('t_admin_roles')
            ->where('id=:id', array(':id'=>$id))
            ->queryRow();

        return $ret;
    }
}