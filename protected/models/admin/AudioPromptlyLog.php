<?php

/**
 * 语音即时发送播报日志
 * This is the model class for table "{{audio_promptly_log}}".
 *
 * The followings are the available columns in table '{{audio_promptly_log}}':
 * @property string $id
 * @property integer $city_id
 * @property string $audio_url
 * @property string $audio_size
 * @property string $created
 * @property string $opt_user_id
 */
class AudioPromptlyLog extends CActiveRecord
{
    const SET_AUDIO_PROMPT_VALUE = 9999999999;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{audio_promptly_log}}';
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
            array('city_id', 'numerical', 'integerOnly'=>true),
            array('audio_url', 'length', 'max'=>100),
            array('audio_size', 'length', 'max'=>50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, city_id, audio_url, audio_size, created ,opt_user_id', 'safe', 'on'=>'search'),
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
            'audio_url' => '音频地址',
            'audio_size' => '音频大小',
            'created' => '创建时间',
            'opt_user_id' => 'Opt User id',
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

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id,true);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('audio_url',$this->audio_url,true);
        $criteria->compare('audio_size',$this->audio_size,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('opt_user_id',$this->opt_user_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AudioPromptlyLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 获取列表
     * @param array $params
     * @return array
     */
    public function getList($params = array()){
        $lists = array();
        if(empty($params)){
            return $lists;
        }
        $lists = Yii::app()->db_readonly->createCommand()
            ->from($this->tableName())
            ->where('opt_user_id=:opt_user_id', array (
                ':opt_user_id'=>$params['opt_user_id']))
            ->order('id DESC')
            ->limit($params['pageSize'])
            ->offset($params['offset'])
            ->queryAll();
        return $lists;
    }
}