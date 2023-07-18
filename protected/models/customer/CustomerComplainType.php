<?php

/**
 * This is the model class for table "{{customer_complain_type}}".
 *
 * The followings are the available columns in table '{{customer_complain_type}}':
 * @property integer $id
 * @property integer $parent_id
 * @property string $full_id
 * @property string $type_name
 * @property string $create_time
 * @property string $update_time
 * @property string $operator
 * @property integer $group_id
 */
class CustomerComplainType extends CActiveRecord
{
    CONST DELETE_STATUS=2;
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
        return '{{customer_complain_type}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, create_time, operator, should_response_hour, should_follow_hour, should_closing_hour,group_id', 'required'),
            array('parent_id, status,score,group_id', 'numerical','integerOnly'=>true),
            array('full_id', 'length', 'max'=>100),
            array('name', 'length', 'max'=>40),
            array('weight,score', 'length', 'max'=>4),
            array('operator', 'length', 'max'=>20),
            array('update_time', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, parent_id, full_id, name, status, create_time, update_time, weight, operator,score, should_response_hour, should_follow_hour, should_closing_hour,group_id', 'safe', 'on'=>'search'),
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
            'parent_id' => 'Parent',
            'full_id' => 'Full',
            'name' => '分类名称',
            'category' => '大类',
            'status'=>'状态',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'weight' => '权重系数',
            'performance' => '绩效扣分',
            'operator' => 'Operator',
            'score' => '司机扣代驾分',
            'should_response_hour' => '投诉响应时间（小时）',
            'should_follow_hour' => '投诉跟进时间（小时）',
            'should_closing_hour' => '投诉结案时间（小时）',
            'group_id' => '投诉任务组'
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

        $criteria->compare('id',$this->id);
        $criteria->compare('parent_id',$this->parent_id);
        $criteria->compare('full_id',$this->full_id,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('weight',$this->weight,true);

        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('score',$this->score,true);
        $criteria->compare('group_id',$this->group_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * 获取投诉分类
     * @param $id 分类id
     * @return string
     */
    public function getCtypeByID($id)
    {
        $data = '';
        if(isset($id)){
            $condition = 'id=:id';
            $params = array(':id'=>$id);
            $data = self::model()->find($condition,$params);
        }
        return $data;
    }

    /**
     * 获取子分类的父分类信息
     * @param $id
     * @return string
     */
    public function getParentTypeById($id)
    {
        $type = self::getTypeById($id);
        if ($type['parent_id']!=0) {
            $parentType = self::getTypeById($type['parent_id']);
        } else {
            return false;
        }
        return $parentType;
    }

    /**
     * 根据id获取分类信息
     * @param $id
     * @return string
     */
    public function getTypeById($id)
    {
        $data='';
        if(isset($id)){
            $condition = 'id=:id';
            $params = array(':id'=>$id);
            $data = self::model()->find($condition,$params);
        }
        return $data;
    }

    public function getComplainType($id){
        $data = '';
        if(isset($id)){
            $condition = 'status=:status and id=:id';
            $params = array(':status'=>1,':id'=>$id);
            $data = self::model()->findAll($condition,$params);

        }
        return $data;
    }

    public function getComplainTypeAll($id){
        $data='';
        if(isset($id)){
            $condition='id=:id';
            $params=array(':id'=>$id);
            $data= self::model()->findAll($condition,$params);

        }
        return $data;
    }
    /**
     * 根据父类ID获取分类
     * @author bidong
     * @param $pid
     * @return array
     */
    public function getComplainTypeByID($pid){
        $ret=array();
        if(isset($pid)){
            $condition='status=:status and parent_id=:pid';
            $params=array(':status'=>1,':pid'=>$pid);
            $data= self::model()->findAll($condition,$params);
            if($data)
                $ret=$data;
        }
        return $ret;
    }

    /**
     * 获取所有大类
     * @return mixed
     */
    public function getComplainCategory()
    {
        $commond = Yii::app()->db_readonly->createCommand();

        $cate = $commond->select('category')->from(self::tableName())->group('category')->queryAll();
        return $cate;
    }

    /**
     * 根据大类获取子分类
     * @param $category
     * @return mixed
     */
    public function getSubTypeByCategory($category)
    {
        $commond = Yii::app()->db_readonly->createCommand();

        $subType = $commond->select('id,name')->from(self::tableName())->where('category=:cate',array(':cate'=>$category))->queryAll();
        return $subType;
    }

    /**
     * 根据父分类id和大类获取子分类
     * @param $pid
     * @param $category
     * @return mixed
     */
    public function getSubTypeByIDAndCategory($pid, $category)
    {
        $commond = Yii::app()->db_readonly->createCommand();

        $subType = $commond->select('id,name')->from(self::tableName())->where('parent_id=:pid and category=:cate',array(':pid'=>$pid,':cate'=>$category))->queryAll();
        return $subType;
    }

    /**
     * 根据父类ID获取分类,包括屏蔽的
     * @author bidong
     * @param $pid
     * @return array
     */
    public function getComplainTypeAllByID($pid){
        $ret=array();
        if(isset($pid)){
            $condition='parent_id=:pid order by status';
            $params=array(':pid'=>$pid);
            $data= self::model()->findAll($condition,$params);
            if($data){
                foreach ($data as $type) {
                $name=$type['name'];
                if($type['status'] == self::DELETE_STATUS){
                    $name=$name.'(已屏蔽)';
                }
                $ret[]=array(
                    'id'=>$type['id'],
                    'name'=>$name,
                    );                
                }
            }
                
        }
        return $ret;
    }

    public function getComplainTypeList(){
        $retType=array();
        $menuMain=$this->getComplainTypeByID(0);
        foreach($menuMain as $item){
            $tempArr=array();
            $id=$item->id;
            $tempArr['id']=$id;
            $tempArr['parent_id']=$item->parent_id;
            $tempArr['full_id']=$item->full_id;
            $tempArr['name']=$item->name;
            $tempArr['category']=$item->category;
            $tempArr['status']=$item->status;
            $tempArr['create_time']=$item->create_time;
            $tempArr['update_time']=$item->update_time;
            $tempArr['weight']=$item->weight;
            $tempArr['performance']=$item->performance;
            $tempArr['operator']=$item->operator;
            $tempArr['score']=$item->score;
            $tempArr['should_response_hour']=$item->should_response_hour;
            $tempArr['should_follow_hour']=$item->should_follow_hour;
            $tempArr['should_closing_hour']=$item->should_closing_hour;
            $tempArr['group_id']=$item->group_id;

            $retType[]=$tempArr;
            $menuSub=$this->getComplainTypeByID($id);
            if($menuSub){
                foreach($menuSub as $m){
                    $temp=array();
                    $temp['id']=$m->id;
                    $temp['parent_id']=$m->parent_id;
                    $temp['full_id']=$m->full_id;
                    $temp['name']=$m->name;
                    $temp['category']=$m->category;
                    $temp['status']=$m->status;
                    $temp['create_time']=$m->create_time;
                    $temp['update_time']=$m->update_time;
                    $temp['weight']=$m->weight;
                    $temp['performance']=$m->performance;
                    $temp['operator']=$m->operator;
                    $temp['score']=$m->score;
                    $temp['should_response_hour']=$m->should_response_hour;
                    $temp['should_follow_hour']=$m->should_follow_hour;
                    $temp['should_closing_hour']=$m->should_closing_hour;
                    $temp['group_id']=$m->group_id;

                    $retType[]=$temp;
                }
            }

        }
        return $retType;
    }

    /**
     * 获取所有子分类
     * @return array
     */
    public function getAllSubType() {
        $retType = array();
        $main = $this->getComplainTypeByID(0);
        $i = 0;
        foreach ($main as $item) {
            $id = $item->id;

            $sub = $this->getComplainTypeByID($id);
            if ($sub) {
                foreach($sub as $m){
                    $retType[$i]['id']=$m->id;
                    $retType[$i]['name']=$m->name;
                    $i ++;
                }
            }
        }
        return $retType;
    }


    /**
    *   add by aiguoxin
    *   get rule
    */
     public function getRuleList($page,$pageSize){
        $limitStart = ($page-1)*$pageSize;
        $rulelist = Yii::app()->db_readonly->createCommand()
            ->select("name,score")
            ->from('t_customer_complain_type')
            ->where('parent_id != 0 and score>0 and status=1')
            ->order('id')
            ->limit($pageSize)
            ->offset($limitStart)
            ->queryAll();

        return $rulelist;
    }

    /*
    *   add by aiguoxin
    *   get rule all count
    */
    public function getRuleCount(){
        $command = Yii::app()->db_readonly->createCommand()
            ->select('COUNT(id)')
            ->from('t_customer_complain_type')
            ->where('parent_id !=0 and score>0 and status=1');
        $query = $command->queryScalar();
        return $query;
    }


}
