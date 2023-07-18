<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 2015/3/26
 * Time: 11:06
 */

class ProcessList extends PomoActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{process_list}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, tail,pid', 'numerical', 'integerOnly' => true),
            array('name,category', 'length', 'max' => 50),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id,pid,tail,category,city_id,process_date,last_changed_date,name', 'safe', 'on' => 'search'),
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
            'id' => '自增主键',
            'pid' => '推广ID',
            'tail' => '上次处理标记值',
            'process_date' => '上次处理标记时间值',
            'last_changed_date' => '最后修改时间',
            'name' => '名称',
            'category' => '分类'
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
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);

        return new CActiveDataProvider('ProcessList', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return EnvelopeAcount the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function getTail($pid)
    {
        $criteria = new CDbCriteria();
        $criteria->select='id,tail,category,name';
        $criteria->addCondition('pid=:pid');
        $criteria->params[':pid']=$pid;
        return self::model()->find($criteria);
    }

}