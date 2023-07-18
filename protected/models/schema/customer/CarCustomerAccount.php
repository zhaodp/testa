<?php

/**
 * This is the model class for table "{{customer_account}}".
 * 用户账户表
 * The followings are the available columns in table '{{customer_account}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $city_id
 * @property integer $type
 * @property string $amount
 * @property string $vip_card
 * @property string $update_time
 */
class CarCustomerAccount extends FinanceActiveRecord
{

    //用户账户类型
    const ACCOUNT_TYPE_S=1;
    const ACCOUNT_TYPE_T=2;

    static $account_type = array(
        self::ACCOUNT_TYPE_S => '现金账户',
        self::ACCOUNT_TYPE_T => '其他');

	/**
	 * 根据用户id列表返回账户余额列表(查从库)
	 *
	 * @param array $userIdList
	 * @return array
	 */
	public function getAmountList($userIdList = array()){
		if(empty($userIdList)){
			return array();
		}
		$criteria = new CDbCriteria();
		$criteria->select = 'user_id, amount';
		$criteria->addInCondition('user_id', $userIdList);
		$amountList = self::model()->findAll($criteria);
		return $amountList;
	}
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_account}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('amount', 'required'),
            array('id, user_id, city_id, type', 'numerical', 'integerOnly'=>true),
            array('amount,credit', 'length', 'max'=>10),
            array('vip_card', 'length', 'max'=>20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, city_id, type, amount, credit, vip_card, update_time', 'safe', 'on'=>'search'),
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
            'user_id' => '用户ID',
            'city_id' => '城市ID',
            'type' => '账户类型',
            'amount' => '现金账户金额',
            'credit' => '信誉度',
            'vip_card' => 'VIP卡号(结账便利)',
            'update_time' => '更新时间',
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
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('type',$this->type);
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('credit',$this->credit,true);
        $criteria->compare('vip_card',$this->vip_card,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CustomerAccount the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    /**
     * 用户账户
     * @param string $user_id userId
     */
    public function getCustomerAccountInfo($user_id)
    {
        $customerAccount = self::model()->find('user_id=:user_id', array(':user_id' => $user_id));
        return $customerAccount;
    }
    /**
     * 全额退款清空账户余额 并且插入用户流水记录
     * @param string $cid custmainid客户id
     * @param string $aid customer_accountid客户账户id
     * @param string $amount 提取的余额现在为账户所有余额
     */
    public function emptyAccountAmount($cid,$aid,$amount=0,$operator='system'){
        $flag = false;
        $accountModel = self::model()->findByPk($aid);
        if($accountModel){
            $balance = $accountModel->amount;//账户当前余额
            if($balance == 0){
                echo 'have no balance '."\n";
                return $flag;
            }
            if($amount > $balance){
                $amount = $balance;
            }
            $accountModel->amount = $balance - $amount;
            $ret = $accountModel->update();
            if($ret){
                //插入流水
                $attributes = array(
                    'user_id' => $cid,
                    'trans_type' => CarCustomerTrans::TRANS_TYPE_FR,//扣款
                    'amount' => $amount * -1,//当前交易金额
                    'balance' => $balance - $amount,//当前余额
                    'source' => CarCustomerTrans::TRANS_SOURCE_S,//订单来源
                    'remark' => '普通账户全额退款:' . $amount. ' 操作人:'.$operator,
                    'create_time' => date('Y-m-d H:i:s'),
                    'operator' => $operator,
                );
                $add_trans = BCustomers::model()->addCustomerTrade($attributes);
                if ($add_trans['code'] === 0) {
                    $flag = true;
                }
            }else{
                $flag = false;
                echo '------refund failed---------';
            }

        }
        return $flag;
    }
}
