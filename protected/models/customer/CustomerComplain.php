<?php

/**
 * This is the model class for table "{{customer_complain}}".
 *
 * The followings are the available columns in table '{{customer_complain}}':
 * @property integer $id
 * @property integer $attention
 * @property string $name
 * @property string $phone
 * @property string $customer_phone
 * @property string $driver_id
 * @property string $driver_phone
 * @property integer $order_id
 * @property string $service_time
 * @property integer $complain_type
 * @property integer $source
 * @property string $detail
 * @property string $create_time
 * @property string $update_time
 * @property string $created
 * @property string $operator
 * @property integer $city_id
 * @property integer $status
 * @property integer $cs_process
 * @property integer $sp_process
 * @property integer $dm_process
 * @property integer $finance_process
 * @property integer $support_ticket_id
 * @property integer $driver_read
 * @property integer $reply_status
 * @property integer $pnode
 * @property integer $group_id
 * @property integer $user_id
 * @property string $contact_phone
 */
class CustomerComplain extends CActiveRecord
{
    const STATUS_CS=1;     //客服创建
    const STATUS_SP=2;     //品鉴已处理
    const STATUS_DM=3;     //司管已处理
    const STATUS_FC=4;     //财务已处理
    const STATUS_END=5;     //关闭
    const STATUS_REVERT=6;   //撤销
    const STATUS_MONEY_CONFIRM=7;//待财务审核
    const STATUS_EFFECT=8;//投诉已生效
    const STATUS_REJECT=9;//已驳回


    public static $singleDriverCount = array();    //司机的投诉次数

    public static $newStatus= array(
        '0' => '全部',
        '1'=>'待品监处理',
        '7'=>'待财务审核',
        '8'=>'投诉已生效',
        '9'=>'已驳回',
        '5'=>'已关闭',
        '6'=>'已撤销');

    public static $status= array(
        '0' => '全部',
        '1'=>'待品监处理',
        '2'=>'待司管处理',
        '3'=>'待财务处理',
        '4'=>'财务已处理',
        '5'=>'已关闭',
        '6'=>'已撤销');

    const SP_PROCESS_S=4;   //品监排除投诉
    const SP_PROCESS_O=1;   //品监备注
    const SP_PROCESS_T1=2;   //品监确认投诉,优惠券补偿
    const SP_PROCESS_T2=3;   //品监确认投诉,现金补偿
    const SP_PROCESS_T13=13;   //撤销处理完毕


    public static $sp_result=array(
        '1'=>'品监已定位订单',
        '2'=>'优惠券补偿',
        '3'=>'现金补偿',
        '4'=>'排除投诉');

    const DM_PROCESS_O=5;   //屏蔽1天
    const DM_PROCESS_T1=6;   //屏蔽3天
    const DM_PROCESS_T2=7;   //屏蔽7天
    const DM_PROCESS_S=8;   //解约
    const DM_PROCESS_P=9;   //不处罚
    public static $driver_spro=array(
        '5'=>'屏蔽1天',
        '6'=>'屏蔽3天',
        '7'=>'屏蔽7天',
        '8'=>'解约',
        '9'=>'不处罚',
    );


    public static $source=array(
        '1'=>'400客服',
        '2'=>'短信评价',
        '3'=>'APP评价(客户)',
        '8'=>'APP反馈(司机)',
        '4'=>'微信微博',
        '5'=>'品监电话',
        '6'=>'系统',
        '7'=>'内部自查',
    );
    public static $pnode=array(
        '0'=>'全部',
        '1'=>'联系司机',
        '2'=>'未联系上客人',
        '3'=>'已联系上客人',
        '4'=>'估损',
        '7'=>'处理',
        '5'=>'疑难案件',
        '6'=>'诉讼',
        '8'=>'完结',
    );

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerComplain the static model class
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
        return '{{customer_complain}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
//    public function rules()
//    {
//        // NOTE: you should only define rules for those attributes that
//        // will receive user inputs.
//        return array(
//            array('name, phone, service_time, detail, create_time, created, operator', 'required'),
//            array('attention, order_id, complain_type, source, city_id, status, cs_process, sp_process, dm_process, finance_process, support_ticket_id, driver_read, reply_status, pnode, group_id, user_id', 'numerical', 'integerOnly'=>true),
//            array('name, phone, customer_phone, driver_phone, created, operator, contact_phone', 'length', 'max'=>20),
//            array('driver_id', 'length', 'max'=>10),
//            array('detail', 'length', 'max'=>200),
//            array('update_time', 'safe'),
//            // The following rule is used by search().
//            // @todo Please remove those attributes that should not be searched.
//            array('id, attention, name, phone, customer_phone, driver_id, driver_phone, order_id, service_time, complain_type, source, detail, create_time, update_time, created, operator, city_id, status, cs_process, sp_process, dm_process, finance_process, support_ticket_id, driver_read, reply_status, pnode, group_id, user_id, contact_phone', 'safe', 'on'=>'search'),
//        );
//    }
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,source, phone,customer_phone, detail, create_time,complain_type, operator, city_id', 'required'),
            array('order_id, reply_status, attention, complain_type, source,pnode, city_id, status, cs_process, sp_process, dm_process, finance_process, group_id, user_id', 'numerical', 'integerOnly'=>true),
            array('name, phone, customer_phone, driver_phone,created,  operator', 'length', 'max'=>20),
            array('driver_id', 'length', 'max'=>10),
            array('detail', 'length', 'max'=>200),
            array('update_time,reply_status', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name,reply_status, phone, customer_phone, driver_id, driver_phone, order_id, service_time, complain_type, source,pnode, detail, create_time, update_time,created,  operator, city_id, status, cs_process, sp_process, dm_process, finance_process, group_id, user_id,contact_phone', 'safe', 'on'=>'search'),
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
            'attention' => '是否加关注',
            'name' => '投诉人姓名',
            'phone' => '投诉人手机',
            'customer_phone' => '预约电话',
            'driver_id' => '司机工号',
            'driver_phone' => '司机手机',
            'order_id' => '订单ID',
            'service_time' => '代驾时间',
            'complain_type' => '投诉类型',
            'source' => '投诉来源',
            'pnode' => '处理节点',
            'detail' => '投诉详情',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'created' => '创建人',
            'operator' => '处理人',//by 曾志海
            'status' => '投诉状态',
            'reply_status'=>'是否回复',
            'city_id' => '城市',
            'cs_process' => '客服处理结果',
            'sp_process' => '品鉴处理结果',
            'dm_process' => '司管处理结果',
            'finance_process' => '财务处理结果',
            'group_id' => '任务组id',
            'user_id' => '任务人id',
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
        $criteria->compare('attention',$this->attention);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('customer_phone',$this->customer_phone,true);
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('driver_phone',$this->driver_phone,true);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('service_time',$this->service_time,true);
        $criteria->compare('complain_type',$this->complain_type);
        if($this->source){
            $criteria->compare('source',$this->source);
        }
        if($this->pnode){
            $criteria->compare('pnode',$this->pnode);
        }
        $criteria->compare('detail',$this->detail,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('operator',$this->operator,true);
        if(!empty($this->status)){
            $criteria->compare('status',$this->status);
        }
        if($this->city_id) {
            $criteria->compare('city_id',$this->city_id);
        }
        $criteria->compare('cs_process',$this->cs_process);
        $criteria->compare('sp_process',$this->sp_process);
        $criteria->compare('dm_process',$this->dm_process);
        $criteria->compare('finance_process',$this->finance_process);
        $criteria->compare('support_ticket_id',$this->support_ticket_id);
        $criteria->compare('driver_read',$this->driver_read);
        $criteria->compare('reply_status',$this->reply_status);
        $criteria->compare('pnode',$this->pnode);
        $criteria->compare('group_id',$this->group_id);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('contact_phone',$this->contact_phone,true);

        $criteria->order='create_time asc';

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function getComplainList($criteria){
        return  self::model()->findAll($criteria);
    }

    public function updateStatus($pk,$status=array()){
        if(!empty($status)){

        }
        //Post::model()->updateAll($attributes,$condition,$params);
    }

    /**
     * 增加扣分所用投诉，方便司机申诉
     *
     */
    public function addCompleteComplain($driver_id,$city_id,$reason,$complainTypeId){
        $complain = new CustomerComplain();
        $complain->name="处罚";
        $complain->phone=$complain->customer_phone=0;
        $complain->driver_id=$driver_id;
        $complain->driver_phone=0;
        $complain->order_id=0;
        $complain->complain_type=$complainTypeId;
        $complain->city_id=$city_id;
        $complain->operator="system";
        $complain->source=6;//系统
        $complain->detail=$reason;
        $complain->status=self::STATUS_EFFECT;
        $complain->service_time=date('Y-m-d H:i:s');
        $complain->create_time=date('Y-m-d H:i:s');
        $complain->save();
        return $complain->id;
    }

    public function insertComplain($data = array())
    {
        if (empty($data) || !is_array($data))
            return;

        foreach ($data as $c) {
            $condition = 'driver_id=:did and order_id=:oid and source=:s and create_time=:ctime';
            $params = array(':did' => $c['driver_id'],
                ':oid' => $c['order_id'],
                ':s' => $c['source'], ':ctime' => date('Y-m-d', strtotime($c['create_time'])));
            $c_cnt = self::model()->find($condition, $params);

            if (empty($c_cnt)) {

                $orderInfo = Order::getDbReadonlyConnection()->createCommand()
                    ->select('order_id,order_number,name,phone,booking_time,driver_phone')
                    ->from('t_order')
                    ->where('order_id=:oid', array(':oid' => $c['order_id']))
                    ->queryRow();

                if ($orderInfo) {
                    $complain = new CustomerComplain();
                    $complain->unsetAttributes();

                    $complain->name=$orderInfo['name'];
                    $complain->phone=$complain->customer_phone=$orderInfo['phone'];
                    $complain->driver_id=$c['driver_id'];
                    $complain->driver_phone=$orderInfo['driver_phone'];
                    $complain->order_id=$c['order_id'];
                    $complain->city_id=$c['city_id'];
                    $complain->operator=$c['operator'];
                    $complain->source=$c['source'];
                    $complain->detail=$c['detail'];
                    $complain->status=CustomerComplain::STATUS_CS;


                    $complain->service_time=date('Y-m-d H:i:s', $orderInfo['booking_time']);
                    $complain->create_time=$c['create_time'];

                    $ret= $complain->insert();
                }

            }
        }

    }
    public static function getDriverCount($driverId){
        if(!isset(self::$singleDriverCount[$driverId])){
            self::$singleDriverCount[$driverId] = Yii::app()->db_readonly->createCommand()
                ->select('count(1)')
                ->from('{{customer_complain_deduct}}')
                ->where('driver_id = :driver_id AND mark <> :mark')
                ->queryScalar(array(':driver_id'=>$driverId,':mark'=>'0.0'));
        }
        return self::$singleDriverCount[$driverId];
    }
    public function addAttention($pk, $status){
        return self::model()->updateByPk($pk, array('attention'=>$status));
    }

    /**
     * @param $complain_id
     * @param string $type
     * @return array|bool|CActiveRecord|mixed|null|string
     * @author daiyihui
     */
    public function getDetailAndType($complain_id, $type = '')
    {
        if(!empty($complain_id)){
            $complainData = self::model()->find('id = :id', array(':id' => $complain_id));
            if($type === 1){
                return $complainData;
            }else{
                $TypeList = CustomerComplainType::model()->getComplainType($complainData['complain_type']);
                if($TypeList){
                    return $TypeList[0]->name;
                }else
                    return '无分类';
            }

        }else
            return false;
    }

    /**
     * 根据客户手机号码获取该客户投诉次数
     * @param <string> $phone
     * @return <int>
     */
    public function getCountByCustomerPhone($phone){
        $cache = Yii::app()->cache;
        $key = 'ComplainCountByCustomerPhone_'.$phone;
        if ($cacheValue = $cache->get($key)) {
            return $cacheValue ? $cacheValue : 0;
        }
        $count = CustomerComplain::model()->count('phone = :phone OR customer_phone = :phone', array(':phone'=>$phone));
        $cache->set($key,$count,60*60);    //保存1小时的有效期
        return $count ? $count : 0;
    }

    /**
     * 根据driver_id city_id 查询司机被投诉列表
     * wanglonghuan 2014-1-17
     */
    public function getComplainListByDriver($driver_id,$page,$pageSize,$timeType=0,$refresh=false)
    {
        //add by aiguoxin  2 weeks
        $end_time = date("Y-m-d H:i:s",strtotime("-14 day"));
        //add by aiguoxin
        $limitStart = ($page-1)*$pageSize;
        $cache_key = 'CUSTOMER_COMPLAIN_LIST_' . $driver_id . "_".$page."_".$pageSize;
        $json = Yii::app()->cache->get($cache_key);
        if (!$json||$json=='[]'||$refresh)
        {
            $id_list = SupportTicket::model()->getComplainIds($driver_id);
            $ids = '0';
            if(!empty($id_list)){
                $ids = implode(',',$id_list);
            }
            if($timeType == 1){
                $sql = "select `id`,`detail`,`create_time`,`status`,`complain_type`,`driver_read`
                from t_customer_complain
                where driver_id=:driver_id
                and `status` not in(5,6)
                and `id` not in(".$ids.")
                and `create_time` > :end_time
                order by create_time desc
                limit ".$limitStart.",".$pageSize;
                $params = array('driver_id'=>$driver_id,'end_time'=>$end_time);
            }else{
                $sql = "select `id`,`detail`,`create_time`,`status`,`complain_type`,`driver_read`
                from t_customer_complain
                where driver_id=:driver_id
                and `status` not in(5,6)
                and `id` not in(".$ids.")
                order by create_time desc
                limit ".$limitStart.",".$pageSize;
                $params = array('driver_id'=>$driver_id);
            }
            self::$db = Yii::app ()->db_readonly;
            $models = self::model()->findAllBySql($sql, $params);
            $data = array();
            if(!empty($models)){
                foreach($models as $k=> $model){
                    $complain_type_name = '';
                    $type= CustomerComplainType::model()->getComplainType($model->complain_type);
                    if($type){
                        $complain_type_name= $type[0]->name;
                    }
                    //add by aiguoxin 2014-07-08 兼容旧状态
                    $status = $model->status;
                    if($status == self::STATUS_SP || $status == self::STATUS_DM || $status == self::STATUS_FC){
                        $status=self::STATUS_EFFECT;
                    }
                    $data[] = array(
                        'id' => $model->id,
                        // 'content' => Helper::cut_str($model->detail,20),
                        'content' => $model->detail,
                        'date' => date("Y-m-d H:i",strtotime($model->create_time)),
                        'status' => CustomerComplain::$newStatus[$status],
                        'complain_type'=>$complain_type_name,
                        'read'=>$model->driver_read,
                    );
                }
            }
            $json = json_encode($data);
            Yii::app()->cache->set($cache_key, $json, 3600);
        }
        return json_decode($json);
    }
    /*
     * 根据id 获取详情
     */
    public function getComplaintDetailById($id,$refresh=false)
    {
        $cache_key = 'CUSTOMER_COMPLAIN_DETAIL_' . $id;
        $json = Yii::app()->cache->get($cache_key);
        if (!$json||$json=='[]'||$refresh)
        {
            $sql = "select id,detail,create_time from t_customer_complain where id=:id";
            $params = array('id'=>$id);
            $data = Yii::app()->db_readonly->createCommand($sql)->queryRow(true,$params);
            $json = json_encode($data);
            Yii::app()->cache->set($cache_key, $json, 3600);
        }
        return json_decode($json);
    }

    /**
     *   update complain has been driver read
     *   aiguoxin
     */
    public function updateDriverRead($id,$driver_id){
        $receive_time = array('driver_read' => 1);
        $where = 'id = :id and driver_id=:driver_id';
        $param = array(':id' => $id,':driver_id'=>$driver_id);

        $ret = $this->updateCounters($receive_time, $where, $param);
        if ($ret) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *   get unread complain
     *   aiguoxin
     */
    public function getUnreadComplainCount($driver_id){
        //add by aiguoxin  2 weeks
        $end_time = date("Y-m-d H:i:s",strtotime("-14 day"));
        //add by aiguoxin

        $id_list = SupportTicket::model()->getComplainIds($driver_id);
        $ids = '0';
        if(!empty($id_list)){
            $ids = implode(',',$id_list);
        }
        $command = Yii::app()->db_readonly->createCommand()
            ->select('count(1)')
            ->from(self::model()->tableName())
            ->where('driver_id = :driver_id
                and `status` not in(5,6)
                and `id` not in('.$ids.')
                and `create_time` > :end_time
                and `driver_read` = 0');
        $query = $command->queryScalar(array(':driver_id'=>$driver_id,':end_time'=>$end_time));
        return $query;
    }

    /**
     *   add by aiguoxin
     *   set status
     */
    public function setStatus($id,$status){
        $res = $this->updateByPk($id, array('status'=>$status));
        return $res;
    }

    /**
     * @param $id
     * @param $status
     * 在确认投诉的时候，只有不在生效和财务确认时候，才能生效
     */
    public function setHandled($id,$status){
        $sql = "update t_customer_complain set status=:status where id=:id and status not in(7,8)";
        $res = Yii::app()->db->createCommand($sql)->execute(array(
            ':id' => $id,
            'status'=>$status
        ));
        return $res;
    }

    /*
     * 根据id 获取complain
     */
    public function getComplainById($id){
        $sql = "select id,order_id,customer_phone,detail,complain_type,group_id,user_id from t_customer_complain where id=:id";
        $params = array('id'=>$id);
        $data = Yii::app()->db_readonly->createCommand($sql)->queryRow(true,$params);
        return $data;
    }


    /**
     *	更新回复状态
     *
     */
    public function updateReplyStatus($id,$reply_status){
        $res = $this->updateByPk($id,array('reply_status'=>$reply_status));
        return $res;
    }

    /**
     * 统计未处理投诉数量
     * @param int $city_id
     * @return mixed
     */
    public function getUntrictedData($city_id = 0){
        $command = Yii::app()->db_readonly->createCommand();

        $where = 'status=:s and sp_process!=:t1';
        $params = array(':s'=>CustomerComplain::STATUS_SP,':t1'=>CustomerComplain::SP_PROCESS_S);
        if ($city_id != 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }
        //待处理
        $untreated = $command->select('count(1) as cnt')->from('t_customer_complain')
            ->where($where,$params)
            ->queryScalar();
        $command->reset();
        return $untreated;
    }

    /**
     * 设置投诉分类
     * @param $id
     * @param $tid
     */
    public function setComplainType($id, $tid) {
        $model = new CustomerComplain;
        return $model->updateByPk($id,array('complain_type'=>$tid));
    }

    /**
     * 设置投诉任务人
     * @param $id
     * @param $tid
     */
    public function setComplainUser($id, $gid, $uid) {
        $model = new CustomerComplain;
        return $model->updateByPk($id,array('group_id'=>$gid,'user_id'=>$uid));
    }

    /**
     * 获取分配给任务人的投诉
     * @param $uid
     * @return mixed
     */
    public function getComplainByTaskUser($uid)
    {
        $complains = Yii::app()->db_readonly->createCommand()->select('id, complain_type, create_time')->from(self::tableName())
            ->where('user_id=:uid',array(':uid'=>$uid))->queryAll();
        return $complains;
    }

    /**
     * 获取未派工的投诉（品监待处理的）
     * @return mixed
     */
    public function getUnDispatchComplain()
    {
        $complains = Yii::app()->db_readonly->createCommand()->select('id, complain_type, phone, create_time')->from(self::tableName())
            ->where('status=:st and user_id is null',array(':st'=>1))->limit(500)->queryAll();//品监待处理的投诉
        return $complains;
    }

    /**
     * 获取其他投诉的任务人
     * @param $phone 投诉人手机号
     * @param $id 投诉id
     */
    public function getOtherComplainUser($phone, $id)
    {
        $conditions = 'phone=:phone';
        $params = array(':phone'=>$phone);
        $complains = Yii::app()->db_readonly->createCommand()->select('id,user_id,group_id')->from(self::tableName())
            ->where($conditions, $params)->queryAll();
        if ($complains) {
            $user_id = '';
            $group_id = '';
            foreach ($complains as $k=>$v) {
                if ($v['id'] != $id && !CnodeLog::model()->isClosed($v['id'])) {
                    $user_id = $v['user_id'];
                    $group_id = $v['group_id'];
                    break;
                }
            }
            if ($user_id && $group_id) {
                return array('group_id'=>$group_id,'user_id'=>$user_id);
            }
        }
        return false;
    }

    /**
     * 获取任务人未结案投诉数量
     * @param $user_id
     * @return int
     */
    public function getUnCloseTaskNum($user_id)
    {
        $number = 0;
        $complains = self::getComplainByTaskUser($user_id);
        if ($complains) {
            foreach ($complains as $k=>$v) {
                if (!CnodeLog::model()->isClosed($v['id'])) {
                    $number ++;
                }
            }
        }
        return $number;
    }
}
