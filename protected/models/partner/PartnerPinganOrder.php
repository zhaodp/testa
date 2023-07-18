<?php

/**
 * This is the model class for table "{{partner_pingan_order}}".
 *
 * The followings are the available columns in table '{{partner_pingan_order}}':
 * @property integer $order_id
 * @property string $partner_order_id
 * @property string $channel
 * @property string $created
 */
class PartnerPinganOrder extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PartnerPinganOrder the static model class
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
        return '{{partner_pingan_order}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id, partner_order_id, id_card, channel, created', 'required'),
            array('order_id', 'numerical', 'integerOnly'=>true),
            array('partner_order_id', 'length', 'max'=>32),
            array('id_card', 'length', 'max'=>16),
            array('channel', 'length', 'max'=>5),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('order_id, partner_order_id, id_card, channel, created', 'safe', 'on'=>'search'),
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
            'order_id' => 'Order',
            'partner_order_id' => 'Partner Order',
            'id_card' => 'Id Card',
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

        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('partner_order_id',$this->partner_order_id,true);
        $criteria->compare('id_card',$this->id_card,true);
        $criteria->compare('channel',$this->channel,true);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function insertData($order_id, $partner_order_id, $id_card, $channel='03004') {
        $model = new PartnerPinganOrder();
        $model->order_id = $order_id;
        $model->partner_order_id = $partner_order_id;
        $model->channel = $channel;
        $model->id_card = $id_card;
        $model->created = date('Y-m-d H:i:s', time());
        return $model->save();
    }
}