<?php

/**
 * This is the model class for table "{{domain_setting}}".
 *
 * The followings are the available columns in table '{{domain_setting}}':
 * @property integer $id
 * @property string $name
 * @property string $ip
 * @property string $remark
 * @property integer $useable
 * @property integer $active
 * @property integer $create_time
 * @property integer $update_time
 */
class SysDomainSetting extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{domain_setting}}';
	}
        
        public function getDbConnection() {
            return Yii::app()->dbsys;
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, ip', 'required'),
			array('create_time, update_time, useable, active', 'numerical', 'integerOnly'=>true),
			array('name, ip', 'length', 'max'=>32),
			array('remark', 'length', 'max'=>256),
			array('useable, active', 'length', 'max'=>4),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, ip, remark, useable, active, create_time, update_time', 'safe', 'on'=>'search'),
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
			'name' => '域名',
			'ip' => 'ip地址',
			'remark' => '备注',
			'useable' => '是否可用',
			'active' => '状态',
			'create_time' => '创建时间',
			'update_time' => '最后一次修改时间',
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
	public function search($extCriteria=null, $pageSize=10)
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('remark',$this->remark,true);
                $criteria->compare('useable',$this->useable);
                $criteria->compare('active',$this->active);
                
                if($extCriteria !== null){
                    $criteria->mergeWith($extCriteria);
                }

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
                        'pagination'=>array(
                            'pageSize'=>$pageSize,
                        )
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CarDomainSetting the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
