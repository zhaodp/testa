<?php

/**
 * This is the model class for table "{{admin_event}}".
 *
 * The followings are the available columns in table '{{admin_event}}':
 * @property integer $id
 * @property integer $author
 * @property string $title
 * @property string $begin
 * @property string $end
 * @property integer $type
 * @property string $description
 * @property string $created
 * @property string $updated
 * @property integer $is_delete
 * @property integer $status
 */
class AdminEvent extends CActiveRecord
{
    const STATUS_FINISHED = 2;

    public static $klass = array(
        0=>'my-task',
        1=>'send-task',
        2=>'public',
    );
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AdminEvent the static model class
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
		return '{{admin_event}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('title', 'required'),
			array('author, type, is_delete, status', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>256),
			array('description', 'length', 'max'=>1024),
			array('begin, end, created, updated', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, author, title, begin, end, type, description, created, updated, is_delete, status, created_begin, created_end', 'safe', 'on'=>'search'),
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
			'author' => 'Author',
			'title' => 'Title',
			'begin' => 'Begin',
			'end' => 'End',
			'type' => 'Type',
			'description' => 'Description',
			'created' => 'Created',
			'updated' => 'Updated',
            'is_delete' => 'Is Delete',
			'status' => 'Status',
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
		$criteria->compare('author',$this->author);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('begin',$this->begin,true);
		$criteria->compare('end',$this->end,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('updated',$this->updated,true);
        $criteria->compare('is_delete',  $this->is_delete);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
    
    public function beforeSave() {
        $time = date('Y-m-d H:i:s');
        if(parent::beforeSave()){
            if($this->isNewRecord){
                $this->author = Yii::app()->user->user_id;
                $this->created = $time;
                $this->is_delete = 0;
                $this->type = $this->type ? $this->type : 1;
            }
            $this->updated = $time;
            return TRUE;
        }
        return FALSE;
    }

    public function delete(){
        if(!$this->getIsNewRecord())
        {
            if($this->beforeDelete())
            {
                $this->is_delete = 1;
                $result = $this->update(array('is_delete'));
                $this->afterDelete();
                return $result;
            }
            else
                return false;
        }
        else
            throw new CDbException(Yii::t('yii','The active record cannot be deleted because it is new.'));
    }

    /**
     * 获取某个时间段内进行中的事项
     * @param $btime    开始时间（时间戳）
     * @param $etime     结束时间（时间戳）
     * @return array
     */
    public function getMyEventBetweenTimes($btime, $etime){
        $criteria = new CDbCriteria();
        $criteria->addCondition('begin <= :end');
        $criteria->addCondition('end >= :begin');
        $criteria->params = array(
            ':begin'=>date('Y-m-d H:i:s',$btime),
            ':end'=>date('Y-m-d H:i:s',$etime),
        );
        $criteria->compare('author', Yii::app()->user->user_id);
        $criteria->compare('is_delete',0);
        $models = AdminEvent::model()->findAll($criteria);
        return $models;
    }
    
    public function listEventByDays($btime, $etime){
        $dayEvent = array();
        $models = self::model()->getMyEventBetweenTimes($btime, $etime);
        $dayTime = $btime;
        while (true) {
            $day = date('Y-m-d', $dayTime);
            $dayTime += 86400;
            if($day > date('Y-m-d', $etime)){
                break;
            }
            foreach($models as $model){
                $begin = $day.' 00:00:00';
                $end = date('Y-m-d', $dayTime).' 00:00:00';
                if(($model->begin < $end && $model->end >= $begin)){
                    $dayEvent[$day][] = $model;
                }
            }
        }
        return $dayEvent;
    }

    /**
     * 返回klass属性值
     * @param int $kId
     * @param bool $isOut
     * @return string
     */
    public function getKlass($kId = 0, $isOut = false){
        $result = self::$klass[0];
        if(isset(self::$klass[$kId])){
            $result = self::$klass[$kId];
        }
        if($isOut){
            $result .= '-out';
        }
        return $result;
    }

    /**
     * 随机获取klass属性值
     * @return string
     */
    public function getRandKlass(){
        $kId = rand(0,count(self::$klass)-1);
        return $this->getKlass($kId);
    }
}