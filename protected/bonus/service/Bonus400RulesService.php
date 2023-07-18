<?php


class Bonus400RulesService extends BaseService
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
    public function getBonusRules($id)
    {
        return Bonus400Rules::model()->getBonusRules($id);
    }
}