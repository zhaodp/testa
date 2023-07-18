<?php

/**
 * This is the model class for table "{{driver_export_log}}".
 *
 * The followings are the available columns in table '{{driver_export_log}}':
 * @property integer $id
 * @property string $name
 * @property integer $total
 * @property string $order_time
 * @property string $update_time
 */
class DriverExportLog extends CActiveRecord
{
    const CARD_EXPORT_TYPE=0; //工卡导出
    const LOGISTIC_EXPORT_TYPE=1;//物流导出


    CONST STATUS_EXPORTING = 0;
    CONST STATUS_FINISH = 1;
    CONST STATUS_FIELD = 2;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_export_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('update_time', 'required'),
            array('total,type,status', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>20),
            array('order_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, total, order_time, update_time, type, url, status', 'safe', 'on'=>'search'),
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
            'name' => 'Name',
            'total' => '订单数量',
            'order_time' => '订单时间',
            'update_time' => '更新时间',
            'type' => '导出类型',
            'url' => '下载链接',
            'status' => '导出状态',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('total',$this->total);
        $criteria->compare('order_time',$this->order_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('url',$this->url,true);
        $criteria->compare('status',$this->status,true);
        $criteria->order = 'id desc';

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverExportLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
     * @param $params
     * @return mixed
     * 增加导入日志
     */
    public function addExportLog($params){
        $driverExportLog = new DriverExportLog();
        $driverExportLog->name = $params['name'];
        $driverExportLog->total = $params['total'];
        $driverExportLog->order_time = date("Y-m-d H:i:s");
        $driverExportLog->type = $params['type'];
        isset($params['url']) && $driverExportLog->url = $params['url'];
        $res = $driverExportLog->save(false);
        if($res){
            return $driverExportLog->id;
        }
        return false;
    }


    /**
     * 获取用户状态列表
     * @param string $status
     * @return array|bool
     */
    public static function getStatus($status = ''){
        $status_array = array();

        $status_array[self::STATUS_EXPORTING] = '导出中';
        $status_array[self::STATUS_FINISH] = '已完成';
        $status_array[self::STATUS_FIELD] = '失败';
        if($status !== ''){
            if(isset($status_array[$status]))
                return $status_array[$status];
            else return false;

        }
        return $status_array;
    }
}
