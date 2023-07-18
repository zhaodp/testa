<?php

/**
 * This is the model class for table "{{material_money}}".
 *
 * The followings are the available columns in table '{{material_money}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $equipment_deposit
 * @property string $cellphone_deposit
 * @property string $simcard_deposit
 * @property string $cash_deposit
 * @property string $invoice
 * @property integer $status
 * @property string $leave_time
 * @property string $update_time
 * @property string $create_time
 */
class MaterialMoney extends CActiveRecord
{
    CONST STATUS_NORMAL = 0;
    CONST STATUS_UNSIGNED = -1;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{material_money}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver_id', 'required'),
            array('status', 'numerical', 'integerOnly'=>true),
            array('driver_id,city_id', 'length', 'max'=>12),
            array('operator', 'length', 'max'=>15),
            array('equipment_deposit, cellphone_deposit, simcard_deposit, cash_deposit, invoice,total', 'length', 'max'=>10),
            array('leave_time, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, driver_id,  status, leave_time, update_time, create_time', 'safe', 'on'=>'search'),
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
            'city_id'=> 'cityid',
            'equipment_deposit' => '装备押金',
            'cellphone_deposit' => '手机押金',
            'simcard_deposit' => '手机卡',
            'cash_deposit' => '保证金',
            'invoice' => '发票',
            'total' => '总金额',
            'status' => '状态',
            'operator' => '操作人',
            'leave_time' => '解约时间',
            'update_time' => '更新时间',
            'create_time' => '签约时间',
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
        $criteria->compare('driver_id',$this->driver_id);
        $criteria->compare('status',$this->status);
        $criteria->compare('leave_time',$this->leave_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MaterialMoney the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getInfoByDriverId($driver_id){
        $criteria = new CDbCriteria() ;
        $criteria -> condition = 'driver_id = :did';
        $criteria -> order = 'id desc';
        $criteria ->params = array (':did' => $driver_id) ;
        return $this->find($criteria);
    }


    public function changeDriverId($recruitment_id,$driver_id){
        $sql = 'update '.$this->tableName().' set driver_id = "'.$driver_id.'" where driver_id = "'.$recruitment_id.'"';
        $command = Yii::app()->db->createCommand($sql);
        $res = $command->execute();

        if(!$res){ EdjLog::info('materialMoney update field  driver_id '.$driver_id.' recruitment_id : '.$recruitment_id); }
        return $res;
    }
}