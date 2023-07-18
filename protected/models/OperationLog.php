<?php
/**
 * 操作日志管理
 * User: zhanglimin
 * Date: 13-8-23
 * Time: 下午4:36
 *
 * This is the model class for table "operation_log".
 *
 * The followings are the available columns in table 'operation_log':
 * @property string $id
 * @property string $route
 * @property string $mod_name
 * @property string $mod_code
 * @property string $opt_type
 * @property string $data_log
 * @property string $opt_user
 * @property string $created
 */
class OperationLog extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{operation_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('route, mod_name, mod_code, opt_type, data_log, opt_user, created', 'required'),
            array('route', 'length', 'max'=>200),
            array('mod_name, mod_code', 'length', 'max'=>100),
            array('opt_type, opt_user', 'length', 'max'=>50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, route, mod_name, mod_code, opt_type, data_log, opt_user, created', 'safe', 'on'=>'search'),
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
            'route' => '控制器',
            'mod_name' => '模块名称',
            'mod_code' => '模块类型',
            'opt_type' => '操作类型',
            'data_log' => 'Data Log',
            'opt_user' => '操作人',
            'created' => '创建时间',
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
        $criteria->compare('route',$this->route,true);
        $criteria->compare('mod_name',$this->mod_name,true);
        $criteria->compare('mod_code',$this->mod_code,true);
        $criteria->compare('opt_type',$this->opt_type,true);
        $criteria->compare('data_log',$this->data_log,true);
        $criteria->compare('opt_user',$this->opt_user,true);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OperationLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
     * 获取并定义模块属性
     * @return array
     */
    public function getModCodeConfig($code = ""){
        $ret = array(
             '1001' => '皇冠司机管理',
             '1002' => '节假日管理',
             '1003' => '拒不升级的司机',
             '1004' => '修改订单',
             '1005' => '意见反馈指定责任人',
        );

        if(!empty($code)){
            if(isset($ret[$code])){
                return $ret[$code];
            }else{
                return array();
            }
        }else{
            return $ret;
        }
    }


    public function getModTypeConfig($type = ""){
        $ret = array(
            'insert' => '新增',
            'update' => '更新',
            'delete' => '删除',
        );

        if(!empty($type)){
            if(isset($ret[$type])){
                return $ret[$type];
            }else{
                return array();
            }
        }else{
            return $ret;
        }
    }

    /**
     * 新增日志
     * @param array $params
     * @return array
     */
    public function insertLog($params = array()){
        $msg = array(
            'flag' => false,
            'msg'=> '',
        );

        if(empty($params)){
            $msg['msg'] = '参数不能为空!';
            return $msg;
        }

        if(empty($params['mod_code'])){
            $msg['msg'] = 'mod_code参数不能为空!';
            return $msg;
        }

        if(empty($params['opt_type'])){
            $msg['msg'] = '操作类型不能为空!';
            return $msg;
        }

        $ope_type = array_keys($this->getModTypeConfig());
        if(!in_array($params['opt_type'],$ope_type)){
            $msg['msg'] = '操作类型不正确!';
            return $msg;
        }

        $mod_name = $this->getModCodeConfig($params['mod_code']);
        if(empty($mod_name)){
            $msg['msg'] = '模块code 不存在!';
            return $msg;
        }

        $params['mod_name'] = $mod_name;

        $params['data_log'] = is_array($params['data_log']) ? json_encode($params['data_log']) : $params['data_log'];

        $params['opt_user'] = empty($params['opt_user']) ? Yii::app()->user->id : $params['opt_user'];

        $params['created'] =  date("Y-m-d H:i:s");


        //添加task队列
        $task=array(
            'method'=>'operation_log_insert',
            'params'=>$params,
        );

        QueueProcess::model()->operation_log_insert($params);
        Queue::model()->putin($task,'task');

        $msg = array(
            'flag' => true,
            'msg'=> '添加成功',
        );
        return $msg;

    }


    /**
     * 获取LOG数据List
     * @param $params
     * @return array
     */
    public function getLogList($params){
        $msg = array(
            'flag'=>false,
            'msg'=>'',
            'list'=>'',
        );
        if(empty($params)){
           return $msg;
        }
        if(!isset($params['mod_code']) || empty($params['mod_code'])){
            $msg['msg'] = 'mod_code 不能为空';
            return $msg;
        }

        $mod_name = $this->getModCodeConfig($params['mod_code']);
        if(empty($mod_name)){
            $msg['msg'] = '模块code 不存在!';
            return $msg;
        }

        $criteria=new CDbCriteria();
        $criteria->addCondition("mod_code = :mod_code");
        $criteria->params[':mod_code']= $params['mod_code'];

        if(isset($params['start_date']) && isset($params['end_date'])){
            $params['start_date'] = date("Y-m-d H:i:s" ,strtotime($params['start_date']));
            $params['end_date'] = date("Y-m-d H:i:s" ,strtotime($params['end_date']));
            $criteria->addBetweenCondition('created',$params['start_date'],$params['end_date']);
        }
        $criteria->order = " id desc";
        $criteria->limit =  isset($params['limit']) ? intval($params['limit']) : 30;
        $data=$this->findAll($criteria);
        $list = array();
        if(!empty($data)){
            foreach($data as $val){
                $list[] = array(
                    'id' =>$val->id,
                    'route' =>$val->route,
                    'mod_code' =>$val->mod_code,
                    'mod_name' =>$val->mod_name,
                    'opt_type' =>$val->opt_type,
                    'data_log' =>$val->data_log,
                    'created' =>$val->created,
                );
            }
        }
        $msg['flag'] = true;
        $msg['msg'] = '获取成功';
        $msg['list'] = $list;
        return $msg;


    }
}