<?php

/**
 * This is the model class for table "{{callcenter_error}}".
 *
 * The followings are the available columns in table '{{callcenter_error}}':
 * @property integer $id
 * @property integer $queue_id
 * @property integer $order_id
 * @property integer $city_id
 * @property string $order_time
 * @property string $driver_id
 * @property string $location_start
 * @property string $location_end
 * @property string $agent_id
 * @property string $error_type
 * @property string $mark
 * @property string $create_time
 * @property string $update_time
 * @property string $operator
 */
class CallcenterError extends CActiveRecord
{

    public  $start_time;
    public  $end_time;
    public static  $errorArr=array('1'=>'城市','2'=>'电话','3'=>'地址','4'=>'人数','5'=>'时间','6'=>'其他');
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CallcenterError the static model class
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
        return '{{callcenter_error}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id, order_time, agent_id, error_type, create_time, operator', 'required'),
            array('queue_id, order_id, city_id', 'numerical', 'integerOnly'=>true),
            array('driver_id, agent_id, operator', 'length', 'max'=>10),
            array('location_start, location_end, error_type', 'length', 'max'=>20),
            array('mark', 'length', 'max'=>100),
            array('update_time', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, queue_id, order_id, city_id, order_time, driver_id, location_start, location_end, agent_id, error_type, mark, create_time, update_time, operator', 'safe', 'on'=>'search'),
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
            'queue_id' => 'Queue',
            'order_id' => 'Order',
            'city_id' => 'City',
            'order_time' => 'Order Time',
            'driver_id' => 'Driver',
            'location_start' => 'Location Start',
            'location_end' => 'Location End',
            'agent_id' => 'Agent',
            'error_type' => 'Error Type',
            'mark' => 'Mark',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'operator' => 'Operator',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('queue_id',$this->queue_id);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('order_time',$this->order_time,true);
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('location_start',$this->location_start,true);
        $criteria->compare('location_end',$this->location_end,true);
        $criteria->compare('agent_id',$this->agent_id,true);
        $criteria->compare('error_type',$this->error_type,true);
        $criteria->compare('mark',$this->mark,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('operator',$this->operator,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function addErrorByQueueId($queue_id,$error_type,$mark){
        $model=new CallcenterError();
        $ret=false;
        $queueInfo= Yii::app()->db_readonly->createCommand()
            ->select('city_id,address,booking_time,agent_id,created')
            ->from('{{order_queue}}')
            ->where("id=:queue_id")
            ->order('id desc')
            ->queryRow(true,array(':queue_id'=>$queue_id));
        if($queueInfo){
            $model->city_id=$queueInfo['city_id'];
            $model->agent_id=$queueInfo['agent_id'];
            $model->error_type=$error_type;
            $model->mark=$mark;
            $model->location_start=$queueInfo['address'];
            $model->order_time=$queueInfo['created'];
            $model->queue_id=$queue_id;
            $model->operator=Yii::app()->user->id;
            $model->create_time=date('Y-m-d H:i:s',time());

            $ret=$model->insert();
        }

        return $ret;
    }
    public function addErrorByOrderId($order_id,$error_type,$mark){
        $model=new CallcenterError();
        $ret=false;
        
        $orderQueueMap = OrderQueueMap::model()->findByAttributes(
            array('order_id' => $order_id),
            array('select'   => 'queue_id')
        );
        $queue_id = $orderQueueMap->queue_id;

        $agent_id= Yii::app()->db_readonly->createCommand()
            ->select('agent_id')
            ->from('{{order_queue}}')
            ->where("id=:queue_id")
            ->queryScalar(array(':queue_id'=>$queue_id));

        $orderInfo= Order::getDbReadonlyConnection()->createCommand()
            ->select('driver_id,city_id,created,location_start,location_end')
            ->from('{{order}}')
            ->where("order_id=:order_id")
            ->order('order_id desc')
            ->queryRow(true,array(':order_id'=>$order_id));

        if($orderInfo){
            $model->queue_id=$queue_id;
            $model->order_id=$order_id;
            $model->driver_id=$orderInfo['driver_id'];
            $model->city_id=$orderInfo['city_id'];
            $model->agent_id=$agent_id?$agent_id:'';
            $model->error_type=$error_type;
            $model->mark=$mark;
            $model->location_start=$orderInfo['location_start'];
            $model->location_end=$orderInfo['location_end'];
            $model->order_time=date('Y-m-d H:i:s',$orderInfo['created']) ;

            $model->operator=Yii::app()->user->id;
            $model->create_time=date('Y-m-d H:i:s',time());

            $ret=$model->insert();
        }

        return $ret;
    }
} 
