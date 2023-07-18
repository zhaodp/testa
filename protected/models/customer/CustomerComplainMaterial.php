<?php

/**
 * This is the model class for table "{{customer_complain_type}}".
 *
 * The followings are the available columns in table '{{customer_complain_type}}':
 * @property integer $id
 * @property integer $complain_id
 * @property string $fileurl
 * @property string $filename
 * @property string $type
 * @property string $create_time
 * @property string $operator
 */
class CustomerComplainMaterial extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CustomerComplainType the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_complain_material}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('complain_id, fileurl, filename, type, create_time, operator', 'required'),
            array('complain_id, type', 'numerical','integerOnly'=>true),
            array('fileurl', 'length', 'max'=>512),
            array('filename', 'length', 'max'=>256),
            array('operator', 'length', 'max'=>20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, complain_id, fileurl, filename, type, create_time, operator', 'safe', 'on'=>'search'),
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
            'complain_id' => 'complainID',
            'fileurl' => '文件链接',
            'filename' => '文件名',
            'type' => '文件类型',
            'create_time' => 'Create Time',
            'operator' => 'Operator',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('complain_id',$this->complain_id);
        $criteria->compare('fileurl',$this->fileurl,true);
        $criteria->compare('filename',$this->filename,true);
        $criteria->compare('type',$this->status);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('operator',$this->operator,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * 保存投诉的材料
     * @param $cid
     * @param $filename
     * @return bool
     */
    public function saveMaterial($cid, $fileurl, $filename)
    {
        $model = new CustomerComplainMaterial;
        $param['complain_id'] = $cid;
        $param['fileurl'] = $fileurl;
        $param['filename'] = $filename;
        $ext = substr($filename,strrpos($filename,'.')+1);
        $image = array('jpg','png','gif','jpeg','bmp');
        if (in_array($ext,$image)) {
            $type = 1;
        } else {
            $type = 2;
        }
        $param['type'] = $type;
        $param['operator'] = Yii::app()->user->id;
        $param['create_time'] = date('Y-m-d H:i:s');
        $model->attributes = $param;
        if ($model->save()) {
            return true;
        }
        return false;
    }

    /**
     * 获取投诉的材料列表
     * @param $cid
     * @return mixed
     */
    public function getMaterialsByCid($cid)
    {
        $list = Yii::app()->db_readonly->createCommand()->select('')->from(self::tableName())
            ->where('complain_id=:cid',array(':cid'=>$cid))->queryAll();
        return $list;
    }

    /**
     * 删除投诉材料
     * @param $id
     * @return mixed
     */
    public function delMaterial($id)
    {
        $model = new CustomerComplainMaterial;
        return $model->deleteByPk($id);
    }
}
