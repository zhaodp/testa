<?php

class BankCustomerBonusService extends BaseService
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
        return BankCustomerBonus::model()->search();
    }


    /**获取满足条件的司机列表
     * @param array $arr
     * @return mixed
     */
    public function getList($flag = 0, $thread, $limit = 1000)
    {
        return BankCustomerBonus::model()->getList($flag, $thread, $limit);
    }


    /**获取满足条件的司机列表
     * @param array $arr
     * @return mixed
     */
    public function getYesterdayList($offset, $limit = 1000)
    {
        return BankCustomerBonus::model()->getYesterdayList($offset, $limit);
    }


    /**获取满足条件的司机列表
     * @param array $arr
     * @return mixed
     */
    public function checkCardIsExit($card_id, $name, $phone)
    {
        return BankCustomerBonus::model()->checkCardIsExit($card_id, $name, $phone);
    }
}