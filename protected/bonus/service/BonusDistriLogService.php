<?php

class BonusDistriLogService extends BaseService
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
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		return BonusDistriLog::model()->search();
	}

    /**
     * 获取城市分配人列表
     * @param int $city_id
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getCityDistri($city_id)
    {
        return BonusDistriLog::model()->getCityDistri($city_id);
    }

    /**
     * 获取分配人分配渠道列表
     * @param string $disTri_by
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelByDistriBy($disTri_by)
    {
        return BonusDistriLog::model()->getChannelByDistriBy($disTri_by);
    }


    /**
     * 获取分配人分配渠道列表
     * @param string $disTri_by
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelDistriCount($channel,$dateStart,$dateEnd)
    {
        return BonusDistriLog::model()->getChannelDistriCount($channel,$dateStart,$dateEnd);
    }


    /**
     * 获取分配人分配渠道列表
     * @param string $disTri_by
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelDistriByCity($city_id)
    {
        return BonusDistriLog::model()->getChannelDistriByCity($city_id);
    }

    /**
     * 获取渠道被分配数量
     * @param array $log_id
     * @return array
     * @auther mengtianxue
     */
    public static function getChannelDistri($log_id)
    {
        return BonusDistriLog::model()->getChannelDistri($log_id);
    }
}
