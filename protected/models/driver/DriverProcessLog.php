<?php


/**
 * This is the model class for table "{{driver_process_log}}".
 *
 * The followings are the available columns in table '{{driver_process_log}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $search_stime
 * @property string $search_etime
 * @property integer $is_recoup
 * @property integer $is_leave
 * @property string $mark
 * @property integer $process_type
 * @property string $operator
 * @property string $create_time
 */
class DriverProcessLog extends CActiveRecord
{


    const TYPE_SHIELD=1; //shield unshield  follow
    const TYPE_UNSHIELD=2;
    const TYPE_FOLLOW=3;

    public static $processType=array('1'=>'屏蔽', '2'=>'解除屏蔽', '3'=>'跟进');



    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DriverProcessLog the static model class
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
        return '{{driver_process_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver_id, operator, create_time', 'required'),
            array('is_recoup, is_leave, process_type', 'numerical', 'integerOnly'=>true),
            array('driver_id, operator', 'length', 'max'=>10),
            array('mark', 'length', 'max'=>50),
            array('search_stime, search_etime', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, driver_id, search_stime, search_etime, is_recoup, is_leave, mark, process_type, operator, create_time', 'safe', 'on'=>'search'),
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
            'driver_id' => 'Driver',
            'search_stime' => 'Search Stime',
            'search_etime' => 'Search Etime',
            'is_recoup' => 'Is Recoup',
            'is_leave' => 'Is Leave',
            'mark' => 'Mark',
            'process_type' => 'Process Type',
            'operator' => 'Operator',
            'create_time' => 'Create Time',
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
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('search_stime',$this->search_stime,true);
        $criteria->compare('search_etime',$this->search_etime,true);
        $criteria->compare('is_recoup',$this->is_recoup);
        $criteria->compare('is_leave',$this->is_leave);
        $criteria->compare('mark',$this->mark,true);
        $criteria->compare('process_type',$this->process_type);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * 添加司机处理记录
     * @param $driver_id
     * @param $search_start_time
     * @param $search_end_time
     * @param $is_recoup
     * @param $is_leave
     * @param $mark
     * @return bool
     */
    public function createProcessLog($driver_id,$search_start_time,$search_end_time,$is_recoup,$is_leave,$mark,$type){

        $flag=false;
        $model=new DriverProcessLog();
        $model->driver_id=$driver_id;
        $model->search_stime=$search_start_time;
        $model->search_etime=$search_end_time;
        $model->is_recoup=intval($is_recoup);
        $model->is_leave=intval($is_leave);
        $model->mark=$mark;
        $model->process_type=$type;
        $model->operator=Yii::app()->user->id;
        $model->create_time=date('Y-m-d H:i:s',time());

        $ret=$model->insert();
        if($ret)
            $flag=true;

        return $flag;
    }

    /**
     * 获取司机因销单屏蔽次数
     * @param $driver_id
     */
    public function getProcessNum($driver_id){

        $block_num=0;
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('count(driver_id)');
        $command->from('{{driver_process_log}}');
        $command->where('driver_id = :driver_id and process_type=:type');

        $param=array(':driver_id' => $driver_id,':type'=>DriverProcessLog::TYPE_SHIELD);

        $block_num = $command->queryScalar($param);

        return $block_num;

    }
} 