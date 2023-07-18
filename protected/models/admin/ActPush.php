 <?php

/**
 * This is the model class for table "{{act_push}}".
 *
 * The followings are the available columns in table '{{act_push}}':
 * @property integer $id
 * @property string $content
 * @property string $url
 * @property string $citys
 * @property integer $customer_type
 * @property integer $phone_type
 * @property integer $status
 * @property string $app_ver
 * @property string $create_time
 * @property string $push_time
 * @property string $update_time
 */
class ActPush extends CActiveRecord
{
    public static $status=array(
        0=>'待推送',
        1=>'已推送',
        2=>'已取消',
        );
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{act_push}}';
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
            array('platform, status', 'numerical', 'integerOnly'=>true),
            array('title', 'length', 'max'=>128),
            array('content', 'length', 'max'=>128),
            array('url, customer_type', 'length', 'max'=>255),
            array('citys', 'length', 'max'=>500),
            array('app_ver', 'length', 'max'=>16),
            array('create_time, push_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, title, content, url, citys, customer_type, platform, status, app_ver, create_time, push_time, update_time', 'safe', 'on'=>'search'),
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
            'title' => 'Title',
            'content' => 'Content',
            'url' => 'Url',
            'citys' => 'Citys',
            'customer_type' => 'Customer Type',
            'platform' => 'platform',
            'status' => 'Status',
            'app_ver' => 'App Ver',
            'create_time' => 'Create Time',
            'push_time' => 'Push Time',
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
        $criteria->order = " id desc ";
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
                    'pagination'=>array(
                        'pageSize'=>30,
                    ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ActPush the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}