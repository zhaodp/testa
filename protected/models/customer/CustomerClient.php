<?php
/**
 * 客户客户端注册
 * User: zhanglimin
 * Date: 13-8-5
 * Time: 下午2:41
 *
 * This is the model class for table "{{customer_client}}".
 *
 * The followings are the available columns in table '{{customer_client}}':
 * @property string $id
 * @property string $client_id
 * @property string $udid
 * @property string $phone
 * @property string $created
 */

class CustomerClient extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_client}}';
    }

    /**
     * Get db connection
     */
    public function getDbConnection()
    {
	return self::getDbMasterConnection();
    }

    /**
     * Master db connection
     */
    public static function getDbMasterConnection()
    {
	return Yii::app()->dborder;
    }

    /**
     * Slave db connection
     */
    public static function getDbReadonlyConnection()
    {
	return Yii::app()->dborder_readonly;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('udid, created', 'required'),
            array('client_id, udid', 'length', 'max'=>100),
            array('phone', 'length', 'max'=>20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, client_id, udid, phone, created', 'safe', 'on'=>'search'),
            array('type', 'safe'),
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
            'client_id' => 'Client',
            'udid' => 'Udid',
            'phone' => 'Phone',
            'created' => 'Created',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('client_id',$this->client_id);
        $criteria->compare('udid',$this->udid);
        $criteria->compare('phone',$this->phone);
        $criteria->compare('created',$this->created);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CustomerClient the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 获取客户端注册信息
     * @param string $driver_id 司机用户ID
     */
    public function getInfo($udid) {
        if(empty($udid)) return "";

        $driver = CustomerClient::getDbReadonlyConnection()->createCommand()
            ->select('*')
            ->from('{{customer_client}}')
            ->where('udid=:udid',array(':udid'=>$udid))
            ->queryRow();

        return $driver;
    }
    
    /**
     * 获取client
     * @param string $phone
     * @return array
     */
    public function getByPhone($phone = '') {
    	if (empty($phone)) {
    		return array();
    	}
    	
    	$client = CustomerClient::getDbReadonlyConnection()->createCommand()
    	             ->select('client_id')
    	             ->from('t_customer_client')
    	             ->where('phone = :phone' , array(':phone' => $phone))
    	             ->order('id desc')
    	             ->queryRow();
        return $client;
    }

    /**
     * 获取client
     * @param string $phone
     * @return array
     */
    public function getByPhoneAndLast($phone = '') {
        if (empty($phone)) {
            return array();
        }
        
        $client =  CustomerClient::getDbReadonlyConnection()->createCommand()
                     ->select('*')
                     ->from('t_customer_client')
                     ->where('phone = :phone' , array(':phone' => $phone))
                     ->order('created desc')
                     ->queryRow();
        return $client;
    }

}
