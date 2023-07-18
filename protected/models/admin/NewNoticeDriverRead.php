<?php

/**
 * 司机己读公告
 * This is the model class for table "{{new_nocite_driver_read}}".
 *
 * The followings are the available columns in table '{{new_nocite_driver_read}}':
 * @property string $id
 * @property string $notice_id
 * @property string $driver_id
 * @property string $created
 */
class NewNoticeDriverRead extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{new_notice_driver_read}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('created', 'required'),
            array('notice_id', 'length', 'max'=>11),
            array('driver_id', 'length', 'max'=>50),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, notice_id, driver_id, created', 'safe', 'on'=>'search'),
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
            'notice_id' => 'Nocite',
            'driver_id' => 'Driver',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('notice_id',$this->notice_id,true);
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return NewNociteDriverRead the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
     * 获取noticeRead
     */
    public function getNoticeRead($params = array()){
        $ret = array();
        if(empty($params)){
            return $ret;
        }
        $info = Yii::app()->db_readonly->createCommand()
            ->from($this->tableName())
            ->where('driver_id=:driver_id and notice_id=:notice_id')
            ->queryRow(true,array('driver_id'=>$params['driver_id'],'notice_id'=>$params['notice_id']));
        $ret = empty($info) ? $ret : $info;
        return $ret;

    }

    /**
     * 保存己读记录
     */
    public function newNoticeReadSave($params){
        if($params['driver_id']){
            $c = array(
                'driver_id'=>$params['driver_id'],
                'flag'=>1,
            );
            $read_ids = NoticeStatus::model()->getDriverNoticeIds($c);
            if(in_array($params['notice_id'],$read_ids)){
                return true;
            }

            $info = $this->getNoticeRead($params);
            if(empty($info)){
                unset($params['flag']);
                $params['created'] = date("Y-m-d H:i:s");
                $r = Yii::app()->db->createCommand()->insert($this->tableName(),$params);
                if($r){
                    //写入司机己读缓存
                     NoticeStatus::model()->setDriverReadNotice($params);
                }
            }else{
		   //如果数据库中有，缓存中没有，则重新放入缓存
                   NoticeStatus::model()->setDriverReadNotice($params);

            }
            return true;
        }
    }
}
