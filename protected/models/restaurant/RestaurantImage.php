<?php
/**
 * This is the model class for table "{{restaurant_image}}".
 *
 * The followings are the available columns in table '{{restaurant_image}}':
 * @property integer $id
 * @property integer $restaurant_id
 * @property string $url
 * @property integer $user_id
 * @property integer $created
 */
class RestaurantImage extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{restaurant_image}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id, restaurant_id, url, user_id, created', 'required'),
            array('id, restaurant_id, user_id, created', 'numerical', 'integerOnly'=>true),
            array('url', 'length', 'max'=>100),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, restaurant_id, url, user_id, created', 'safe', 'on'=>'search'),
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
            'restaurant_id' => 'Restaurant',
            'url' => 'Url',
            'user_id' => 'User',
            'created' => 'Created',
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
        $criteria->compare('restaurant_id',$this->restaurant_id);
        $criteria->compare('url',$this->url,true);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('created',$this->created);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RestaurantImage the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    public function insertPhotosInfo($params ,$condition){
        if(empty($params)){
            return false;
        }
        foreach($params as $url){
            if(!empty($url)){
                $data = array(
                    'url' =>trim($url),
                    'restaurant_id'=>$condition['restaurant_id'],
                    'user_id'=>$condition['user_id'],
                    'created'=>$condition['created'],
                );
                Yii::app()->db->createCommand()->insert('{{restaurant_image}}',$data);
            }

        }

        return true;
    }
}