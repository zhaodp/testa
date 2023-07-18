 <?php

/**
 * This is the model class for table "{{customer_suggestion_reply}}".
 *
 * The followings are the available columns in table '{{customer_suggestion_reply}}':
 * @property integer $id
 * @property integer $suggestion_id
 * @property string $content
 * @property integer $role
 * @property string $user
 * @property string $create_time
 * @property string $update_time
 */
class CustomerSuggestion extends CActiveRecord
{
    const TYPE_FEEDBACK=0; //  反馈
    const TYPE_COMPLAIN=1; // 投诉

    const STATUS_PROCESS=0;//处理中
    const STATUS_FINISH=1;//已处理   

      /**
     * @return CDbConnection database connection
     */
    public function getDbConnection()
    {
        return Yii::app()->dbreport;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_suggestion}}';
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
            array('status, type, opinion_id', 'numerical', 'integerOnly'=>true),
            array('phone', 'length', 'max'=>11),
            array('title', 'length', 'max'=>50),
            array('create_time,opinion_id', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, opinion_id, phone, title, status, type, create_time, update_time', 'safe', 'on'=>'search'),
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
            'title' => 'Title',
            'status' => 'Status',
            'type' => 'Type',
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
        $criteria->compare('title',$this->title,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('type',$this->type);
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
     * @return CustomerSuggestion the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
    *   获取用户反馈信息
    *
    */
    public function getMsgList($phone){

        $sql="select id,opinion_id,type,status,title,create_time,update_time from t_customer_suggestion
                        where phone=:phone order by update_time desc";
        $command = Yii::app()->dbreport->createCommand($sql);
        $command->bindParam(":phone", $phone);
        $msg_list = $command->queryAll();
        return $msg_list;
    }

    /**
    *   添加建议，需要添加主题表和回复表
    *
    */
    public function initSuggestion($phone,$title,$type,$opinion_id){
        $id = $this->addSuggestion($phone,$title,$type,$opinion_id);
        if($id){
            CustomerSuggestionReply::model()->addSuggestionReply($id,$title,CustomerSuggestionReply::ROLE_CUSTOMER,$phone);
        }
    }

    /**
    *   添加反馈或投诉
    *
    */
    public function addSuggestion($phone,$title,$type,$opinion_id){
        $model = new CustomerSuggestion;
        $model->phone=$phone;
        $model->title=$title;
        $model->type=$type;
        $model->opinion_id=$opinion_id;
        $model->create_time=date('Y-m-d H:i:s');
        $model->save(false);
        return $model->attributes['id'];
    }

    /**
    *   更新建议状态
    *   @param $id建议id,$status状态
    */
    public function updateStatus($id,$status){
        $sql = "UPDATE `t_customer_suggestion` SET `status` = :status WHERE id = :id";
        $res =  Yii::app()->dbreport->createCommand($sql)->execute(array(
            ':id' => $id,
            ':status' => $status,
        ));
        return $res;
    }

    /**
    *   根据意见ID和类型，找到反馈
    *
    */
    public function findSuggestionByTypeAndOpinionId($type,$opinion_id){
        $suggestion = Yii::app()->dbreport->createCommand()
            ->select("*")
            ->from("t_customer_suggestion")
            ->where("opinion_id = :opinion_id and type=:type", array(':opinion_id' => $opinion_id,':type'=>$type))
            ->queryRow();
        return $suggestion;
    }

    /**
    *   更新建议类型成投诉类型
    *
    *
    */
    public function updateTypeAndOpinionId($opinion_id,$new_opinion_id){
        $sql = "UPDATE `t_customer_suggestion` SET `type` = 1,`opinion_id`=:new_opinion_id WHERE opinion_id = :opinion_id and `type`=0";
        $res =  Yii::app()->dbreport->createCommand($sql)->execute(array(
            ':new_opinion_id' => $new_opinion_id,
            ':opinion_id'=>$opinion_id,
        ));
        return $res;
    }
}