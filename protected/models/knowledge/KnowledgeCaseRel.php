<?php

/**
 * This is the model class for table "{{knowledge_case_rel}}".
 *
 * The followings are the available columns in table '{{knowledge_case_rel}}':
 * @property string $id
 * @property integer $kc_id
 * @property integer $k_id
 * @property string $created
 */
class KnowledgeCaseRel extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return KnowledgeCaseRel the static model class
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
		return '{{knowledge_case_rel}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('k_id, created', 'required'),
			array('kc_id, k_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, kc_id, k_id, created', 'safe', 'on'=>'search'),
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
			'kc_id' => 'Kc',
			'k_id' => 'K',
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
		$criteria->compare('kc_id',$this->kc_id);
		$criteria->compare('k_id',$this->k_id);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function case_save($params)
    {
        $model = KnowledgeCaseRel::model()->find('kc_id = :kc_id and k_id = :k_id',
            array(':kc_id' => $params['kc_id'], 'k_id' => $params['k_id']));
        if (empty($model)) {
            $model = new KnowledgeCaseRel();
            $params['created'] = date('Y-m-d H:i:s');
        }

        $model->attributes = $params;
        if ($model->save()) {
            return true;
        } else {
            return false;
        }
    }


    public function getCase($k_id)
    {
        $cast = Yii::app()->db->createCommand()
            ->select('*')
            ->from($this->tableName())
            ->where('k_id = :k_id', array(':k_id' => $k_id))
            ->queryAll();
        return $cast;
    }
}