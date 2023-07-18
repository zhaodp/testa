<?php

/**
 * This is the model class for table "{{invest_question}}".
 *
 * The followings are the available columns in table '{{invest_question}}':
 * @property integer $id
 * @property integer $invest_id
 * @property string $title
 * @property string $create_time
 * @property string $update_time
 * @property string $operator
 * @property integer $complain_main_type
 * @property integer $complain_sub_type
 */
class InvestQuestion extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{invest_question}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('update_time', 'required'),
            array('invest_id, complain_main_type, complain_sub_type', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 255),
            array('operator', 'length', 'max' => 50),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, invest_id, title, create_time, update_time, operator, complain_main_type, complain_sub_type', 'safe', 'on' => 'search'),
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
            'invest_id' => 'Invest',
            'title' => 'Title',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'operator' => 'Operator',
            'complain_main_type' => 'Complain Main Type',
            'complain_sub_type' => 'Complain Sub Type',
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
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('invest_id', $this->invest_id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('operator', $this->operator, true);
        $criteria->compare('complain_main_type', $this->complain_main_type);
        $criteria->compare('complain_sub_type', $this->complain_sub_type);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db_activity;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return InvestQuestion the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**
     * delete question by investId
     * @param $investId
     */
    public function  deleteQuestionByInvestId($investId)
    {
        $sql = 'delete from `t_invest_question` where invest_id =:investId';
        $this->getDbConnection()->createCommand($sql)->execute(array(':investId' => $investId,));
    }

    /**
     * 删除问题
     * @param $questionIds
     */
    public function  deleteQuestion($questionIds)
    {
        foreach ($questionIds as $questionId) {
            InvestOption::model()->deleteAll("question_id=:questionId", array("questionId" => $questionId,));
            $this->deleteByPk($questionId);
        }
    }
}
