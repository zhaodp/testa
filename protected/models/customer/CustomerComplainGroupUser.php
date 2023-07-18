<?php

/**
 * This is the model class for table "{{customer_complain_group}}".
 *
 * The followings are the available columns in table '{{customer_complain_group}}':
 * @property integer $gid
 * @property integer $uid
 * @property string $uname
 * @property string $role
 * @property integer $status
 * @property string $operator
 * @property string $created
 */
class CustomerComplainGroupUser extends CActiveRecord
{
    /**
     *  状态，1 正常
     */
    const STATUS_NORMAL = 1;
    /**
     *  状态，2 删除
     */
    const STATUS_DEL = 2;
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerComplainType the static model class
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
        return '{{customer_complain_group_user}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('gid, uid, uname, role created, operator', 'required'),
            array('status', 'numerical','integerOnly'=>true),
            array('uname, operator', 'length', 'max'=>40),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('gid, uid, uname, role, status, created, operator', 'safe', 'on'=>'search'),
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
            'gid' => 'GID',
            'uid' => 'UID',
            'uname' => '用户名称',
            'role' => '角色',
            'status' => '状态',
            'created' => 'Created',
            'operator' => 'Operator',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('gid',$this->gid);
        $criteria->compare('uid',$this->uid);
        $criteria->compare('uname',$this->uname,true);
        $criteria->compare('role',$this->role,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('created',$this->created,true);

        $criteria->compare('operator',$this->operator,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * 增加任务人到任务组
     * @param $name 组名
     */
    public function addUser($gid, $uid, $uname, $role)
    {
        $user = $this->getGroupUser($gid, $uid);
        if (!$user) {
            $ret = $this->add($gid, $uid, $uname, $role);
            return $ret;
        } else if ($user['status'] == self::STATUS_DEL) {
            $model = new CustomerComplainGroupUser;
            $model->updateByPk(array('gid'=>$gid,'uid'=>$uid),array('status'=>self::STATUS_NORMAL,'operator'=>Yii::app()->user->name));
            return true;
        }
        return false;
    }

    /**
     * 添加任务人
     * @param $gid 组id
     * @param $uid 用户id
     * @param $uname 用户名
     * @param $role 角色
     * @return bool
     */
    protected function add($gid, $uid, $uname, $role)
    {
        $model = new CustomerComplainGroupUser;
        $param['gid'] = (int)$gid;
        $param['uid'] = (int)$uid;
        $param['uname'] = $uname;
        $param['role'] = $role;
        $param['operator'] = Yii::app()->user->name;
        $param['created'] = date('Y-m-d H:i:s');
        $model->attributes = $param;
        if ($model->save()) {
            return true;
        }
        return false;
    }

    /**
     * 更新任务组
     * @param $gid 组id
     * @param $ouid 原用户id
     * @param $uid 用户id
     * @param $uname 用户名
     * @param $role 角色
     * @return bool
     */
    public function updateGroupUser($gid, $ouid, $uid, $uname, $role)
    {
        $model = new CustomerComplainGroupUser;
        if ($ouid == $uid) {//修改当前用户的角色
            $model->updateByPk(array('gid'=>$gid,'uid'=>$uid),array('role'=>$role,'operator'=>Yii::app()->user->name));
            return true;
        } else {//删除原用户，增加新用户
            $this->deleteUser($gid,$ouid);
            $user = $this->getGroupUser($gid,$uid);
            if (!$user) {
                $ret = $this->add($gid, $uid, $uname, $role);
                return $ret;
            } else if ($user['status'] == self::STATUS_DEL) {
                $model->updateByPk(array('gid'=>$gid,'uid'=>$uid),array('status'=>self::STATUS_NORMAL,'role'=>$role,'operator'=>Yii::app()->user->name));
                return true;
            }
        }
        return false;
    }

    /**
     * 删除任务人
     * @param $id
     */
    public function deleteUser($gid, $uid)
    {
        $model = new CustomerComplainGroupUser;
        $model->updateByPk(array('gid'=>$gid,'uid'=>$uid),array('status'=>self::STATUS_DEL));
        return true;
    }

    /**
     * 查询任务组的一个用户
     * @param $gid 组id
     * @param $uid 用户id
     * @return mixed
     */
    public function getGroupUser($gid, $uid)
    {
        $conditions = 'gid=:gid and uid=:uid';
        $params = array(':gid'=>$gid,':uid'=>$uid);
        $user = Yii::app()->db_readonly->createCommand()->select('gid,uid,uname,status,role')->from(self::tableName())->where($conditions,$params)->queryRow();
        return $user;
    }

    /**
     * 获取一个任务组所有状态正常的任务人
     * @return mixed
     */
    public function getAllGroupUser($gid)
    {
        $conditions = 'gid=:gid and status=:st';
        $params = array(':gid'=>$gid,':st'=>self::STATUS_NORMAL);
        $users = Yii::app()->db_readonly->createCommand()->select('gid,uid,uname,role')->from(self::tableName())->where($conditions,$params)->queryAll();
        return $users;
    }

    /**
     * 获取所有的任务人
     * @return array
     */
    public function getAllUser()
    {
        $userArr = array();
        $group = CustomerComplainGroup::model()->getAllGroup();
        foreach ($group as $k=>$v) {
            $user = self::getAllGroupUser($v['id']);
            $userArr = array_merge($userArr,$user);
        }
        return $userArr;
    }


    /**
     * 获取投诉任务人
     * @param string $id 二级分类id
     */
    public function getTaskUser($id='')
    {
        $group_id = '';
        if (!empty($id)) {//分类id存在
            $type = CustomerComplainType::model()->getTypeById($id);//查询分类的关联任务组
            $group_id = $type['group_id'];
        }
        if (!$group_id) {
            //查询特殊任务组
            $group = CustomerComplainGroup::model()->getDefaultGroup();
            $group_id = $group['id'];
        }

        //获取合适的任务人
        $user_id = self::getFreeUser($group_id);
        if ($user_id) {
            return array('group_id'=>$group_id,'user_id'=>$user_id);
        }
        return false;
    }

    /**
     * 获取相对空闲的任务人
     * @param $group_id
     * @return bool
     */
    private function getFreeUser($group_id)
    {
        $users = self::getAllGroupUser($group_id);
        if ($users) {//取任务最少的人
            foreach ($users as $k=>$v) {
                $num = CustomerComplain::model()->getUnCloseTaskNum($v['uid']);
                $user[$v['uid']] = $num;
            }
            asort($user);
            return array_keys($user)[0];
        }
        return false;
    }
}
