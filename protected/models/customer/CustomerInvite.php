<?php

/**
 * This is the model class for table "{{customer_invite}}".
 *
 * The followings are the available columns in table '{{customer_invite}}':
 * @property integer $id
 * @property string $macaddress
 * @property string $bonus_sn
 * @property string $source
 * @property integer $created
 */
class CustomerInvite extends CActiveRecord
{
	const BONUS_TYPE_ID = 16; 
	const MAX_INVITE_COUNT = 50;	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerInvite the static model class
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
		return '{{customer_invite}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created', 'numerical', 'integerOnly'=>true),
			array('macaddress', 'length', 'max'=>50),
			array('bonus_sn', 'length', 'max'=>20),
			array('source', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, macaddress, bonus_sn, source, created', 'safe', 'on'=>'search'),
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
			'macaddress' => 'Macaddress',
			'bonus_sn' => 'Bonus Sn',
			'source' => 'Source',
			'created' => 'Created',
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
		$criteria->compare('macaddress',$this->macaddress,true);
		$criteria->compare('bonus_sn',$this->bonus_sn,true);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public function getInviteByMacaddress($macaddress) {
		return self::model()->find('macaddress=:macaddress', array(':macaddress'=>$macaddress));
	}

	public function getNextId()
	{
		$command=Yii::app()->db->createCommand("show table status like 't_customer_invite'");
		$command->execute();
		$row=$command->queryRow();
		if ($row)
		{
			$id = $row['Auto_increment'];
			return $id;
		}
		return 0;
	}	
}