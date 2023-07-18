<?php

/**
 * This is the model class for table "{{support_ticket_fee}}".
 */
class SupportTicketFee extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{support_ticket_fee}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
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

	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SupportTicketMsg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}


	 public static function getSupportTicketFeeList($param){
                $sql = 'select t1.id,t1.city_id,t1.type,t1.class,t1.content,t1.driver_id,t2.information_fee+t2.insurance_fee+t2.fine_fee+t2.other_fee 
                        as total,t2.status,t2.create_user,t2.create_time,t2.deal_user,t2.deal_time from t_support_ticket t1 right join t_support_ticket_fee 
                        t2 on t1.id = t2.support_ticket_id where (t2.status !=3 or (t2.status = 3 and t1.status = 2))';
                if(isset($param['support_ticket_id']) && !empty($param['support_ticket_id'])){
                        $sql .= ' and t1.id = '.$param['support_ticket_id'];
                }
                if(isset($param['driver_id']) && !empty($param['driver_id'])){
                        $sql .= " and t1.driver_id = '".$param['driver_id']."'";
                }
                if(isset($param['city_id']) && !empty($param['city_id'])){
                        $sql .= ' and t1.city_id = '.$param['city_id'];
                }
                if(isset($param['status']) && !empty($param['status'])){
                        $sql .= ' and t2.status = '.$param['status'];
                }
                if(isset($param['create_user']) && !empty($param['create_user'])){
                        $sql .= " and t2.create_user = '".$param['create_user']."'";
                }
                if(isset($param['deal_user']) && !empty($param['deal_user'])){
                        $sql .= " and t2.deal_user = '".$param['deal_user']."'";
                }
                if(isset($param['create_time_begin']) && !empty($param['create_time_begin'])){
                         $sql .= " and t1.create_time>= '".$param['create_time_begin']."'";
                }
                if(isset($param['create_time_end']) && !empty($param['create_time_end'])){
                        $sql .= " and t1.create_time <= '".$param['create_time_end']."'";
                }
                if(isset($param['feec_time_begin']) && !empty($param['feec_time_begin'])){
                        $sql .= " and t2.create_time >= '".$param['feec_time_begin']."'";
                }
                if(isset($param['feec_time_end']) && !empty($param['feec_time_end'])){
                          $sql .= " and t2.create_time <= '".$param['feec_time_end']."'";
                }
                if(isset($param['feed_time_begin']) && !empty($param['feed_time_begin'])){
                         $sql .= " and t2.deal_time  >= '".$param['feed_time_begin']."'";
                }
                if(isset($param['feed_time_end']) && !empty($param['feed_time_end'])){
                        $sql .= " and t2.deal_time <= '".$param['feed_time_end']."'";
                }
		$sql .=' order by t2.status asc,t2.create_time asc';
                $rawData = Yii::app()->db->createCommand($sql)->queryAll();
                $dataProvider=new CArrayDataProvider($rawData, array(
                                                                 'pagination'=>array(
                                                                        'pageSize'=>30,
                                                                 ),
                                   ));
		return $dataProvider;
        }
	public static function getSupportTicketFeeForExport($param){
                $sql = 'select t1.id,t1.city_id,t1.type,t1.class,t1.content,t1.driver_id,t2.information_fee+t2.insurance_fee+t2.fine_fee+t2.other_fee 
                        as total,t2.status,t2.create_user,t2.create_time,t2.deal_user,t2.deal_time from t_support_ticket t1 right join t_support_ticket_fee 
                        t2 on t1.id = t2.support_ticket_id where (t2.status !=3 or (t2.status = 3 and t1.status = 2))';
                if(isset($param['support_ticket_id']) && !empty($param['support_ticket_id'])){
                        $sql .= ' and t1.id = '.$param['support_ticket_id'];
                }
                if(isset($param['driver_id']) && !empty($param['driver_id'])){
                        $sql .= " and t1.driver_id = '".$param['driver_id']."'";
                }
                if(isset($param['city_id']) && !empty($param['city_id'])){
                        $sql .= ' and t1.city_id = '.$param['city_id'];
                }
                if(isset($param['status']) && !empty($param['status'])){
                        $sql .= ' and t2.status = '.$param['status'];
                }
                if(isset($param['create_user']) && !empty($param['create_user'])){
                        $sql .= " and t2.create_user = '".$param['create_user']."'";
                }
                if(isset($param['deal_user']) && !empty($param['deal_user'])){
                        $sql .= " and t2.deal_user = '".$param['deal_user']."'";
                }
                if(isset($param['create_time_begin']) && !empty($param['create_time_begin'])){
                         $sql .= " and t1.create_time>= '".$param['create_time_begin']."'";
                }
                if(isset($param['create_time_end']) && !empty($param['create_time_end'])){
                        $sql .= " and t1.create_time <= '".$param['create_time_end']."'";
                }
                if(isset($param['feec_time_begin']) && !empty($param['feec_time_begin'])){
                        $sql .= " and t2.create_time >= '".$param['feec_time_begin']."'";
                }
                if(isset($param['feec_time_end']) && !empty($param['feec_time_end'])){
                          $sql .= " and t2.create_time <= '".$param['feec_time_end']."'";
                }
                if(isset($param['feed_time_begin']) && !empty($param['feed_time_begin'])){
                         $sql .= " and t2.deal_time  >= '".$param['feed_time_begin']."'";
                }
                if(isset($param['feed_time_end']) && !empty($param['feed_time_end'])){
                        $sql .= " and t2.deal_time <= '".$param['feed_time_end']."'";
                }
                $sql .=' order by t2.status asc,t2.create_time asc';
                $data = Yii::app()->db->createCommand($sql)->queryAll();
                return $data;
        }
	public function getSupportTicketFeeByTicketId($support_ticket_id){   
		 $model = self::model()->find("support_ticket_id = :st_id",
                        array(':st_id' => $support_ticket_id));
		 return $model;
	}
}
