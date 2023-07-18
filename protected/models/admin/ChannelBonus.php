<?php

/**
 * This is the model class for table "{{channel_bonus}}".
 *
 * The followings are the available columns in table '{{channel_bonus}}':
 * @property integer $id
 * @property string $owner
 * @property integer $channel_id
 * @property integer $type_id
 * @property integer $sn_start
 * @property integer $sn_end
 * @property string $create_by
 * @property integer $created
 * @property string $update_by
 * @property integer $updated
 */
class ChannelBonus extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ChannelBonus the static model class
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
		return '{{channel_bonus}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('owner, channel_id, type_id, sn_start, sn_end', 'required'),
			array('channel_id, type_id, sn_start, sn_end, created, updated', 'numerical', 'integerOnly'=>true),
			array('owner', 'length', 'max'=>256),
			array('create_by, update_by', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, owner, channel_id, type_id, sn_start, sn_end, create_by, created, update_by, updated', 'safe', 'on'=>'search'),
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
			'owner' => '拥有者',
			'channel_id' => '渠道',
			'type_id' => '券类型',
			'sn_start' => 'Sn 起始',
			'sn_end' => 'Sn 截止',
			'create_by' => '创建人',
			'created' => '创建时间',
			'update_by' => '更新人',
			'updated' => '更新时间',
		);
	}
	
	public function beforeSave(){
		if (parent::beforeSave()) {
			if ($this->isNewRecord) {
				$this->create_by = Yii::app()->user->getId();
				$this->created = time();
			}
			
			$this->updated = time();
			$this->update_by = Yii::app()->user->getId();
			
			return true;
		}
		return parent::beforeSave();
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
		
		if ($this->sn_start) {
			$criteria->addCondition(':sn between sn_start and sn_end');
			$criteria->params = array (':sn'=>$this->sn_start);
		}

//		$criteria->compare('id',$this->id);
		$criteria->compare('owner',$this->owner,true);
		$criteria->compare('channel_id',$this->channel_id);
		$criteria->compare('type_id',$this->type_id);
		
//		$criteria->compare('sn_end',$this->sn_end);
//		$criteria->compare('create_by',$this->create_by,true);
//		$criteria->compare('created',$this->created);
//		$criteria->compare('update_by',$this->update_by,true);
//		$criteria->compare('updated',$this->updated);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}