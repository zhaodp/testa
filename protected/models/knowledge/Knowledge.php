<?php

/**
 * This is the model class for table "{{knowledge}}".
 *
 * The followings are the available columns in table '{{knowledge}}':
 * @property string $id
 * @property string $title
 * @property integer $typeid
 * @property integer $catid
 * @property integer $category_pid
 * @property integer $category_cid
 * @property string $keywords
 * @property string $description
 * @property integer $is_case
 * @property integer $status
 * @property integer $praise_num
 * @property integer $listorder
 * @property string $operator
 * @property string $updated
 * @property string $created
 */
class Knowledge extends CActiveRecord
{

    const NOT_AUDIT_STATUS = 1;

    const APPROVED_STATUS = 2;

    const RECYCLING_STATUS = 3;

    const PICTURE_HOST = "http://edaijia.b0.upaiyun.com";

    const PIC_BASE_PATH = 'knowledge';

    CONST PICTURE_NORMAL = 'normal'; //限定宽度(400px)，高度自适应 ，质量: 95 + 锐化

    public $city_id = '';
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Knowledge the static model class
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
		return '{{knowledge}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title,description,typeid,city_id', 'required'),
			array('typeid, catid, is_case, status, praise_num, listorder', 'numerical', 'integerOnly'=>true),
			array('title, description,driver_desc,customer_desc', 'length', 'max'=>100),
			array('keywords', 'length', 'max'=>40),
			array('category_pid, category_cid, operator', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, typeid, catid, category_pid, category_cid, keywords, description,driver_desc,customer_desc,citylist , is_case, status, praise_num, listorder, operator, updated', 'safe', 'on'=>'search'),
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
			'id' => '序号',
			'title' => '标题',
			'typeid' => '类型',
			'city_id' => '城市',
            'catid' => '分类',
            'category_cid'  => '二级分类',
            'category_pid'  => '一级分类',
            'keywords' => '关键字',
			'description' => '通用正文',
			'is_case' => '是否有案例',
			'status' => '审核状态',
			'praise_num' => '顶次数',
			'listorder' => '排序',
			'operator' => '操作人',
			'updated' => '修改时间',
			'created' => '创建时间',
			'driver_desc' => '面向司机',
			'customer_desc' => '面向客户',
			'city_id' => '城市',

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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('title',$this->title,true);
        if($this->typeid != 0){
		    $criteria->compare('typeid',$this->typeid);
        }

        if ($this->catid= 0) {
            $criteria->compare('catid,',$this->catid);
        }

        if ($this->category_pid= '') {
		    $criteria->compare('category_pid,',$this->category_pid);
        }

        if ($this->category_cid= '') {
            $criteria->compare('category_cid,',$this->category_cid);
        }
		$criteria->compare('citylist',$this->citylist,true);
		$criteria->compare('keywords',$this->keywords,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('driver_desc',$this->driver_desc,true);
		$criteria->compare('customer_desc',$this->customer_desc,true);
        if ($this->is_case != 0) {
		    $criteria->compare('is_case',$this->is_case);
        }

        if ($this->status != 0) {
		    $criteria->compare('status',$this->status);
        }else{
            $criteria->addInCondition('status',array(self::NOT_AUDIT_STATUS, self::APPROVED_STATUS));
        }
//		$criteria->compare('praise_num',$this->praise_num);
//		$criteria->compare('listorder',$this->listorder);
//		$criteria->compare('operator',$this->operator,true);
//		$criteria->compare('updated',$this->updated,true);
//		$criteria->compare('created',$this->created,true);
        $criteria->order = "id desc";
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 50
            ),
		));
	}


    public function search_index($params){
        $criteria = new CDbCriteria;
        if (!empty($params['title'])) {
            $criteria->compare('title', $params['title'], true);
        }

        if (isset($params['category_pid, category_cid'])) {
            $criteria->compare('category_pid, category_cid', $params['category_pid, category_cid'], true);
        }

        if (isset($params['id'])) {
            $criteria->compare('id', $params['id'], true);
        }
        $criteria->addCondition('status = 2');
        $criteria->order = "id desc";
        return new CActiveDataProvider('Knowledge', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10
            ),
        ));
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->operator = Yii::app()->user->getId();
            if ($this->isNewRecord) {
                $this->created = date('Y-m-d H:i:s');
                $this->updated = date('Y-m-d H:i:s');
            }else{
                $this->updated = date('Y-m-d H:i:s');
            }
            //var_dump($this->getAttributes());exit;
            return true;
        }

    }

    /**
     * 根据不同状态获取不通的知识条数
     * @param $status
     * @return mixed
     * author mengtianxue
     */
    public function getAudit($status)
    {
        $where = '';
        $params = array();
        if($status != 0){
            $where .='status = :status';
            $params[':status'] = $status;
        }
        $num = Yii::app()->db_readonly->createCommand()
            ->select("count(*)")
            ->from("t_knowledge")
            ->where($where, $params)
            ->queryScalar();
        return $num;
    }



    public function getKnowledgeByCat($cat_id){
        $arr = Yii::app()->db_readonly->createCommand()
            ->select("*")
            ->from("t_knowledge")
            ->where(' category_cid = :category_cid and status = :status', array(':category_cid' => $cat_id,':status' => self::APPROVED_STATUS))
            ->order("praise_num")
            ->limit("5")
            ->queryAll();
        return $arr;
    }

    public function getKnowledgeById($id){
        return Yii::app()->db_readonly->createCommand()
                    ->select("*")
                    ->from('{{knowledge}}')
                    ->where('id = :id', array(":id" => $id))
                    ->queryRow();
    }

    /**
     * 获得知识库地址
     * @param string $pic_path 路径
     * @param string $pic_name   图片名称
     * @param string $size  'normal'; //限定宽度(400px)，高度自适应 ，质量: 95 + 锐化
     * @return string
     */
    public static function getPictureUrl($pic_path, $pic_name, $size='400', $version=false)
    {
        $url = self::PICTURE_HOST . '/' .$pic_path . '/' . $pic_name . '.jpg_' . $size;
        if ($version) {
            $url = $url.'?ver='.time();
        }
        return $url;
    }

    /**
     * 获取知识库文章列表
     * $params  $pid 一级分类id $cid 二级分类id
     * $return array
     */
    public static function getKnowledgeList($pid, $cid,$city_id=0,$pageSize=10,$offset, $refresh = true)
    {
        $cache_key = 'KNOWLEDGE_LIST_' . $pid . '_' .$cid . '_'.$pageSize . "_" . $offset;
        $json = Yii::app()->cache->get($cache_key);

        if (!$json||$json=='[]'|| $refresh)
        {
            $sql = "select
                  {columns}
            from t_knowledge as tk
            join  t_knowledge_city_map as tkcm
            on tk.id=tkcm.knowledge_id
            where tkcm.city_id=:city_id and tk.category_pid=:category_pid
            ";
            $params =array(
                'category_pid' => $pid,
                'city_id' => $city_id,
            );
            if(!empty($cid)){
                $sql .= " and tk.category_cid=:category_cid";
                $params['category_cid'] = $cid;
            }
            $countSql = str_replace('{columns}','count(*) count',$sql) . ";";
            $count = Yii::app()->db_readonly->createCommand($countSql)->queryScalar($params);

            $sql .= " limit " . $offset . "," . $pageSize;
            $sql .= ";";
            $sql = str_replace('{columns}','tk.id,tk.title',$sql);
            $arr = Yii::app()->db_readonly->createCommand($sql)->queryAll(true, $params);
            $ret_data = array(
                'count'=>$count,
                'data' =>$arr
            );
            $json = json_encode($ret_data);
            Yii::app()->cache->set($cache_key, $json, 3600);
        }
        return json_decode($json);
    }

    /**
     * 获取知识库文章
     * @params knowledge_id
     * @return array
     */
    public function getContentById($kid, $refresh = false)
    {
        if(empty($kid)){
            return false;
        }
        $cache_key = 'KNOWLEDGE_CONTENT_' . $kid;
        $json = Yii::app()->cache->get($cache_key);

        if (!$json||$json=='[]'|| $refresh)
        {
            $sql = "select tk.title,tkd.content from t_knowledge as tk
            left join t_knowledge_data as tkd on tk.id=tkd.k_id
            where tk.id=:id";
            $params =array('id'=>$kid);
            $sql .=";";
            $arr = Yii::app()->db_readonly->createCommand($sql)->queryRow(true, $params);
            $json = json_encode($arr);
            Yii::app()->cache->set($cache_key, $json, 3600);
        }
        return json_decode($json);
    }

    /**
     *  获取知识库一级分类
     *  wanglonghuan 2013.11.08
     *  return array
     */
    public static  function getKnowledgeCategoryList()
    {
        $knowledgeCatList = Dict::items('knowledge_cat_driver');
        $ret = array();
        foreach($knowledgeCatList as $k => $v){
            if(!strpos($k,'_')){
                $ret[$k] = $v;
            }
        }
        return $ret;
    }
    /**
     * 获取二级分类 wanglonghuan 2013.11.8
     * @params pid int
     * @return array
     */
    public static function getChildCategoryList($pid)
    {
        if(empty($pid))
            return array();
        $knowledgeCatList = Dict::items('knowledge_cat_driver');
        $ret = array();
        foreach($knowledgeCatList as $k => $v){
           if(substr($k,0,strpos($k, '_')) == $pid){
              $ret[$k] = $v;
           }
        }
        return $ret;
    }
}