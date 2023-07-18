<?php

/**
 * This is the model class for table "{{marketing_activity}}".
 *
 */
class MarketingActivity extends CActiveRecord
{
    const UNLIMITED_CUSTOMER = 3; //不限
    const NEW_CUSTOMER = 1; //新客户
    const OLD_CUSTOMER = 2; //老客户

    const UNLIMITED_PLATFORM = 3; //不限
    const IOS_PLATFORM = 1; //ios
    const ANDROID_PLATFORM = 2; //android


   // const UNLIMITED_VERSION = 0; //不限
   // const VERSION1 = 1; //5.0
   // const VERSION2= 2; //3.3.0
   // const VERSION3 = 3; //3.3.2


    public static $customers = array(
        self::UNLIMITED_CUSTOMER => '不限',     
        self::NEW_CUSTOMER => '新客户',       
        self::OLD_CUSTOMER => '老客户',
    );

     public static $platforms = array(
        self::UNLIMITED_PLATFORM => '不限',        
        self::IOS_PLATFORM => 'IOS版',       
        self::ANDROID_PLATFORM => 'Android版',          
     );

    /** public static $versions = array(
        self::UNLIMITED_VERSION => '不限',
        self::VERSION1 => '3.0', 
	self::VERSION2 => '3.3.0', 
	self::VERSION3 => '3.3.2', 
     );**/


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{marketing_activity}}';
	}

	
        /**
         * Returns the static model of the specified AR class.
         * Please note that you should have this exact method in all your CActiveRecord descendants!
         * @param string $className active record class name.
         * @return TicketUser the static model class
         */
        public static function model($className=__CLASS__)
        {
                return parent::model($className);
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			 array('begintime, endtime, title, url,customer,platform,city_ids','required'),
            		 array('title', 'length', 'max'=>50),
            		 array('url', 'length', 'max'=>255),
            		// The following rule is used by search().
            		// Please remove those attributes that should not be searched.
            		//array('id, title, begintime, endtime, city_ids, customer, platform, status','safe', 'on'=>'search'),

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
			'id' => 'ID',
			'title' => '活动标题',
			'status' => '活动状态',
			'click' => '总点击量',
			'status' => '状态',
			'begin_time' => '开始时间', 
			'end_time' => '结束时间',
		        'city_ids' => '适用地区', 
			'customer' => '新老客限制', 
			'platform' => '适用平台', 
			'url' => '页面预览', 
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

		$criteria=new CDbCriteria;
        	$criteria->condition = " status=0 ";
		$criteria->addCondition("endtime>'".date('Y-m-d H:i:s',time())."'"); 
		$criteria->order = " id desc ";
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            		'pagination'=>array(
                		'pageSize'=>30,
            		),
		));
	}

	public function getDataList($model){
                $criteria=new CDbCriteria;
		if(isset($model['title'])&&!empty($model['title'])){
			$criteria->compare('title', $model['title'],true); 
		}
		if($model['customer']!=0){
			$criteria->compare('customer', $model['customer']);
		}
		if($model['platform']!=0){
			$criteria->compare('platform', $model['platform']);
		}
                $criteria->addCondition(" endtime<='".$model['endtime']." 23:59:59'");
		$criteria->addCondition(" begintime>='".$model['begintime']." 00:00:00'");
		if($model->status=='1'){
			$criteria->addCondition(" status=0 and begintime>'".date('Y-m-d H:i:s',time())."'");
		}else if($model->status=='2'){
			 $criteria->addCondition(" status=0 and begintime<='".date('Y-m-d H:i:s',time())."' and endtime>'".date('Y-m-d H:i:s',time())."'");
		}else if($model->status=='3'){
			$criteria->addCondition(" (status=1 or endtime<='".date('Y-m-d H:i:s',time())."')");
		}
		if($model->city_ids!=0){
		$sql=" (city_ids like '".$model->city_ids.",%' or city_ids like '%,".$model->city_ids.",%' or city_ids like '%,".$model->city_ids."' or city_ids='0')";
			 $criteria->addCondition($sql);
		}
                $criteria->order = " id desc ";
                return new CActiveDataProvider($this, array(
                        'criteria'=>$criteria,
                        'pagination'=>array(
                                'pageSize'=>30,
                        ),
                ));
	}

	public function getActivitiesByCityId($city_id){
              $sql = "SELECT * FROM t_marketing_activity WHERE status=0 and endtime>now() and (city_ids like '".$city_id.",%' or city_ids like '%,".$city_id.",%' or city_ids like '%,".$city_id."' or city_ids=0 or city_ids='".$city_id."') order by begintime asc";
                $dbvalues = Yii::app()->db_readonly->createCommand($sql)->queryAll();
                return $dbvalues;
        }

}
