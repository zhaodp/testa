<?php

/**
 * This is the model class for table "{{partner_access_log}}".
 *
 * The followings are the available columns in table '{{partner_access_log}}':
 * @property integer $id
 * @property string $channel
 * @property string $api_name
 * @property string $params
 * @property string $result
 * @property string $created
 */
class PartnerAccessLog extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PartnerAccessLog the static model class
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
        return '{{partner_access_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('channel, api_name, params, created', 'required'),
            array('channel', 'length', 'max'=>5),
            array('api_name', 'length', 'max'=>32),
            //array('params', 'length', 'max'=>255),
            //array('result', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, channel, api_name, params, result, created', 'safe', 'on'=>'search'),
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
            'channel' => 'Channel',
            'api_name' => 'Api Name',
            'params' => 'Params',
            'result' => 'Result',
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
        $criteria->compare('channel',$this->channel,true);
        $criteria->compare('api_name',$this->api_name,true);
        $criteria->compare('params',$this->params,true);
        $criteria->compare('result',$this->result,true);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * 记录第三方API访问LOG
     * @param $channel
     * @param $api_name
     * @param array $params
     * @param array $result
     * @return bool
     */
    public function insertData($channel, $api_name, array $params, $result) {
        $model = new PartnerAccessLog();
        $model->channel = $channel;
        $model->api_name = trim($api_name);
        $model->params = json_encode($params);
        $model->result = is_array($result) ? json_encode($result) : $result;
        $model->created = date('Y-m-d H:i:s', time());
        $result = $model->save();
        return $result;
    }
}