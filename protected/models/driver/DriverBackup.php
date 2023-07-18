<?php

/**
 * This is the model class for table "{{driver_backup}}".
 *
 * The followings are the available columns in table '{{driver_backup}}':
 * @property integer $id
 * @property string $user
 * @property string $name
 * @property string $phone
 * @property string $ext_phone
 * @property string $level
 * @property integer $status
 * @property string $activate
 * @property integer $entry_time
 * @property string $imei
 * @property string $address
 * @property string $domicile
 * @property integer $license_time
 * @property string $id_card
 * @property string $is_behalf_driver
 * @property string $remarks
 */
class DriverBackup extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverBackup the static model class
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
		return '{{driver_backup}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, phone, status, activate, entry_time, imei, domicile, license_time, id_card, is_behalf_driver, remarks', 'required'),
			array('status, entry_time, license_time', 'numerical', 'integerOnly'=>true),
			array('user, level, is_behalf_driver', 'length', 'max'=>10),
			array('name, phone, ext_phone, id_card', 'length', 'max'=>20),
			array('activate', 'length', 'max'=>2),
			array('imei', 'length', 'max'=>15),
			array('address, domicile', 'length', 'max'=>255),
			array('remarks', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user, name, phone, ext_phone, level, status, activate, entry_time, imei, address, domicile, license_time, id_card, is_behalf_driver, remarks', 'safe', 'on'=>'search'),
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
			'user' => 'User',
			'name' => 'Name',
			'phone' => 'Phone',
			'ext_phone' => 'Ext Phone',
			'level' => 'Level',
			'status' => 'Status',
			'activate' => 'Activate',
			'entry_time' => 'Entry Time',
			'imei' => 'Imei',
			'address' => 'Address',
			'domicile' => 'Domicile',
			'license_time' => 'License Time',
			'id_card' => 'Id Card',
			'is_behalf_driver' => 'Is Behalf Driver',
			'remarks' => 'Remarks',
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
		$criteria->compare('user',$this->user,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('ext_phone',$this->ext_phone,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('activate',$this->activate,true);
		$criteria->compare('entry_time',$this->entry_time);
		$criteria->compare('imei',$this->imei,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('domicile',$this->domicile,true);
		$criteria->compare('license_time',$this->license_time);
		$criteria->compare('id_card',$this->id_card,true);
		$criteria->compare('is_behalf_driver',$this->is_behalf_driver,true);
		$criteria->compare('remarks',$this->remarks,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}