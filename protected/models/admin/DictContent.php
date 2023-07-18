<?php

/**
 * This is the model class for table "{{dict_content}}".
 *
 * The followings are the available columns in table '{{dict_content}}':
 * @property integer $id
 * @property string $dictname
 * @property string $code
 * @property string $name
 * @property integer $postion
 */
class DictContent extends CActiveRecord
{
	private static $_items = array ();
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DictContent the static model class
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
		return '{{dict_content}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dictname, code, postion', 'required'),
			array('postion', 'numerical', 'integerOnly'=>true),
			array('dictname, code', 'length', 'max'=>20),
			array('name', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dictname, code, name, postion', 'safe', 'on'=>'search'),
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
			'dictname' => 'Dictname',
			'code' => 'Code',
			'name' => 'Name',
			'postion' => 'Postion',
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
		$criteria->compare('dictname',$this->dictname,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('postion',$this->postion);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public static function items($dictname) {
		if (!isset(self::$_items[$dictname]))
			self::loadItems($dictname);
		return self::$_items[$dictname];
	}
	
	public static function item($dictname, $code) {
		if (!isset(self::$_items[$dictname][$code]))
			self::loadItemsByCode($dictname, $code);
		return isset(self::$_items[$dictname][$code]) ? self::$_items[$dictname][$code] : false;
	}
	
	private static function loadItems($dictname) {
		self::$_items[$dictname] = array ();
		$models = self::model()->findAll(array (
			'condition'=>'dictname=:dictname', 
			'params'=>array (
				':dictname'=>$dictname
			), 
			'order'=>'postion'
		));
		foreach($models as $model)
			self::$_items[$dictname][$model->code] = $model->name;
	}

	private static function loadItemsByCode($dictname, $code) {
		self::$_items[$dictname] = array ();
		$models = self::model()->findAll(array (
			'condition'=>'dictname=:dictname and code=:code', 
			'params'=>array (
				':dictname'=>$dictname,
				':code'=>$code
			), 
			'order'=>'postion'
		));
		foreach($models as $model)
			self::$_items[$dictname][$model->code] = $model->name;
	}		
}