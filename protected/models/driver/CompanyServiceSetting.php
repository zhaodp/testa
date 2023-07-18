<?php

/**
 * This is the model class for table "{{company_service_setting}}".
 *
 * The followings are the available columns in table '{{company_service_setting}}':
 * @property integer $id
 * @property integer $city_id
 * @property integer $use_date
 * @property integer $type_id
 * @property integer $basic_score
 * @property integer $chanllenge
 * @property integer $goal
 * @property integer $standard
 * @property integer $c_score
 * @property integer $g_score
 * @property integer $s_score
 * @property integer $uns_score
 * @property string $created
 */
class CompanyServiceSetting extends CActiveRecord
{

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CompanyServiceSetting the static model class
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
		return '{{company_service_setting}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        return array(
            array('city_id, use_date, type_id', 'numerical', 'integerOnly'=>true),
            array('basic_score, chanllenge, goal, standard, c_score, g_score, s_score, uns_score', 'numerical'),
            array('created', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, city_id, use_date, type_id, basic_score, chanllenge, goal, standard, c_score, g_score, s_score, uns_score, created', 'safe', 'on'=>'search'),
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
			'city_id' => '城市',
			'use_date' => '使用时间',
			'type_id' => '类型',
			'basic_score' => '分类基础分',
			'chanllenge' => '挑战值',
			'goal' => '目标值',
			'standard' => '合格值',
			'c_score' => '完成挑战值得分',
			'g_score' => '完成目标值得分',
			's_score' => '完成合格值得分',
			'uns_score' => '不合格得分',
			'created' => '设置时间',
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
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('use_date',$this->use_date);
        $criteria->compare('type_id',$this->type_id);
        $criteria->compare('basic_score',$this->basic_score);
        $criteria->compare('chanllenge',$this->chanllenge);
        $criteria->compare('goal',$this->goal);
        $criteria->compare('standard',$this->standard);
        $criteria->compare('c_score',$this->c_score);
        $criteria->compare('g_score',$this->g_score);
        $criteria->compare('s_score',$this->s_score);
        $criteria->compare('uns_score',$this->uns_score);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
	}

    public function insertData($data) {
        $data['use_date'] = isset($data['use_date']) ? $data['use_date'] : date('Ym', time());
        $data['created'] = date('Y-m-d H:i:s', time());
        $model = $model = self::model()->find('city_id=:city_id and use_date=:use_date and type_id=:type_id', array(':city_id'=>$data['city_id'], ':use_date'=>$data['use_date'], ':type_id'=>$data['type_id']));
        if (!$model){
            $model = new CompanyServiceSetting();
        }
        $model->attributes = $data;

        $result = $model->save();
        return $result;
    }

    public function afterSave(){
        $key = CompanyKpiCommon::getMemKey($this->city_id, $this->use_date, $this->type_id);
        Yii::app()->cache->set($key, $this->attributes);
        return parent::afterSave();
    }

    public function getSettingInfoByType($city_id, $use_date, $type_id) {
        $key = CompanyKpiCommon::getMemKey($city_id, $use_date, $type_id);
        $data = Yii::app()->cache->get($key);
        if (!$data) {
            $model = self::model()->find('city_id=:city_id and use_date=:use_date and type_id=:type_id', array(':city_id'=>$city_id, ':use_date'=>$use_date, ':type_id'=>$type_id));
            if ($model) {
                $data = $model->attributes;
                Yii::app()->cache->set($key, $data);
            }
        }
        return $data;
    }

    public function getSettingInfo($city_id, $use_date) {
        $type_id_list = CompanyKpiCommon::$service_list;
        $data = array();
        foreach($type_id_list as $type_id=>$name) {
            $_tmp = $this->getSettingInfoByType($city_id, $use_date, $type_id);
            $_tmp['name'] = $name;
            $data[$type_id] = $_tmp;
        }
        return $data;
    }

    public function getBusinessSettingInfo($city_id, $use_date) {
        $type_id_list = CompanyKpiCommon::$business_list;
        $data = array();
        foreach($type_id_list as $type_id=>$name) {
            $_tmp = $this->getSettingInfoByType($city_id, $use_date, $type_id);
            $_tmp['name'] = $name;
            $data[$type_id] = $_tmp;
        }
        return $data;
    }

    /**
     * 修改数据前 检查市场推广的数据是否有删除，需要先获取原有数据
     * @param   int    $city_id    城市id
     * @param   int    $use_date    时间 eg: 201303
     * @param   array    $type    获取的type_id 范围
     * @return  array    description
     * @access  public
     */
    public function getServiceMarketInfo($city_id , $use_date , $type = array(9,10,11,12))
    {
        //desc
        if (is_array($type) && !empty($type))
        {
            $type_str = implode(',',$type);
        } else return false;

        $sql = "SELECT id, type_id, use_date FROM `t_company_service_setting` WHERE city_id ='{$city_id}' AND use_date ='{$use_date}' AND type_id in ( {$type_str} )";
        $data = Yii::app()->db_readonly->createCommand($sql)->queryAll();

        //desc
        if (!empty($data))
        {
            $return = array();
            //desc
            foreach ($data as $v)
            {
                $return[$v['type_id']] = $v;
            }
            return $return;
        }else return false;

    } // end func
}