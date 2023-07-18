<?php

/**
 * This is the model class for table "t_envelope_dict".
 *
 * The followings are the available columns in table 't_envelope_dict':
 * @property integer $id
 * @property string $dictname
 * @property integer $code
 * @property string $name
 */
class EnvelopeDictService extends BaseService
{
    /**
     * Returns the static service of the specified AR class.
     * @param string $className active record class name.
     * @return  the static model class
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
		return EnvelopeDict::model()->search();
	}

}