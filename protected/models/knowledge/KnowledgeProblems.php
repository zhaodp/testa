<?php

/**
 * This is the model class for table "{{knowledge_problems}}".
 *
 * The followings are the available columns in table '{{knowledge_problems}}':
 * @property string $id
 * @property string $driver_id
 * @property string $name
 * @property string $phone
 * @property string $title
 * @property string $content
 * @property integer $status
 * @property string $operator
 * @property string $solve
 * @property string $updated
 * @property string $created
 */
class KnowledgeProblems extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return KnowledgeProblems the static model class
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
		return '{{knowledge_problems}}';
	}

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('status', 'numerical', 'integerOnly' => true),
            array('driver_id, updated, created', 'length', 'max' => 20),
            array('name, title, callid', 'length', 'max' => 100),
            array('phone, operator, solve', 'length', 'max' => 20),
            array('content', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, driver_id, name, phone, title, content, status, callid, operator, solve, updated, created', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'driver_id' => '司机工号',
            'name' => '司机名字',
            'phone' => '手机号码',
            'title' => '标题',
            'content' => '描述',
            'status' => '状态',
            'callid' => 'CallId',
            'operator' => '操作人',
            'solve' => '解决人',
            'updated' => '修改时间',
            'created' => '时间',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('driver_id', $this->driver_id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('phone', $this->phone, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('content', $this->content, true);
        if ($this->status != '') {
            $criteria->compare('status', $this->status);
        }
        $criteria->compare('operator', $this->operator, true);
        $criteria->compare('solve', $this->solve, true);
        $criteria->compare('updated', $this->updated, true);
        $criteria->compare('created', $this->created, true);
        $criteria->order = "id desc";

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10
            ),
        ));
    }

    public function collect_save($params)
    {
        self::$db = Yii::app()->db;
        $model = new KnowledgeProblems;
        $model->attributes = $params;

        if ($model->save())
            return true;
        else
            return false;

    }

    public function getCollectByPhone($phone, $date = 0)
    {
        $criteria = new CDbCriteria();
        $params = array(':phone' => $phone);
        if ($date != 0) {
            $criteria->addCondition('created > :created');
            $params[':created'] = $date;
        }
        $criteria->addCondition('phone = :phone');
        $criteria->addCondition('status = 0');
        $criteria->params = $params;
        $criteria->order = "id desc";
        return self::find($criteria);
    }

    public function getProblemsById($id){
        return Yii::app()->db_readonly->createCommand()
                    ->select("*")
                    ->from('{{knowledge_problems}}')
                    ->where('id = :id', array(':id' => $id))
                    ->queryRow();
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->operator = Yii::app()->user->getId();
                $this->created = date('Y-m-d H:i:s');
                $this->updated = date('Y-m-d H:i:s');
            } else {
                $this->updated = date('Y-m-d H:i:s');
            }
            return true;
        }

    }
}