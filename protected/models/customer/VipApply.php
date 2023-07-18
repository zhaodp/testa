<?php

/**
 * This is the model class for table "{{vip_apply}}".
 *
 * The followings are the available columns in table '{{vip_apply}}':
 * @property string $id
 * @property integer $type
 * @property string $name
 * @property string $phone
 * @property string $mail
 * @property string $company_name
 * @property string $city_id
 * @property string $book_money
 * @property string $create_time
 * @property string $mark
 */
class VipApply extends FinanceActiveRecord
{
    const APPLY_TYPE1=0;//企业
    const APPLY_TYPE2=1;//个人

    public static  $apply_type=array(
                                    '0'=>'企业',
                                    '1'=>'个人',
    );

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{vip_apply}}';
    }

    public $verifyCode; //验证码

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, phone, city_id', 'required'),
            array('type', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>40),
            array('phone', 'length', 'max'=>32),
            array('mail', 'length', 'max'=>50),
            array('company_name', 'length', 'max'=>160),
            array('city_id', 'length', 'max'=>4),
            array('book_money', 'length', 'max'=>10),
            array('mark', 'length', 'max'=>255),
            array('mail', 'email','message'=>'电子邮箱格式不对','pattern'=>'/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, type, name, phone, mail, company_name, city_id, book_money, create_time, mark', 'safe', 'on'=>'search'),
            array (
                'verifyCode',
                'captcha',
                'on'=>'insert',
                'allowEmpty'=>!CCaptcha::checkRequirements(),
                'message'=>'请输入正确的验证码')
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
            'type' => '申请类型',
            'name' => '申请人姓名',
            'phone' => '申请人电话',
            'mail' => '邮件地址',
            'company_name' => '公司名称',
            'city_id' => '办理城市',
            'book_money' => '充值金额',
            'create_time' => '创建时间',
            'mark' => '备注',
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
        $criteria->compare('type',$this->type);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('phone',$this->phone,true);
        $criteria->compare('mail',$this->mail,true);
        $criteria->compare('company_name',$this->company_name,true);
        $criteria->compare('city_id',$this->city_id,true);
        $criteria->compare('book_money',$this->book_money,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('mark',$this->mark,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return TVipApply the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /** 统计区间时间内的vip 申请数量
     * @param $start_time 2014-10-12 :00:00:11
     * @param $end_time
     * @param string $city_id
     * @return mixed
     */
    public function summaryData($start_time, $end_time,$city_id = ''){
        $where = " create_time between :date_start and :date_end";
        $params = array(':date_start' => $start_time, ':date_end' => $end_time);
        if($city_id){
            $where .=' and city_id = :city_id';
            $params[':city_id'] = $city_id;
        }

        $count = Yii::app()->db_finance->createCommand()
            ->select('count(1) as cnt')->from($this->tableName())
            ->where($where,$params)
            ->queryScalar();
        return $count;
    }
}
