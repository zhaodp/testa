<?php

/**
 * This is the model class for table "{{material}}".
 *
 * The followings are the available columns in table '{{material}}':
 * @property integer $id
 * @property integer $type_id
 * @property string $name
 * @property string $price
 * @property string $depreciation
 * @property string $loss_cost
 * @property string $third_id
 * @property integer $status
 * @property string $operator
 * @property string $update_time
 * @property string $create_time
 */

class Material extends CActiveRecord
{

    CONST STATUS_NORMAL = 0;
    CONST STATUS_FORBIDEN = -1;
    CONST STATUS_DELETE = -2;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{material}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,price,third_id,depreciation,loss_cost', 'required'),
            array('type_id, status', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>64),
            array('price, depreciation, loss_cost', 'length', 'max'=>10),
            array('third_id', 'length', 'max'=>32),
            array('operator', 'length', 'max'=>15),
            array('create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('type_id, name, price, depreciation, loss_cost, third_id, status', 'safe', 'on'=>'search'),
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
            'id'=>'id',
            'type_id' => '类型ID',
            'name' => '名称',
            'price' => '价格',
            'depreciation' => '折旧费',
            'loss_cost' => '遗失赔偿费',
            'third_id' => '物料ID',
            'status' => '状态',
            'operator'=>'最后操作人',
            'update_time' => '更新时间',
            'create_time' => '创建时间'
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

        $criteria->compare('type_id',$this->type_id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('price',$this->price,true);
        $criteria->compare('depreciation',$this->depreciation,true);
        $criteria->compare('loss_cost',$this->loss_cost,true);
        $criteria->compare('third_id',$this->third_id,true);
        //$criteria->compare('status',$this->status);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('create_time',$this->create_time,true);

        $criteria->addCondition('status > :s');
        $criteria->params[':s'] = self::STATUS_DELETE;
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 30)

        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Material the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function getStatus($status = ''){
        $status_array = array(
            self::STATUS_NORMAL=> '正常',
            self::STATUS_FORBIDEN=> '屏蔽',
            //self::STATUS_DELETE=> '删除',
        );

        if($status !== ''){
            if(isset($status_array[$status]))
                return $status_array[$status];
            else return false;

        }
        return $status_array;
    }


    public function haveViewPermission($controller,$action){
        return 1;
    }

    public static function getTypeInfo($type = 0){
        $info =  array(
            1=> array(
                'name'=>'马甲',
                'isredpacket'=>1
            ),
            2=> array(
                'name'=>'T恤',
                'isredpacket'=>1
            ),
            3=> array(
                'name'=>'支架',
                'isredpacket'=>1
            ),
            4=> array(
                'name'=>'工牌',
                'isredpacket'=>1
            ),
            5=> array(
                'name'=>'手机',
                'isredpacket'=>0
            ),
            6=> array(
                'name'=>'手机卡',
                'isredpacket'=>0
            ),

        );

        if($type == 0){
            $info['7'] = array(
                'name'=>'保证金',
                'isredpacket'=>0
            );
        }
        return $info;
    }

    public static function getTypeInfoName($type_id = ''){
        $all = self::getTypeInfo(0);
        if($type_id !== ''){
            if(isset($all[$type_id])){
                return $all[$type_id]['name'];
            }
            return '';
        } else {
            foreach($all as $key => $v) {
                $res[$key] = $v['name'];
            }
            return $res;
        }


    }


//    public function countByType_id($type_id){
//        $this->count('');
//    }

    /**
     * 获取所有正常的物料 通过type_id 分割数组，
     * @return mixed
     */
    public function getAllNormalName($only_name = false){
        $res = $this->findAll('status = :s',array(':s'=>self::STATUS_NORMAL));
        $re = array();
        rsort($res);
        if(!$only_name){
            foreach($res as $v){
                $re[$v->id] = $v->attributes;
            }
        }
        else{
            foreach($res as $v){
                $re[$v->id] = $v->name;
            }
        }
        return $re;
    }

    public function getInfoByTypeid($type_id = ''){
        if(is_numeric($type_id)){
            $re = array();
            $res = $this->findAll(array('select'=>'id,name','condition'=>'status = :s and type_id = :tid','params'=>array(':s'=>self::STATUS_NORMAL,':tid'=>$type_id)));
            if($res){
                rsort($res);
                foreach($res as $v){
                    $re[$v->id] = $v->name;
                }
            }
            return $re;
        } else {
            return $this->getAllNormalName(true);
        }
    }
}