<?php

/**
 * This is the model class for table "{{partner_queue_record}}".
 *
 * The followings are the available columns in table '{{partner_queue_record}}':
 * @property integer $queue_id
 * @property string $id_card
 * @property string $password
 * @property string $channel
 * @property string $created
 */
class PartnerQueueRecord extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PartnerQueueRecord the static model class
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
        return '{{partner_queue_record}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('queue_id, id_card, password, channel, created', 'required'),
            array('queue_id', 'numerical', 'integerOnly'=>true),
            array('id_card', 'length', 'max'=>32),
            array('password', 'length', 'max'=>16),
            array('channel', 'length', 'max'=>5),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('queue_id, id_card, password, channel, created', 'safe', 'on'=>'search'),
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
            'queue_id' => 'Queue',
            'id_card' => 'Id Card',
            'password' => 'Password',
            'channel' => 'Channel',
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

        $criteria->compare('queue_id',$this->queue_id);
        $criteria->compare('id_card',$this->id_card,true);
        $criteria->compare('password',$this->password,true);
        $criteria->compare('channel',$this->channel,true);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


    public function insertData($queue_id, $id_card, $password, $channel='03004') {
        $model = new PartnerQueueRecord();
        $model->queue_id = $queue_id;
        $model->id_card = $id_card;
        $model->password = $password;
        $model->channel = $channel;
        $model->created = date('Y-m-d H:i:s');
        return $model->save();
    }

    public function getCustomerByQueueId($order_queue_id) {
        $model = self::model()->find('queue_id=:queue_id', array(':queue_id'=>$order_queue_id));
        if ($model) {
            return $model->attributes;
        } else {
            return false;
        }
    }
}