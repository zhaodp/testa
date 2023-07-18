<?php

/**
 * This is the model class for table "{{customer_feedback}}".
 *
 * The followings are the available columns in table '{{customer_feedback}}':
 * @property integer $id
 * @property string $device
 * @property string $os
 * @property string $macaddress
 * @property string $content
 * @property string $email
 * @property string $version
 * @property string $source
 * @property integer $created
 */
class CustomerFeedback extends CActiveRecord
{
	public $btime = null;
    public $etime = null;

    const NO_REPLY_STATUS=0; //未回复
    const REPLY_STATUS=1; //已回复
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerFeedback the static model class
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
        return '{{customer_feedback}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('os', 'required'),
            array('created, status,reply_status, type', 'numerical', 'integerOnly'=>true),
            array('device', 'length', 'max'=>128),
            array('os, version', 'length', 'max'=>64),
            array('macaddress', 'length', 'max'=>50),
            array('content, email', 'length', 'max'=>255),
            array('source', 'length', 'max'=>15),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, reply_status,device, os, macaddress, content, email, version, source, created, status, type,btime, etime', 'safe', 'on'=>'search'),
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
            'device'=>'设备类型',
            'os'=>'操作系统',
            'macaddress'=>'MAC Address',
            'content'=>'反馈意见',
            'email'=>'Email地址',
            'version'=>'App 版本',
            'source'=>'来源渠道',
            'created'=>'创建时间',
            'driver_id'=> '司机工号',
            'type'=> '类型',
            'status'=>'状态',
            'reply_status'=>'是否回复',
            'follow_up'=>'负责人',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($extCriteria = NULL,$pageSize=NULL)
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('device',$this->device,true);
        $criteria->compare('os',$this->os,true);
        $criteria->compare('macaddress',$this->macaddress,true);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('version',$this->version,true);
        $criteria->compare('source',$this->source,true);
        $criteria->compare('created',$this->created);
        $criteria->compare('driver_id',$this->email, true);
        $criteria->compare('status',$this->status);
        $criteria->compare('reply_status',$this->reply_status);
        $criteria->compare('type',$this->type);

        if($extCriteria !== NULL){
            $criteria->mergeWith($extCriteria);
        }
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize' => $pageSize ? $pageSize : 10,
            ),
        ));
    }
    
    public function getType($type = NULL){
        $classArr = array(0=>'未分类',1=>'技术类',2=>'投诉',3=>'表扬',4=>'建议',5=>'咨询');
        if($type !== NULL){
            return isset($classArr[$type]) ? $classArr[$type] : '';
        }
        return $classArr;
    }

    /**
     * 将推送消息的Id更新至APP意见反馈的msgcount字段中
     * @param $pushMessageId 推送消息表ID
     * @param $feedId        App意见反馈表ID
     * @return 影响行数
     * @author 戴艺辉
     */
    public function updateMsgCount($pushMessageId, $feedId)
    {
        $feedList = CustomerFeedback::model()->findByPk($feedId);
        if($feedList['msgcount'] == ''){
            $newMsgCount = $pushMessageId;
        }else{
            $newMsgCount = $feedList['msgcount'].','.$pushMessageId;
        }
        $feedList->msgcount=$newMsgCount;
        return $feedList->save();
    }
    
    /**
     * 转投诉(app反馈（司机）)
     */
    public function toComplain($ids, &$error = NULL) {
        $saveNum = 0;
        $error = '';
        foreach ($ids as $id) {
            $model = self::model()->findByPk($id);
            if($model && $model->source!='driverclient'){
                if(!Common::checkPhone(trim($model->email))){
                    $error .= 'id'.$id.'没有手机号，无法转投诉';
                    continue;
                }
                if ($model->status > 0) {
                    $error .= 'id'.$id.'已经转投诉';
                    continue;
                }
                $city = Helper::PhoneLocation(trim($model->email));
                $customer_complain = new CustomerComplain();
                $customer_complain->source = 3; //app反馈（司机）
                $customer_complain->city_id = $city;
                $customer_complain->phone = $customer_complain->customer_phone = $customer_complain->name = trim($model->email);
                $customer_complain->detail = $model->content . '(' . $model->device . ' | ' . $model->version . ' | ' . $model->source . ')';
                $customer_complain->created = $customer_complain->operator = Yii::app()->user->id;
                $customer_complain->create_time = date('Y-m-d H:i:s', time());
                $customer_complain->status = 1;
                $saveOk = $customer_complain->save();
                if($saveOk){
                    $model->status = 1;
                    $model->save(FALSE);
                    $saveNum++;
                    //add by aiguoxin 2014-09-22 更新类型和意见id
                    CustomerSuggestion::model()->updateTypeAndOpinionId($id,$customer_complain->attributes['id']);
                }else{
                    $error .= CHtml::errorSummary($customer_complain);
                }
            }else{
                if (!$model || !trim($model->getDriverId()) || $model->status > 0) {
                    $error .= 'id'.$id.'缺少司机工号 或 已经转投诉';
                    continue;
                }
                $driverId = trim($model->getDriverId());
                $driver = Driver::model()->findByAttributes(array('user' => $driverId));
                if(!$driver){
                    $error .= 'id'.$id.'=>'.$driverId.'不存在';
                    continue;
                }
                $customer_complain = new CustomerComplain();
                $customer_complain->source = 8; //app反馈（司机）
                $customer_complain->city_id = DriverStatus::model()->getItem($driverId,'city_id');
                $customer_complain->detail = $model->content . '(' . $model->device . ' | ' . $model->version . ')';


                $customer_complain->name = $driverId;
                $customer_complain->customer_phone = $driver->phone;
                $customer_complain->created = $customer_complain->operator = Yii::app()->user->id;
                $customer_complain->status = 1;
                $customer_complain->phone = $driver->phone;
                $customer_complain->create_time = date('Y-m-d H:i:s', time());
                $saveOk = $customer_complain->save();
                if($saveOk){
                    $model->status = 1;
                    $model->save();
                    $saveNum++;
                    //add by aiguoxin 2014-09-22 更新类型和意见id
                    CustomerSuggestion::model()->updateTypeAndOpinionId($id,$customer_complain->attributes['id']);
                }else{
                    $error .= CHtml::errorSummary($customer_complain);
                }
            }
        }
        
        return $saveNum ? TRUE : FALSE;
    }
    
    /**
     * 修改分类
     */
    public function toClass($ids, $type){
        $saveOk = self::model()->updateByPk($ids, array('type'=>$type));
        return $saveOk;
    }
    
    /**
     * 修改状态
     */
    public function toStatus($ids, $status = 1){
        $saveOk = self::model()->updateByPk($ids, array('status'=>$status));
        return $saveOk;
    }

    /**
     * 获取当前评价次数
     * @param $phone
     * @return mixed
     * @auther mengtianxue
     */
    public function getFeedBackCountByPhone($phone){
        $start_time = strtotime("-1 day");
        return Yii::app()->db_readonly->createCommand()
            ->select("count(*)")
            ->from("{{customer_feedback}}")
            ->where('email = :email and created > :created', array(':email' => $phone, ':created' => $start_time))
            ->queryScalar();
    }
    
    /**
     * 获取当前反馈意见的司机工号（从Email中获取）
     * @return <string> 司机工号
     */
    public function getDriverId(){
        $driverId = '';
        $email = $this->email;
        $driverId .= strstr($email, '@', true);
        return $driverId;
    }

    /**
     * 获取负责人 array
     */
    public function getHeadArray($type)
    {
        $headArray = array(
            //'王龙欢' => 'wanglonghuan@edaijia-inc.cn',
            '孙婷_运营'=>	'sunting@edaijia-inc.cn',
            '杨祎琦_运营' => 'yangyiqi@edaijia-inc.cn',
            '赵新磊_运营' => 'zhaoxinlei@edaijia-inc.cn',
            '刘忱_品监' => 'liuchen@edaijia-inc.cn',
            '颜小琦_品监' => 'yanxiaoqi@edaijia-inc.cn',
            '孙英珍_财务' => 'sunyingzhen@edaijia-inc.cn',
            '柳柳_市场推广' => 'liuliu@edaijia-inc.cn',
            '李玉卿_技术' => 'liyuqing@edaijia-inc.cn',
            //'孙洪静_技术' => 'sunhongjing@edaijia-inc.cn',
            //'王栋_安卓' => 'wangdong@edaijia-inc.cn',
            '李邦木_呼叫中心' => 'libangmu@edaijia-inc.cn',
            '曹丽娜_呼叫中心' => 'caolina@edaijia-inc.cn',
            '张浩彬_北京分公司经理' => 'zhanghaobin@edaijia-inc.cn',
            '王鹏_上海分公司经理' => 'wangpeng@edaijia-inc.cn',
            '詹于善_杭州分公司经理' => 'zhanyushan@edaijia-inc.cn',
            '林徐茂_深圳、广州分公司经理' => 'linxumao@edaijia-inc.cn',
            '杨明智_重庆、成都分公司经理' => 'yangmingzhi@edaijia-inc.cn',
            '蒋雪松_武汉分公司经理' => 'jiangxuesong@edaijia-inc.cn',
            '崔崇_济南分公司经理' => 'cuichong@edaijia-inc.cn',
            '付德森_郑州分公司经理' => 'fudesen@edaijia-inc.cn',
            '刘凯_南京分公司经理' => 'liukai@edaijia-inc.cn',
            '朱天亮_西安分公司经理' => 'zhutianliang@edaijia-inc.cn',
            '赵昱龙_天津分公司经理' => 'zhaoyulong@edaijia-inc.cn',
            '郭军_长沙分公司经理' => 'guojun@edaijia-inc.cn',
            '周志伟_青岛分公司经理' => 'zhouzhiwei@edaijia-inc.cn',
            '任青_苏州分公司经理' => 'renqing@edaijia-inc.cn',
            '李友文_福州分公司经理' => 'liyouwen@edaijia-inc.cn',
            '麦惠东_厦门分公司经理' => 'maihuidong@edaijia-inc.cn',
            '李磊_合肥分公司经理' => 'lilei@edaijia-inc.cn',
            '马继华_大连分公司经理' => 'majihua@edaijia-inc.cn ',
            '宁凯_沈阳分公司经理' => 'ningkai@edaijia-inc.cn',
            '杨小冬_无锡分公司经理' => 'yangxiaodong@edaijia-inc.cn',
        );
        if($type == 'name'){
            $ret = array();
            foreach($headArray as $k=>$v){
                $ret[$k] = $k;
            }
            return $ret;
        }elseif($type == 'value'){
            return array_values($headArray);
        }
        return $headArray;
    }

    //获取负责人邮箱
    public function getHeadEmail($key)
    {
        if(empty($key)){
            return false;
        }
        $headArray = $this->getHeadArray('');
        if(isset($headArray[$key])){
            return $headArray[$key];
        }else{
            //不存在
            return false;
        }
    }

    /**
    *   更新回复状态
    *   
    */
    public function updateReplyStatus($id,$reply_status){
        $res = $this->updateByPk($id,array('reply_status'=>$reply_status));
        return $res;
    }
}