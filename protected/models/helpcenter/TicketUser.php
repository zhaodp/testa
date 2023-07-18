<?php

/**
 * This is the model class for table "{{ticket_user}}".
 *
 * The followings are the available columns in table '{{ticket_user}}':
 * @property integer $id
 * @property string $user
 * @property integer $is_admin
 * @property string $create_time
 * @property integer $status
 */
class TicketUser extends CActiveRecord
{
    const TICKET_CATEGORY_PHONE = 1; //手机
    const TICKET_CATEGORY_SUGGEST = 2; //建议
    const TICKET_CATEGORY_CONSULT = 3; //咨询
    const TICKET_CATEGORY_COMPLAINT = 4; //投诉
    const TICKET_CATEGORY_APPEAL = 5; //申诉
    const TICKET_CATEGORY_ORDER = 6; //订单

    const TICKET_DEPARTMENT_TECH = 1; //技术
    const TICKET_DEPARTMENT_PRODUCT = 2; //产品
    const TICKET_DEPARTMENT_OPERATE= 3; //运营
    const TICKET_DEPARTMENT_QS = 4; //品监
    const TICKET_DEPARTMENT_DM = 5; //司管
    const TICKET_DEPARTMENT_FINANCE = 6; //财务


    public static $maps = array(
        self::TICKET_CATEGORY_PHONE => self::TICKET_DEPARTMENT_TECH,        //手机 -> 技术
        self::TICKET_CATEGORY_SUGGEST => self::TICKET_DEPARTMENT_PRODUCT,       //建议 -> 产品
        self::TICKET_CATEGORY_CONSULT => self::TICKET_DEPARTMENT_OPERATE,          //咨询 -> 运营
        self::TICKET_CATEGORY_COMPLAINT => self::TICKET_DEPARTMENT_QS,           //投诉 -> 品监
        self::TICKET_CATEGORY_APPEAL => self::TICKET_DEPARTMENT_QS,           //申诉 -> 品监
        self::TICKET_CATEGORY_ORDER => self::TICKET_DEPARTMENT_QS,          //订单 -> 品监
    );
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{ticket_user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_admin, status', 'numerical', 'integerOnly'=>true),
			array('user', 'length', 'max'=>20),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user, is_admin, create_time, status', 'safe', 'on'=>'search'),
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
			'user' => '名称',
			'is_admin' => '管理员',
			'create_time' => '创建时间',
			'status' => '状态',
            'city_id'=> '城市',
            'group' => '部门',
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
        $criteria->condition = " status=1 ";
		$criteria->compare('id',$this->id);
		$criteria->compare('user',$this->user,true);
		$criteria->compare('is_admin',$this->is_admin);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('status',$this->status);

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
	 * @return TicketUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * 判断是否为管理员
     * $user
     * 返回  true|false
     * wanglonghuan 2013-12-25
     */
    public function checkUserAdmin($user = '')
    {
        $sql = "select is_admin from t_ticket_user where `user`=:user";
        $t_model = $this->model()->findBySql($sql,array('user'=>$user));
        if(!empty($t_model)){
            if($t_model->is_admin == 1){
                return true;
            }
        }
        return false;
    }

    /*
     * 判断是否存在
     * @patams $user
     * @return int
     * wanglonghuan 2013-12-25
     */
    public function checkUserExist($user = '')
    {
        $sql = "select count(id) count from t_ticket_user where `user`=:user";
        return Yii::app()->db_readonly->createCommand($sql)->queryScalar(array('user'=>$user));
    }

    //管理员列表
    public function adminList()
    {
        $sql = "select `id`,`user` from t_ticket_user where `is_admin`=:is_admin";
        return Yii::app()->db_readonly->createCommand($sql)->queryAll(true,array('is_admin'=>1));
    }

    //城市经理列表
    public function cityManagerList()
    {
        $sql = "select `id`,`user`,`city_id` from t_ticket_user where `city_manager`=:city_manager";
        return Yii::app()->db_readonly->createCommand($sql)->queryAll(true,array('city_manager'=>1));
    }

    //获取当前用户group
    public function getGroup($user = '')
    {
        $sql = "select `group` from `t_ticket_user` where `user`=:user ";
        return Yii::app()->db_readonly->createCommand($sql)->queryScalar(array('user'=>$user));
    }

    //获取分类 by User
    public function getCategoryByUser($user = "")
    {
        $group = $this->getGroup($user);
        $ret = array();
        foreach(self::$maps as $k=>$v){
            if($v == $group){
                $ret[] = array('category_id'=>$k);
            }
        }
        return $ret;
    }

    //获取部门处理人 根据 部门id
    public function getUsers($group)
    {
        $sql = "select `user` from `t_ticket_user` where `group`=:group ";
        $res = Yii::app()->db_readonly->createCommand($sql)->queryAll(true,array('group' => $group));
        $ret = array();
        foreach($res as $v){
            $ret[] = $v['user'];
        }
        return array_unique($ret);
    }

    //删除用户操作
    public function removeUser($user_id)
    {
        if(empty($user_id)){
            return false;
        }
//        $transaction = Yii::app()->db->beginTransaction(); //开启实务
//        try{
            $sql = "delete from t_ticket_user where id=:id";
            return Yii::app()->db->createCommand($sql)->execute(array('id'=>$user_id));

            //$sql = "delete from t_ticket_group_map where `user`=:user";
            //Yii::app()->db->createCommand($sql)->execute(array('user'=>$username));
//            $transaction->commit();
//        }catch (Exception $e){
//            $transaction->rollback(); //如果系统异常，实务回滚
//            throw new CHttpException(500,$e->getMessage());
//        }

    }

    /**
     * 获取 部门对应 处理人
     */
    public function loadUsersByGroup($group,$city_id,$refresh = false)
    {
        $cache_key = 'TICKET_GROUP_USERS_' . $group;
        $json = Yii::app()->cache->get($cache_key);
        if (!$json||$json=='[]'||$refresh)
        {
            $condition = '`group`=:group';
            $params = array(':group' =>  $group);
            if($group == 5)   //司管需要根据城市分配
            {
                $condition .= " and city_id=:city_id ";
                $params['city_id'] = $city_id;
            }

            $models = self::model()->findAll(array(
                'condition'=>$condition,
                'params'=>$params,
                'order'=>'cursor_sort'));
            $data = array();
            foreach($models as $v){
                $data[] = array('group'=>$v->group,'user'=>$v->user);
            }
            $json = json_encode($data);
            Yii::app()->cache->set($cache_key, $json, 3600);
        }
        return json_decode($json, true);
    }

    /**
     * 轮循获取 分配跟单人
     * @param $category_id $group 存在参数部门 则按部门获取maps 和 follow_user
     * @return array
     * wanglonghuan 2013.12.18
     */
    public function getFollowUser($category_id,$group = '',$city_id = '')
    {
        //load 处理部门处理人
        $group = isset(self::$maps[$category_id])?self::$maps[$category_id]:$group;
        $maps = self::model()->loadUsersByGroup($group,$city_id,true);
        $follow_user = SupportTicket::model()->getOperationUserByGroup($group);

        $ret = array();
        foreach($maps as $k=>$v){
            if($v['user'] == $follow_user){
                if(isset($maps[($k+1)])){
                    $ret = $maps[($k+1)];
                }
            }
        }
        if(empty($ret)){
            $keys = array_keys($maps);
            if(!empty($keys))
                $ret = $maps[$keys[0]];
        }

        return $ret;
    }
}
