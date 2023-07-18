<?php

/**
 * This is the model class for table "{{admin_app}}".
 *
 * The followings are the available columns in table '{{admin_app}}':
 * @property integer $id
 * @property string $name
 * @property string $desc
 * @property string $url
 * @property string $key
 * @property integer $status
 * @property string $update_time
 * @property string $create_time
 */
class AdminApp extends CActiveRecord
{
    //status
    CONST STATUS_NORMAL = 0;
    CONST STATUS_FORBIDEN = -1;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{admin_app}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, desc, url, status', 'required'),
            array('status', 'numerical', 'integerOnly'=>true),
            array('name, key', 'length', 'max'=>32),
            array('desc, url', 'length', 'max'=>255),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, desc, url, key, status, update_time, create_time', 'safe', 'on'=>'search'),
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
            'name' => '名称',
            'desc' => '描述',
            'url' => '应用地址',
            'key' => 'Key',
            'status' => '状态',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('desc',$this->desc,true);
        $criteria->compare('url',$this->url,true);
        $criteria->compare('key',$this->key,true);
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
     * @return AdminApp the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 获取所有应用信息
     * @param bool $show_name_only
     * @return array
     */
    public function getAll($show_name_only = false){
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
        }else{
            if($show_name_only){
                $data[''] = '所有';
            }
        }
        return $data;
    }

    public function getAllForUpdate(){
    	$apps = $this->getAll(1);
	foreach($apps as $k=>$v){
		if(empty($k)){
			unset($apps[$k]);	
		}
	}
    	return $apps;
    }

    /**
     * 用来缓存数据
     * 可实现一个通用的内存缓存类，减少重复数据的数据库查询
     */
    private $apps_cache = array();

    /**
     * 根据id获取名称
     * @param int $app_id
     * @return string
     */
    public  function getAppName($app_id){
	if(isset($this->apps_cache[$app_id])){
		$res = $this->apps_cache[$app_id];
	}else{
		$res = $this->findByPk($app_id);
		$this->apps_cache[$app_id] = $res;
	}

	if($res){
		return $res->name;
	}else{
		return '无';
	}
    }

    public static function getStatus($status = ''){
        $status_arr = array(
            self::STATUS_NORMAL =>'正常',
            self::STATUS_FORBIDEN => '禁用',
        );
        if($status !== ''){
            if(isset($status_arr[$status])){
                return $status_arr[$status];
	    }else{
		return false;
	    }
        }
        return $status_arr;
    }

    public function getAllToArray(){
        $res = $this->findAll();
        $data = array();
        if($res){
                foreach($res as  $obj){
                    $data[$obj->id] = $obj->attributes;
                }
        }
        return $data;
    }



}
