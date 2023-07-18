<?php

/**
 * This is the model class for table "{{order_invoice}}".
 *
 * The followings are the available columns in table '{{order_invoice}}':
 * @property integer $id
 * @property integer $order_id
 * @property string $title
 * @property string $content
 * @property string $contact
 * @property string $address
 * @property string $zipcode
 * @property string $telephone
 * @property integer $status
 * @property string $description
 * @property integer $created
 */
class OrderInvoice extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderInvoice the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{order_invoice}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'order_id, title, content, contact, address, zipcode, telephone', 
				'required'
			), 
			array (
				'order_id, status, created', 
				'numerical', 
				'integerOnly'=>true
			), 
			array (
				'title, content, address', 
				'length', 
				'max'=>200
			), 
			array (
				'contact', 
				'length', 
				'max'=>100
			), 
			array (
				'zipcode', 
				'length', 
				'max'=>6
			), 
			array (
				'telephone', 
				'length', 
				'max'=>20
			), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'order_id, title, content, contact, address, zipcode, telephone, status, description, created', 
				'safe', 
				'on'=>'search'
			)
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array (
			'order'=>array (
				self::BELONGS_TO, 
				'Order', 
				'order_id'
			),
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'order_id'=>'订单号', 
			'title'=>'发票抬头', 
			'content'=>'发票内容', 
			'contact'=>'联系人', 
			'address'=>'收件地址', 
			'zipcode'=>'邮政编码', 
			'telephone'=>'联系电话', 
			'status'=>'处理状态', 
			'description'=>'Description', 
			'created'=>'Created'
		);
	}
	
	public function beforeSave() {
		if (parent::beforeSave()) {
			if ($this->isNewRecord) {
				$this->created = time();
				return true;
			}
		}
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		
		$criteria = new CDbCriteria();		
				
		$criteria->compare('o.order_number', $this->order_id);		
		$criteria->compare('title', $this->title);
		$criteria->compare('o.driver_id', $this->contact);		
		
		if (!$this->status) $this->status = 0;
		if ($this->status == '9') $this->status = '';
				
		$criteria->compare('i.status', $this->status);		
		$criteria->join = 'JOIN t_order AS o ON o.order_id = i.order_id';
		$criteria->alias = 'i';
		$criteria->order = 'created desc';
		
		return new CActiveDataProvider('OrderInvoice', array (
			'pagination'=>array (
				'pageSize'=>15
			), 
			'criteria'=>$criteria
		));		
	}

	public function getOrderInvoiceList($driver_id){
	    $orders = Order::model()->getOrdersByDriverId($driver_id);
	    $in = '(';
	    if(!empty($orders)){
		foreach($orders as $order){
		    $in .= $order['order_id'].',';
		}
	    }
	    $in .= '0)';
	    $sql="select i.order_id as id, i.order_id, i.title, i.content, i.contact, i.address, i.zipcode, i.telephone, i.status, i.description from t_order_invoice i WHERE i.order_id in".$in;
            $rawData=Yii::app()->db_readonly->createCommand($sql)->queryAll();
	    if(!empty($rawData)){
		$size = count($rawData);
		for($i=0 ;$i<$size; $i++){
		    foreach($orders as $order){
			if($rawData[$i]['order_id'] == $order['order_id']){
			    $rawData[$i]['order_number'] = $order['order_number'];
			    $rawData[$i]['created'] = $order['created'];
			    break;
			}
		    }
		}
	    }
            return new CArrayDataProvider($rawData, array(
               'pagination' => array(
               'pageSize' => 15
               ),
            ));
	}
}
