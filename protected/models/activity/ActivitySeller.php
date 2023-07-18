<?php
/**
 * BD商家
 */
class ActivitySeller extends CActiveRecord
{

    static $industry = array(
        '0' => '请选择',
        '1' => '餐饮',
        '2' => '娱乐',
        '3' => '购物',
        '4' => '汽车',
        '5' => '其他',
    );
    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db_activity;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RedPacketLog the static model class
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
        return '{{activity_seller}}';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,province,city,phone', 'required'),
            array('details', 'length', 'max'=>50),
            array('industry,code,create_user,create_time', 'safe'),
        );
    }

    public function relations()
    {
        return array(
          //  'name'=>'商家名称',
            //'phone'=>'商家联系电话',
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(

        );
    }
}
