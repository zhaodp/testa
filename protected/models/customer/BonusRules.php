<?php

/**
 * This is the model class for table "{{bonus_rules}}".
 *
 * The followings are the available columns in table '{{bonus_rules}}':
 * @property string $id
 * @property string $bonus_sn
 * @property integer $balance
 * @property integer $number
 * @property string $phone
 * @property integer $phone_num
 * @property string $merchants
 * @property integer $type
 * @property string $sms
 * @property string $create_by
 * @property string $created
 * @property string $update_by
 * @property string $update
 */
class BonusRules extends FinanceActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BonusRules the static model class
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
		return '{{bonus_rules}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('bonus_sn, number', 'required'),
			array('balance, number, phone_num, type', 'numerical', 'integerOnly'=>true),
			array('sms', 'length', 'max'=> 200),
            array('bonus_sn', 'length', 'max' => 20),
			array('merchants', 'length', 'max'=>30),
			array('create_by, update_by, audit', 'length', 'max'=>32),
			array('phone', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, bonus_sn, balance, number, phone, phone_num, merchants, type, sms, create_by, created, update_by, update, audit, audit_time', 'safe', 'on'=>'search'),
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
			'id' => '序号',
			'bonus_sn' => '优惠码',
			'balance' => '金额',
			'number' => '绑定张数',
            'phone' => '<input type="radio" name="importType" value="1" id="type_02">批量手动输入手机号，最好不要超过5000哦',
            'phone_num' => '手机号总数',
			'merchants' => '商家',
			'type' => '类型',
			'sms' => '短信内容',
			'create_by' => '创建人',
			'created' => '创建时间',
			'update_by' => '生成人',
			'update' => '生成时间',
            'audit' => '审核人',
            'audit_time' => '审核时间',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('bonus_sn',$this->bonus_sn,true);
		$criteria->compare('balance',$this->balance);
		$criteria->compare('number',$this->number);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('phone_num',$this->phone_num);
		$criteria->compare('merchants',$this->merchants,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('sms',$this->sms,true);
		$criteria->compare('create_by',$this->create_by,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('update_by',$this->update_by,true);
		$criteria->compare('update',$this->update,true);
        $criteria->compare('audit', $this->audit, true);
        $criteria->compare('audit_time', $this->audit_time, true);
        $criteria->order = "id desc";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 30,
            ),
		));
	}


    /**
     * 保存之前要更新的字段
     * @return bool
     * author mengtianxue
     */
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_by = Yii::app()->user->getId();
                $this->created = date("Y-m-d H:i:s");
            }else{
                $this->update_by = Yii::app()->user->getId();
                $this->update = date("Y-m-d H:i:s");
            }
            return true;
        }
        return parent::beforeSave();
    }

    public function getBonusRules($id){
		$bonus_rules = self::model()->findByPk($id);
        return $bonus_rules;
    }

}