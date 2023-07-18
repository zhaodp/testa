<?php

/**
 * 知识库城市map
 * This is the model class for table "{{knowledge_city_map}}".
 *
 * The followings are the available columns in table '{{knowledge_city_map}}':
 * @property integer $id
 * @property integer $knowledge_id
 * @property integer $city_id
 */
class KnowledgeCityMap extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return KnowledgeCityMap the static model class
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
		return '{{knowledge_city_map}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('knowledge_id, city_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, knowledge_id, city_id', 'safe', 'on'=>'search'),
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
			'knowledge_id' => '知识库id',
			'city_id' => '城市id',
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
		$criteria->compare('knowledge_id',$this->knowledge_id);
		$criteria->compare('city_id',$this->city_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 获取知识库对应城市关系列表
     * @auther wanglonghuan 2013.11.6
     * @params id int 知识库id
     * @return string
     */
    public static function getKnowledgeCityMaps($id)
    {
        $model = new KnowledgeCityMap();
        $maps = $model->findAll("knowledge_id=:k_id", array('k_id' => $id));
        $ret_str = '';
        if(!empty( $maps )){
            foreach($maps as $map){
                $ret_str .= $map->city_id .",";
            }
        }

        return rtrim($ret_str, ",");
    }
}