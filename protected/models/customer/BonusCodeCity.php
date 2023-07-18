<?php

/**
 * This is the model class for table "{{bonus_code_city}}".
 *
 * The followings are the available columns in table '{{bonus_code_city}}':
 * @property string $id
 * @property integer $bonus_id
 * @property string $city_id
 * @property string $created
 */
class BonusCodeCity extends FinanceActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return BonusCodeCity the static model class
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
		return '{{bonus_code_city}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('city_id, created', 'required'),
			array('bonus_id', 'numerical', 'integerOnly'=>true),
			array('city_id', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, bonus_id, city_id, created', 'safe', 'on'=>'search'),
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
			'bonus_id' => 'Bonus',
			'city_id' => '城市限制',
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
		$criteria->compare('bonus_id',$this->bonus_id);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 根据id获取优惠劵信息
     * @param $bonus_id
     * @param $city_id
     * @return mixed
     * author mengtianxue
     */
    public function getBonusCodeCityID($bonus_id,  $city_id = null)
    {
		$criteria = new CDbCriteria();
		$criteria->compare('bonus_id', $bonus_id);
		if (!is_null($city_id)) {
			$criteria->compare('city_id', $city_id);
		}
		return self::model()->findAll($criteria);
    }

    /**
     * 执行批量插入城市
     * @param array $splitArr
     * @param null $bonus_code_id
     * @return bool
     * @author daiyihui
     */
    public function doCityInsert($splitArr = array(), $bonus_code_id=null)
    {
        //接收成功插入城市表返回ID的<array>
        $returnCityArray = array();
        if(!empty($splitArr) && !empty($bonus_code_id)){
            //遍历数组循环执行插入城市
            $now_time = date("Y-m-d H:i:s");
            for($i = 0; $i < count($splitArr); $i++){
                //重置$model_city
                $this->setIsNewRecord(true);
                $this->id=null;
                $this->bonus_id = $bonus_code_id;
                $this->created = $now_time;
                $this->city_id = $splitArr[$i];
                //执行插入城市表
                $this->save();
                $returnCityArray[]  = $this->id;
            }
            if(count($splitArr) == count($returnCityArray)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }


}