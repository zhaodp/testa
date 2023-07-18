<?php

/**
 * This is the model class for table "{{support_ticket}}".
 *
 * The followings are the available columns in table '{{support_ticket}}':
 * @property integer $id
 * @property integer $type
 * @property string $content
 * @property integer $level
 * @property integer $source
 * @property integer $status
 * @property string $group
 * @property string $operation_user
 * @property string $follow_user
 * @property integer $city_id
 * @property string $phone_number
 * @property integer $order_id
 * @property string $last_reply_user
 * @property string $last_reply_time
 * @property string $create_user
 * @property string $create_time
 * @property string $deadline
 * @property string $close_time
 */
class SupportTicket extends CActiveRecord
{
    const ST_STATUS_BEFORE = 0; //未处理
    const ST_STATUS_PROCESSING = 1; //处理中
    const ST_STATUS_CLOSE = 2; //已处理
    const ST_STATUS_HANGUP = 3; //挂起
    const ST_STATUS_REJECT = 4; //驳回

    public static  $statusList = array(
        self::ST_STATUS_BEFORE => '未处理',
        self::ST_STATUS_PROCESSING => '处理中',
        self::ST_STATUS_CLOSE => '已处理',
        self::ST_STATUS_HANGUP => '挂起',
        self::ST_STATUS_REJECT => '驳回'
    );
    const TICKET_MSG_REDIS_PREFIX  = 'support_ticket_msg_';
    //投诉对象类型
    public static $driver_complaint_type = array(
        0 => '其它',
        1 => '司机',
        2 => '客人',
        3 => '公司工作人员',
    );
    //事件时间
    public $events_time_start;
    public $events_time_end;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{support_ticket}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('group, type, level, source, status, city_id, order_id', 'numerical', 'integerOnly'=>true),
			array('content', 'length', 'max'=>3000),
			array('operation_user', 'length', 'max'=>50),
			array('follow_user, phone_number, last_reply_user, create_user', 'length', 'max'=>20),
			array('last_reply_time, create_time, deadline, close_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, content, level, source, status, group, operation_user, follow_user, city_id, phone_number, order_id,driver_id,
			last_reply_user, last_reply_time, create_user, create_time, deadline, close_time,
			version,os,device,class', 'safe', 'on'=>'search'),
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
			'id' => '工单ID',
			'type' => '类型',
			'class' => '分类',
			'content' => '内容',
			'level' => '等级',
			'source' => '来源',
			'status' => '状态',
			'group' => '部门',
			'operation_user' => '处理人',
			'follow_user' => '跟单人',
			'city_id' => '城市',
			'phone_number' => '电话',
			'order_id' => '订单id',
            'driver_id' => '司机工号',
			'last_reply_user' => '最后回复人',
			'last_reply_time' => '最后回复时间',
			'create_user' => '创建人',
			'create_time' => '创建时间',
			'deadline' => '超时时间',
			'close_time' => '结束时间',
			'version'=> '版本号',
			'os'=> '操作系统版本',
			'device' => '设备',
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
		$criteria->compare('content',$this->content,true);
		$criteria->compare('level',$this->level);
		$criteria->compare('source',$this->source);
		$criteria->compare('operation_user',$this->operation_user,true);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('phone_number',$this->phone_number,true);
        $criteria->compare('order_id',$this->order_id);

        $criteria->compare('last_reply_user',$this->last_reply_user,true);
		//$criteria->compare('last_reply_time',$this->last_reply_time,true);
//        echo $this->last_reply_time_start.'----'.$this->last_reply_time_end;die;
//        if (!empty($this->last_reply_time_start) && !empty($this->last_reply_time_end) && $this->last_reply_time_start <= $this->last_reply_time_end) {
//            $criteria->addBetweenCondition('last_reply_time', strtotime($this->last_reply_time_start), strtotime($this->last_reply_time_end));
//        }

		$criteria->compare('create_user',$this->create_user,true);

        //isset($_GET['search']) && print_r($_GET['search']);
        if(isset($_GET['search']['create_time_start']) && $_GET['search']['create_time_start']) {
            $criteria->addCondition('create_time >= "'.$_GET['search']['create_time_start'].'"');
        }
        if(isset($_GET['search']['create_time_end']) && $_GET['search']['create_time_end']) {
            $criteria->addCondition('create_time <= "'.$_GET['search']['create_time_end'].'"');
        }

        if(isset($_GET['search']['last_reply_time_start']) && $_GET['search']['last_reply_time_start']) {
            $criteria->addCondition('last_reply_time >= "'.$_GET['search']['last_reply_time_start'].'"');
        }
        if(isset($_GET['search']['last_reply_time_end']) && $_GET['search']['last_reply_time_end']) {
            $criteria->addCondition('last_reply_time <= "'.$_GET['search']['last_reply_time_end'].'"');
        }

        if(isset($_GET['search']['close_time_start']) && $_GET['search']['close_time_start']) {
            $criteria->addCondition('close_time >= "'.$_GET['search']['close_time_start'].'"');
        }
        if(isset($_GET['search']['close_time_end']) && $_GET['search']['close_time_end']) {

            $criteria->addCondition('close_time <= "'.$_GET['search']['close_time_end'].'"');
        }

		$criteria->compare('deadline',$this->deadline,true);
		$criteria->compare('close_time',$this->close_time,true);
        $criteria->order = " create_time desc,last_reply_time desc ";
		//$criteria->order = " last_reply_user_type desc,last_reply_time desc,create_time desc "; //duke zhushi
        $admin = TicketUser::model()->checkUserAdmin(Yii::app()->user->name);
        if(!$admin){
           $tUser = TicketUser::model()->find("`user`=:user",array('user'=>Yii::app()->user->name));
            if($tUser->city_manager == 1){
                $criteria->compare('city_id',$tUser->city_id);
            }else{
                $name = Yii::app()->user->name;
                $criteria->condition = "operation_user=:operation_user or follow_user=:follow_user";
                $criteria->params = array('operation_user'=>$name,'follow_user'=>$name);
            }
        }
        if(isset($this->follow_user)){
            $criteria->compare('follow_user',trim($this->follow_user));
        }
        if(isset($this->status)){
            $criteria->compare('status',$this->status);
        }
        if(isset($this->id)){
            $criteria->compare('id',trim($this->id));
        }
        if(isset($this->driver_id)){
            $criteria->compare('driver_id',trim($this->driver_id),true);
        }
        if(isset($this->type)){
            $criteria->compare('type',$this->type);
        }
	if(isset($this->class)){
            $criteria->compare('class',$this->class);
        }
        if(isset($this->group)){
            $criteria->compare('`group`',$this->group);
        }
		if(isset($this->version)){
           $criteria->compare('version',$this->version,true);
        }
		if(isset($this->device)){
            $criteria->compare('device',$this->device,true);
        }
		if(isset($this->os)){
            $criteria->compare('os',$this->os,true);
        }





		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>30,
            ),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SupportTicket the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     *  根据分类id  获取最后跟进人
     * @params   $category_id
     * @return string
     * @author wanglonghuan 2013/12/18
     */
    public function getOperationUser($category_id)
    {
        $sql = "select follow_user from t_support_ticket where id =(select max(id) from t_support_ticket where  type=:category_id);";
        return Yii::app()->db->createCommand($sql)->queryScalar(array('category_id'=>$category_id));
    }

    /**
     * 根据部门id 获取最后跟进人
     */
    public function getOperationUserByGroup($group)
    {
        $sql = "select follow_user from t_support_ticket where id =(select max(id) from t_support_ticket where  `group`=:group);";
        return Yii::app()->db->createCommand($sql)->queryScalar(array('group'=>$group));
    }
    /*
     * 接口获取工单列表列表
     * @param driver_id
     * @return array
     * @author wanglonghuan 2013/12/26
     */
    public function loadSTlist($driver_id, $page=1, $pageSize=10, $refresh=false)
    {
        $limitStart = ($page-1)*$pageSize;
        $cache_key = 'SUPPORT_LIST_' . $driver_id . "_".$page."_".$pageSize;
        $json = Yii::app()->cache->get($cache_key);
        if (!$json||$json=='[]'||$refresh)
        {
            $sql = "select `id`,`content`,`type`,`create_time`,`status`,`new_msg`
                 from t_support_ticket
                 where driver_id=:driver_id
                 order by create_time desc
                 limit ".$limitStart.",".$pageSize;
            $params = array('driver_id'=>$driver_id);
            self::$db = Yii::app ()->db_readonly;
            $models = self::model()->findAllBySql($sql, $params);
            $data = array();
            if(!empty($models)){
                foreach($models as $k=> $model){
                    $data[] = array(
                        'id' => $model->id,
                        'content' => Helper::cut_str($model->content,20),
                        'category' => $model->type,
                        'date' => date("Y-m-d H:i",strtotime($model->create_time)),
                        'have_new_msg' => $model->new_msg,
                        'status' => $model->status,
                        'msg_count' => SupportTicketMsg::model()->getCountByTicketId($model->id),
                    );
                }
            }
            $json = json_encode($data);
            Yii::app()->cache->set($cache_key, $json, 60);
            self::$db = Yii::app ()->db;
        }
        return $json;
    }

    /**
     * 获取工单详细信息
     */
    public function loadSTDetail($id)
    {
        $model = self::model()->findByPk($id);
        SupportTicketMsg::$db = Yii::app ()->db_readonly;
        $msg_models = SupportTicketMsg::model()->findAll(
            "support_ticket_id=:support_ticket_id and reply_type in(".SupportTicketMsg::REPLY_TYPE_TO_DRIVER.",".SupportTicketMsg::REPLY_TYPE_FROM_DRIVER.")",array(
            'support_ticket_id'=>$id,
            //'reply_type'=>SupportTicketMsg::REPLY_TYPE_TO_DRIVER,
        ));
        $data = array();
        $data['ticket_content'] = $model->content;
        $data['date'] = $model->create_time;
        foreach($msg_models as $msg_model){
            $data['msg_list'][] = array(
                'message'=>$msg_model->message,
                'date'=>date("Y-m-d H:i",strtotime($msg_model->create_time)),
                'reply_user_type'=>$msg_model->reply_user_type,
            );
        }
        SupportTicketMsg::$db = Yii::app ()->db;
        return $data;
    }

    /**
     * 创建工单
     */
    public function createSupportTicket($params)
    {
        $date = date('Y-m-d H:i:s',time());
        $model = new SupportTicket();
        $model->type = $params['type'];
        $model->content = $params['content'];
        $model->source = 2;
        $model->create_user = $params['driver_id'];
        $model->phone_number  = $params['phone'];
        $model->city_id = isset($params['city_id'])?$params['city_id']:0;
        $model->create_time = $date;
        $model->driver_id = $params['driver_id'];
        $model->deadline = date('Y-m-d H:i:s',strtotime("+2 days"));
        $model->group = $params['groupUserInfo']['group'];
        //提交相关部门
        $model->status = SupportTicket::ST_STATUS_PROCESSING;   //处理中
        $model->operation_user = $params['groupUserInfo']['user'];
        $model->follow_user = $params['groupUserInfo']['user'];
        $model->device = $params['device'];
        $model->os = $params['os'];
        $model->version = $params['version'];
        $model->order_id = $params['order_id'];
        $model->complaint_type = $params['complaint_type'];
        $model->complaint_target = $params['complaint_target'];
        $model->customer_complain_id = $params['complaint_id'];
        if( $model->save()){
            return $model->id;
        }else{
            return false;
        }
    }

    //转处理人
    public function changeOperactionUser($group,$user,$id)
    {
        $deadline = date('Y-m-d H:i:s',strtotime("+2 days"));
        $model = self::model()->findByPk($id);
        $model->group = $group;
        $model->operation_user = $user;
        $model->deadline = $deadline;
        $model->last_reply_user = Yii::app()->user->name;
        $model->last_reply_time = date("Y-m-d H:i:s",time());
        //$model->last_reply_user_type = 2; //他人回复工单  duke zhushi
        return $model->save();
    }

    /**
     * 查询此司机 申诉过得投诉id
     * return array(id,id,id,)  wanglonghuan 2014-1-17
     */
    public function getComplainIds($driver_id)
    {
        $ret = array();
        $sql = "select `customer_complain_id` from t_support_ticket where `driver_id`=:driver_id and `type`=:type";
        $params = array(
            'driver_id'=>$driver_id,
            'type'=>TicketUser::TICKET_CATEGORY_APPEAL
        );
        $res = Yii::app()->db_readonly->createCommand($sql)->queryAll(true,$params);
        if($res){
            foreach($res as $v){
                $ret[] = $v['customer_complain_id'];
            }
        }
        return $ret;
    }
    /**
     * 根据 投诉表id 获取工单申诉id
     * @params complaint_id 投诉id $type 投诉或申诉
     */
    public function getIdByComplaintId($complaint_id,$type = TicketUser::TICKET_CATEGORY_COMPLAINT)
    {
        if(empty($complaint_id)){
            return '';
        }
        $params = array('customer_complain_id' => $complaint_id,'type' => $type);
        $sql = "select id from t_support_ticket where customer_complain_id=:customer_complain_id and `type`=:type";
        return Yii::app()->db_readonly->createCommand($sql)->queryScalar($params);
    }

    public function statDataByTpye($start_time,$end_time,$city_id = 0){
        $command = Yii::app()->db_readonly->createCommand();
        $where = ' (create_time between  :start_time and :end_time) or (last_reply_time between :start_time1 and :end_time1)';
        $params = array(':start_time'=>$start_time,':end_time'=> $end_time,':start_time1'=>$start_time,':end_time1'=>$end_time);
        if ($city_id != 0) {
            $where .= ' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }
        //
        $data = $command->select('count(*) as count,status')->from($this->tableName())
            ->where($where,$params)
            ->group('status')

            ->queryAll();
        $command->reset();
        return $data;
    }
    public function getSupportTicketList($param)
    {
        $criteria = new CDbCriteria;

        if (isset($param['id'])) {
            $criteria->compare('id', trim($param['id']));
        }
        if (isset($param['driver_id'])) {
            $criteria->compare('driver_id', trim($param['driver_id']),true);
        }
        if (isset($param['city_id'])) {
            $criteria->compare('city_id', $param['city_id']);
        }
        $admin = TicketUser::model()->checkUserAdmin(Yii::app()->user->name);
        if (!$admin) {
            $tUser = TicketUser::model()->find("`user`=:user", array('user' => Yii::app()->user->name));
            if ($tUser->city_manager == 1) {
                $criteria->compare('city_id', $tUser->city_id);
            } else {
                $name = Yii::app()->user->name;
                $criteria->condition = "operation_user=:operation_user or follow_user=:follow_user";
                $criteria->params = array('operation_user' => $name, 'follow_user' => $name);
            }
        }
        if (isset($param['follow_user'])) {
            $criteria->compare('follow_user', trim($param['follow_user']));
        }
        if (isset($param['device'])) {
            $criteria->compare('device', trim($param['device']),true);
        }
        if (isset($param['os'])) {
            $criteria->compare('os', trim($param['os']),true);
        }
        if (isset($param['status'])) {
            $criteria->compare('status',$param['status']);
        }
        if (isset($param['type'])) {
            $criteria->compare('type',$param['type']);
        }
        if (isset($param['class'])) {
            $criteria->compare('class',$param['class']);
        }
        if (isset($param['group'])) {
            $criteria->compare('group',$param['group']);
        }
        if (isset($param['last_reply_user'])) {
            $criteria->compare('last_reply_user', trim($param['last_reply_user']),true);
        }
        if (isset($param['content'])) {
            $criteria->compare('content', trim($param['content']),true);
        }
        if (isset($param['create_time_start']) && $param['create_time_start']) {
            $criteria->addCondition('create_time >= "' . $param['create_time_start'] . '"');
        }
        if (isset($param['create_time_end']) && $param['create_time_end']) {
            $criteria->addCondition('create_time <= "' . $param['create_time_end'] . '"');
        }
        if (isset($param['last_reply_time_start']) && $param['last_reply_time_start']) {
            $criteria->addCondition('last_reply_time >= "' .$param['last_reply_time_start'] . '"');
        }
        if (isset($param['last_reply_time_end']) && $param['last_reply_time_end']) {
            $criteria->addCondition('last_reply_time <= "' . $param['last_reply_time_end'] . '"');
        }
        if (isset($param['close_time_start']) && $param['close_time_start']) {
            $criteria->addCondition('close_time >= "' . $param['close_time_start'] . '"');
        }
        if (isset($param['close_time_end']) && $param['close_time_end']) {
            $criteria->addCondition('close_time <= "' . $param['close_time_end'] . '"');
        }
        if (isset($param['version'])) {
            $criteria->compare('version', trim($param['version']),true);
        }
        $criteria->order = " create_time desc,last_reply_time desc ";
        return self::model()->findAll($criteria);
    }

    /**
     * 通过订单id获取司机反馈数据
     * @param $order_id
     */
    public function getSupportTicketByOid($order_id)
    {
        $select = 'id, driver_id, type, class, status, content, create_time, create_user, last_reply_time, last_reply_user';
        $data = Yii::app()->db_readonly->createCommand()->select($select)->from(self::tableName())
            ->where('order_id=:oid',array(':oid'=>$order_id))->queryAll();
        return $data;
    }

    /**
     * 通过投诉id获取司机反馈数据
     * @param $complain_id
     */
    public function getSupportTicketByCid($complain_id)
    {
        $select = 'id, driver_id, type, class, status, content, create_time, create_user, last_reply_time, last_reply_user';
        $data = Yii::app()->db_readonly->createCommand()->select($select)->from(self::tableName())
            ->where('customer_complain_id=:cid',array(':cid'=>$complain_id))->queryAll();
        return $data;
    }
}
