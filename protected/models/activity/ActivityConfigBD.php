<?php
/**
 * BD配置化活动
 */
class ActivityConfigBD extends CActiveRecord
{

    const WAIT_VERIFY = 0; // 待审核
    const ON_LINE = 1; //在线(审核通过)
    const OFF_LINE = 2; //结束
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
     * @return RedPacketLog the static model class
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
        return '{{activity_config_new}}';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,english_name,bonus_sn,bonus_num,template_id,page_json,
            channel,data_send_begin_time,data_send_end_time,begin_time,end_time,status,seller_id', 'required'),
            array('act_target,target_people', 'length', 'max'=>254),
            array('act_sharpen,subsidy_type,send_rate',  'numerical','integerOnly'=>true),
            array('send_mails', 'length', 'max'=>500),
            array('subsidy_amount,bonus_sn2,bonus_sn3,sms,sn_money,sn2_money,sn3_money,
                create_user,update_user,short_url,qrcode,sms2', 'safe'),
        );
    }

    public function relations()
    {
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'name'=>'活动名字',
            'english_name'=>'活动英文名字',
            'template_id'=>'活动类型',
        );
    }

    public function search() {
        $criteria = new CDbCriteria();
        $criteria->compare('name', $this->name,true);
        $criteria->compare('english_name', $this->english_name,true);
        if($this->template_id != 0){
            $criteria->compare('template_id', $this->template_id);
        }
        if($this->isBD()){//BD组的用户只能查看自己的数据
            $user = Yii::app()->user->id;
            $criteria->compare('create_user', $user);
        }
        $criteria->order = 'id desc';
        return new CActiveDataProvider('ActivityConfigNew', array (
            'pagination'=>array (
                'pageSize'=>15
            ),
            'criteria'=>$criteria
        ));
    }


    /**
     * 判断当前用户是否属于BD角色组
     * @return bool
     */
    public function isBD(){
        $user_id =  Yii::app()->user->user_id;
        $roles = AdminUser2role::model()->getRoleInfo($user_id);
        if(!$roles){
            return false;
        }
       /* if(count($roles) == 1){
            foreach($roles as $role){
                if($role['name'] == 'BD'){
                    return true;
                }
            }
        }*/
        foreach($roles as $role){
            if($role['name'] == 'BD'){
                return true;
            }
        }
        return false;
    }

    /**
     * 获取当前登录用户的邮箱
     */
    public function getMail(){
        $user_id =  Yii::app()->user->user_id;
        $adminUser = AdminUserNew::model()->findByPk($user_id);
        if($adminUser){
            return $adminUser->email;
        }
        return '';
    }
}
