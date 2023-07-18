 <?php

/**
 * This is the model class for table "{{message_send}}".
 *
 * The followings are the available columns in table '{{message_send}}':
 * @property integer $id
 * @property string $content
 * @property integer $type
 * @property integer $channel
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
/**
 * This is the model class for table "{{message_send}}".
 *
 * The followings are the available columns in table '{{message_send}}':
 * @property integer $id
 * @property string $phone
 * @property string $content
 * @property integer $type
 * @property integer $channel
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class MessageSend extends CActiveRecord
{
    const WASH_TYPE=1; //洗车类型
    const CHANNEL_MARKET = 0; //默认市场营销类 

    const HAS_SEND=1;//已经发送

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{message_send}}';
    }
    
    /**
     * @return CDbConnection database connection
     */
    public function getDbConnection()
    {
        return Yii::app()->dbreport;
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
            array('type, channel, status', 'numerical', 'integerOnly'=>true),
            array('phone', 'length', 'max'=>11),
            array('content', 'length', 'max'=>255),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, phone, content, type, channel, status, create_time, update_time', 'safe', 'on'=>'search'),
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
            'phone' => 'Phone',
            'content' => 'Content',
            'type' => 'Type',
            'channel' => 'Channel',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
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
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('channel',$this->channel);
        $criteria->compare('status',$this->status);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MessageSend the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
    *   插入发送数据
    *   channel=0默认批量营销短信
    */
    public function addMessageSend($phone,$content,$type,$channel=0){
        $model = new MessageSend();
        $data= array(
            'phone'=>$phone,
            'content'=>$content,
            'type'=>$type,
            'create_time'=>date('Y-m-d H:i:s'),
            );
        $model->attributes = $data;
        return $model->insert(false);
    }

    /**
    *   根据类型和电话查询
    *
    */
    public function findByTypeAndPhone($type,$phone){
        $message_send=$this->find('phone=:phone and type=:type',
         array(':phone'=>$phone,'type'=>$type));
        return $message_send;
    }
}