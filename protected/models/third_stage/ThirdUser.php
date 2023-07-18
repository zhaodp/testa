<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $loginName
 * @property string $name
 * @property string $password
 * @property integer $permission
 * @property string $channel
 * @property string $contactName
 * @property string $contactPhone
 * @property string $meta
 * @property string $created
 * @property string $email
 * @property integer $accessModel
 * @property string $contractNum
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $street
 * @property string $initPassword
 *
 */
class ThirdUser extends ThirdStageActiveRecord
{

    public function getById($id){
        return self::model()->findByPk($id);
    }

    public function getUserIdByChannel($channel){
        $model = self::model()->find('channel = :channel', array(
            ':channel' => $channel,
        ));
        return isset($model['id']) ? $model['id'] : 0;
    }

    public function getName($channel){
        if('all' == $channel){
            return '所有渠道';
        }
        $model = self::model()->find('channel = :channel', array(
            ':channel' => $channel,
        ));
        return isset($model['name']) ? $model['name'] : $channel;
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('loginName, name, password, channel, created', 'required'),
            array('permission, accessModel', 'numerical', 'integerOnly'=>true),
            array('loginName, email, contractNum, province, city, area', 'length', 'max'=>30),
            array('name, street', 'length', 'max'=>100),
            array('password', 'length', 'max'=>128),
            array('channel, contactName', 'length', 'max'=>20),
            array('contactPhone', 'length', 'max'=>13),
            array('meta', 'length', 'max'=>1024),
            // The following rule is used by search(  ).
            // Please remove those attributes that should not be searched.
            array('id, loginName, name, password, permission, channel, contactName, contactPhone, meta, created, email, accessModel, contractNum, province, city, area, street', 'safe', 'on'=>'search'),
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
            'id' => 'Id',
            'loginName' => '登录名称',
            'name' => '商家姓名',
            'password' => '密码',
            'permission' => '权限',
            'channel' => '渠道号',
            'contactName' => '联系人姓名',
            'contactPhone' => '联系人电话',
            'meta' => '备注',
            'created' => '创建时间',
            'email' => '联系邮箱',
            'accessModel' => '接入形式',
            'contractNum' => '发票号码',
            'province' => '省',
            'city' => '城市',
            'area' => '区域',
            'street' => '街道',
            'initPassword' => '初始密码',
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
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);

        $criteria->compare('loginName',$this->loginName,true);

        $criteria->compare('name',$this->name,true);

        $criteria->compare('password',$this->password,true);

        $criteria->compare('permission',$this->permission);

        $criteria->compare('channel',$this->channel,true);

        $criteria->compare('contactName',$this->contactName,true);

        $criteria->compare('contactPhone',$this->contactPhone,true);

        $criteria->compare('meta',$this->meta,true);

        $criteria->compare('created',$this->created,true);

        $criteria->compare('email',$this->email,true);

        $criteria->compare('accessModel',$this->accessModel);

        $criteria->compare('contractNum',$this->contractNum,true);

        $criteria->compare('province',$this->province,true);

        $criteria->compare('city',$this->city,true);

        $criteria->compare('area',$this->area,true);

        $criteria->compare('street',$this->street,true);

        $criteria->compare('initPassword',$this->initPassword,true);

        return new CActiveDataProvider('ThirdUser', array(
            'criteria'=>$criteria,
        ));
    }

	/**
	 * Returns the static model of the specified AR class.
	 * @return ThirdUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function existChannel($channel){
        return self::model()->find('channel = :channel', array(':channel' => $channel));
    }
}