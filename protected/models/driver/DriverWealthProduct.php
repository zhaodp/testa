 <?php

/**
 * This is the model class for table "{{driver_wealth_product}}".
 *
 * The followings are the available columns in table '{{driver_wealth_product}}':
 * @property integer $id
 * @property string $name
 * @property integer $wealth
 * @property string $create_time
 * @property string $update_time
 * @property string $url
 */
class DriverWealthProduct extends CActiveRecord
{
    const PRODUCT_CROWN_TYPE=1;
    const DEFAULT_CROWN_NUM=40;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_wealth_product}}';
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
            array('wealth,type', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>10),
            array('url,introduction', 'length', 'max'=>255),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, wealth, create_time, update_time, url', 'safe', 'on'=>'search'),
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
            'wealth' => 'Wealth',
            'type' => 'type',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'url' => 'Url',
            'introduction' => 'introduction',
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
        $criteria->compare('wealth',$this->wealth);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('url',$this->url,true);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('introduction',$this->introduction,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverWealthProduct the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
    *   获取产品列表
    *
    */
    public function getProductList(){
        $sql="select id,name,wealth,url,introduction,type from t_driver_wealth_product";
        $command = Yii::app()->dbreport->createCommand($sql);
        $product_list = $command->queryAll();
        return $product_list;
    }

    /**
    *  获取商品信息
    *
    */
    public function getProduct($product_id){
        $sql="select id,name,wealth,url,introduction,type from t_driver_wealth_product where id=:product_id";
        $command = Yii::app()->dbreport->createCommand($sql);
        $command->bindParam(":product_id",$product_id);
        $product = $command->queryRow();
        return $product;
    }
}