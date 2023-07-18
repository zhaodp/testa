<?php

/**
 * This is the model class for table "t_bank_customer_bonus_consume_log".
 *
 * The followings are the available columns in table 't_bank_customer_bonus_consume_log':
 * @property string $id
 * @property string $phone
 * @property string $create_date
 * @property string $last_changed_date
 */
class BankCustomerBonusConsumeLogService extends BaseService
{
    /**
     * Returns the static service of the specified  class.
     * @param string $className active service class name.
     * @return BonusCodeCity the static service class
     */
    public static function service($className = __CLASS__)
    {
        return parent::service($className);
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
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        return BankCustomerBonusConsumeLog::model()->search();
    }


    /**获取满足条件的司机列表
     * @param array $arr
     * @return mixed
     */
    public function getYesterdayList($offset, $limit = 1000)
    {
        return BankCustomerBonusConsumeLog::model()->getYesterdayList($offset, $limit);
    }


    /**更新同一周期内其他消费记录
     * @param array $arr
     * @return mixed
     */
    public function updateYesterdayList($id, $phone)
    {
        return BankCustomerBonusConsumeLog::model()->updateYesterdayList($id, $phone);
    }


    /**更新同一周期内其他消费记录
     * @param array $arr
     * @return mixed
     */
    public function updateStatus($id)
    {
        return BankCustomerBonusConsumeLog::model()->updateStatus($id);
    }
}