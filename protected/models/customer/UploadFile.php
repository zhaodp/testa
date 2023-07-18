<?php

/**
 * This is the model class for table "{{upload_file}}".
 *
 * The followings are the available columns in table '{{upload_file}}':
 * @property string $id
 * @property string $content
 */
class UploadFile extends CActiveRecord {


    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Vip the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{upload_file}}';
    }

    public function attributeLabels()
    {
        return array(
            'id' => '序号',
            'content' => '内容',
        );
    }


    /**
     * 保存之前要更新的字段
     * @return bool
     * author mengtianxue
     */
    public function beforeSave()
    {
        return parent::beforeSave();
    }

    public function getPrimary($id){
        $criteria = new CDbCriteria();
        $criteria->compare('id', $id);
        return self::model()->find($criteria);
    }

    public function afterSave(){
        parent::afterSave();

    }
}
