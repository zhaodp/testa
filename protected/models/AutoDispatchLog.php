<?php
/**
 * This is the model class for table "{{auto_dispatch_log}}".
 *
 * The followings are the available columns in table '{{auto_dispatch_log}}':
 * @property integer $id
 * @property integer $queue_id
 * @property string $driver_id
 * @property integer $distance
 * @property integer $flag
 * @property integer $number
 * @property string $dispatch_time
 * @property string $accept_time
 * @property string $success_time
 */
class AutoDispatchLog extends CActiveRecord
{
	const TYPE_DRIVER_NO_DISPATCH = 0;     //取出司机未派单
	const TYPE_DRIVER_DISPATCH = 1;        //取出
	const TYPE_DRIVER_ACCEPT = 2;          //接单
	const TYPE_DRIVER_ACCEPT_SUCCESS = 3;  //接单成功
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{auto_dispatch_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('queue_id, driver_id, distance', 'required'),
            array('queue_id, distance, flag, number', 'numerical', 'integerOnly'=>true),
            array('driver_id', 'length', 'max'=>10),
            array('dispatch_time, accept_time, success_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, queue_id, driver_id, distance, flag, number, dispatch_time, accept_time, success_time', 'safe', 'on'=>'search'),
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
            'driver_id' => 'Driver',
            'distance' => 'Distance',
            'flag' => 'Flag',
            'number' => 'Number',
            'dispatch_time' => 'Dispatch Time',
            'accept_time' => 'Accept Time',
            'success_time' => 'Success Time',
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
        $criteria->compare('queue_id',$this->queue_id);
        $criteria->compare('driver_id',$this->driver_id);
        $criteria->compare('distance',$this->distance);
        $criteria->compare('flag',$this->flag);
        $criteria->compare('number',$this->number);
        $criteria->compare('dispatch_time',$this->dispatch_time);
        $criteria->compare('accept_time',$this->accept_time);
        $criteria->compare('success_time',$this->success_time);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AutoDispatchLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 将从nearby取出的司机保存
     * @param array $drives
     * @param $dispatch_driver 派送司机
     * @param $queue_id
     * @param $order_id
     * @return bool
     */
    public function nearbyDriversSave($drivers = array() , $dispatch_driver ,  $queue_id ,$order_id){
    	//直接返回（先停掉）
    	//return true;
        if(empty($drivers)){
            return false;
        }
        foreach($drivers as $driver){
            $info = array(
              'queue_id'  => $queue_id,
              'driver_id'  => $driver['driver_id'],
              'order_id'  => $order_id,
              'crown'  => isset($driver['crown'])?$driver['crown'] : 0,  //加皇冠 BY AndyCong 2013-07-28
              'distance'  => $driver['distance'],
              'flag' => $driver['driver_id'] == $dispatch_driver['driver_id'] ? 1 : 0, //状态
              'number' => $driver['driver_id'] == $dispatch_driver['driver_id'] ? 1 : 0,//派单次数
              'dispatch_time' => date("Y-m-d H:i:s"),
            );
            $ret = AutoDispatchLog::model()->find(' driver_id = :driver_id and queue_id =:queue_id and order_id = :order_id',
                                            array(
                                                'driver_id'=>$info['driver_id'],
                                                'queue_id'=>$info['queue_id'],
                                                'order_id'=>$info['order_id'],
                                            ));
            if(empty($ret)){
                //新增
                 Yii::app()->db->createCommand()->insert('{{auto_dispatch_log}}',$info);
            }else{
                //更新
                $up = array(
                    'distance'=>$info['distance'],
                    'dispatch_time'=>$info['dispatch_time'],
                    'number'=>$ret->number+1
                );
                if($info['flag'] == 0){
                    //状态取出未派不更新次数
                    unset($up['number']);
                }
                 Yii::app()->db->createCommand()->update('{{auto_dispatch_log}}',
                    $up,
                        'driver_id=:driver_id and queue_id=:queue_id',
                    array(
                        'driver_id'=>$info['driver_id'],
                        'queue_id'=>$info['queue_id']
                    )
                );
            }
        }

        return true;

    }

    /**
     * 订单接受状态更新
     * @param $params
     * @return bool
     */
    public function acceptDriverStatusUpdate($params){
    	//直接返回（先停掉）
    	//return true;
        if(empty($params)){
            return false;
        }
        return Yii::app()->db->createCommand()->update('{{auto_dispatch_log}}',
            $params,
            'driver_id=:driver_id and queue_id=:queue_id',
            array(
                'driver_id'=>$params['driver_id'],
                'queue_id'=>$params['queue_id']
            )
        );

    }
    
    public function dispatchLog($condition) {
    	$time = time();
    	$start_time = date('Y-m-d H:i:s' , $time-86400*3);
    	$end_time = date('Y-m-d H:i:s' , $time);
    	$str = '';
    	if (!empty($condition['flag'])) {
    		$str_flag = ' flag = '.$condition['flag'];
    	}else {
    		$str_flag = ' flag <> '.self::TYPE_DRIVER_NO_DISPATCH;
    	}
    	
    	if (!empty($condition['queue_id'])) {
    		$str = ' and queue_id = '.$condition['queue_id'];
    	}
    	
    	if (!empty($condition['order_id'])) {
    		$str = ' and order_id = '.$condition['order_id'];
    	}
    	if (!empty($condition['start_time'])) {
    		$start_time = $condition['start_time'];
    	}
    	if (!empty($condition['end_time'])) {
    		$end_time = $condition['end_time'];
    	}
    	

    	$sql = "SELECT * FROM t_auto_dispatch_log WHERE ".$str_flag.$str." AND dispatch_time BETWEEN '".$start_time."' AND '".$end_time."'  ORDER BY queue_id DESC , dispatch_time DESC";
		$count = Yii::app()->db_readonly->createCommand()
		            ->select('*')
		            ->from('t_auto_dispatch_log')
		            ->where("flag <> ".self::TYPE_DRIVER_NO_DISPATCH.$str." and dispatch_time between '".$start_time."' and '".$end_time."'")
		            ->query()
		            ->count();
		//sql数据转化成Provider格式 源自：http://blog.yiibook.com/?p=420   Yii手册CSqlDataProvider
		$dataProvider = new CSqlDataProvider($sql, array(
	            'keyField'=>'id',   //必须指定一个作为主键
	            'totalItemCount'=>$count,    //分页必须指定总记录数
	            'db'=>Yii::app()->db_readonly,
	            'pagination'=>array(
			        'pageSize'=>50,
			    ),
	    ));
	    //sql数据转化成Provider格式 END
	    return $dataProvider;
    }
    	
}
