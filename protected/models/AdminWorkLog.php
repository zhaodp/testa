<?php

class AdminWorkLog extends CActiveRecord {
    public $btime = null;
    public $etime = null;
    
    public static $categorys = array(
        '2' => array(//市场
            '市场管理'=>'市场管理',
            'VIP'=>'VIP',
            '物料采购'=>'物料采购',
            '媒体公关'=>'媒体公关',
            '市场运营'=>'市场运营',
            '社会媒体及运营'=>'社会媒体及运营',
        ),
        '5' => array(//技术
            '周报'=>'周报',
        ),
    );


    const TYPE_ALL = 0;
    const TYPE_DAY = 1;
    const TYPE_WEEK = 2;
    static $type = array(
           self::TYPE_ALL => '全部',
           self::TYPE_DAY => '日报',
           self::TYPE_WEEK => '周报',
    );


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{admin_work_log}}';
    }

    public function rules() {
        return array(
            array('city', 'numerical', 'integerOnly' => true),
            array('author', 'length', 'max' => 32),
            array('work_log,type', 'required'),
            array('work_log', 'length', 'min' => 10),
            array('category, department, work_date, create_time, update_time', 'safe'),
            array('id, author, department, category, city, work_log, work_date, create_time, update_time, btime, etime,type', 'safe', 'on' => 'search'),
        );
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'author' => '作者',
            'city' => '城市',
            'category' => '分类',
            'department' => '部门',
            'work_log' => '日志内容',
            'work_date' => '工作日期',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
	    'type' => '日报类型',
        );
    }

    public function search($extCriteria = NULL) {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('author', $this->author, true);
        $criteria->compare('category', $this->category);
        if($this->city > 0){
            $criteria->compare('city', $this->city);
        }
	if($this->type > 0){
            $criteria->compare('type', $this->type);
        }
	
        if(trim($this->department) && $this->department){
            $criteria->compare('department', $this->department);
        }
        $criteria->compare('work_log', $this->work_log, true);
        $btime = $this->btime ? $this->btime : 0;
        $etime = $this->etime ? (strtotime($this->etime) + 86400) : (strtotime(date('Y-m-d', time())) + 86400);
        $criteria->addBetweenCondition('work_date', $btime, date('Y-m-d H:i:s',$etime));
        $criteria->compare('update_time', $this->update_time);
        
        if($extCriteria !== NULL){
            $criteria->mergeWith($extCriteria);
        }

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    
    public function beforeSave() {
        $time = date('Y-m-d H:i:s',time());
        if(parent::beforeSave()){
            if($this->isNewRecord){
                $this->author = Yii::app()->user->id;
                $this->create_time = $time;
                $this->department = Yii::app()->user->getDepartment();
            }
            $this->update_time = $time;
            return true;
        }
        return false;
    }
    
    /**
     * 验证用户当天是否已经写过日志
     * @return bool
     */
    public function hasCreate(){
        $hasCreate = AdminWorkLog::model()->exists('create_time > :ctime AND author = :author', array(
            ':ctime'=>date('Y-m-d',time()),
            ':author'=>Yii::app()->user->id,
        ));
        return $hasCreate;
    }


    /** 统计区间时间内的数量
     * @param $start_time 2014-10-12 :00:00:11
     * @param $end_time
     * @param string $city_id
     * @return mixed
     */
    public function summaryData($start_time, $end_time, $city_id = ''){
        $where = " create_time between :date_start and :date_end";
        $params = array(':date_start' => $start_time, ':date_end' => $end_time);
        if($city_id){
            $where .=' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        $count = Yii::app()->db_readonly->createCommand()
            ->select('count(*) as cnt')->from($this->tableName())
            ->where($where,$params)
            ->queryScalar();
        return $count;
    }



}
