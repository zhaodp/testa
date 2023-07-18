<?php

/**
 * This is the model class for table "{{notice}}".
 *
 * The followings are the available columns in table '{{notice}}':
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $class
 * @property integer $is_top
 * @property integer $created
 * @property string $deadline
 * @property string $top_period
 */
class Notice extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Notice the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{notice}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'city_id, category, title, content, deadline, class, top_period', 
				'required'
			), 
			array (
				'category, is_top, created, class', 
				'numerical', 
				'integerOnly'=>true
			),
            array('title', 'length', 'max'=>100),
            array('author', 'length', 'max'=>32),
            array('city_id', 'length', 'max'=>500),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, author, city_id, category, title, content, is_top, created, deadline, top_period, class', 
				'safe', 
				'on'=>'search'
			)
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
            'author'=>'创建人',
			'city_id'=>'城市',
			'class'=>'公告分类',
			'category'=>'内容分类', 
			'title'=>'标题', 
			'content'=>'内容', 
			'is_top'=>'是否置顶',
			'created'=>'创建时间',
			'deadline'=>'有效期截至时间',
			'top_period'=>'置顶有效期',
		);
	}
	
	public function beforeSave() {
		if (parent::beforeSave()) {
			if ($this->isNewRecord) {
				$this->created = time();
                $this->author = Yii::app()->user->id;
				return true;
			}else{
				return true;
			}
		}
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
        $criteria->compare('author', $this->author);
		$criteria->compare('city_id', $this->city_id, true);
		$criteria->compare('class', $this->class,true);
		$criteria->compare('category', $this->category, true);
		$criteria->compare('title', $this->title);
		$criteria->compare('content', $this->content);
		$criteria->compare('is_top', $this->is_top);
		$criteria->compare('deadline', $this->deadline,true);
		$criteria->compare('top_period', $this->top_period,true);

		if (!empty($this->created)) {
			$criteria->addCondition("FROM_UNIXTIME(created, '%Y-%m-%d') = :create");
			$criteria->params[':create'] = $this->created;
		}
		$criteria->order = 'is_top Desc,id DESC';
		//$criteria->select = "id, CASE WHEN category = 0 THEN '公告' ELSE '培训教材' END AS category, title, FROM_UNIXTIME(created, '%Y-%m-%d %H:%i') AS created";
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria, 
			'pagination'=>array (
				'pageSize'=>15
			)
		));
	}
	
	/**
	 * 获取是否有最新公告
	 */
	public function getNewest($date){
		$return = 0;
		if($date){
			if($date['city_id']==0){
				$notices = $this->find('id NOT IN (SELECT notice_id FROM t_notice_read WHERE driver_id=:driver_id) and
						(deadline>:nowTime AND city_id=0 OR city_id LIKE "%,0" OR city_id LIKE "%,0,%" OR city_id LIKE "0,%" OR city_id="1,2,3,4,5,6,7,8,9,10,11,12,13") ORDER BY is_top DESC,id DESC ', array (
								':driver_id'=>$date['driver_id'],
								':nowTime'=>date('Y-m-d H:i:s')));
			}else{
				$notices = $this->find('id NOT IN (SELECT notice_id FROM t_notice_read WHERE driver_id=:driver_id) and
						(city_id=0 or city_id=:city_id OR city_id LIKE :city_id_1 OR city_id LIKE :city_id_2 OR city_id LIKE :city_id_3) and deadline>:nowTime ORDER BY is_top DESC,id DESC ', array (
								':driver_id'=>$date['driver_id'],
								':city_id'=> $date['city_id'],
								':city_id_1'=> '%,'.$date['city_id'],
								':city_id_2'=> '%,'.$date['city_id'].',%',
								':city_id_3'=> $date['city_id'].',%',
								':nowTime'=>date('Y-m-d H:i:s')));
			}
			
			if ($notices)
				$return = $notices->id;
		}

		return $return;
	}
	
	/**
	 * 获取未读公告列表
	 */
	public function getNewestList($params){
		$return = 0;
		if($params){
			if($params['city_id']==0){
				$notices = Yii::app()->db_readonly->createCommand()
				->select("id, city_id, category, is_top, title, FROM_UNIXTIME(created, '%Y-%m-%d') as created")
				->from('t_notice')
				->where('id NOT IN (SELECT notice_id FROM t_notice_read WHERE driver_id=:driver_id) and
						(city_id=0 OR city_id LIKE "%,0" OR city_id LIKE "%,0,%" OR city_id LIKE "0,%" OR city_id="1,2,3,4,5,6,7,8,9,10,11,12,13")', array (
								':driver_id'=>$params['driver_id']))
								->order('is_top DESC ,id DESC')
								->limit($params['pageSize'])
								->offset($params['offset'])
								->queryAll();
			}else{
			$notices = Yii::app()->db_readonly->createCommand()
					->select("id, city_id, category, is_top, title, FROM_UNIXTIME(created, '%Y-%m-%d') as created")
					->from('t_notice')
					->where('id NOT IN (SELECT notice_id FROM t_notice_read WHERE driver_id=:driver_id) and (city_id = 0 or city_id=:city_id
							OR city_id LIKE :city_id_1 OR city_id LIKE :city_id_2 OR city_id LIKE :city_id_3)', array (
						':driver_id'=>$params['driver_id'], 
						':city_id'=>$params['city_id'],
						':city_id_1'=> '%,'.$params['city_id'],
						':city_id_2'=> '%,'.$params['city_id'].',%',
						':city_id_3'=> $params['city_id'].',%'))
					->order('is_top DESC ,id DESC')
					->limit($params['pageSize'])
					->offset($params['offset'])
					->queryAll();
			}
			
			
			
			if ($notices)
				$return = $notices;
		}
		return $return;
	}
	
	/**
	 * 获取公告内容
	 */
	public function getNoticeByClient($id){
		$notice = Yii::app()->db_readonly->createCommand()
					->select("id, city_id, category, is_top, title, content, FROM_UNIXTIME(created, '%Y-%m-%d %H-%i-%s') as created")
					->from('t_notice')
					->where('id =:id', array (
						':id'=>$id))
					->queryRow();
		
		if ($notice){
			return $notice;
		} else {
			return false;
		}
	}
	

	/**
	 * 按司机城市取公告,妹的公告写的一堆垃圾，不重写没法改了，空着不写了。
	 * @author sunhongjing 2013-07-10
	 * @param unknown_type $params
	 * @return mix
	 */
	public function getDriverCityNoticeList($params=array())
	{
		return true;	
	}
	
	
	public function getNoticeListByClient($params){
		if($params['city_id']==0){
			$noticeList = Yii::app()->db_readonly->createCommand()
			->select("id, city_id, category, is_top, title, FROM_UNIXTIME(created, '%Y-%m-%d %H-%i-%s') as created")
			->from('t_notice')
			->where('category=:category and deadline > :deadline and (city_id=0 OR city_id LIKE "%,0" OR city_id LIKE "%,0,%" OR city_id LIKE "0,%" OR city_id="1,2,3,4,5,6,7,8,9,10,11,12,13")', array (
					':category'=>$params['category'], ':deadline'=>date('Y-m-d',time())))
					->order('is_top DESC ,id DESC')
					->limit($params['pageSize'])
					->offset($params['offset'])
					->queryAll();
		}else{
				
			$noticeList = Yii::app()->db_readonly->createCommand()
					->select("id, city_id, category, is_top, title, FROM_UNIXTIME(created, '%Y-%m-%d %H-%i-%s') as created")
					->from('t_notice')
					->where('city_id = 0 or city_id =:city_id OR city_id LIKE :city_id_1 OR city_id LIKE :city_id_2 OR city_id LIKE :city_id_3 and category=:category and deadline > :deadline', array (
						':city_id'=>$params['city_id'],':city_id_1'=>'%,'.$params['city_id'],':city_id_2'=>'%,'.$params['city_id'].',%',':city_id_3'=>$params['city_id'].',%', ':category'=>$params['category'], ':deadline'=>date('Y-m-d',time())))
					->order('is_top DESC ,id DESC')
					->limit($params['pageSize'])
					->offset($params['offset'])
					->queryAll();
		}
		if ($noticeList){
			return $noticeList;
		} else {
			return false;
		}
	}
	
	/**
	 * 获取公告列表
	 */
	public function getNoticeList($data){


		$params = array();
		$criteria = new CDbCriteria();

		if(isset($data['category'])){
			$criteria->condition = 'category=:category';
			$params[':category'] = $data['category'];
		}
		if( $data['city_id'] != 0){
			//模糊查询
			//$criteria->condition="city_id LIKE  '%,".$data['city_id']."'OR city_id LIKE  '%,".$data['city_id'].",%' OR city_id LIKE  '".$data['city_id'].",%'";
			$criteria->addCondition('city_id = 0 or city_id=:city_id or city_id LIKE :city_id_1 OR city_id LIKE :city_id_2 OR city_id LIKE :city_id_3');
			$params[':city_id'] = $data['city_id'];
			$params[':city_id_1'] = '%,'.$data['city_id'];
			$params[':city_id_2'] = '%,'.$data['city_id'].',%';
			$params[':city_id_3'] = $data['city_id'].',%';
			
			
		}
		
		if (isset($data['class']) && $data['class']!=0){
			$criteria->addCondition('class in (0,:class)');
			$params[':class'] = $data['class'];
		}
		/**
		 * @author 李白阳
		 * @2013-04-22
		 * 修改bug
		 */
		if (!empty($data['title'])){
			$criteria->addCondition('title LIKE :title');
			$params[':title'] = '%' . $data['title'] . '%';
		}

		$criteria->params = $params;
		
		$date = date('H',time());
		if($date == '10'){
			$criterias = new CDbCriteria();
			$criterias->addCondition('is_top = 1 and top_period < :top_period');
			$top_period = date('Y-m-d H:i:s',time());
			$criterias->params = array(':top_period'=>$top_period);
			$notice = Notice::model()->find($criterias);
			
			if(!empty($notice)){
				$params = array();
				$update_sql = 'update t_notice set is_top = 0 where top_period < :top_period';
				$params[':top_period'] = date('Y-m-d H:i:s',time());
				$command = Yii::app()->db->createCommand($update_sql);
				$command->execute($params);
				$command->reset();
			}
			
		}
	//	print_r($criteria);exit;
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria, 
			'pagination'=>array (
				'pageSize'=>15), 
			'sort'=>array (
			'defaultOrder'=>'is_top Desc ,id DESC')));

	}
	
	/**
	 * 
	 * 获取不过期的公告内容
	 * @param int $id 公告ID
	 */
	public function getContent($id){
		$criteria = new CDbCriteria();
		//$criteria->addCondition('id=:id and deadline > :deadline');
		//$criteria->params = array(':id'=>$id,':deadline'=>date('Y-m-d H:i:s',time()));
		$criteria->addCondition('id=:id');
		$criteria->params = array(':id'=>$id);
		$criteria->order = 'id Desc';
		$notice = Notice::model()->find($criteria);
		return $notice;
	}
	
	/**
	 * 
	 * 获取公告管理列表
	 * @param array $date
	 */
	public function getNoticeLists($data){
		
		$params = array();
		$criteria = new CDbCriteria();

		if (isset($data['class']) && $data['class']!=0){
			$criteria->addCondition('class in (0,:class)');
			$params[':class'] = $data['class'];
		}
		
		if(isset($data['city_id']) && $data['city_id'] != 0){
			$criteria->addCondition('city_id = 0 or city_id=:city_id or city_id LIKE :city_id_1 OR city_id LIKE :city_id_2 OR city_id LIKE :city_id_3');
			$params[':city_id'] = $data['city_id'];
			$params[':city_id_1'] = '%,'.$data['city_id'];
			$params[':city_id_2'] = '%,'.$data['city_id'].',%';
			$params[':city_id_3'] = $data['city_id'].',%';
			
			
			
		}

		if (!empty($data['title'])){
			$criteria->addCondition('title LIKE :title');
			$params[':title'] = '%'.$data['title'].'%';
		}
		
		if(isset($data['is_valid']) && $data['is_valid'] != '0'){
			if($data['is_valid'] == '1'){
				$criteria->addCondition('FROM_UNIXTIME(deadline, "%Y-%m-%d") > :deadline');
				$params[':deadline'] = date('Y-m-d',time());
			}else{
				$criteria->addCondition('FROM_UNIXTIME(deadline, "%Y-%m-%d") < :deadline');
				$params[':deadline'] = date('Y-m-d',time());
			}
		}

		if(!empty($data['created'])){
			$criteria->addCondition("FROM_UNIXTIME(created, '%Y-%m-%d') = :create");
			$criteria->params[':create'] = $data['create'];
		}
		
		$criteria->params = $params;

		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria, 
			'pagination'=>array (
				'pageSize'=>15), 
			'sort'=>array (
			'defaultOrder'=>'is_top Desc ,id DESC')));
	}

}