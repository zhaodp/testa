<?php

/**
 * This is the model class for table "{{knowledge_case}}".
 *
 * The followings are the available columns in table '{{knowledge_case}}':
 * @property string $id
 * @property string $content
 * @property string $operator
 * @property string $created
 */
class KnowledgeCase extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return KnowledgeCase the static model class
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
		return '{{knowledge_case}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content', 'length', 'max'=>255),
			array('operator', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, content, operator, created', 'safe', 'on'=>'search'),
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
			'content' => 'Content',
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
		$criteria->compare('content',$this->content,true);
		$criteria->compare('operator',$this->operator,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}


    public function getContent($id)
    {
        $cast = Yii::app()->db->createCommand()
            ->select('*')
            ->from($this->tableName())
            ->where('id = :id', array(':id' => $id))
            ->queryRow();
        return $cast;
    }

    public function getCast($id){
        return KnowledgeCase::model()->find('id = :id', array(':id' => $id));
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->operator = Yii::app()->user->getId();
            if ($this->isNewRecord) {
                $this->created = date('Y-m-d H:i:s');
            }
            return true;
        }
    }
}