<?php

/**
 * This is the model class for table "{{customer_invoice_report}}".
 */
class CustomerInvoiceReport extends FinanceActiveRecord
{

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerInvoice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{customer_invoice_report}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('web,app,vip,confirm,finance_confirm,not_confirm,finance_not_confirm,not_complate_in_sevenday,not_complate_out_sevenday,
				cancel,created', 'required'),
			array('web,app,vip,confirm,finance_confirm,not_confirm,finance_not_confirm,not_complate_in_sevenday,not_complate_out_sevenday,
				cancel,created', 'numerical', 'integerOnly'=>true),
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
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search(){}


	/**
	 *获取当日开票数据
	**/
 	public function getInvoiceReport(){
		$criteria = new CDbCriteria;  
     		$criteria->select='*';
		$criteria->limit =30;
		$criteria->order = 'id desc' ;
     		$datas = CustomerInvoiceReport::model()->findAll($criteria);
       	        return $datas;
    	}

}
