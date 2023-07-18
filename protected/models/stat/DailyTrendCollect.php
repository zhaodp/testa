<?php

/**
 * This is the model class for table "{{daily_trend_collect}}".
 * 订单统计汇总
 * The followings are the available columns in table '{{daily_trend_collect}}':
 * @property integer $id
 * @property integer $city_id
 * @property integer $order_count
 * @property integer $complete_order
 * @property integer $cancel_order
 * @property integer $callcenter_order_count
 * @property integer $app_order_count
 * @property integer $mobile_order_count
 * @property integer $tel_order_count
 * @property integer $new_user_order
 * @property integer $old_user_order
 * @property integer $have_order_driver
 * @property integer $online_driver
 * @property integer $online_no_order
 * @property integer $on_service_driver
 * @property integer $service_driver
 * @property integer $idle_drivers
 * @property integer $driver_cancel
 * @property integer $customer_cancel
 * @property integer $dispatch_cancel
 * @property integer $driver_deny
 * @property string $count_date
 * @property string $create_time
 */
class DailyTrendCollect extends ReportActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DailyTrendCollect the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return CDbConnection database connection
     */
//    public function getDbConnection()
//    {
//        return Yii::app()->dbreport;
//    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{daily_trend_collect}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id, count_date, create_time', 'required'),
            array('city_id, order_count, complete_order, cancel_order, callcenter_order_count, app_order_count, mobile_order_count, tel_order_count, new_user_order, old_user_order, have_order_driver, online_driver, online_no_order, on_service_driver, service_driver, idle_drivers, driver_cancel, customer_cancel, dispatch_cancel, driver_deny', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, city_id, order_count, complete_order, cancel_order, callcenter_order_count, app_order_count, mobile_order_count, tel_order_count, new_user_order, old_user_order, have_order_driver, online_driver, online_no_order, on_service_driver, service_driver, idle_drivers, driver_cancel, customer_cancel, dispatch_cancel, driver_deny, count_date, create_time', 'safe', 'on'=>'search'),
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
            'city_id' => '城市ID',
            'order_count' => '当前总订单数',
            'complete_order' => '报单数',
            'cancel_order' => '消单数',
            'callcenter_order_count' => '呼叫中心订单',
            'app_order_count' => 'APP终端订单',
            'mobile_order_count' => '移动手机订单',
            'tel_order_count' => '固定电话订单',
            'new_user_order' => '新用户订单',
            'old_user_order' => '老用户订单',
            'have_order_driver' => '已接单司机',
            'online_driver' => '已上线司机',
            'online_no_order' => '上线未接单司机',
            'on_service_driver' => '代驾中司机',
            'service_driver' => '服务中司机',
            'idle_drivers' => '空闲司机',
            'driver_cancel' => '司机取消订单',
            'customer_cancel' => '客户取消订单',
            'dispatch_cancel' => '系统取消订单',
            'driver_deny' => '司机拒单',
            'count_date' => '统计日期',
            'create_time' => '创建时间',
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
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('order_count',$this->order_count);
        $criteria->compare('complete_order',$this->complate_order);
        $criteria->compare('cancel_order',$this->comfirm_order);
        $criteria->compare('callcenter_order_count',$this->callcenter_order_count);
        $criteria->compare('app_order_count',$this->app_order_count);
        $criteria->compare('mobile_order_count',$this->mobile_order_count);
        $criteria->compare('tel_order_count',$this->tel_order_count);
        $criteria->compare('new_user_order',$this->new_user_order);
        $criteria->compare('old_user_order',$this->old_user_order);
        $criteria->compare('have_order_driver',$this->have_order_driver);
        $criteria->compare('online_driver',$this->online_driver);
        $criteria->compare('online_no_order',$this->online_no_order);
        $criteria->compare('on_service_driver',$this->on_service_driver);
        $criteria->compare('service_driver',$this->service_driver);
        $criteria->compare('idle_drivers',$this->idle_drivers);
        $criteria->compare('driver_cancel',$this->driver_cancel);
        $criteria->compare('customer_cancel',$this->customer_cancel);
        $criteria->compare('dispatch_cancel',$this->dispatch_cancel);
        $criteria->compare('driver_deny',$this->driver_deny);
        $criteria->compare('count_date',$this->count_date,true);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * 保存数据到汇总表（保存并汇总）
     * @param $detail_model
     * @param date $process_time
     * @author bidong 2013-08-14
     */
    public function addTrendCollect($detail_model,$count_date){
        if (!empty($detail_model) && is_object($detail_model)) {
            $city_id = $detail_model->city_id;

            $condition = 'city_id=:city_id and count_date=:count_date';
            $params = array(':city_id' => $city_id, ':count_date' => $count_date);
            $collectModel = self::model()->find($condition, $params);
            if(empty($collectModel))
                $collectModel=new DailyTrendCollect();

            $collectModel->city_id=$city_id;
            $collectModel->order_count += $detail_model->order_count;
            $collectModel->complete_order +=$detail_model->complete_order;
            $collectModel->cancel_order +=$detail_model->cancel_order;
            $collectModel->callcenter_order_count += $detail_model->callcenter_order_count;
            $collectModel->app_order_count += $detail_model->app_order_count;
            $collectModel->mobile_order_count += $detail_model->mobile_order_count;
            $collectModel->tel_order_count += $detail_model->tel_order_count;
            $collectModel->new_user_order += $detail_model->new_user_order;
            $collectModel->old_user_order += $detail_model->old_user_order;

            $collectModel->have_order_driver += $detail_model->have_order_driver;
            $collectModel->online_driver = $detail_model->online_driver;
            $collectModel->online_no_order += $detail_model->online_no_order;
            $collectModel->on_service_driver = $detail_model->on_service_driver;
            $collectModel->service_driver = $detail_model->service_driver;
            $collectModel->idle_drivers = $detail_model->idle_drivers;
//            $collectModel->driver_cancel=$detail_model->driver_cancel;
//            $collectModel->customer_cancel=$detail_model->customer_cancel;
//            $collectModel->dispatch_cancel=$detail_model->dispatch_cancel;

            $collectModel->count_date = $count_date;
            $collectModel->create_time = date('Y-m-d H:i:s');
            $collectModel->save();
        }
    }


    /**
     * 更新汇总表数据
     * @param array $data
     * @param date $process_time
     * @author bidong 2013-08-16
     */
    public function updateTrendCollect($data,$count_date){

        if (!empty($data)) {
            foreach($data as $k=>$v){
                $city_id = $k;
                $condition = 'city_id=:city_id and count_date=:count_date';
                $params = array(':city_id' => $city_id, ':count_date' => $count_date);
                $collectModel = self::model()->find($condition, $params);
                if(!empty($collectModel)){
                    $collectModel->order_count = $v['total_order'];
                    $collectModel->complete_order =$v['complete_order'];
                    $collectModel->cancel_order =$v['cancel_order'];
                    $collectModel->callcenter_order_count = $v['callcenter_order'];
                    $collectModel->app_order_count = $v['app_order'];
                    $collectModel->have_order_driver = $v['have_order_driver'];
                    $collectModel->driver_cancel=$v['driver_cancel_order'];
                    $collectModel->customer_cancel=$v['customer_cancel_order'];
                    $collectModel->dispatch_cancel=$v['dispatch_cancel_order'];
                    $collectModel->driver_deny=$v['driver_deny'];

                    $collectModel->save();
                }else{
                    $collectModel=new DailyTrendCollect();
                    $collectModel->city_id=$city_id;
                    $collectModel->count_date=$count_date;
                    $collectModel->order_count = $v['total_order'];
                    $collectModel->complete_order =$v['complete_order'];
                    $collectModel->cancel_order =$v['cancel_order'];
                    $collectModel->callcenter_order_count = $v['callcenter_order'];
                    $collectModel->app_order_count = $v['app_order'];
                    $collectModel->have_order_driver = $v['have_order_driver'];
                    $collectModel->driver_cancel=$v['driver_cancel_order'];
                    $collectModel->customer_cancel=$v['customer_cancel_order'];
                    $collectModel->dispatch_cancel=$v['dispatch_cancel_order'];
                    $collectModel->driver_deny=$v['driver_deny'];

                    $collectModel->insert();
                }
            }
        }
    }

    public function updateTrendCollectByDetail($data,$count_date){

        if (!empty($data)) {
            foreach($data as $k=>$v){
                $city_id = $k;
                $condition = 'city_id=:city_id and count_date=:count_date';
                $params = array(':city_id' => $city_id, ':count_date' => $count_date);
                $collectModel = self::model()->find($condition, $params);
                if(!empty($collectModel)){
                    $collectModel->order_count = $v['total_order'];
                    $collectModel->complete_order =$v['complete_order'];
                    $collectModel->cancel_order =$v['cancel_order'];
                    $collectModel->callcenter_order_count = $v['callcenter_order'];
                    $collectModel->app_order_count = $v['app_order'];

                    $collectModel->app_order_count=$v['app_order'];
                    $collectModel->mobile_order_count=$v['mobile_order'];
                    $collectModel->tel_order_count=$v['tel_order'];
                    $collectModel->new_user_order= $v['new_user_order'];
                    $collectModel->old_user_order=$v['old_user_order'];

                    $collectModel->have_order_driver=$v['have_order_driver'];
                    $collectModel->online_driver=$v['online_driver'];
                    $collectModel->online_no_order=$v['online_no_order'];
                    $collectModel->on_service_driver=$v['on_service_driver'];
                    $collectModel->service_driver=$v['service_driver'];
                    $collectModel->idle_drivers=$v['idle_drivers'];

                    $collectModel->save();
                }
            }
        }
    }

    /**
     * 按城市ID 默认返回7天数据
     * @param $city_id
     * @param $days
     */
    public function getCollectDataByDay($city_id=0,$days=7){

        $ret = $condition = $params =$collectData= array();
        $params = array(':start' => date('Y-m-d', strtotime("-$days day")), ':end' => date('Y-m-d', time()));
        if ($city_id) {
            $condition = 'city_id=:city_id and count_date between :start and :end order by count_date desc';
            $params[':city_id'] = $city_id;
            $collectData = self::model()->findAll($condition, $params);
        } else {
            $condition = 'select
                count_date,sum(order_count) as order_count,
            sum(complete_order) as complete_order,
            sum(cancel_order)	as cancel_order,
            sum(callcenter_order_count)	as callcenter_order_count,
            sum(app_order_count) as app_order_count,
            sum(mobile_order_count) as mobile_order_count,
            sum(tel_order_count) as tel_order_count,
            sum(new_user_order) as new_user_order,
            sum(old_user_order) as old_user_order,
            sum(driver_cancel) as driver_cancel,
            sum(customer_cancel) as customer_cancel,
            sum(dispatch_cancel) as dispatch_cancel,
            sum(driver_deny) as driver_deny
            from {{daily_trend_collect}} where id>0 and count_date between :start and :end  group by count_date order by count_date desc';
            $collectData = self::model()->findAllBySql($condition, $params);
        }

        foreach ($collectData as $collect) {
            $temp['count_date'] = $collect->count_date;
            $temp['order_count'] = $collect->order_count;
            $temp['complete_order'] = $collect->complete_order;
            $temp['cancel_order'] = $collect->cancel_order;
            $temp['callcenter_order_count'] = $collect->callcenter_order_count;
            $temp['app_order_count'] = $collect->app_order_count;
            $temp['mobile_order_count'] = $collect->mobile_order_count;
            $temp['tel_order_count'] = $collect->tel_order_count;
            $temp['new_user_order'] = $collect->new_user_order;
            $temp['old_user_order'] = $collect->old_user_order;
            $temp['cancel'] = $collect->customer_cancel + $collect->driver_cancel + $collect->dispatch_cancel+$collect->driver_deny;

            $ret[] = $temp;
        }


        return $ret;
    }

} 