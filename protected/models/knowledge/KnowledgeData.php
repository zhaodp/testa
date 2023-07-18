<?php

/**
 * This is the model class for table "{{knowledge_data}}".
 *
 * The followings are the available columns in table '{{knowledge_data}}':
 * @property string $id
 * @property integer $k_id
 * @property string $content
 * @property string $operator
 * @property string $updated
 * @property string $created
 */
class KnowledgeData extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return KnowledgeData the static model class
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
		return '{{knowledge_data}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('content', 'required'),
			array('k_id', 'numerical', 'integerOnly'=>true),
			array('operator', 'length', 'max'=>20),
            array('content,drviercontent,customercontent', 'length', 'max' => 500),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, k_id, content,drviercontent,customercontent, operator, updated, created', 'safe', 'on'=>'search'),
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
			'k_id' => '父类id',
			'content' => '通用正文',
			'operator' => '操作人',
			'updated' => '修改时间',
			'created' => '创建时间',
			'drviercontent' => '面向司机',
			'customercontent' => '面向客户',
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
		$criteria->compare('k_id',$this->k_id);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('drviercontent',$this->drviercontent,true);
		$criteria->compare('customercontent',$this->customercontent,true);
		$criteria->compare('operator',$this->operator,true);
		$criteria->compare('updated',$this->updated,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getKnowledgeData($id){
        return Yii::app()->db->createCommand()
                ->select("*")
                ->from("{{knowledge_data}}")
                ->where("k_id = :k_id", array(':k_id' => $id))
                ->queryRow();
    }


    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->operator = Yii::app()->user->getId();
            if ($this->isNewRecord) {
                $this->created = date('Y-m-d H:i:s');
                $this->updated = date('Y-m-d H:i:s');
            } else {
                $this->updated = date('Y-m-d H:i:s');
            }
            return true;
        }

    }


}