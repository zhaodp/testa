<?php

/**
 * This is the model class for table "{{company_business_info}}".
 *
 * The followings are the available columns in table '{{company_business_info}}':
 * @property string $id
 * @property string $name
 * @property string $address
 * @property string $contact
 * @property string $phone
 * @property string $contact_phone
 * @property string $options
 * @property integer $type_id
 * @property integer $use_date
 * @property integer $city_id
 * @property string $images
 * @property string $remarks
 * @property integer $status
 * @property string $operator
 * @property string $created
 */
class CompanyBusinessInfo extends CActiveRecord
{

    const STATUS_NORMAL = 0; //已经提交
    const STATUS_UPLOAD = 1; //已经上传
    const STATUS_REVIEW = 2; //已经审核

    public static $status_dict = array(
        self::STATUS_NORMAL => '已经提交',
        self::STATUS_UPLOAD => '已经上传',
        self::STATUS_REVIEW => '已经审核'
    );
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CompanyBusinessInfo the static model class
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
		return '{{company_business_info}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, address, contact, phone, contact_phone, type_id, use_date, images, operator', 'required'),
			array('type_id, use_date, city_id, status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>32),
			array('address', 'length', 'max'=>64),
			array('contact, operator', 'length', 'max'=>16),
			array('phone, contact_phone', 'length', 'max'=>13),
			//array('remarks', 'length', 'max'=>128),
			array('options, created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
            /*自定义验证*/
            //array('phone, contact_phone', 'checkPhone'),
			array('id, name, address, contact, phone, contact_phone, options, type_id, use_date, city_id, images, remarks, status, operator, created', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'address' => 'Address',
			'contact' => 'Contact',
			'phone' => 'Phone',
			'contact_phone' => 'Contact Phone',
			'options' => 'Options',
			'type_id' => 'Type',
			'use_date' => 'Use Date',
			'city_id' => 'City',
			'images' => 'Images',
			'remarks' => 'Remarks',
			'status' => 'Status',
			'operator' => 'Operator',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('contact_phone',$this->contact_phone,true);
		$criteria->compare('options',$this->options,true);
		$criteria->compare('type_id',$this->type_id);
		$criteria->compare('use_date',$this->use_date);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('images',$this->images,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('operator',$this->operator,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function checkPhone() {
        if (!Common::checkPhone($this->phone)) {
            $this->addError('phone','手机号码格式错误');
        }

        if (!Common::checkPhone($this->contact_phone)) {
            $this->addError('contact_phone','紧急联系人手机号码格式错误');
        }
    }

    public function singUpValidate(array $attributes) {
        $model = self::model();
        $model->attributes = $attributes;
        $model->validate();
        $errors = $model->getErrors();
        if (is_array($errors) && count($errors)) {
            return array(
                'status' => 'false',
                'data' => $errors
            );
        } else {
            return array('status'=>ture);
        }
    }

    public function saveData(array $attributes) {
        $attributes = self::parseAttributes($attributes);
        $attributes['created'] = date('Y-m-d H:i:s', time());
        $model = new CompanyBusinessInfo();
        $model->attributes = $attributes;
        $model->remarks = $attributes['remarks'];
        $result = $model->save();
        return $result;
    }

    public function updateData($id, $attributes) {
        $attributes = self::parseAttributes($attributes);
        $model = CompanyBusinessInfo::model()->findByPk($id);
        if ($model) {
            $model->attributes = $attributes;
            $model->remarks = $attributes['remarks'];
            $result = $model->save();
            return $result;
        } else {
            return false;
        }
    }

    public static function parseAttributes($attributes) {

        if (isset($attributes['options']) && is_array($attributes['options']) && count($attributes['options'])) {
            $attributes['options'] = json_encode($attributes['options']);
        }
        if (isset($attributes['images']) && is_array($attributes['images']) && count($attributes['images'])) {
            $attributes['images'] = json_encode($attributes['images']);
        }
        foreach ($attributes as $k=>$v) {
            if ($k != 'images' && $k != 'options') {
                $attributes[$k] = Common::clean_xss($v);
            }
        }
        return $attributes;
    }

    public function getInfo($id) {
        $model = self::model()->findByPk($id);
        if ($model) {
            $info = $model->attributes;
            $info['options'] = json_decode($info['options'], true);
            $info['images'] = json_decode($info['images'], true);
            return $info;
        } else {
            return false;
        }
    }

    public function changeStatus($id, $status) {
        $model = self::model()->findByPk($id);
        if ($model) {
            $model->status = $status;
            return $model->save();
        } else {
            return false;
        }
    }

    /**
     * 统计地推审核数据
     * @param $yearMonth 201410
     * @param $start_time 2014-10-10 23:23:23
     * @param $end_time 2014-10-10 23:23:23
     * @param int $city_id
     */
    public function summaryData( $start_time, $end_time, $city_id = 0){

        $where = " created between :date_start and :date_end";
        $params = array(':date_start' => $start_time, ':date_end' => $end_time);
        if($city_id) {
            $where .=' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        $count = Yii::app()->db_readonly->createCommand()
            ->select('status,count(1) as cnt')->from($this->tableName())
            ->where($where,$params)
            ->group('status')
            ->queryAll();
        return $count;
    }

    /**
     * 统计月份内的审核数据
     * @param $yearMonth
     * @param int $status
     * @return mixed
     */
    public function summaryMonth($yearMonth,$status = self::STATUS_REVIEW){
        $monthstat = Yii::app()->db_readonly->createCommand()
            ->select('count(*) as cnt')
            ->from($this->tableName())
            ->where('status = :status AND use_date = :ud',array(':status'=>$status,':ud'=>$yearMonth))
            ->queryScalar();
        return $monthstat;
    }
}