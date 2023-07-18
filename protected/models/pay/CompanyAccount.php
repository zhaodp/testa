<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2015/1/22
 * Time: 11:56
 */

class CompanyAccount extends FinanceActiveRecord{

    const ACCOUNT_TYPE_CAST = 1;//现金账户
    const ACCOUNT_TYPE_OTHER = 2;//其他

    const ACCOUNT_CHANNEL_WASH = 1;//洗车账户渠道

    public static $companyChannel = array(
        self::ACCOUNT_CHANNEL_WASH
    );

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{company_account}}';
    }
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => '账户名',
            'type' => '账户类型',
            'channel' => '账户渠道',
            'amount' => '账户金额',
            'update_time' => '更新时间',
        );
    }
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('name',$this->user_id,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }


    /**
     * 根据渠道得到账户信息
     * @param $channel
     */
    public function getAccountInfoByChannel($channel){
        $model = self::model()->find('channel=:channel', array(':channel' => $channel));
        return $model;
    }

    /**
     * 根据渠道新建一个公司账户
     * @param $channel
     */
    public function buildNewCompanyAccount($channel){
        $accountName = '';
        $flag = false;
        if($channel == self::ACCOUNT_CHANNEL_WASH){
            $accountName = '公司洗车账户';
        }
        $model = new CompanyAccount();
        $model->type = self::ACCOUNT_TYPE_CAST;
        $model->name = $accountName;
        $model->channel = $channel;
        $model->amount = 0;
        $model->update_time = date('Y-m-d H:i:s');
        $ret = $model->save();
        if (!$ret) {
            EdjLog::info('---根据channel创建公司账户失败-channel:--'.$channel.'--');
            EdjLog::error(json_encode($model->getErrors()));
        }else{
            $flag = true;
        }
        return $flag;
    }

    /**
     * 根据id更新对应该公司账户余额 当前为充值
     * @param $channel
     * @param $amount
     */
    public function updateCompanyAccountBalance($params = array()){
        $flag = false;
        if (!empty($params) && isset($params['id'])) {
            $update_params['amount'] = isset($params['amount']) ? trim($params['amount']) : 0;

            $account = self::model()->updateCounters($update_params, 'id = :id',
                array(':id' => trim($params['id'])));
            if ($account) {
                $flag = true;
            }
        }
        return $flag;
    }
}