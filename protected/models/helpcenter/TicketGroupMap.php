<?php

/**
 * This is the model class for table "{{ticket_group_map}}".
 *
 * The followings are the available columns in table '{{ticket_group_map}}':
 * @property integer $id
 * @property integer $category_id
 * @property string $group
 * @property string $user
 * @property integer $status
 * @property integer $cursor
 * @property integer $cursor_sort
 */
class TicketGroupMap extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{ticket_group_map}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('category_id, group, cursor_sort, city_id', 'numerical', 'integerOnly'=>true),
			array('user', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, category_id, group, user, city_id,  cursor_sort', 'safe', 'on'=>'search'),
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
			'category_id' => '分类',
			'group' => '处理部门',
			'user' => '处理人',
			'cursor_sort' => '轮循排序',
            'create_time'=>'创建时间',
            'create_user'=>'创建人'
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

		$criteria->compare('id',$this->id);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('`group`',$this->group);
		$criteria->compare('`user`',$this->user);
		$criteria->compare('cursor_sort',$this->cursor_sort);
        $criteria->order = " id desc ";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TicketGroupMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * 根据分类获取 处理部门 用户关系 缓存
     * wanglonghuan 2013/12/18
     */
    public function loadMapsByCategory($category_id,$refresh = false)
    {
        $cache_key = 'TICKET_GROUP_MAP_' . $category_id;
        $json = Yii::app()->cache->get($cache_key);
        if (!$json||$json=='[]'||$refresh)
        {
            $models = self::model()->findAll(array(
                'condition'=>'category_id=:category_id',
                'params'=>array(
                    ':category_id'=>$category_id),
                'order'=>'cursor_sort'));
            $data = array();
            foreach($models as $k=>$v){
                $data[$v->cursor_sort] = array('group'=>$v->group,'user'=>$v->user);
            }
            $json = json_encode($data);
            Yii::app()->cache->set($cache_key, $json, 3600);
        }
        return json_decode($json, true);
    }
    /**
     * 获取 部门对应 处理人
     */
    public function loadMapsByGroup($group,$city_id,$refresh = false)
    {
        $cache_key = 'TICKET_GROUP_MAP_GROUP_' . $group;
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
            foreach($models as $k=>$v){
                $data[$v->cursor_sort] = array('group'=>$v->group,'user'=>$v->user);
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
        $maps = array();
        $follow_user = "";
        if(empty($group)){
            //load 处理部门处理人
            $maps = self::model()->loadMapsByCategory($category_id,true);
            $follow_user = SupportTicket::model()->getOperationUser($category_id);
        }else{
            $maps = self::model()->loadMapsByGroup($group,$city_id,true);
            $follow_user = SupportTicket::model()->getOperationUserByGroup($group);
        }
        if(empty($maps)){
            //没有或没有处理部门处理人关系
            return false;
        }
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
            $ret = $maps[$keys[0]];
        }

        return $ret;
    }

    //获取当前用户的组
    public static function getGroupByUser($name = '')
    {
        $map_model = self::model()->find("user=:user",array('user'=>$name));
        if(!empty($map_model)){
            return  $map_model->group;
        }else{
            return '';
        }
    }


}
