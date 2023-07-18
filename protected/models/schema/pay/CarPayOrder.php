<?php

/**
 * This is the model class for table "{{pay_order}}".
 *
 * The followings are the available columns in table '{{pay_order}}':
 * @property integer $id
 * @property string $order_id
 * @property integer $channel
 * @property string $user_id
 * @property string $order_amount
 * @property integer $currency
 * @property integer $trans_type
 * @property string $trans_time
 * @property integer $trans_status
 * @property string $description
 * @property string $resp_tn
 * @property string $resp_qn
 * @property integer $verify
 * @property string $trans_end_time
 * @property string $create_time
 * @property string $update_time
 * @property string $resp_msg_fist
 * @property string $resp_msg_second
 */
class CarPayOrder extends FinanceActiveRecord
{

    //trans_type交易类型   01.消费  31.消费撤销   04.退货交易
    const TRANS_TYPE_USE='1';
    const TRANS_TYPE_CANCEL='31';
    const TRANS_TYPE_RETURN='04';



    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{pay_order}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id, user_id, order_amount, trans_time, create_time', 'required'),
            array('channel, trans_status, verify ,source', 'numerical', 'integerOnly'=>true),
            array('order_id, user_id, resp_tn, resp_qn', 'length', 'max'=>32),
            array('order_amount', 'length', 'max'=>10),
            array('trans_type', 'length', 'max'=>5),
            array('trans_time', 'length', 'max'=>20),
            array('description', 'length', 'max'=>50),
            array('resp_msg_fist, resp_msg_second,callback_url,order_number', 'length', 'max'=>500),
            array('trans_end_time, update_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, order_id, channel, user_id, order_amount, trans_type, trans_time, trans_status, description, resp_tn, resp_qn, verify, trans_end_time, create_time, update_time, resp_msg_fist, resp_msg_second,source,callback_url,order_number', 'safe', 'on'=>'search'),
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
            'order_id' => '订单号',
            'channel' => '支付渠道',
            'user_id' => '用户ID',
            'order_amount' => '订单金额',
            'trans_type' => '交易类型',
            'trans_time' => '交易时间',
            'trans_status' => '交易状态',
            'description' => '订单描述',
            'resp_tn' => '交易流水号',
            'resp_qn' => '查询流水号',
            'verify' => '是否对账',
            'trans_end_time' => '交易结束时间',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'resp_msg_fist' => 'Resp Msg Fist',
            'resp_msg_second' => 'Resp Msg Second',
            'source' => '订单来源',
            'callback_url' => '回调地址',
            'order_number' => '来源订单号',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('order_id',$this->order_id,true);
        $criteria->compare('channel',$this->channel);
        $criteria->compare('user_id',$this->user_id,true);
        $criteria->compare('order_amount',$this->order_amount,true);
        $criteria->compare('trans_type',$this->trans_type);
        $criteria->compare('trans_time',$this->trans_time,true);
        $criteria->compare('trans_status',$this->trans_status);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('resp_tn',$this->resp_tn,true);
        $criteria->compare('resp_qn',$this->resp_qn,true);
        $criteria->compare('verify',$this->verify);
        $criteria->compare('trans_end_time',$this->trans_end_time,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('resp_msg_fist',$this->resp_msg_fist,true);
        $criteria->compare('resp_msg_second',$this->resp_msg_second,true);
        $criteria->compare('source',$this->source,true);
        $criteria->compare('callback_url',$this->callback_url,true);
        $criteria->compare('order_number',$this->order_number,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PayTrade the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
