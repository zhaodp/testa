<?php

/**
 * This is the model class for table "{{comments}}".
 *
 * The followings are the available columns in table '{{comments}}':
 * @property integer $id
 * @property string $employee_id
 * @property string $uuid
 * @property string $name
 * @property integer $level
 * @property string $comments
 * @property string $insert_time
 */
class Comments extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comments the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{comments}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'employee_id, uuid, comments', 
				'required'), 
			array (
				'level,status', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'employee_id, uuid', 
				'length', 
				'max'=>255), 
			array (
				'name, insert_time', 
				'length', 
				'max'=>20),
			array('order_status','length','max'=>1), 
			array (
				'comments', 
				'length', 
				'max'=>1024), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, employee_id, uuid, name, level, status, comments, insert_time', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array (
			'reply'=>array (
				self::HAS_ONE, 
				'CommentsReply', 
				'comment_id'));
	}
	
	public function updateEmployeeID($oldOne, $newOne) {
		/*
		$criteria = new CDbCriteria;
		$criteria->condition = 'employee_id=:employee_id';
		$criteria->params = array(':employee_id'=>$oldOne);
		
		Comments::model()->updateAll(array('employee_id'=>$newOne), $criteria);
		*/
		$sql = "UPDATE t_comments SET employee_id = :newOne WHERE employee_id = :oldOne";
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":newOne", $newOne);
		$command->bindParam(":oldOne", $oldOne);
		$command->execute();
		$command->reset();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'employee_id'=>'Employee', 
			'uuid'=>'Uuid', 
			'name'=>'客户', 
			'level'=>'评价等级', 
			'comments'=>'评价内容', 
			'insert_time'=>'发布时间',
			'order_status' => '评论类型');
	}
	
	public static function getListByDriverID($pageNo = 0, $pageSize = 10, $driverID = 'BJ9000') {
//		$criteria = new CDbCriteria();
//		$criteria->select = 't.name, t.level, t.comments, t.insert_time, d.user AS employee_id';
//		$criteria->join = 'LEFT JOIN t_comments_reply AS cr ON cr.comment_id = t.id JOIN t_driver AS d ON d.imei = t.employee_id';
//		$criteria->condition = "((t.level = 1 AND cr.comment_id IS NOT NULL) OR t.level > 1) AND t.comments <> ''";
//		$criteria->addCondition('d.user=:user');
//		$criteria->params = array (
//			':user'=>$driverID);
//		$criteria->group = 't.name, t.level, t.comments, t.insert_time, d.user, t.uuid';
//		$criteria->order = 't.id desc';
//		$count = Comments::model()->count($criteria);
//		$criteria->offset = $pageNo*$pageSize;
//		$criteria->limit = $pageSize;
//		$comments = Comments::model()->findAll($criteria);
//		

// 		$sql = 'select count(*) from (select count(*)
// 				from t_comments c JOIN t_driver d ON d.imei = c.employee_id
// 				where d.user=:user and (c.level=1 and c.status =1 or c.level =3)
// 				group by employee_id,uuid)a';
		
		//2013-01-18	李白阳	修改查询评价条件 
		$sql = 'select count(*) from (select count(*)
		from t_comments c JOIN t_driver d ON d.imei = c.employee_id
		where d.user=:user and (c.level>0)
		group by employee_id,uuid)a';
		
		
		//and c.order_status=0 and c.level >0 
		
		$total = Yii::app()->db->createCommand($sql)->queryScalar(array(':user'=>$driverID));
		
		$offset = $pageNo*$pageSize;
		
// 		$sql = 'select * from (
// 				select c.name, c.level, c.comments, c.insert_time, d.user AS employee_id,status,uuid
// 				from t_comments c JOIN t_driver d ON d.imei = c.employee_id
// 				where d.user=:user and (c.level=1 and c.status =1 or c.level =3) order by c.insert_time desc  ) a
// 				group by employee_id,uuid  order by insert_time desc limit '.$offset.','.$pageSize;
		//2013-01-18	李白阳	修改查询评价条件
		$sql = 'select * from (
		select c.name, c.level, c.comments, c.insert_time, d.user AS employee_id,status,uuid
		from t_comments c JOIN t_driver d ON d.imei = c.employee_id
		where d.user=:user and (c.level>0) order by c.insert_time desc  ) a
		group by employee_id,uuid  order by insert_time desc limit '.$offset.','.$pageSize;
		
		//where d.user=:user and c.order_status=0 and c.level >0 order by c.insert_time desc  ) a
		
		$command = new CDbCommand(Yii::app()->db, $sql);
		
		$comments = $command->queryAll(true,array(':user'=>$driverID));
		
		$ret = array ();
		foreach($comments as $comment) {
			if (preg_match('%\d{11}%s', $comment['name'])) {
				$comment['name'] = substr_replace($comment['name'], '****', 3, 4);
			}
			//add by sunhongjing 2013-04-17  兼容新老接口
			$comment['new_level'] = $comment['level'];
			$comment['level'] = self::parseLevel($comment['level']);
			$comment['comments'] = self::parseComments($comment['comments'],$comment['new_level']);
			
			$ret[] = $comment;
		}
		$ret['total'] = $total;
		return $ret;
	}
	
	
	/**
	 * 重新计算原星级
	 * 
	 * @author sunhongjing 2013-04-17 
	 * Enter description here ...
	 * @param unknown_type $level
	 */
	public static function parseLevel($level=3){	
		$ret = 3;
		switch ($level) {
			case 1:$ret = "1";break;
			case 2:$ret = "2";break;
			case 3:
			case 4:
			case 5:
				$ret = "3";break;	
			default:break;
		}
		
		return $ret;
		
	}
	
	/**
	 * 重新整理信息内容
	 * 
	 * @author sunhongjing 2013-04-17 
	 * @param unknown_type $level
	 */
	public static function parseComments($comment='',$level=3){	
		$ret = '非常满意';	
		switch ($level) {
			case 1:$ret = '不满意';break;
			case 2:$ret = '一般';break;
			case 3:$ret = '满意';break;
			case 4:$ret = '很满意';break;
			case 5:$ret = '非常满意';break;
			default:break;
		}	
		
		$comment = trim($comment);
		return empty($comment) ? $ret : $comment;
	}
	
	public static function getList($pageNo = 0, $pageSize = 10) {
		$criteria = new CDbCriteria();
		$criteria->select = 't.name, t.level, t.comments, t.insert_time, d.name AS employee_id';
		$criteria->join = 'LEFT JOIN t_comments_reply AS cr ON cr.comment_id = t.id JOIN t_driver AS d ON d.imei = t.employee_id';
		$criteria->condition = "((t.level = 1 AND cr.comment_id IS NOT NULL) OR t.level > 1) AND t.comments <> ''";
		$criteria->group = 't.name, t.level, t.comments, t.insert_time, d.user, t.uuid';
		$criteria->order = 't.id desc';
		$count = Comments::model()->count($criteria);
		
		$criteria->offset = $pageNo*$pageSize;
		$criteria->limit = $pageSize;
		
		$comments = Comments::model()->findAll($criteria);
		$ret = array ();
		
		foreach($comments as $comment) {
			if (preg_match('%\d{11}%s', $comment->name)) {
				$comment->name = substr_replace($comment->name, '******', 3, 6);
			}
			$ret[] = $comment->attributes;
		}
		$ret['total'] = $count;
		return $ret;
	}
	
	public function beforeSave() {
		if (parent::beforeSave()) {
			$this->insert_time = time();
			return true;
		}
	}
	
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		$startTime = isset($_REQUEST['startTime']) ? $_REQUEST['startTime'] : 0;
		$endTime = isset($_REQUEST['endTime']) ? $_REQUEST['endTime'] : 0;
		$mobile = isset($_REQUEST['mobile']) ? $_REQUEST['mobile'] : 0;
		$star = isset($_REQUEST['star']) ? $_REQUEST['star'] : '';
		$starType = isset($_REQUEST['starType']) ? $_REQUEST['starType'] : 0;
		$orderStatus = isset($_REQUEST['orderStatus']) ? $_REQUEST['orderStatus'] : '';
		
		$criteria = new CDbCriteria();
		
		$criteria->select = "t.id, t.employee_id, t.name, t.level, t.status, t.comments, 
						t.order_status, t.insert_time, t_driver.name AS uuid";
		$criteria->join = 'JOIN t_driver ON t_driver.imei = t.employee_id';
		
		if (isset($this->employee_id)&&trim($this->employee_id)!='') {
			$criteria->compare('t_driver.user', $this->employee_id, true);
		}
	
		if($this->uuid > 0){
			$criteria->compare('t_driver.city_id', $this->uuid);
		}
		$criteria->compare('t.name', $this->name, true);
// 		if ($this->level!=0) {
// 			$criteria->compare('t.level', $this->level);
// 		}
		if($startTime!=0){
			$criteria->addCondition('t.insert_time>="'.$startTime.' 00:00:00"');
		}
		if($endTime!=0){
			$criteria->addCondition('t.insert_time<"'.$endTime.' 23:59:59"');
		}
		if($mobile!=0){
			$criteria->addCondition('t_driver.phone="'.$mobile.'"');
		}
		if($orderStatus!=''){
			$criteria->addCondition('t.order_status='.$orderStatus);
		}
		if($starType!=0&&$star!=''){
			if($starType=='1'){
				$criteria->addCondition('t.level>='.$star);
			}else if($starType=='2'){
				$criteria->addCondition('t.level<='.$star);
			}else if($starType=='3'){
				$criteria->addCondition('t.level='.$star);
			}
		}
		
		$criteria->addCondition(" t.employee_id<>'' "); 
		if($this->status!=''){
			$criteria->compare('status', $this->status);
		}
		
		$criteria->order = 't.id desc';
		//$criteria->params = $params;
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria, 
			'pagination'=>array (
				'pageSize'=>50)));
	
	}
	
	/**
	 * 
	 * 获取评论信息
	 * @param $params 查询条件参数
	 */
	public function getComments($params){
		$criteria_m = new CDbCriteria();
		$criteria_m->condition = "uuid = :uuid";
		$criteria_m->addCondition("status = 0");
		$criteria_m->order = "id desc";
		$criteria_m->params = $params;
		$criteria_m_info = $this->find($criteria_m);
		return $criteria_m_info;
	}
}