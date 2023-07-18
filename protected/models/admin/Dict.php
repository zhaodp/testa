<?php

/**
 * This is the model class for table "{{dict}}".
 *
 * The followings are the available columns in table '{{dict}}':
 * @property integer $id
 * @property string $dictname
 * @property string $name
 * @property string $code
 * @property integer $postion
 */
class Dict extends CActiveRecord
{

    private static $_items = array();

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Dic the static model class
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
        return '{{dict}}';
    }

    /**
     * @return CDbConnection database connection
     */
    public function getDbConnection()
    {
        return Yii::app()->db;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'dictname, name, code, postion',
                'required'),
            array(
                'postion',
                'numerical',
                'integerOnly'=>true),
            array(
                'dictname, name, code',
                'length',
                'max'=>20),
            array(
                'code','rule_code'
            ),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'id, dictname, name, code, postion',
                'safe',
                'on'=>'search'));
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
            'id'=>'ID',
            'dictname'=>'字典类型',
            'name'=>'字典名称',
            'code'=>'Code',
            'postion'=>'Postion');
    }

    public function rule_code(){
        $criteria = new CDbCriteria();
        $criteria->compare('dictname', $this->dictname);
        $criteria->compare('code', $this->code);
        $hasCode = self::model()->find($criteria);
        if($hasCode && ($hasCode->id != $this->id)){
            $this->addError('code', 'code已经存在');
        }
    }

    public function search($extCriteria = null)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('dictname',$this->dictname,true);
        $criteria->compare('code',$this->code,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('postion',$this->postion);
        
        if($extCriteria !== null){
            $criteria->mergeWith($extCriteria);
        }

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function afterSave() {
        parent::afterSave();
        $this->loadItems($this->dictname, TRUE);
    }

    public static function items($dictname)
    {
        if (!isset(self::$_items[$dictname]))
            self::loadItems($dictname);
        return self::$_items[$dictname];
    }

    public static function item($dictname, $code)
    {
        if (!isset(self::$_items[$dictname])){
        	self::loadItems($dictname);
        }    
        return isset(self::$_items[$dictname][$code]) ? self::$_items[$dictname][$code] : false;
    }

    /**
     * 根据内容，返回code，这个方法原来写的有bug
     * 
     * @editor sunhongjing 2013-11-10
     * 
     * @param unknown_type $dictname
     * @param unknown_type $item
     * @return int
     */
    public static function code($dictname, $item)
    {
        if (!isset(self::$_items[$dictname])){
        	self::loadItems($dictname);
        }  
        return array_search( $item , self::$_items[$dictname] );
    }

    private static function loadItems($dictname, $refresh = false)
    {
        self::$_items[$dictname] = array();

        $cache_key = 'SYSTEM_DICTS_'.$dictname;
        $json = Yii::app()->cache->get($cache_key);
        if (!$json || !is_array($json) || $refresh)
        {
            $models = self::model()->findAll(array(
                'condition'=>'dictname=:dictname',
                'params'=>array(
                    ':dictname'=>$dictname),
                'order'=>'postion'));
            foreach($models as $model)
            {
                self::$_items[$dictname][$model->code] = $model->name;
            }
            $json = self::$_items[$dictname];
            Yii::app()->cache->set($cache_key, $json, 3600);
        } else
        {
            self::$_items[$dictname] = $json;
        }
    }

    public static function items_arr($params){
        $return = array();
        if(is_array($params)){
            foreach($params as $item){
                $lists = self::items($item);
                $return = $return + $lists;
            }
        }else{
            $return = self::items($params);
        }
        return $return;
    }

    public static function getMaxCode($dictname){
	$sql="select max(code) from t_dict where dictname='".$dictname."'";
	$max_code=Yii::app()->db_readonly->createCommand($sql)->queryColumn();	
	if($max_code){
	    return $max_code;
	}
	return '';
    }


    public static function getNameList($name)
    {
            $criteria = new CDbCriteria;
            $criteria->select = 'code,name';
            if($name!=''){
                $criteria->compare('name', $name, true);
            }

            $criteria->addCondition("dictname='city'");
            self::$db = Yii::app()->db_readonly;
            $names = self::model()->findAll($criteria);
            self::$db = Yii::app()->db;

            return $names;
    }

    /**获取相应活动报单次数
     * @param $name
     * @return mixed
     */
    public static function getEnvelopeTypeNub($arr)
    {
        $result=-1;
        $criteria = new CDbCriteria;
        $criteria->select = 'postion';
        $criteria->addCondition("dictname=:dictname");
        $criteria->addCondition("code=:code");
        $criteria->params[':code']=$arr['code'];
        $criteria->params[':dictname']=$arr['dictname'];
        self::$db = Yii::app()->db_readonly;
        $names = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;

        if($names){
            $result=$names[0]['postion'];
        }

        return $result;
    }
}
