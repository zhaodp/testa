<?php

/**
 * This is the model class for table "{{crontab_host}}".
 *
 * The followings are the available columns in table '{{crontab_host}}':
 * @property string $id
 * @property string $host_name
 * @property string $host
 * @property integer $is_enable
 */
class ScronHost extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{crontab_host}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('host_name, host', 'required'),
            array('is_enable', 'numerical', 'integerOnly'=>true),
            array('host_name', 'length', 'max'=>255),
            array('host', 'length', 'max'=>15),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, host_name, host, is_enable', 'safe', 'on'=>'search'),
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
            'host_name' => '主机名',
            'host' => '主机IP',
            'is_enable' => '是否配置',
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
        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('host_name',$this->host_name,true);
        $criteria->compare('host',$this->host,true);
        $criteria->compare('is_enable',$this->is_enable);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CrontabHost the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->dbsys;
    }

    public function restDbConnection()
    {
        self::$db=Yii::app()->db;
    }


    public function getStartHost(){
       $ret = array();
       $result =  self::model()->findAll(' is_enable=:is_enable',array('is_enable'=>1));
       if(!empty($result)){
          foreach($result as $row){
              $ret[$row['host']] = $row['host_name'];
          }
       }
       return $ret;
    }
}