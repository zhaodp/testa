<?php

/**
 * This is the model class for table "{{bonus_code_log}}".
 *
 * The followings are the available columns in table '{{bonus_code_log}}':
 * @property string $id
 * @property integer $bonus_id
 * @property string $content
 * @property string $remark
 * @property string $operation
 * @property string $operator
 * @property string $created
 */
class BonusCodeLog extends FinanceActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return BonusCodeLog the static model class
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
        return '{{bonus_code_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('content, remark, operation, operator, created', 'required'),
            array('bonus_id', 'numerical', 'integerOnly'=>true),
            array('content', 'length', 'max'=>3000),
            array('remark, operation', 'length', 'max'=>100),
            array('operator', 'length', 'max'=>20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, bonus_id, content, remark, operation, operator, created', 'safe', 'on'=>'search'),
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
            'bonus_id' => 'Bonus',
            'content' => 'Content',
            'remark' => 'Remark',
            'operation' => 'Operation',
            'operator' => 'Operator',
            'created' => 'Created',
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
        $criteria->compare('bonus_id',$this->bonus_id);
        $criteria->compare('content',$this->content,true);
        $criteria->compare('remark',$this->remark,true);
        $criteria->compare('operation',$this->operation,true);
        $criteria->compare('operator',$this->operator,true);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * 添加优惠券变更日志
     * @param   <int>       $bonusId
     * @param   <string>    $remark      备注
     * @param   <string>    $operation   操作类型
     * @param   <string>    $operation   操作人
     * @param   <array>     $operArray   操作数据数组
     * @return  <bool>                   是否添加成功
     * @author liuxiaobo    2013-9-12
     */
    public function addAuditLog($bonusId, $operation, $remark = '', $operator = NULL, $operArray = array()){
        if($operator === NULL){
            $operator = Yii::app()->user->id;
        }
        $model = new BonusCodeLog;
        $model->bonus_id = $bonusId;
        $model->remark = $remark;
        $model->operation = $operation;
        $model->operator = $operator;
        $model->created = date('Y-m-d H:i:s', time());
        if(!empty($operArray)){
            $model->content = CJSON::encode($operArray);
        }
        $saveOk = $model->save(false);
        return $saveOk;
    }
}