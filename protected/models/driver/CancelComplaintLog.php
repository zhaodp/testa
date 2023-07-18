<?php
/**
 * User: zhanglimin
 * Date: 13-7-31
 * Time: 下午4:55
 *
 * This is the model class for table "{{cancel_complaint_log}}".
 *
 * The followings are the available columns in table '{{cancel_complaint_log}}':
 * @property string $id
 * @property string $driver_id
 * @property string $phone
 * @property integer $order_id
 * @property string $order_number
 * @property string $type
 * @property integer $cid
 * @property string $cid_desc
 * @property string $content
 * @property string $created
 */

class CancelComplaintLog extends CActiveRecord
{
    // TODO source 1 代表开车前 2 开车后


    public static $type = array(
        0 => 'order_cancel_type',
        1 => 'order_complaint_type',
    );

    public static $get_type_name = array(
        'order_cancel_type'=>"销单",
        'order_complaint_type'=>"投诉",
    );

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{cancel_complaint_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver_id, phone, created', 'required'),
            array('order_id, cid', 'numerical', 'integerOnly'=>true),
            array('driver_id', 'length', 'max'=>10),
            array('phone, order_number, type', 'length', 'max'=>20),
            array('cid_desc, content', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, driver_id, phone, order_id, order_number, type, cid, cid_desc, content, created ,source', 'safe', 'on'=>'search'),
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
            'phone' => '客户电话',
            'order_id' => '订单ID',
            'order_number' => '订单号',
            'type' => '类型',
            'cid' => 'Cid',
            'cid_desc' => '描述',
            'content' => '原因',
            'created' => '创建时间',
            'source' => '来源',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('order_number',$this->order_number,true);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('cid',$this->cid);
        $criteria->compare('cid_desc',$this->cid_desc,true);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('source',$this->source,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                 'pageSize'=>50,
                ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CancelComplaintLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     *
     * @param array $params
     * @return bool
     */
    public function insertLog($params = array()){
        if(empty($params)){
            return false;
        }
        $isComplaint = $params['isComplaint'] == 1  ? 1 : 0 ;
        unset($params['isComplaint']);
        $params['created'] = date("Y-m-d H:i:s");

        for($i=0;$i<=$isComplaint;$i++){

            $params['type'] = self::$type[$i];

            Yii::app()->db->createCommand()->insert("{{cancel_complaint_log}}",$params);
        }
        return true;
    }
}