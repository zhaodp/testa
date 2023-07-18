<?php

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
class WxFreeLog extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{wx_free_log}}';
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
            array('wx_user,wx_helper', 'length', 'max'=>64),
            array('phone', 'length', 'max'=>15),
            array('act_name', 'length', 'max'=>20),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('wx_user, wx_helper,phone, money, create_time,act_name', 'safe', 'on'=>'search'),
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
            'wx_helper' => 'wx_helper',
            'money' => 'money',
            'head_url'=>'head_url',
            'nickname'=>'nickname',
            'msg'=>'msg',
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
        $criteria->compare('wx_helper',$this->wx_helper,true);
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

    /**
    *   获取所有帮助者信息
    *
    */
    public function getHelpList($act_name,$wx_user){
        $helpList = WxFreeLog::model()->findAll('wx_user=:wx_user and act_name=:act_name ORDER BY id DESC', array(':wx_user'=>$wx_user,'act_name'=>$act_name));
        
        $data = array();
        foreach ($helpList as $helper) {
            $data[] = array(
                'helpopenid' => $helper['wx_helper'],
                'head_url'=>$helper['head_url'],
                'nickname'=>$helper['nickname'],
                'create_time' => date("m-d H:i",strtotime($helper['update_time'])),
                'money' => $helper['money'] > 0 ? '+'.$helper['money']:$helper['money'] ,
                'msg'=>$helper['msg'],
                );
        }
        return $data;
    }

    /**
    *   根据钱正负，返回不同信息
    *
    */
    public function getRandomMsg($money){
        $data=array('走错路又回到原点！','回家要跪搓板了！','又失去一个好朋友！','头都大了duang duang duang！','喝多了再也不帮你叫代驾！');
        $dataTwo=array('好兄弟帮你到这里！','颜值高的好盆友都这么任性！','泪流满面，终于等到你！','买条白金小裙子送女神~');
        if($money > 0){
            $data = $dataTwo;
        }
        $val = array_rand($data, 1);
        return $data[$val];

    }

    /**
    *   增加日志记录
    *
    */
    public function addLog($act_name,$wx_user,$wx_helper,$money,$head_url,$nickname,$msg){
        $create_time=date("Y-m-d H:m:s");
        $wxFreeLog = new WxFreeLog();
        $wxFreeLog['wx_user'] = $wx_user;
        $wxFreeLog['wx_helper'] = $wx_helper;
        $wxFreeLog['head_url'] = $head_url;
        $wxFreeLog['nickname'] = $nickname;
        $wxFreeLog['money'] = $money;
        $wxFreeLog['msg'] = $msg;
        $wxFreeLog['create_time'] = $create_time;
        $wxFreeLog['act_name'] = $act_name;
        $wxFreeLog->attributes = $wxFreeLog;
        return $wxFreeLog->insert();
    }
}