<?php
/**
 * Created by PhpStorm.
 * User: Thinkpad
 * Date: 2015/3/26
 * Time: 11:06
 */

class ProcessListService extends BaseService
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
        return ProcessList::model()->search();
    }



    public function getTail($pid)
    {
        return ProcessList::model()->getTail($pid);
    }
}