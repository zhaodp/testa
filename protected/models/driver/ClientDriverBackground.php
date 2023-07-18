  <?php

/**
 * This is the model class for table "{{client_driver_background}}".
 *
 * The followings are the available columns in table '{{client_driver_background}}':
 * @property integer $id
 * @property string $operator
 * @property string $background_image
 * @property string $normal_image
 * @property string $hightlight_image
 * @property integer $city_id
 * @property integer $status
 * @property string $start_time
 * @property string $end_time
 * @property string $create_time
 * @property string $update_time
 */
class ClientDriverBackground extends CActiveRecord
{
    const NORMAL_STATUS=0;//正常
    const DEL_STATUS=1;//已删除
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{client_driver_background}}';
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
            array('update_time,act_name,background_image, act_name,normal_image, hightlight_image,start_time,half_star_image,font_color, end_time,', 'required'),
            array('city_id, status', 'numerical', 'integerOnly'=>true),
            array('operator', 'length', 'max'=>11),
            array('background_image,half_star_image,,font_color, act_name,normal_image, hightlight_image', 'length', 'max'=>255),
            array('start_time, end_time, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, operator, background_image, normal_image, hightlight_image, city_id, status, start_time, end_time, create_time, update_time', 'safe', 'on'=>'search'),
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
            'operator' => 'Operator',
            'act_name' => '活动名称',
            'background_image' => '背景图片',
            'normal_image' => '星级评价2',
            'hightlight_image' => '星级评价1',
            'font_color'=>'字体颜色',
            'half_star_image'=>'半个星级评价',
            'city_id' => '城市',
            'status' => '状态',
            'start_time' => '生效开始时间',
            'end_time' => '生效结束时间',
            'create_time' => '发布时间',
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
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('act_name',$this->act_name,true);
        $criteria->compare('background_image',$this->background_image,true);
        $criteria->compare('normal_image',$this->normal_image,true);
        $criteria->compare('hightlight_image',$this->hightlight_image,true);
        $criteria->compare('half_star_image',$this->half_star_image,true);
        $criteria->compare('font_color',$this->font_color,true);
        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('status',$this->status);
        $criteria->compare('start_time',$this->start_time,true);
        $criteria->compare('end_time',$this->end_time,true);
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
     * @return ClientDriverBackground the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
    *   @param 
    *   添加背景记录
    */
    public function addBackGround($params){
        $model = new ClientDriverBackground();
        $data= array(
            'operator'=>$params['operator'],
            'city_id'=>$params['city_id'],
            'background_image'=>$params['background_image'],
            'normal_image'=>$params['normal_image'],
            'hightlight_image'=>$params['hightlight_image'],
            'half_star_image'=>$params['half_star_image'],
            'font_color'=>$params['font_color'],
            'start_time'=>$params['start_time'],
            'end_time'=>$params['end_time'],
            'act_name'=>$params['act_name'],
            'create_time'=>date('Y-m-d H:i:s'),
            );
        $model->attributes = $data;
        return $model->insert(false);
    }

    /**
    *   获取当前时间之间的城市背景图片设置
    *
    */
    public function getBackGround($city_id){
        $data = array(
            'background_image'=>'',
            'star_image_nomal'=>'',
            'star_image_highlight'=>'',
            'half_star_image'=>'',
            'font_color'=>''
            );
        $today = date('Y-m-d H:i:s');
        $result = ClientDriverBackground::model()->find('city_id=:city_id 
            and status=0 and start_time<=:today and end_time>=:today', 
            array(':city_id'=>$city_id,':today'=>$today));
        if($result){
            $data['background_image']=$result['background_image'];
            $data['star_image_nomal']=$result['normal_image'];
            $data['star_image_highlight']=$result['hightlight_image'];
            $data['half_star_image']=$result['half_star_image'];
            $data['font_color']=$result['font_color'];
        }
        return $data;
    }

    /**
    *  判断是否有生效的城市配置
    *
    */
    public function findBackground($city_id){
        $result = ClientDriverBackground::model()->findAll('city_id=:city_id and status=0', 
            array(':city_id'=>$city_id));
        return $result;
    }


    /**
    *   获取所有有效的配置
    */
    public function getAll(){
        $result = ClientDriverBackground::model()->findAll();;
        return $result;
    }
}