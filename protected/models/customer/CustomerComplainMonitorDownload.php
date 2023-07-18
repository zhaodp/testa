<?php

/**
 * This is the model class for table "{{customer_complain_monitor_download}}".
 *
 * The followings are the available columns in table '{{customer_complain_monitor_download}}':
 * @property string $id
 * @property string $data
 * @property string $updated
 *
 */
class CustomerComplainMonitorDownload extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerCar the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{customer_complain_monitor_download}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'id, data', 'required'
            ),
			array (
				'id', 'length', 'max'=>60
            ),

			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, data, updated', 'safe', 'on'=>'search'
            )
        );
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'Key',
			'data'=>'Data',
			'updated'=>'Updated'
        );
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('data', $this->data);
		$criteria->compare('updated', $this->updated);

		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}

    /**
     * 保存数据
     * @param $id
     * @param $data
     * @return bool
     */
    public function saveData($id,$data) {
        //echo $id;die;
        $model = new CustomerComplainMonitorDownload;
        $ret = $model->findByPk($id);
        if ($ret) {
            $model->updateByPk($id,array('data'=>serialize($data)));
            return true;
        } else {
            $model->attributes = array('id'=>$id,'data'=>serialize($data));
            if ($model->save()) {
                return true;
            } else {
                //var_dump($model->getErrors());die;
            }
        }
        return false;
    }

    /**
     * 取数据
     * @param $id
     * @return mixed
     */
    public function getData($id) {
        $conditions = 'id=:id';
        $params = array(':id'=>$id);
        $res = Yii::app()->db_readonly->createCommand()->select('*')->from(self::tableName())
            ->where($conditions,$params)->queryRow();
        return unserialize($res['data']);
    }
}