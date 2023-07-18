<?php

/**
 * This is the model class for table "{{material_log}}".
 *
 * The followings are the available columns in table '{{material_log}}':
 * @property integer $id
 * @property string $driver_id
 * @property integer $type_id
 * @property integer $m_id
 * @property string $group_id
 * @property string $type_name
 * @property string $name
 * @property integer $quantity
 * @property integer $money
 * @property integer $status
 * @property string $remark
 * @property string $operator
 * @property string $ip
 * @property string $create_time
 */
class MaterialMoneyLog extends CActiveRecord
{

    CONST STATUS_LOST = -1;
    CONST STATUS_APPLY = 0;
    CONST STATUS_CHANGE = 1;
    CONST STATUS_BUY = 2;
    CONST STATUS_GIVE = 3;
    CONST STATUS_RECYCLE = 4;

    public $start_time = '';
    public $end_time = '';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{material_money_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('status,city_id', 'numerical', 'integerOnly'=>true),
            array('driver_id', 'length', 'max'=>12),
            array('money,equipment_deposit, cellphone_deposit, simcard_deposit, cash_deposit, invoice', 'length', 'max'=>10),
            array('ip', 'length', 'max'=>32),
            array('content', 'length', 'max'=>512),
            array('remark', 'length', 'max'=>128),
            array('operator', 'length', 'max'=>15),
            array('create_time,mark_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, driver_id, content, money, status, remark, operator, ip, mark_time', 'safe', 'on'=>'search'),
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
            'city_id'=>'城市',
            'content' => '详情',
            'money' => '金额',
            'equipment_deposit' => '装备押金',
            'cellphone_deposit' => '手机押金',
            'simcard_deposit' => '手机卡',
            'cash_deposit' => '保证金',
            'invoice' => '发票',
            'status' => '状态',
            'remark' => '备注',
            'operator' => '操作人',
            'ip' => 'Ip',
            'create_time' => '操作时间',
            'mark_time'=> '归属时间'
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
        $criteria=new CDbCriteria;
        $criteria->compare('id',$this->id);
        $criteria->compare('driver_id',$this->driver_id);
        $criteria->compare('money',$this->money);
        $criteria->compare('status',$this->status);
        $criteria->compare('city_id',$this->city_id);
        if($this->start_time){
            $criteria->addCondition('mark_time >= :start_time');
            $criteria->params[':start_time'] = $this->start_time;
        }
        if($this->end_time){
            $criteria->addCondition('mark_time <= :end_time');
            $criteria->params[':end_time'] = $this->end_time;
        }
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 30
            ))
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MaterialMoneyLog the static model class
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
            self::STATUS_LOST=>'遗失',
        );

        if($status !== ''){
            if(isset($status_array[$status]))
                return $status_array[$status];
            else return false;

        }
        return $status_array;
    }

    public function changeDriverId($recruitment_id,$driver_id){
        $sql = 'update '.$this->tableName().' set driver_id = "'.$driver_id.'" where driver_id = "'.$recruitment_id.'"';
        $command = Yii::app()->db->createCommand($sql);
        $res = $command->execute();

        if(!$res){ EdjLog::info('materialmoneylog update field  driver_id '.$driver_id.' recruitment_id : '.$recruitment_id); }
        return $res;
    }

    public function statData($field,$condition){
        $sql = 'select '.$field . ' from '.$this->tableName().' where '.$condition;
        $low_command = Yii::app()->db_readonly->createCommand($sql);
        $low_data = $low_command->queryRow();
        return $low_data;
    }

}