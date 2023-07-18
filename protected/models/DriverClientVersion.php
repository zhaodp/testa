 <?php

/**
 * This is the model class for table "{{driver_client_version}}".
 *
 * The followings are the available columns in table '{{driver_client_version}}':
 * @property integer $id
 * @property string $name
 * @property string $beta_latest
 * @property string $beta_url
 * @property string $latest
 * @property string $deprecated
 * @property string $updatetime
 * @property string $url
 * @property string $create_time
 * @property string $update_time
 */
class DriverClientVersion extends CActiveRecord
{
    const DRIVER_CLIENT_VERSION_KEY = "driver_client_version_";
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_client_version}}';
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
            array('name, deprecated, latest, url, beta_latest, beta_url, updatetime,deprecated_int,latest_int', 'required'),
            array('name, beta_latest,operator, latest, deprecated', 'length', 'max'=>50),
            array('beta_url, url', 'length', 'max'=>255),
        	array('up_desc', 'length', 'max'=>500),
        	array('deprecated_int,latest_int', 'length', 'max'=>9),
        	//array('deprecated_int,latest_int', 'length', 'min'=>9),
        	//array('deprecated_int,latest_int', 'length', 'numerical', 'integerOnly'=>true),
            array('create_time,updatetime','safe'),
            array('deprecated,latest','verisonValidate'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, beta_latest, beta_url, latest, deprecated, updatetime, url, create_time, update_time,up_desc', 'safe', 'on'=>'search'),
        );
    }
    public function verisonValidate(){
        if(!$this->hasErrors())
        {
            if (!preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $this->deprecated)) {
                $this->addError('deprecated','版本号不符合规则');
            }
            if (!preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $this->latest)) {
                $this->addError('latest','版本号不符合规则');
            }

        }
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
            'name' => '版本名',
            'beta_latest' => '测试版本号(没有使用到)',
            'beta_url' => '测试url',
            'latest' => '当前版本',
        	'latest_int' => '当前版本(9位整形)',
            'deprecated' => '过期版本（小于等于这个版本的都强制升级）',
        	'deprecated_int' => '过期版本(9位整形)',
            'updatetime' => '更新时间',
            'url' => '当前版本下载Url',
            'operator'=>'operator',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        	'up_desc' => '升级文案（说明升级的内容）',
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
        $criteria->order = "id";
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
     * @return DriverClientVersion the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

     /**
    *  获取版本信息列表
    *
    */
    public function getList(){
        $sql = "SELECT * FROM t_driver_client_version";
       
        $command = Yii::app()->dbreport->createCommand($sql);
        $result = $command->queryAll();
        $data = array();
        foreach ($result as $version) {
            $data[$version['id']]=$version['name'];
        }
        return $data;
    }

}