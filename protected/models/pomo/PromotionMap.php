<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaoyin
 * Date: 2015/3/24
 * Time: 15:53
 */

/**
 * This is the model class for table "t_promotion_map".
 *
 * The followings are the available columns in table 't_promotion_map':
 * @property integer $id
 * @property integer $pid
 * @property integer $shop_id
 * @property integer $city_id
 * @property integer $statue
 * @property integer $pomo_type
 * @property string $creation_date
 * @property string $last_changed_date
 */
class PromotionMap extends PomoActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{promotion_map}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pid, shop_id,city_id,statue', 'numerical', 'integerOnly' => true),
            array('msg', 'length', 'max' => 100),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id,pid,shop_id,city_id,creation_date,last_changed_date', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => '自增主键',
            'pid' => '推广ID',
            'shop_id' => '店铺ID',
            'city_id' => '城市ID',
            'statue' => '状态',
            'msg' => '信息',
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
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id, true);

        return new CActiveDataProvider('MarketPomo', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @return EnvelopeAcount the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function getCreatAmount($dateStart, $dateEnd)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'DATE(creation_date) as  creation_date ,count(*) as city_id';
        $criteria->addBetweenCondition('creation_date', $dateStart, $dateEnd);
        $criteria->addCondition('statue in (0,1,2,9,10)');
        $criteria->group = 'DATE(creation_date)';
        $result = array();
        $date = self::model()->findAll($criteria);
        if (!empty($date)) {
            foreach ($date as $da) {
                $result[$da->creation_date] = $da->city_id;
            }
        }

        return $result;
    }


    public function getPassAmount($dateStart, $dateEnd)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'DATE(last_changed_date) as  last_changed_date ,count(*) as city_id';
        $criteria->addBetweenCondition('last_changed_date', $dateStart, $dateEnd);
        $criteria->addCondition('statue=2');
        $criteria->group = 'DATE(last_changed_date)';
        $result = array();
        $date = self::model()->findAll($criteria);
        if (!empty($date)) {
            foreach ($date as $da) {
                $result[$da->last_changed_date] = $da->city_id;
            }
        }

        return $result;
    }


    public function getPassingAmount($dateEnd,$statue)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('last_changed_date<:last_changed_date');
        $criteria->params[':last_changed_date']=$dateEnd.' 23:59:59';
        $criteria->addCondition('statue=:statue');
        $criteria->params[':statue']=$statue;
        $result = self::model()->count($criteria);
        return $result;
    }

}