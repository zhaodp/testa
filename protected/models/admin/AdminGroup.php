<?php
/**
 * This is the model class for table "{{admin_group}}".
 *
 * The followings are the available columns in table '{{admin_group}}':
 * @property integer $id
 * @property integer $parentid
 * @property string $code
 * @property string $name
 * @property integer $position
 * @property string $created
 */
class AdminGroup extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AdminGroup the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{admin_group}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'parentid, code, name, position, created', 
				'required'), 
			array (
				'parentid, position', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'code', 
				'length', 
				'max'=>60), 
			array (
				'name', 
				'length', 
				'max'=>30), 
			array (
				'mods', 
				'length', 
				'max'=>3000), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, parentid, code, name, position, created', 
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
			'parentid'=>'Parentid', 
			'code'=>'Code', 
			'name'=>'Name', 
			'position'=>'Position', 
			'created'=>'Created');
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('parentid', $this->parentid);
		$criteria->compare('code', $this->code, true);
		$criteria->compare('name', $this->name);
		$criteria->order='position';
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria,
				'pagination'=>array(
						'pageSize'=>30,
				),));
	}

    public function afterSave(){
        //更改用户权限，清空用户个人缓存
        Menu::model()->removeAllCache();
        return parent::afterSave();
    }
	
	public function getGroups($id = 0) {
		$group = array ();
		
		$criteria = new CDbCriteria();
		$criteria->select = 'id,parentid,code,name,position';
		$criteria->order = 'position';
		$criteria->condition = 'parentid=:id';
		$criteria->params = array (
			':id'=>$id);
		
		$childs = self::model()->findAll($criteria);
		
		if ($childs) {
			foreach($childs as $child) {
				$ret = $this->getGroups($child->id);
				
				if ($ret==null) {
					$group[$child->id] = $child->attributes;
				} else {
					$parent = $child->attributes;
					$parent = array_merge($parent, array (
						'children'=>$ret));
					$group[$child->id] = $parent;
				}
			}
			return $group;
		}
		return null;
	}
	

	/**
	 * 返回权限名称的ID
	 * 
	 * @param unknown_type $name
	 */
	public function getID($name) {
		$group_id = '';
		
		$cache_key = 'cache_admin_group_id_'.md5($name);
		$group = Yii::app()->cache->get($cache_key);
		if (!$group) {
			$group = self::model()->find('name=:name', array (
				':name'=>$name));
			Yii::app()->cache->set($cache_key, $group, 3600);
		}
		if ($group) {
			$group_id =  $group->id;
		}
		
		return $group_id;
	}

    public function getIdByCode($code) {

		$group_id = '';

		$cache_key = 'cache_admin_code_'.md5($code);
		$group = Yii::app()->cache->get($cache_key);
		if (!$group) {
			$group = self::model()->find('code=:name', array (
				':name'=>$code));
			Yii::app()->cache->set($cache_key, $group, 3600);
		}
		if ($group) {
			$group_id =  $group->id;
		}

		return $group_id;
    }

	/**
	 * 返回权限ID的名称
	 * 
	 * @param unknown_type $id
	 */
	public function getName($id) {
		$group_name = '';

		$cache_key = 'cache_admin_group_name_'.$id;
		$group = Yii::app()->cache->get($cache_key);
		if (!$group) {
			$group = self::model()->findByPk($id);	
			Yii::app()->cache->set($cache_key, $group, 86400);
		}
		if( $group ){
			$group_name = $group->name;
		}
		
		return $group_name;
	}
	

	/**
	 * 得到分组的功能列表
	 *
	 * @author sunhongjing 2013-04-04
	 * @param array $group_id
	 */
	public function getGroupModsList($group_id_list=array())
	{
		$my_group_mods_list = array();

		if( !empty($group_id_list) ){
			$all_group_mods_list = AdminRoles::model()->getAllGroupModsList();
			
			if( !empty($all_group_mods_list) ){
				foreach ( $group_id_list as $gid ) {
					$my_group_mods_list[$gid] = array();
					$my_group_mods_list[$gid]['name'] = self::getName($gid);
					if( isset($all_group_mods_list[$gid]) ){
						$my_group_mods_list[$gid]['mods'] = $all_group_mods_list[$gid]['mods'];
					}
				}
			}
		}	
		
		return $my_group_mods_list;
	}


	
}
