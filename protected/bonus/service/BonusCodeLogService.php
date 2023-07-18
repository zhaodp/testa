<?php

class BonusCodeLogService extends BaseService
{
    /**
     * Returns the static service of the specified  class.
     * @param string $className active service class name.
     * @return  the static service class
     */
    public static function service($className = __CLASS__)
    {
        return parent::service($className);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        return BonusCodeLog::model()->search();
    }

    /**
     * 添加优惠券变更日志
     * @param   <int>       $bonusId
     * @param   <string>    $remark      备注
     * @param   <string>    $operation   操作类型
     * @param   <string>    $operation   操作人
     * @param   <array>     $operArray   操作数据数组
     * @return  <bool>                   是否添加成功
     * @author liuxiaobo    2013-9-12
     */
    public function addAuditLog($bonusId, $operation, $remark = '', $operator = NULL, $operArray = array())
    {
        return BonusCodeLog::model()->addAuditLog($bonusId, $operation, $remark, $operator, $operArray);
    }
}