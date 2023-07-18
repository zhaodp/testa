<?php

/**
 * This is the model class for table "{{vip_record}}".
 *
 * The followings are the available columns in table '{{vip_record}}':
 * @property integer $id
 * @property string $vip_id
 * @property string $operator_id
 * @property string $mark_content
 * @property integer $create_time
 * @property string $ext_info_cost
 */
class CarVipRecord extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{vip_record}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time', 'numerical', 'integerOnly'=>true),
			array('vip_id, operator_id', 'length', 'max'=>32),
			array('mark_content', 'length', 'min'=>5, 'allowEmpty'=>FALSE),
			array('ext_info_cost', 'length', 'min'=>0, 'allowEmpty'=>TRUE),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, vip_id, operator_id, mark_content, create_time, ext_info_cost', 'safe', 'on'=>'search'),
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
			'vip_id' => 'vip的id',
			'operator_id' => '操作人',
			'mark_content' => '备注信息',
			'create_time' => '创建时间',
			'ext_info_cost' => '扩展信息（三周内消费情况）',
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
		$criteria->compare('vip_id',$this->vip_id,true);
		$criteria->compare('operator_id',$this->operator_id,true);
		$criteria->compare('mark_content',$this->mark_content,true);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CarVipRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
