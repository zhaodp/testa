<?php

/**
 * This is the model class for table "{{material_log}}".
 *
 * The followings are the available columns in table '{{material_log}}':
 * @property integer $id
 * @property string $driver_id
 * @property integer $city_id
 * @property integer $type_id
 * @property integer $m_id
 * @property string $type_name
 * @property string $name
 * @property integer $quantity
 * @property integer $status
 * @property string $operator
 * @property string $ip
 * @property string $create_time
 * @property string $mark_time
 */
class MaterialLog extends CActiveRecord
{
    CONST STATUS_LOST = -1;
    CONST STATUS_APPLY = 0;
    CONST STATUS_CHANGE = 1;
    CONST STATUS_BUY = 2;
    CONST STATUS_GIVE = 3;
    CONST STATUS_RECYCLE = 4;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{material_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id, type_id, m_id, quantity, status', 'numerical', 'integerOnly'=>true),
            array('driver_id', 'length', 'max'=>12),
            array('type_name, ip', 'length', 'max'=>32),
            array('name', 'length', 'max'=>64),
            array('operator', 'length', 'max'=>15),
            array('create_time, mark_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(' driver_id, city_id,  mark_time', 'safe', 'on'=>'search'),
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
            'driver_id' => '司机工号',
            'city_id' => '城市ID',
            'type_id' => '物料类型',
            'm_id' => '物料ID',
            'type_name' => '物料类型名称',
            'name' => '物料明恒',
            'quantity' => '数量',
            'status' => '状态',
            'operator' => '操作人',
            'ip' => 'Ip',
            'create_time' => 'Create Time',
            'mark_time' => '标记时间',
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
        //$criteria ->select = 'm_id,type_id,name,type_name,sum(IF(`status`= '.self::STATUS_RECYCLE.',TRUE,NULL)) AS recycle_num,sum(IF(`status`!= '.self::STATUS_RECYCLE.',TRUE,NULL)) AS fafang,';
        //$criteria->compare('id',$this->id);
        $criteria->compare('driver_id',$this->driver_id);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('type_id',$this->type_id);
        $criteria->compare('m_id',$this->m_id);
        $criteria->compare('status',$this->status);
        $criteria->compare('mark_time',$this->mark_time,true);
        //$criteria->group = 'm_id';

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MaterialLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function getstatus($status){
        $status_array = array(
            self::STATUS_APPLY=>'签约发放',
            self::STATUS_CHANGE=>'物料更换',
            self::STATUS_BUY=>'额外申领',
            self::STATUS_GIVE=>'补领/赠送',
            self::STATUS_RECYCLE=>'解约回收',
            self::STATUS_LOST =>'遗失'
        );

        if($status !== ''){
            if(isset($status_array[$status]))
                return $status_array[$status];
            else return false;

        }
        return $status_array;
    }

    public function stat($condition,$params) {
        $data = Yii::app()->db_readonly->createCommand()
            ->select('m_id,sum(quantity) as quantitys')
            ->from($this->tableName())
            ->where($condition,$params)
            ->order('id DESC')
            ->group('m_id')
            //->limit(10)
            ->queryAll();
        return $data;
    }

    public function changeDriverId($recruitment_id,$driver_id){
        $sql = 'update '.$this->tableName().' set driver_id = "'.$driver_id.'" where driver_id = "'.$recruitment_id.'"';
        $command = Yii::app()->db->createCommand($sql);
        $res = $command->execute();

        if(!$res){ EdjLog::info('materialmoneylog update field  driver_id '.$driver_id.' recruitment_id : '.$recruitment_id); }
        return $res;
    }
}