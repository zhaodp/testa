<?php

/**
 * This is the model class for table "{{customer_complain_group}}".
 *
 * The followings are the available columns in table '{{customer_complain_group}}':
 * @property integer $id
 * @property string $name
 * @property string $operator
 * @property integer $status
 * @property string $created
 * @property string $updated
 */
class CustomerComplainGroup extends CActiveRecord
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
     *  是否默认，1 是
     */
    const DEFAULT_YES = 1;
    /**
     *  是否默认，2 否
     */
    const DEFAULT_NO = 2;

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
        return '{{customer_complain_group}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, created, operator', 'required'),
            array('status', 'numerical','integerOnly'=>true),
            array('name', 'length', 'max'=>64),
            array('operator', 'length', 'max'=>40),
            array('updated', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, status, created, updated, operator', 'safe', 'on'=>'search'),
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
            'name' => '分组名称',
            'status' => '状态',
            'created' => 'Created',
            'updated' => 'Updated',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('updated',$this->updated,true);

        $criteria->compare('operator',$this->operator,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * 创建任务组
     * @param $name 组名
     */
    public function saveGroup($name)
    {
        $group = $this->getGroupByName($name);
        if (!$group) {
            $model = new CustomerComplainGroup;
            $param['name'] = $name;
            $param['operator'] = Yii::app()->user->name;
            $param['created'] = date('Y-m-d H:i:s');
            $model->attributes = $param;
            if ($model->save()) {
                return true;
            }
        } else if ($group['status'] == self::STATUS_DEL) {
            $model = new CustomerComplainGroup;
            $model->updateByPk($group['id'],array('status'=>self::STATUS_NORMAL,'operator'=>Yii::app()->user->name));
            return true;
        }
        return false;
    }

    /**
     * 更新任务组
     * @param $id
     * @param $name
     * @return bool
     */
    public function updateGroup($id, $name)
    {
        $group = $this->getGroupById($id);
        if ($group && $group['status']==self::STATUS_NORMAL) {
            $model = new CustomerComplainGroup;
            $model->updateByPk($id,array('name'=>$name,'operator'=>Yii::app()->user->name));
            return true;
        }
        return false;
    }

    /**
     * 删除任务组
     * @param $id
     */
    public function deleteGroup($id)
    {
        $model = new CustomerComplainGroup;
        $model->updateByPk($id,array('status'=>self::STATUS_DEL));
        return true;
    }

    /**
     * 查询任务组
     * @param $id 组id
     * @return mixed
     */
    public function getGroupById($id)
    {
        $conditions = 'id=:id';
        $params = array(':id'=>$id);
        $group = Yii::app()->db_readonly->createCommand()->select('id,name,status')->from(self::tableName())->where($conditions,$params)->queryRow();
        return $group;
    }

    /**
     * 查询任务组
     * @param $name 组名
     * @return mixed
     */
    public function getGroupByName($name)
    {
        $conditions = 'name=:name';
        $params = array(':name'=>$name);
        $group = Yii::app()->db_readonly->createCommand()->select('id,name,status')->from(self::tableName())->where($conditions,$params)->queryRow();
        return $group;
    }

    /**
     * 获取所有状态正常的任务组
     * @return mixed
     */
    public function getAllGroup()
    {
        $conditions = 'status=:st';
        $params = array(':st'=>self::STATUS_NORMAL);
        $groups = Yii::app()->db_readonly->createCommand()->select('id,name,default')->from(self::tableName())->where($conditions,$params)->queryAll();
        return $groups;
    }

    /**
     * 获取默认任务组
     * @return mixed
     */
    public function getDefaultGroup()
    {
        $conditions = '`default`=:dt';
        $params = array(':dt'=>self::DEFAULT_YES);
        $group = Yii::app()->db_readonly->createCommand()->select('id,name')->from(self::tableName())->where($conditions,$params)->queryRow();
        return $group;
    }
}
