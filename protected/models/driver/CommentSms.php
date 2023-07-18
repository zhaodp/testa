<?php

/**
 * This is the model class for table "{{comment_sms}}".
 *
 * The followings are the available columns in table '{{comment_sms}}':
 * @property integer $id
 * @property string $sender
 * @property string $driver_id
 * @property string $imei
 * @property integer $level
 * @property string $content
 * @property string $raw_content
 * @property integer $confirm
 * @property string $order_status
 * @property string $created
 * @property integer $order_id
 * @property integer $sms_type
 * @property integer $status
 */
class CommentSms extends CActiveRecord
{


    public $pageSize = 10;
    public static $resonArray = array(
        '1'=> array(
            array('code'=>1,'detail'=>'未穿统一服装',),
            array('code'=>2,'detail'=>'个人卫生不好', ),
            array('code'=>3,'detail'=>'未展示计价器',),
            array('code'=>4,'detail'=>'态度不友好', ),
            array('code'=>5,'detail'=>'猛踩刹车油门',),
            array('code'=>6,'detail'=>'多收费', ),
        ),
        '2'=> array(
            array('code'=>1,'detail'=>'未穿统一服装',),
            array('code'=>2,'detail'=>'个人卫生不好', ),
            array('code'=>3,'detail'=>'未展示计价器',),
            array('code'=>4,'detail'=>'态度不友好', ),
            array('code'=>5,'detail'=>'猛踩刹车油门',),
            array('code'=>6,'detail'=>'多收费', ),
        ),
        '3'=> array(
            array('code'=>1,'detail'=>'未穿统一服装',),
            array('code'=>2,'detail'=>'个人卫生不好', ),
            array('code'=>3,'detail'=>'未展示计价器',),
            array('code'=>4,'detail'=>'态度不友好', ),
            array('code'=>5,'detail'=>'猛踩刹车油门',),
            array('code'=>6,'detail'=>'多收费', ),
        ),
        '4'=> array(
            array('code'=>1,'detail'=>'未穿统一服装',),
            array('code'=>2,'detail'=>'个人卫生不好', ),
            array('code'=>3,'detail'=>'未展示计价器',),
            array('code'=>4,'detail'=>'态度不友好', ),
            array('code'=>5,'detail'=>'猛踩刹车油门',),
            array('code'=>6,'detail'=>'多收费', ),
        ),
        '5'=> array(
            array('code'=>1,'detail'=>'开车平稳安全',),
            array('code'=>2,'detail'=>'路线熟悉', ),
            array('code'=>3,'detail'=>'态度亲切友好',),
            array('code'=>4,'detail'=>'服务专业周到', ),
            array('code'=>5,'detail'=>'就位迅速及时',),
            array('code'=>6,'detail'=>'着装整洁得体',),
        ),
);

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CommentSms the static model class
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
        return '{{comment_sms}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('sender, driver_id, level, content, raw_content, confirm, created', 'required'),
            array('level, confirm, order_id, sms_type, status, channel', 'numerical', 'integerOnly'=>true),
            array('sender, imei', 'length', 'max'=>15),
            array('driver_id', 'length', 'max'=>10),
            array('content, raw_content', 'length', 'max'=>255),
            array('order_status', 'length', 'max'=>1),
            array('reason', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, sender, driver_id, imei, level, content, raw_content, confirm, order_status, created, order_id, sms_type, status, channel,reason_codes, reason', 'safe', 'on'=>'search'),
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
            'sender' => '客户',
            'driver_id' => 'Driver',
            'imei' => 'Imei',
            'level' => '    评价等级',
            'content' => '  评价内容',
            'raw_content' => 'Raw Content',
            'confirm' => 'Confirm',
            'order_status' => '订单类型',
            'created' => '发布时间',
            'order_id' => 'Order',
            'sms_type' => 'Type',
            'status' => '处理情况',
            'channel' => '来源渠道',
            'reason' => '销单原因'
        );
    }



    public static function getListByDriverID($pageNo = 0, $pageSize = 10, $driverID = 'BJ9000') {

        $sql = 'select count(*) from t_comment_sms where driver_id=:user and (level>0) and order_status=0 and content<>"" AND content IS NOT NULL';
        $total = Yii::app()->db->createCommand($sql)->queryScalar(array(':user'=>$driverID));

        $offset = $pageNo*$pageSize;


        $sql = 'select sender,level,content,created,driver_id,status from t_comment_sms where driver_id=:user and (level>0) and order_status=0 and content<>"" AND content IS NOT NULL order by created desc limit '.$offset.','.$pageSize;

        $command = new CDbCommand(Yii::app()->db, $sql);
        $comments = $command->queryAll(true,array(':user'=>$driverID));

        $ret = array ();
        foreach($comments as $comment) {
            $tmpArr =array();
            if (preg_match('%\d{11}%s', $comment['sender'])) {
                $tmpArr['name'] = substr_replace($comment['sender'], '****', 3, 4);
            }
            if($comment['created']<'2013-04-12'){
                $tmpArr['new_level'] = ($comment['level']==3) ? 5 : $comment['level'];
            }else{
                $tmpArr['new_level'] = $comment['level'];
            }

            $tmpArr['level'] = self::parseLevel($comment['level']);
            $tmpArr['comments'] = self::parseComments($comment['content'],$comment['level']);
            $tmpArr['insert_time'] = $comment['created'];
            $tmpArr['employee_id'] = $comment['driver_id'];
            $tmpArr['uuid'] = $comment['sender'];
            $tmpArr['status'] = $comment['status'];
            $ret[] = $tmpArr;
        }
        $ret['total'] = $total;
        return $ret;
    }


    /**
     * 重写该方法，保持返回值一样
     *
     * @author sunhongjing 2013-06-01
     * @param unknown_type $pageNo
     * @param unknown_type $pageSize
     */
    public static function getList($pageNo = 0, $pageSize = 10,$driver_id = '' ) {
        //只显示2天之前的评价，fix bug 客户司机冲突问题 aiguoxin
        $before_yesterday = date("Y-m-d 00:00:00",strtotime("-2 day"));

        $ret = array();
        $pageNo = ( empty($pageNo) || $pageNo< 0 ) ? 0 : intval($pageNo);
        $pageSize = ( empty($pageSize) || $pageSize< 0 ) ? 10 : intval($pageSize);

        //增加缓存
        $cache_key = 'Cache_driver_comments_sms_list_new_'.$driver_id."_".$pageNo."_".$pageSize;
        $ret = Yii::app()->cache->get($cache_key);

        if (empty($ret)) {
            CommentSms::$db = Yii::app()->db_readonly;
            $criteria = new CDbCriteria();
            $criteria->select = 'sender,level,content,created,driver_id,status';
            if(!empty($driver_id) && strlen($driver_id)<10 ){
                $criteria->condition = " driver_id='{$driver_id}' and ";
            }else{
                $criteria->condition = " id>0 and ";
            }
            $criteria->condition = $criteria->condition . " level>0 and created<'{$before_yesterday}' and order_status in(1,4) ";
            $criteria->order = 'id desc';
            $total = CommentSms::model()->count($criteria);

            $criteria->offset = $pageNo*$pageSize;
            $criteria->limit = $pageSize;

            $comments = CommentSms::model()->findAll($criteria);
            CommentSms::$db = Yii::app()->db;

            $ret = array ();
            foreach($comments as $comment) {
                $tmpArr =array();
                if (preg_match('%\d{11}%s', $comment['sender'])) {
                    $tmpArr['name'] = substr_replace($comment['sender'], '******', 3, 6);
                }else{
                    $tmpArr['name'] = $comment['sender'];
                }

                $tmpArr['new_level']    = $comment['level'];
                $tmpArr['level']        = self::parseLevel($comment['level']);
                $tmpArr['comments']     = self::parseComments($comment['content'],$comment['level']);
                $tmpArr['insert_time']  = $comment['created'];
                $tmpArr['employee_id']  = '';//$comment['driver_id'];
                $tmpArr['uuid']         = '';//$tmpArr['name'];
                $tmpArr['status']       = '';//$comment['status'];

                $ret[]                  = $tmpArr;
            }
            $ret['total'] = $total;

            Yii::app()->cache->set($cache_key, $ret, 7200);//缓存2小时

        }
        return $ret;
    }
    /**
     *说明：把用户评价和系统评价combined以后返回给接口调用者
     *combined规则：
     *时间周期：2天前至一周内
     *在指定时间周期内的用户评价按时间逆序后combined相同时间周期内的系统评价逆序
     *最后，将结果集与指定时间周期以后的用户评价和系统评价统一按时间逆序combined在一起
     * @author zhongfuhai 2015-04-23
     * @param int $pageNo
     * @param int $pageSize
     * @param int $startDay 
     * @param int $endDay
     */
    public static function getCombinedList($pageNo = 0, $pageSize = 10,$driver_id = '',$startDay=-9,$endDay=-2 ) {
    	
    	$startDay = empty($startDay) ? -9 : intval($startDay);
    	$endDay = empty($endDay) ? -2 : intval($endDay);
        //默认时间周期是2天前至一周内，fix bug 司机评论显示排序问题 zhongfuhai
        $before_yesterday = date("Y-m-d 00:00:00",strtotime($endDay." day"));
        $before_lastweek = date("Y-m-d 00:00:00",strtotime($startDay." day"));

        $ret = array();
        $pageNo = ( empty($pageNo) || $pageNo< 0 ) ? 0 : intval($pageNo);
        $pageSize = ( empty($pageSize) || $pageSize< 0 ) ? 10 : intval($pageSize);

        //增加缓存
        $cache_key = 'Cache_driver_comments_sms_list_combined_'.$driver_id."_".$pageNo."_".$pageSize."_sd_".$startDay."_ed_".$endDay;
        $ret = Yii::app()->cache->get($cache_key);

        if (empty($ret)) {
        	
			$userCommentSql   = "( select sender,level,content,created,driver_id,status from t_comment_sms where driver_id=:user and created<:endDate and created>:startDate and content <> '非常满意~' and content <> '非常满意' and id>0 and level>0 and order_status in(1,4) order by created desc limit 100000000 ) ";
			$systemCommentSql = "( select sender,level,content,created,driver_id,status from t_comment_sms where driver_id=:user and created<:endDate and created>:startDate and (content = '非常满意~' or content  = '非常满意') and id>0 and level>0 and order_status in(1,4) order by created desc limit 100000000 ) ";
			$otherCommentSql  = "( select sender,level,content,created,driver_id,status from t_comment_sms where driver_id=:user and created<=:startDate and content <> '' and id>0 and level>0 and order_status in(1,4) order by created desc limit 100000000 ) ";
            $querySql = $userCommentSql." union ".$systemCommentSql." union ".$otherCommentSql;
            
            $total = Yii::app()->db_readonly->createCommand($querySql)->queryScalar(array(':user'=>$driver_id,':startDate'=>$before_lastweek,':endDate'=>$before_yesterday));
            
            $offset = $pageNo*$pageSize;
            $querySql = $querySql." limit ".$offset.",".$pageSize;
            $command = new CDbCommand(Yii::app()->db_readonly, $querySql);
            $comments = $command->queryAll(true,array(':user'=>$driver_id,':startDate'=>$before_lastweek,':endDate'=>$before_yesterday));

            $ret = array ();
            foreach($comments as $comment) {
                $tmpArr =array();
                if (preg_match('%\d{11}%s', $comment['sender'])) {
                    $tmpArr['name'] = substr_replace($comment['sender'], '******', 3, 6);
                }else{
                    $tmpArr['name'] = $comment['sender'];
                }

                $tmpArr['new_level']    = $comment['level'];
                $tmpArr['level']        = self::parseLevel($comment['level']);
                $tmpArr['comments']     = self::parseComments($comment['content'],$comment['level']);
                $tmpArr['insert_time']  = $comment['created'];
                $tmpArr['employee_id']  = '';//$comment['driver_id'];
                $tmpArr['uuid']         = '';//$tmpArr['name'];
                $tmpArr['status']       = '';//$comment['status'];

                $ret[]                  = $tmpArr;
            }
            $ret['total'] = $total;

            Yii::app()->cache->set($cache_key, $ret, 7200);//缓存2小时

        }
        return $ret;
    }


    public static function parseLevel($level=3){
        $ret = 3;
        switch ($level) {
            case 1:$ret = "1";break;
            case 2:$ret = "2";break;
            case 3:
            case 4:
            case 5:
                $ret = "3";break;
            default:break;
        }

        return $ret;

    }

    public static function parseComments($comment='',$level=3){
        $ret = '非常满意';
        switch ($level) {
            case 1:$ret = '不满意';break;
            case 2:$ret = '一般';break;
            case 3:$ret = '满意';break;
            case 4:$ret = '很满意';break;
            case 5:$ret = '非常满意';break;
            default:break;
        }

        $comment = trim($comment);
        return empty($comment) ? $ret : $comment;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($pageSize = NULL)
    {
            if ($pageSize !== NULL) {
                $this->pageSize = $pageSize;
            }
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $startTime = isset($_REQUEST['startTime']) ? $_REQUEST['startTime'] : 0;
        $endTime = isset($_REQUEST['endTime']) ? $_REQUEST['endTime'] : 0;
        $mobile = isset($_REQUEST['mobile']) ? $_REQUEST['mobile'] : 0;
        $star = isset($_REQUEST['star']) ? $_REQUEST['star'] : '';
        $starType = isset($_REQUEST['starType']) ? $_REQUEST['starType'] : 0;
        $orderStatus = isset($_REQUEST['orderStatus']) ? $_REQUEST['orderStatus'] : '';
        $city = isset($_REQUEST['city']) ? $_REQUEST['city'] : '';
        $driver_id = isset($_REQUEST['driver_id']) ? $_REQUEST['driver_id'] : '';
        $sender = isset($_REQUEST['sender']) ? $_REQUEST['sender'] : '';
        $mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
        $sms_type = isset($_GET['sms_type']) ? $_GET['sms_type'] : '';
        //增加按司机姓名搜索功能
        $driver_name = isset($_GET['driver_name']) ? $_GET['driver_name'] : '';
        if ($driver_name) {
            $driver = Driver::getDriverByName($_GET['driver_name']);
            $driver_id = $driver[0]['user'];
        }

        if($city==0){
            $city='';
        }

        $criteria=new CDbCriteria;

        $criteria->select = "t.id, t.driver_id, t.sender, t.level, t.status, t.content, t.raw_content, t.order_id,
        t.order_status, t.created, t.sms_type,t.channel, t_driver.name AS uuid";
        $criteria->join = 'JOIN t_driver ON t_driver.user = t.driver_id';
        //$criteria->addCondition('t.level<>0');
        $drivers = '';
        $driverids = array();
        if($driver_name!=''){
            $drivers = Driver::getDriverByName($driver_name);
        }
        if(!empty($drivers)){
            foreach($drivers as $key=>$item){
                    $driverids[]=$item['user'];
            }
        }
        if (isset($driver_id)&&trim($driver_id)!='') {
            $criteria->compare('t_driver.user', $driver_id);
        }else if(!empty($driverids)){
            $criteria->addInCondition('t_driver.user', $driverids);
        }

        if($city!=''){
            $criteria->compare('t_driver.city_id', $city);
        }

        if($mobile!=''){
            $criteria->addCondition('t_driver.phone="'.$mobile.'"');
        }
        if($sms_type!=''){
                $criteria->addCondition('t.sms_type='.$sms_type);
        }
        if($sender!=''){
            $criteria->addCondition('t.sender="'.$sender.'"');
        }
        if($orderStatus!=""){
            //报单
            if($orderStatus==1){
                $criteria->addCondition('t.order_status in (2,3)');
            }
            //销单
            if($orderStatus==0){
                $criteria->addCondition('t.order_status in (1,4)');
            }
        }


        if($startTime!=0){
            $criteria->addCondition('t.created>="'.$startTime.' 00:00:00"');
        }
        if($endTime!=0){
            $criteria->addCondition('t.created<"'.$endTime.' 23:59:59"');
        }


        if($starType!=0&&$star!=''){
            if($starType=='1'){
                $criteria->addCondition('t.level>='.$star);
            }else if($starType=='2'){
                $criteria->addCondition('t.level<='.$star);
            }else if($starType=='3'){
                $criteria->addCondition('t.level='.$star);
            }
        }
        if($this->status!==null){
            $criteria->compare('status', $this->status);
        }
        $criteria->order = 't.id desc';
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
                    'pagination'=>array(
                        'pageSize'=>$this->pageSize,
                    ),
        ));
    }

    /***
     * 获得司机某天的好评数
     * @param $driver_id
     * @param $date 如 2013-08-21
     * @return int
     */
    public function getHighOpinionCount($driver_id, $date) {
        $date_start = date('Y-m-d', strtotime($date));
        $date_end = date('Y-m-d', strtotime($date_start)+86400);
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('count(*)');
        $command->from('t_comment_sms');
        $command->where('level>=:level and driver_id=:driver_id and created>=:date_start and created<:date_end', array(':level'=>3, ':driver_id'=>$driver_id, ':date_start'=>$date_start, ':date_end'=>$date_end));
        return intval($command->queryScalar());
    }

    /**
     * 获得某天司机的差评数
     * @param $driver_id
     * @param $date
     * @return int
     */
    public function getBadReview($driver_id, $date) {

        $date_start = date('Y-m-d H:i:s', strtotime($date));
        $date_end = date('Y-m-d H:i:s', strtotime($date_start)+86400);
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('count(*)');
        $command->from('t_comment_sms');
        //$command->where("level<3 and driver_id='{$driver_id}' and created>='{$date_start}' and created<'{$date_end}'");
        $command->where('level<:level and driver_id=:driver_id and created>=:date_start and created<:date_end', array(':level'=>3, ':driver_id'=>$driver_id, ':date_start'=>$date_start, ':date_end'=>$date_end));
        $count = $command->queryScalar();
        return $count;
    }

    /**
     * 查询订单是否有评价
     * @param $order_id
     * @return mixed
     * author mengtianxue
     */
    public function getCommandSmsByOrderId($order_id){
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_comment_sms');
        $command->where('order_id = :order_id and sms_type = :sms_type', array(':order_id' => $order_id, ':sms_type' => 0));
        $info = $command->queryRow();
        return $info;
    }
    
    /**
     * 查询订单by order_id and type
     * @param $order_id
     * @return mixed
     * author aiguoxin
     */
    public function getCommandSmsByOrderIdAndType($order_id,$sms_type){
        $order_id = (int)$order_id;
        $sms_type = (int)$sms_type;
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('*');
        $command->from('t_comment_sms');
        $command->where('order_id = :order_id and sms_type = :sms_type', array(':order_id' => $order_id, ':sms_type' => $sms_type));
        $info = $command->queryRow();
        return $info;
    }

    /**
     * 添加评价
     * @param $params
     * @return int   0：添加成功  1：添加失败  2：已经添加
     * author mengtianxue
     */
    public function addOrderCommand($params){
        $order_id = $params['order_id'];
        $command_sms = $this->getCommandSmsByOrderId($order_id);
        if(!$command_sms){
            $params['channel'] = 1;
            $params['created'] = date('Y-m-d H:i:s');
            $params['raw_content'] = $params['content'];
            $params['confirm'] = 0;

            $commentSms = new CommentSms();
            $commentSms->attributes = $params;
            $commentSms->reason_codes = $params['reason_codes'];
            if($commentSms->save()){
                $result = array();
                $result['is_comment'] = 'Y';
                $result['level'] = isset($params['level']) ? $params['level'] : '5';

                //保存成功删除之前评价的缓存
//                $cache_key = 'ORDER_COMMENT_' . $order_id;
//                Yii::app()->cache->delete($cache_key);
//                Yii::app()->cache->set($cache_key, $result, 86400);
                ROrderComment::model()->setComment($order_id, $result);
                return 0;
            }else{
                return 1;
            }
        }else{
            return 2;
        }

    }

     /**
    *   add by aiguoxin
    *   set status revert
    */  
    public function setLevelZero($orderId){
        $sql = "UPDATE `t_comment_sms` SET `level` =:level WHERE order_id = :order_id";
        return Yii::app()->db->createCommand($sql)->execute(array(
            ':order_id' => $orderId,
            ':level' => 0,
        ));
    }

}
