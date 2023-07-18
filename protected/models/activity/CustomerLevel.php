<?php
/**
*三周年客户回馈活动
**/
class CustomerLevel extends ReportActiveRecord
{
	public function tableName()
	{
		return '{{customer_level}}';
	}

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

	public function rules()
	{
		return array(
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
		);
	}

    /**
     * @param $max_id 上次处理到的客户id
     * @param $number_per_time 每次从数据库获取客户数
     * @return bool
     */
	public function getCustomerListByMaxId($max_id,$number_per_time){
	    $criteria = new CDbCriteria() ;        
	    $criteria -> select = '*';                
	    $criteria -> condition = 'id>:max_id';
	    $criteria -> limit = $number_per_time;
	    $criteria -> params = array (':max_id' => $max_id) ;        
	    $customers = CustomerLevel::model()->findAll($criteria);
	    if(!$customers){
		    return false;
	    }
	    return $customers;
	}

}
