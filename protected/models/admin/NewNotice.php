<?php

/**
 * This is the model class for table "{{new_notice}}".
 *
 * The followings are the available columns in table '{{new_notice}}':
 * @property string $id
 * @property string $title
 * @property integer $type
 * @property string $city_ids
 * @property integer $category
 * @property string $deadline
 * @property integer $booking_push_flag
 * @property string $booking_push_datetime
 * @property string $content
 * @property integer $is_check
 * @property string $source
 * @property string $audio_url
 * @property integer $is_delete
 * @property string $opt_user
 * @property string $create_time
 * @property string $update_time
 * @property string $is_pass
 *
 */
class NewNotice extends CActiveRecord
{

    const TEXT=0;//文本公告
    const VOICE=1;//语音公告

    public static $types=array(
        0=>'文本公告',
        1=>'语音公告',
    );

    //公告优先级
    public static $prioritys=array(
        1=>'普通',
        0=>'必读',
    );

    const CHECK=0;//待审核
    const PASS=1;//通过

    public static $is_checks=array(
        0=>'待审',
        1=>'通过',
    );

    const PASS_FAI=0;//待发布
    const PASS_SUC=1;//发布

    public static $passes=array(
        0=>'待发布',
        1=>'已发布',
    );

    public static $categorys=array(
        0=>'规则',
        1=>'警示',
        2=>'调度',
        3=>'奖惩',
        4=>'通知',
        5=>'系统',
        6=>'提醒',
        7=>'培训',
    );


    public static $WebCategorys=array(
        0=>'【规则】',
        1=>'【警示】',
        2=>'【调度】',
        3=>'【奖惩】',
        4=>'【通知】',
        5=>'【系统】',
        6=>'【提醒】',
        7=>'【培训】',
    );

    //预约发布的开启和禁用
    const OPEN=1;//开启
    const STOP=0;//禁用

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{new_notice}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type, category, deadline, booking_push_flag, booking_push_datetime, content, is_check, create_time, update_user', 'required'),
            array('type, category, priority, booking_push_flag, is_check, is_delete, is_pass', 'numerical', 'integerOnly'=>true),
            array('title, city_ids', 'length', 'max'=>255),
            array('source, audio_url', 'length', 'max'=>100),
            array('opt_user, update_user', 'length', 'max'=>50),
            array('update_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, priority,title, type, city_ids, category, deadline, booking_push_flag, booking_push_datetime, content, is_check, source, audio_url, is_delete, opt_user, create_time, update_time, update_user, is_pass , audio_second， post_id', 'safe', 'on'=>'search'),
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
            'title' => '标题',
            'type' => '类型',
            'city_ids' => '城市',
            'category' => '标题类型',
            'deadline' => '截止时间',
            'booking_push_flag' => '是否预约发布',
            'booking_push_datetime' => '预约发布时间',
            'content' => '内容',
            'is_check' => '是否审核',
            'source' => 'Source',
            'audio_url' => '语音',
            'audio_second' => '语音秒数',
            'is_delete' => 'Is Delete',
            'opt_user' => 'Opt User',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'update_user' => '更新操作者',
            'is_pass' => '是否发布成功',
            'post_id' => '长文章id',
            'priority' =>'通知级别',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('title',$this->title,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('city_ids',$this->city_ids,true);
        $criteria->compare('category',$this->category);
        $criteria->compare('deadline',$this->deadline,true);
        $criteria->compare('booking_push_flag',$this->booking_push_flag);
        $criteria->compare('booking_push_datetime',$this->booking_push_datetime,true);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('is_check',$this->is_check);
        $criteria->compare('source',$this->source,true);
        $criteria->compare('audio_url',$this->audio_url,true);
        $criteria->compare('audio_second',$this->audio_second);
        $criteria->compare('is_delete',$this->is_delete);
        $criteria->compare('opt_user',$this->opt_user,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('update_user',$this->update_user,true);
        $criteria->compare('is_pass',$this->is_pass);
        $criteria->compare('post_id',$this->post_id);
        $criteria->compare('priority',$this->priority);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return NewNotice the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    //插入数据公共方法
    public function insertData($data=array()){
        if(empty($data)){
            return false;
        }
        return Yii::app()->db->createCommand()->insert($this->tableName(),$data);
    }


    /**
     * 获取所有己读未读公告列表 给API 使用 直接获取所有的列表就行了
     */
    public function getList($params = array()){
        $list = array();
        if(empty($params)){
            return $list;
        }
        $list=$this->_getNoticeList($params);
        return $list;
    }


    /**
     * 获取司机己读公告
     */
    public  function getDriverReadList($params){
        $list = array();
        if(empty($params)){
            return $list;
        }
        if(!isset($params['driver_id'])){
            return $list;
        }
        if(!isset($params['city_id'])){
            return $list;
        }
        $params['pageNo']= $params['pageNo'] < 0 ? 1 : intval($params['pageNo']);
        $params['pageSize'] = $params['pageSize'] < 0 ? 10 : intval($params['pageSize']);
        $params['flag'] = 1 ;
        $list = $this->_getNoitceStatusList($params);
        return $list;
    }

    /**
     * 获取司机未读公告
     */
    public  function getDriverUnReadList($params,$with_all=false){
        $list = array();
        if(empty($params)){
            return $list;
        }
        if(!isset($params['driver_id'])){
            return $list;
        }
        if(!isset($params['city_id'])){
            return $list;
        }
        $params['pageNo']= $params['pageNo'] < 0 ? 1 : intval($params['pageNo']);
        $params['pageSize'] = $params['pageSize'] < 0 ? 10 : intval($params['pageSize']);
        $params['flag'] = 0 ;
        $list = $this->_getNoitceStatusList($params,$with_all);
        return $list;
    }


    /**
     * 获取不同状态获取公告列表信息
     * @param $params
     * @return array
     */
    private function _getNoitceStatusList($params,$all=false){
        $list = array();
        if(empty($params)){
            return $list;
        }
        if(!isset($params['driver_id'])){
            return $list;
        }
        if(!isset($params['city_id'])){
            return $list;
        }
        $pageNo = $params['pageNo'] < 0 ? 1 : intval($params['pageNo']);
        $pageSize = $params['pageSize'] < 0 ? 10 : intval($params['pageSize']);

        $conditions = array(
            'driver_id'=>$params['driver_id'],
            'flag'=>$params['flag'], // 0 未读 1 己读
            'city_id'=>$params['city_id'],
        );
        $notice_ids = NoticeStatus::model()->getDriverNoticeIds($conditions);
        if(empty($notice_ids)){
            return $list;
        }
        
        //如果$all =true,就返回所有未读信息，否则只返回语音信息 add by sunhongjing 2013-12-13
        if($all){
        	$where_str = "";
        }else{
        	$where_str = " and type = 1 ";
        }

        $ids = empty($notice_ids) ?  0 : implode(",",$notice_ids);

        $ret = Yii::app()->db_readonly->createCommand()
            ->select("id,priority,title,type,create_time,audio_url,audio_second,content,deadline,booking_push_datetime,category")
            ->from($this->tableName())
            ->where('is_delete = 0 and is_pass = 1 and is_check = 1 '.$where_str.' and deadline > :deadline and booking_push_datetime <= :booking_push_datetime and id in ('.$ids.')', array (
                ':deadline'=>date('Y-m-d H:i:s'),':booking_push_datetime'=>date('Y-m-d H:i:s')))
            ->order('booking_push_datetime DESC ')
            ->offset(intval($pageSize * ($pageNo-1)))
            ->limit($pageSize)
            ->queryAll();
        if(empty($ret)){
            return $list;
        }

        foreach($ret as $val){
            if($val['audio_second']>=60){
                $val['audio_second']=floor($val['audio_second']/60).'′'.round($val['audio_second']%60).'″';
            }elseif($val['audio_second']<60){
                $val['audio_second']=round($val['audio_second']%60).'″';
            }
            $list[] = array(
                'id'=>$val['id'],
                'title'=>$val['title'],
                'type'=>$val['type'],
                'booking_push_datetime'=>date("m-d H:i",strtotime($val['booking_push_datetime'])),
                'audio_url'=>$val['audio_url'],
                'audio_second'=>$val['audio_second'],
                'content'=> $val['type'] == 0  ? "" : $val['content'],
                'category'=>NewNotice::$categorys[$val['category']],
                'read' => $params['flag'], // 0 未读 1 己读
                'priority'=> $val['priority'],
            );
        }
        return $list;
    }

    /**
     * 获取所有公司列表信息(包含 己读 未读)
     * @param array $params
     * @return array
     */
    private function _getNoticeList($params = array()){
        $list = array();
        if(empty($params)){
            return $list;
        }
        if(!isset($params['driver_id'])){
            return $list;
        }
        if(!isset($params['city_id'])){
            return $list;
        }
        $pageNo = $params['pageNo'] < 0 ? 1 : intval($params['pageNo']);
        $pageSize = $params['pageSize'] < 0 ? 10 : intval($params['pageSize']);


        $ret = Yii::app()->db_readonly->createCommand()
            ->select("id,priority,title,type,create_time,audio_url,audio_second,content,deadline,booking_push_datetime,category")
            ->from($this->tableName())
            ->where(' FIND_IN_SET(:city_id,city_ids) and    is_delete = 0 and is_pass = 1 and is_check = 1 and deadline > :deadline and booking_push_datetime <= :booking_push_datetime', array (
                ':city_id'=>$params['city_id'] , ':deadline'=>date('Y-m-d H:i:s'),':booking_push_datetime'=>date('Y-m-d H:i:s')))
            ->order('booking_push_datetime DESC ')
            ->offset(intval($pageSize * ($pageNo-1)))
            ->limit($pageSize)
            ->queryAll();
        if(empty($ret)){
            return $list;
        }

        //获取已读的所有的notice_id
        $noticeReadIds = NoticeStatus::model()->getDriverNoticeIds(array(
            'driver_id'=>$params['driver_id'],
            'flag'=>1, // 0 未读 1 己读
            'city_id'=>$params['city_id'],
        ));

        foreach($ret as $val){
            if(in_array($val['id'],$noticeReadIds)){
                $params['flag']=1;
            }else{
                $params['flag']=0;
            }
            if($val['audio_second']>=60){
                $val['audio_second']=floor($val['audio_second']/60).'′'.round($val['audio_second']%60).'″';
            }elseif($val['audio_second']<60){
                $val['audio_second']=round($val['audio_second']%60).'″';
            }
            $list[] = array(
                'id'=>$val['id'],
                'title'=>$val['title'],
                'type'=>$val['type'],
                'booking_push_datetime'=>date("m-d H:i",strtotime($val['booking_push_datetime'])),
                'audio_url'=>$val['audio_url'],
                'audio_second'=>$val['audio_second'],
                'content'=> $val['type'] == 0  ? "" : $val['content'],
                'category'=>NewNotice::$categorys[$val['category']],
                'read' => $params['flag'], // 0 未读 1 己读
                'priority'=> $val['priority']
            );
        }
        return $list;
    }


    /**
     * 获取当前公告信息
     * @param string $id
     * @return array
     */
    public function getInfo($id = ""){
        $ret = array();
        if(empty($id)){
            return $ret;
        }
        $info = Yii::app()->db_readonly->createCommand()
                ->from($this->tableName())
                ->where('id=:id and is_delete = 0 and is_pass=1 and is_check=1')
                ->queryRow(true,array('id'=>$id));
        $ret = empty($info) ? $ret : $info;
        return $ret;
    }


    /**
     * 当语音类型时，直接给司机端推详情
     * @param array $params
     * @return bool
     */
    public function pushAudioMsg($params = array()){
        $flag = false;
        if(empty($params)){
            return $flag;
        }
        if(!isset($params['city_id'])){
            return $flag;
        }

        if(!isset($params['notice_id'])){
            return $flag;
        }

        if(!empty($city_id)){
            $pus_data = array(
                'notice_id' => $params['notice_id'],
                'content' => $params['content'],
                'url' => $params['url'],
                'city_id' => $city_id,

                'category'=>trim($params['category']),
                'title'=>trim($params['title']),
                'created'=>$params['created'], //预约发布时间 格式 月-日 时:分
                'audio_time'=>$params['audio_time'], //语言时长
                //暂时测试工号的id
                //'drivers'=>Common::getTestDriverIds(1),
            );
            PushMessage::model()->PushNoticeAudio($pus_data);
        }

        $flag = true;
        return $flag;

    }

    /**
     * 获取司机未读公告个数
     * @param $params
     * @return int
     */
    public function getDriverUnreadcount($params){

        $list = array();
        $all=true;
        if(empty($params)){
            return count($list);
        }
        if(!isset($params['driver_id'])){
            return count($list);
        }
        if(!isset($params['city_id'])){
            return count($list);
        }

        $conditions = array(
            'driver_id'=>$params['driver_id'],
            'flag'=>0, // 0 未读 1 己读
            'city_id'=>$params['city_id'],
        );
        $notice_ids = NoticeStatus::model()->getDriverNoticeIds($conditions);
        if(empty($notice_ids)){
            return count($list);
        }
        
        //如果$all =true,就返回所有未读信息，否则只返回语音信息 add by sunhongjing 2013-12-13
        if($all){
            $where_str = "";
        }else{
            $where_str = " and type = 1 ";
        }

        $ids = empty($notice_ids) ?  0 : implode(",",$notice_ids);

        $ret = Yii::app()->db_readonly->createCommand()
            ->select("id,priority,title,type,create_time,audio_url,audio_second,content,deadline,booking_push_datetime,category")
            ->from($this->tableName())
            ->where('is_delete = 0 and is_pass = 1 and is_check = 1 '.$where_str.' and deadline > :deadline and booking_push_datetime <= :booking_push_datetime and id in ('.$ids.')', array (
                ':deadline'=>date('Y-m-d H:i:s'),':booking_push_datetime'=>date('Y-m-d H:i:s')))
            ->order('booking_push_datetime DESC ')
            ->queryAll();
        if(empty($ret)){
            return count($list);
        }
        return count($ret);
    }

    /**
     * 直接给司机端推 add by aiguoxin 改成单个城市推送，放到redis运行，防止报警
     * @param array $params
     * @return bool
     */
    public function pushCommonMsg($city_id){
        if(!empty($city_id)){
            $drivers = Driver::model()->getDrivers($city_id , 0);
            if($drivers){
                foreach ($drivers as $driver) {
                    EdjLog::info('----------'.$city_id.'-------'.$driver['user']);
                    DriverPush::model()->pushUnreadNotice($driver['user'],$city_id);
                }
            }
        }
    }


    public function summaryData(){
        $command = Yii::app()->db_readonly->createCommand();
        //$sql = 'SELECT * FROM `t_new_notice` `t` WHERE is_delete= :is_del ORDER BY is_pass asc,create_time desc LIMIT 20';
        $where = 'is_pass = :ispa and is_delete =:is_del';
        $params = array(':ispa'=>1,':is_del'=>0);

        //待处理
        $data = $command->select('title,type,create_time')->from($this->tableName())
            ->where($where,$params)
            ->order('is_pass asc,create_time desc')
            -> limit(5)
            ->queryAll();
        $command->reset();
        return $data;
    }

}
