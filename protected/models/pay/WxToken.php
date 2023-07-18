<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 2015/1/26
 * Time: 22:35
 */

class WxToken extends FinanceActiveRecord {
    const  CHANNEL_WX = 1;//微信支付渠道

    /**
     * Returns the static model of the specified AR class.
     * @return SubsidyRecord the static model class
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
        return '{{wx_token}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('access_token', 'required'),
            array('id,channel,update_time', 'safe', 'on'=>'search'),
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
            'id' => 'Id',
            'channel' => 'channel',
            'access_token' => 'access_token',
            'update_time' => 'update_time',
        );
    }
    public  function  getAccessTokenByChannel($channel){
        $model = self::model()->find('channel=:channel', array(':channel' => $channel));
        return $model;
    }

    /**
     * 根据实际判断是否需要拉取token  一个小时拉取一次(改为半小时更新一次0401)
     * @param $time
     * @param $channel
     */
    public  function  isFetchAccessToken($current_time,$channel){
        $tokenModel = self::model()->find('channel=:channel', array(':channel' => $channel));
        if(empty($tokenModel)){
            return true;
        }
        $updateTime = strtotime($tokenModel->update_time);
        $ret = (($current_time - $updateTime)/1800 > 1) ? true : false;//如果距上次更新token已经超过一个小时则重新获取一下token
        return $ret;
    }

    /**
     * 根据渠道更新最新的token
     * @param $channel
     * @param $access_token
     */
    public  function  updateAccessTokenByChannel($channel,$access_token){
        $model = $this->getAccessTokenByChannel($channel);
        if(empty($model)){
            //第一次插入token
            $newModel = new WxToken();
            $newModel->channel = $channel;
            $newModel->access_token = $access_token;
            $newModel->update_time = date('Y-m-d H:i:s',time());
            if($newModel->save()){
                return true;
            }
        }else{
            $model->access_token = $access_token;
            $model->update_time = date('Y-m-d H:i:s',time());
            if ($model->update()) {
                return true;
            } else {
                return false;
            }
        }
    }

} 