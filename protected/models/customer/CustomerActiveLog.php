 <?php

/**
 * This is the model class for table "{{customer_active_log}}".
 *
 * The followings are the available columns in table '{{customer_active_log}}':
 * @property integer $id
 * @property integer $city_id
 * @property integer $state
 * @property string $phone
 * @property string $create_time
 * @property string $update_time
 */
class CustomerActiveLog extends CActiveRecord
{   
    // 1.分享弹窗取消 2.分享弹窗确定 3.朋友圈分享成功 
    const SHARE_CANCEL=1;
    const SHARE_OK=2;
    const SHARE_FRIEND=3;
    const CITY_ACTIVE_DISPLAY = 'CITY_ACTIVE_DISPLAY_'; 
    const ACTIVE_NAME_NANJING = 'football_display';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_active_log}}';
    }

   /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('update_time', 'required'),
            array('city_id, state', 'numerical', 'integerOnly'=>true),
            array('phone, active_name', 'length', 'max'=>20),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, city_id, state, phone, active_name, create_time, update_time', 'safe', 'on'=>'search'),
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
            'city_id' => 'City',
            'state' => '1.分享弹窗取消 2.分享弹窗确定 3.朋友圈分享成功',
            'phone' => '客户手机号',
            'active_name' => '活动名',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
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
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('state',$this->state);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('active_name',$this->active_name,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CustomerActiveLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /*
    *   add by aiguoxin
    *   add customerActiveLog
    */
    public function addCustomerActiveLog($phone,$state,$city_id,$active_name){
        $attr = array(
            'state' => $state,
            'phone' => $phone,
            'city_id' => $city_id,
            'active_name' => $active_name,
            'create_time' => date('Y-m-d h:i:s', time()));
        $CustomerActiveLog = new CustomerActiveLog();
        $CustomerActiveLog->attributes = $attr;
        return $CustomerActiveLog->insert();
    }

    /*
    *   add by aiguoxin
    *   find customerActiveLog
    */
    public function isDisplay($phone,$city_id){
        $key = self::CITY_ACTIVE_DISPLAY . $city_id.$phone.$this::ACTIVE_NAME_NANJING;
        $activeLog = Yii::app()->cache->get($key);
        if(empty($activeLog)){
            $activeLog = $this->getActiveLog($phone,$city_id,$this::ACTIVE_NAME_NANJING);
            Yii::app()->cache->set($key, $activeLog, 3600);
        }
        return $activeLog;
    }

    public function getActiveLog($phone,$city_id,$active_name){
        $criteria = new CDbCriteria();
        $criteria->select = "*";
        $criteria->compare('phone', $phone);
        $criteria->compare('city_id', $city_id);
        $criteria->compare('active_name', $active_name);

        $activeLog = self::model()->find($criteria);
        return $activeLog;
    }

    public function updateState($phone,$city_id,$active_name,$state){
        
        $activeLog=$this->getActiveLog($phone,$city_id,$active_name);       
        if(empty($activeLog)){
            return false;
        }
        $activeLog->state = $state;
        if ($activeLog->update()){
            return true;
        } else {
            return false;
        }

    }
}