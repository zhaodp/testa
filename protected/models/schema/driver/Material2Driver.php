<?php

/**
 * This is the model class for table "{{material2driver}}".
 *
 * The followings are the available columns in table '{{material2driver}}':
 * @property integer $id
 * @property string $driver_id
 * @property integer $type_id
 * @property integer $m_id
 * @property integer $quantity
 * @property integer $status
 * @property string $operator
 * @property string $update_time
 * @property string $create_time
 */
class Material2Driver extends CActiveRecord
{

    CONST STATUS_LOST = -1;
    CONST STATUS_APPLY = 0;
    CONST STATUS_CHANGE = 1;
    CONST STATUS_BUY = 2;
    CONST STATUS_GIVE = 3;
    CONST STATUS_RECYCLE = 4;


    CONST GIFT_STATUS_NO = 0; //礼包状态：不是礼包
    CONST GIFT_STATUS_YES = 1; // 礼包状态 ：是礼包
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{material2driver}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver_id,type_id,m_id,status', 'required'),
            array('type_id, m_id, quantity, status,city_id,is_gift_bag', 'numerical', 'integerOnly'=>true),
            array('driver_id', 'length', 'max'=>12),
            array('operator', 'length', 'max'=>32),
            array('create_time,update_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(' driver_id, type_id, m_id, city_id,status, operator, update_time, create_time', 'safe', 'on'=>'search'),
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
            'driver_id' => '工号',
            'city_id' => '城市',
            'type_id' => '物料类型',
            'm_id' => '物料ID',
            'quantity' => '数量',
            'is_gift_bag'=>'礼包状态',
            'status' => '状态',
            'operator' => '操作人',
            'update_time' => '更新时间',
            'create_time' => '创建时间',
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

        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('type_id',$this->type_id);
        $criteria->compare('m_id',$this->m_id);
        $criteria->compare('quantity',$this->quantity);
        $criteria->compare('status',$this->status);
        $criteria->compare('operator',$this->operator,true);
        //$criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 30)
            )
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Material2driver the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function countByMid($m_id){
        return $this->count('m_id = :m_id ',array(':m_id'=> $m_id));
    }

    public function getInfoByDriverid($driver_id){
        $res = $this->findAll('driver_id = :did',array(':did'=>$driver_id));
        return $res;
    }

    public static function getstatus($status){
        $status_array = array(
            self::STATUS_APPLY=>'签约发放',
            self::STATUS_CHANGE=>'物料更换',
            self::STATUS_BUY=>'额外申领',
            self::STATUS_GIVE=>'补领/赠送',
            self::STATUS_RECYCLE=>'解约回收',
            self::STATUS_LOST => '遗失',
        );

        if($status !== ''){
            if(isset($status_array[$status]))
                return $status_array[$status];
            else return false;

        }
        return $status_array;
    }

    public static function getIsGiftStatus($status){
        $status_array = array(
            self::GIFT_STATUS_YES=>'是礼包',
            self::GIFT_STATUS_NO=>'不是礼包',
        );

        if($status !== ''){
            if(isset($status_array[$status]))
                return $status_array[$status];
            else return false;

        }
        return $status_array;
    }


    public function getDriverByCondition($params){
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('driver_id');
        $command->from($this->tableName());
        $condition = '';
        $param = array();
        if($params['city_id']) {
            $condition .= ' city_id = :city_id and ';
            $param[':city_id'] = $params['city_id'];
        }

        if($params['type_id']) {
            $condition .= ' type_id = :type_id and ';
            $param[':type_id'] = $params['type_id'];
        }

        if($params['m_id']) {
            $condition .= ' m_id = :m_id  ';
            $param[':m_id'] = $params['m_id'];
        }
        $command->where($condition, $param);
        $res = $command->queryAll();
        $re = array();
        if($res){
            foreach($res as $driver_id){
                $re[] = strtoupper($driver_id['driver_id']);
            }
        }
        return $re;
    }

    public function changeDriverId($recruitment_id,$driver_id){

        $sql = 'update '.$this->tableName().' set driver_id = "'.$driver_id.'" where driver_id = "'.$recruitment_id.'"';
        $command = Yii::app()->db->createCommand($sql);
        $res = $command->execute();
        if(!$res){ EdjLog::info('material2driver update field  driver_id '.$driver_id.' recruitment_id : '.$recruitment_id); }
        return $res;
    }


    public function stat($condition,$params){
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('count(distinct(driver_id)) as quantitys , m_id');
        $command->from($this->tableName());

        $command->where($condition, $params)
         ->order('id DESC')
         ->group('m_id');
        $res = $command->queryAll();
        return $res;
    }
}