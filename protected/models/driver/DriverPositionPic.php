 <?php

/**
 * This is the model class for table "{{driver_position_pic}}".
 *
 * The followings are the available columns in table '{{driver_position_pic}}':
 * @property integer $id
 * @property string $city_prefix
 * @property string $image_url
 * @property string $create_time
 * @property string $update_time
 */
class DriverPositionPic extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_position_pic}}';
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
            array('city_prefix', 'length', 'max'=>10),
            array('image_url', 'length', 'max'=>255),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, city_prefix, image_url, create_time, update_time', 'safe', 'on'=>'search'),
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
            'city_prefix' => '城市拼音简写',
            'image_url' => '图片URL',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
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
        $criteria->compare('city_prefix',$this->city_prefix,true);
        $criteria->compare('image_url',$this->image_url,true);
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
     * @return DriverPositionPic the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
    * get latest hot pic by city pinying such as:BJ
    * add by aiguoxin
    *
    **/
    public function getLatestPic($city_prefix){
        
        $image_url = Yii::app()->db_readonly->createCommand()
                     ->select('image_url')
                     ->from('t_driver_position_pic')
                     ->where('city_prefix = :city_prefix' , array(':city_prefix' => $city_prefix))
                     ->order('create_time desc')
                     ->queryScalar();
        return $image_url;
    }
}