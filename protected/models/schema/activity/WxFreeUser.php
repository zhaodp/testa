<?php
Yii::import('application.models.pay.subsidy.*');

/**
 * This is the model class for table "{{wx_free_user}}".
 * 
 * The followings are the available columns in table '{{wx_free_user}}':
 * @property integer $id
 * @property string $token
 * @property string $phone
 * @property integer $order_id
 * @property integer $type
 * @property integer $share_times
 * @property string $update_time
 * @property string $create_time
 */
class WxFreeUser extends CActiveRecord
{

    CONST OVER_TIME= '2015-03-11 23:59:59';
    CONST HAS_DRAW = 1; //已经支取
    CONST NOT_DRAW = 0; //尚未支取
    CONST DRAW_MONEY = 38; //支取标准
    CONST INIT_MONEY = 10; //初始金额
    CONST BOUNS_NO = '1234563838'; //优惠券4727416194356,5264

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{wx_free_user}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('create_time,wx_user', 'required'),
            array('is_draw', 'integerOnly'=>true),
            array('wx_user', 'length', 'max'=>64),
            array('phone', 'length', 'max'=>15),
            array('act_name', 'length', 'max'=>20),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('wx_user, phone, money, is_draw, create_time,act_name', 'safe', 'on'=>'search'),
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
            'wx_user' => 'wx_user',
            'phone' => 'Phone',
            'is_draw' => 'is_draw',
            'money' => 'money',
            'head_url'=>'head_url',
            'nickname'=>'nickname',
            'help_num'=>'help_num',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
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


        $criteria->compare('wx_user',$this->wx_user,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('money',$this->money);
        $criteria->compare('is_draw',$this->is_draw);
        $criteria->compare('act_name',$this->act_name);


        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db_activity;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RedPacket the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /*
    *   初始化活动用户信息
    *
    */
    public function initData($act_name,$wx_user,$headurl,$nickname){
        $create_time=date("Y-m-d H:m:s");
        $help_num=$this->getHelpRandom();
        $wxFreeUser = new WxFreeUser();
        $wxFreeUser['wx_user'] = $wx_user;
        $wxFreeUser['head_url'] = $headurl;
        $wxFreeUser['nickname'] = $nickname;
        $wxFreeUser['help_num'] = $help_num;
        $wxFreeUser['create_time'] = $create_time;
        $wxFreeUser['money'] = self::INIT_MONEY;
        $wxFreeUser['act_name'] = $act_name;
        $wxFreeUser->attributes = $wxFreeUser;
        return $wxFreeUser->insert();
    }

    /**
    *   获取用户信息
    *
    */
    public function getUserInfo($act_name,$wx_user){
        $user_info = WxFreeUser::getDbConnection()->createCommand()
            ->select('id,wx_user,phone,is_draw,money,head_url,nickname,help_num')
            ->from('t_wx_free_user')
            ->where('wx_user=:wx_user and act_name=:act_name', array(':wx_user' => $wx_user,':act_name' => $act_name))
            ->queryRow();
        return $user_info;
    }

    /**
    *   更新用户金额
    *   @param  $user更新用户 $addMoney增加金额 $num第几次 $max_num最大次数
    */
    public function updateMoney($act_name,$wx_user,$addMoney,$num,$max_num){
        if($num < $max_num){
            $sql = "UPDATE `t_wx_free_user` SET `money` = :addMoney+`money` WHERE wx_user = :wx_user and act_name=:act_name and `money`<38";
            return Yii::app()->db_activity->createCommand($sql)->execute(array(
                ':wx_user' => $wx_user,
                ':act_name' => $act_name,
                ':addMoney' => $addMoney,
            ));
        }else{
            $sql = "UPDATE `t_wx_free_user` SET `money` = 38  WHERE wx_user = :wx_user and act_name=:act_name and `money`<38";
            return Yii::app()->db_activity->createCommand($sql)->execute(array(
                ':wx_user' => $wx_user,
                ':act_name' => $act_name,
            ));
        }
        
    }

    /**
    *   帮他一把，随机给用户增加一条记录，并更新用户金额
    *   @param $num第几次
    */
    public function helpUser($act_name,$wx_user,$wx_helper,$num,$headurl,$nickname){
        $user_info = self::getUserInfo($act_name,$wx_user);
        if(empty($user_info)){
            EdjLog::info('user='.$wx_user.'不存在');
            return false;
        }

        //前9个金额，随机生成，第10个用80减掉前9个之和
        $random = self::genRandom();
        if($num == 1){
            $random = 5;
        }
        if($num >= $user_info['help_num'] || ($random+$user_info['money']>self::DRAW_MONEY)){ //防止失败,保证够80 防止超过最大
            $random = self::DRAW_MONEY - $user_info['money'];
        }
        try{
            $msg = WxFreeLog::model()->getRandomMsg($random);
            //增加帮助记录
            $begin = microtime(TRUE);
            $res = WxFreeLog::model()->addLog($act_name,$wx_user,$wx_helper,$random,$headurl,$nickname,$msg);
            $end = microtime(TRUE);
            $time=($end-$begin)*1000;
            EdjLog::info('comeon----user='.$wx_user.'|helper='.$wx_helper.'添加帮助信息到t_wx_free_log耗费:'.$time.'ms');

            EdjLog::info('-------wx_user='.$wx_user.',helper='.$wx_helper.',res='.$res);
            if($res){
                //更新用户总金额
                $total = $random+$user_info['money'];
                //小于80才更新
                $begin = microtime(TRUE);
                $result = $this->updateMoney($act_name,$wx_user,$random,$num,$user_info['help_num']);
                $end = microtime(TRUE);
                $time=($end-$begin)*1000;
                EdjLog::info('comeon----user='.$wx_user.'|helper='.$wx_helper.'更新用户金额到t_wx_free_user耗费:'.$time.'ms');

                EdjLog::info('--------更新金额money='.$total.'user='.$wx_user.',result='.$result);
                if($result){
                    //更新用户缓存
                    $begin = microtime(TRUE);
                    RWxUser::model()->reloadUserInfo($act_name,$wx_user);
                    $end = microtime(TRUE);
                    $time=($end-$begin)*1000;
                    EdjLog::info('comeon----user='.$wx_user.'|helper='.$wx_helper.'重新加载用户信息到redis耗费:'.$time.'ms');

                    EdjLog::info('更新用户总金额成功，user='.$wx_user.',helpUser='.$wx_helper.',incMoney='.$random.',total='.$total);
                }else{
                    $res = 0;
                }
            }else{
                $res = 0;
                $key= $act_name.'_'.RWxUser::WX_ACT_USER.$wx_user;
                RWxUser::model()->automicIncr($key);
            }
        }catch(Exception $e){
            $res=0;
        }
        return $res;
    }

    /**
    *   生成随机数
    */
    private function genRandom(){
        $data=array(-3,-2,-1,4,5,6,7,8,9);
        $val = array_rand($data, 1);
        return $data[$val];
    }

    /**
    *   随机生成10-15之间，最大帮助次数
    *
    */
    private function getHelpRandom(){
        $data=array(5,6);
        // $data=array(4,5,6);
        $val = array_rand($data, 1);
        return $data[$val];
    }


    /**
    *   支取代驾费,够80才可以
    *
    */
    public function drawMoney($act_name,$wx_user,$phone){
        $res = 0;
        //先找到用户信息，未支取
        $user_info = self::getUserInfo($act_name,$wx_user);

        if(empty($user_info)){
            $res = 1;//用户不存在
            EdjLog::info('user='.$wx_user.' 不存在');
            return $res;
        }

        if($user_info['money'] < self::DRAW_MONEY){
            $res = 2; //不到支取标准
            EdjLog::info('user='.$wx_user.',money='.$user_info['money'].' 不到支取标准');
            return $res;
        }

        if($user_info['is_draw'] == self::HAS_DRAW){
            $res = 3; //已经支取过
            EdjLog::info('user='.$wx_user.',phone='.$user_info['phone'].' 已经支取过');
            return $res;
        }

        $count = WxFreeUser::model()->findAll("phone=:phone and act_name=:act_name",array('phone' => $phone,'act_name' => $act_name));
        if($count){
            $res = 4; //一个手机号只能领取一次
            EdjLog::info('phone='.$phone.' 已经支取过一次');
            return $res;
        }    
        //支付逻辑 1.更新支取状态和电话号码 2.调用支付接口
        try{
            $result = WxFreeUser::model()->updateAll(array ('is_draw'=>self::HAS_DRAW,'phone'=>$phone),'wx_user = :wx_user and act_name=:act_name and is_draw = 0', array(':wx_user'=>$wx_user,'act_name' => $act_name));
            if($result){
                EdjLog::info('user='.$wx_user.',phone='.$phone.'更新支取状态成功');
                //调用支付 todo
                $ret = FinanceWrapper::bindBonusBySn($phone, self::BOUNS_NO);
                EdjLog::info('优惠券绑定user='.$wx_user.',phone='.$phone.',ret='.$ret);
                if($ret){
                    EdjLog::info('user='.$wx_user.',phone='.$phone.'支取成功');
                }else{
                    EdjLog::info('user='.$wx_user.',phone='.$phone.'支取失败---error');
                }
            }else{
                EdjLog::info('user='.$wx_user.',phone='.$phone.'更新支取状态失败---error');
            }
        }catch(Exception $e){
            EdjLog::info('user='.$wx_user.',phone='.$phone.'更新支取状态失败---error');
        }
        //更新缓存
        RWxUser::model()->reloadUserInfo($act_name,$wx_user);
        return $res;
    }
}