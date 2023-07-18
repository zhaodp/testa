<?php

/**
 * This is the model class for table "{{admin_logs}}".
 *
 * The followings are the available columns in table '{{admin_logs}}':
 * @property integer $id
 * @property string $ip
 * @property string $agent
 * @property string $status
 * @property string $created
 */
class AdminLogs extends CActiveRecord {
    public $db_suffix = '';
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AdminLogs the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
        if(!$this->db_suffix) $this->db_suffix = date('Ym');
		return '{{admin_logs_'.$this->db_suffix.'}}';
	}

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->dbstat;
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'ip, agent, status, created', 
				'required'), 
			array (
				'ip', 
				'length', 
				'max'=>20), 
			array (
				'agent', 
				'length', 
				'max'=>100), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, ip, agent, status,url, created', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	public function beforeSave() {
		if (parent::beforeSave()) {
			$this->created = date(Yii::app()->params['formatDateTime'], time());
		}
	}
	
	public static function addLogs($username, $status) {
		$attributes = array (
			'username'=>$username,
			'ip'=>Yii::app()->request->getUserHostAddress(), 
			'agent'=>Yii::app()->request->getUserAgent(), 
			'status'=>$status, 
			'created'=>date(Yii::app()->params['formatDateTime'], time()));
		
		Yii::app()->dbstat->createCommand()->insert('t_admin_logs', $attributes);
	}

    /**
     * 纪录admin opt log
     *
     * @param unknown_type $params
     */
    public function addAdminOptLogs($params){

        if(empty($params)){
            return false;
        }

        if(!isset($params['user_id'])){
            //$params['user_id'] = Yii::app()->user->user_id;
            if(empty($params['user_id'])){
                return false;
            }
            return false;
        }

        $env = Common::isOnlineEnv();
        if(!$env){
            EdjLog::info('testenv not save');
            return false;
        }

        $tableName = 't_admin_logs_'.date('Ym');
        $sourceTableName = 't_admin_logs';

        //检测表是否存在不存在则创建
        $sql = "show tables like '".$tableName."'";
        $_aRows = Yii::app()->dbstat_proxy->createCommand($sql)->queryRow();
        if(empty($_aRows)){
            $sql = "create table ".$tableName." like ".$sourceTableName;
            $_aRows = Yii::app()->dbstat_proxy->createCommand($sql)->execute();
        }

        $ret = Yii::app()->dbstat_proxy->createCommand()->insert($tableName, $params);

        EdjLog::info("Atlas--t_admin_logs, code{$ret}, params: ".json_encode($params), 'console');



        return $ret;
    }
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'ip'=>'Ip', 
			'agent'=>'Agent', 
			'created'=>'Created');
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('ip', $this->ip);
		$criteria->compare('agent', $this->agent);
		$criteria->compare('created', $this->created);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}


    /**
     * 查询一定时间内 用户访问地址的统计记录
     * @param $user_name 中文
     * @param $start_time 2014-09-08 20:00:00
     * @param $end_time 2014-09-09 20:00:00
     * @return CActiveRecord[]
     */
    public function getCountByUser($user_name,$start_time,$end_time){
        $user_info = AdminUserNew::model()->getInfoByName($user_name);
        $user_id = $user_info->id;
        $drivers = Yii::app()->dbstat_proxy->createCommand()
            ->select('url')
            ->from('t_admin_logs')
            ->where('user_id = :uid and username = :name and created < :end_time and created > :start_time ',
                array(':uid'=> $user_id,':name' => $user_name,':start_time'=>$start_time,':end_time'=>$end_time))
            ->queryAll();
        $data = array();
        if($drivers){
            foreach($drivers as $v){
                $parse = parse_url($v['url']);
                parse_str($parse['query']);
                $r = isset($r) ? $r :'';
                if($r){
                    $data[$r] =  isset($data[$r]) ? ( $data[$r] + 1) : (1);
                }
                unset($r);
            }
        }
        arsort($data);
        return $data;
    }
}
