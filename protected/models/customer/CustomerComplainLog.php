<?php

/**
 * This is the model class for table "{{customer_complain_log}}".
 *
 * The followings are the available columns in table '{{customer_complain_log}}':
 * @property integer $id
 * @property integer $complain_id
 * @property integer $process_type
 * @property integer $result
 * @property integer $recoup_type
 * @property integer $recoup_amount
 * @property string $bind_phone
 * @property integer $cast
 * @property integer $clothing_fee
 * @property integer $card_fee
 * @property integer $other_fee
 * @property string $mark
 * @property string $processed
 * @property string $create_time
 */
class CustomerComplainLog extends CActiveRecord
{
    const PROCESS_ZERO=0;    //客服处理   By 曾志海
	const PROCESS_ONE=1;    //品监处理
    const PROCESS_ONE_ONE=11;    //品监备注处理
    const PROCESS_TWO=2;    //司管处理
    const PROCESS_THREE=3;    //财务处理

    public static $process_type=array( '0'=>'客服',
    								   '1'=>'品监',
                                       '2'=>'司管',
                                       '3'=>'财务');
    public static $process_result=array('0'=>'财务处理完毕',
        '1'=>'品监|客服创建',
        '11'=>'品监备注',
        '2'=>'优惠券补偿',
        '3'=>'现金补偿',
        '4'=>'排除投诉',
        '5'=>'屏蔽1天',
        '6'=>'屏蔽3天',
        '7'=>'屏蔽7天',
        '8'=>'解约',
        '9'=>'不处罚',
        '10'=>'VIP补偿',
        '12'=>'解约退费',
        '13'=>'撤销处理完毕');

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerComplainLog the static model class
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
        return '{{customer_complain_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('complain_id, process_type, create_time, operator', 'required'),
            array('complain_id, process_type, result, recoup_type, recoup_amount, payer, cast, clothing_fee, card_fee, other_fee', 'numerical', 'integerOnly'=>true),
            array('recoup_user, operator', 'length', 'max'=>20),
            array('mark', 'length', 'max'=>500),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, complain_id, process_type, result, recoup_type, recoup_amount, recoup_user, vip_card, payer, cast, clothing_fee, card_fee, other_fee, mark, create_time, operator', 'safe', 'on'=>'search'),
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
            'complain_id' => 'Complain',
            'process_type' => 'Process Type',
            'result' => 'Result',
            'recoup_type' => 'Recoup Type',
            'recoup_amount' => 'Recoup Amount',
            'bind_phone' => 'Bind Phone',
            'vip_card' => 'Vip Card',
            'payer' => 'Payer',
            'cast' => 'Cast',
            'clothing_fee' => 'Clothing Fee',
            'card_fee' => 'Card Fee',
            'other_fee' => 'Other Fee',
            'mark' => 'Mark',
            'create_time' => 'Create Time',
            'operator' => 'Operator',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('complain_id',$this->complain_id);
        $criteria->compare('process_type',$this->process_type);
        $criteria->compare('result',$this->result);
        $criteria->compare('recoup_type',$this->recoup_type);
        $criteria->compare('recoup_amount',$this->recoup_amount);
        $criteria->compare('recoup_user',$this->recoup_user,true);
        $criteria->compare('vip_card',$this->vip_card);
        $criteria->compare('payer',$this->payer);
        $criteria->compare('cast',$this->cast);
        $criteria->compare('clothing_fee',$this->clothing_fee);
        $criteria->compare('card_fee',$this->card_fee);
        $criteria->compare('other_fee',$this->other_fee);
        $criteria->compare('mark',$this->mark,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('operator',$this->operator,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    /**
     * by 曾志海
     * 新增投诉插入日志
     */
    public function insertData($complainlog,$id,$type,$detail=''){
        self::$db=Yii::app()->db;
		$complainlog->complain_id=$id;
		$complainlog->process_type=$type;   //品监处理
		$complainlog->operator=Yii::app()->user->id;
		$complainlog->result=CustomerComplain::STATUS_CS;    
		$complainlog->create_time=date('Y-m-d H:i:s',time());
		$complainlog->mark=$detail;
		if($complainlog->insert()) return true;
    }

    /**
     * @param $complain_id
     * @return array|bool|CActiveRecord|mixed|null
     * @author daiyihui
     */
    public function getComplainLogList($complain_id)
    {
        if(!empty($complain_id)){
            $logList = self::model()->findAll('complain_id = :complain_id', array(':complain_id' => $complain_id));
            if($logList){
                return $logList;
            }else
                return false;
        }else
            return false;

    }
}