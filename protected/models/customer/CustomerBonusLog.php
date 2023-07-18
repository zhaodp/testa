<?php

/**
 * This is the model class for table "{{customer_bonus}}".
 *
 * The followings are the available columns in table '{{customer_bonus}}':
 * @property integer $id
 * @property integer $bonus_type_id
 * @property integer $channel
 * @property string $bonus_sn
 * @property integer $parity_bit
 * @property integer $order_id
 * @property integer $customer_id
 * @property string $customer_phone
 * @property string $create_by
 * @property integer $created
 * @property integer $updated
 */
class CustomerBonusLog extends FinanceActiveRecord
{
    /**
     * 客户端
     */
    const CHANNELAPP = 0;
    /**
     * 呼叫中心
     */
    const CHANNELCALLCENTER = 1;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerBonus the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_bonus_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'parity_bit, create_by, created',
                'required'),
            array(
                'bonus_type_id, balance, use_money, money, back_type, channel, parity_bit, order_id, customer_id, created, used, updated, end_date, user_limited, channel_limited,bonus_use_limit',
                'numerical',
                'integerOnly' => true),
            array(
                'bonus_sn, channel',
                'length',
                'max' => 20),
            array(
                'sn_type',
                'length',
                'max' => 2),
            array(
                'customer_phone, create_by',
                'length',
                'max' => 32),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'id, bonus_type_id, balance, sn_type, use_money, money, back_type, channel, bonus_sn, parity_bit, order_id, customer_id, customer_phone, create_by, created, used, updated, end_date, user_limited, channel_limited',
                'safe',
                'on' => 'search'));
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'bonus_type' => array(
                self::BELONGS_TO,
                'BonusCode',
                'bonus_type_id'
            ),
        );
    }

    /**
	* 记录优惠券分配日志
	*  by jiajingtao
	*/
	public function addCustomerBonusLog($attr){
		
		$customerBonusLog = new CustomerBonusLog();
		$customerBonusLog->attributes = $attr;
        if ($customerBonusLog->insert()) {
            return $customerBonusLog->id;
        } else {
            return false;
        }
	}
	
	
}
