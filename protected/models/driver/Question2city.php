<?php

/**
 * This is the model class for table "{{question2city}}".
 *
 * The followings are the available columns in table '{{question2city}}':
 * @property integer $id
 * @property integer $city_id
 * @property integer $question_id
 * @property integer $status
 * @property string $update_time
 * @property string $create_time
 */
class Question2city extends CActiveRecord
{
    CONST STATUS_NORMAL = 0;
    CONST STATUS_DELETE = -1;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{question2city}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id,question_id', 'required'),
            array('city_id,question_id,status', 'numerical', 'integerOnly'=>true),
            array('create_time', 'safe'),
            // The following rule is used by search().
            array('city_id,question_id,status', 'safe', 'on'=>'search'),
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
            'city_id' => 'City',
            'question_id' => 'Question',
            'status' => 'Status',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
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

        $criteria=new CDbCriteria;

        $criteria->compare('city_id',$this->city_id);
        $criteria->compare('question_id',$this->question_id);
        $criteria->compare('status',$this->status);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Question2city the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    public function addData($citys, $question_id){

        if(is_array($citys)){
            foreach($citys as $city_id) {
                if($city_id != 0){
                    $mod = new Question2city();
                    $mod->unsetAttributes();
                    //print_r($mod);
//                    $data = array('city_id'=>$city_id,'question_id'=>$question_id,'status'=>'0','create_time'=>date('Y-m-d H:i:s'));
//                    print_r($data);
                    $mod->city_id = (int)$city_id;
                    $mod->question_id = (int)$question_id;
                    $mod->status = 0;
                    $mod->create_time = date('Y-m-d H:i:s');
                    $res = $mod->save();
                    //var_dump($res);die;
                }

            }
        }
        else {
            $mod = new Question2city();
            $data = array('city_id'=>$citys,'question_id'=>$question_id,'create_time'=>date('Y-m-d H:i:s'));
            $mod->attributes = $data;
            $res = $mod->save();
        }
    }

    public function updateData($city_ids,$question_id,$status = self::STATUS_NORMAL){
        $old_city_ids = array();
        $new_city_ids = $city_ids;
        $old_citys = $this->findAll('question_id= :qid',array(':qid'=>$question_id));
        if($old_citys){
            foreach($old_citys as $obj_city){
                $old_city_ids[] = $obj_city->city_id;
            }
        }



        if($old_city_ids){
            $new_add_city_id = array_diff($new_city_ids,$old_city_ids); //新增的城市id
            $forbiden_city_id = array_diff($old_city_ids,$new_city_ids); //取消的城市id
            $alreday_has_city_id = array_intersect($old_city_ids,$new_city_ids); //原来就有的城市id 需要判断是否是禁用状态 如果是则需要改成默认状态。


            //print_r($new_add_role_id);
            if($new_add_city_id){
                foreach($new_add_city_id as $city_id_new_add){
                    $this->addData($city_id_new_add,$question_id);
                }
            }
            //print_r(array_keys($forbiden_role_id));die;
            if($forbiden_city_id){
                $this->deleteInfo($forbiden_city_id , $question_id );
            }
            //print_r($alreday_has_role_id);die;

            if($alreday_has_city_id){
//                $arr = array('status' => $status);
//                $this->updateInfo($alreday_has_city_id,$question_id,$arr);
            }
        }
        else{
            if($new_city_ids){
                $this->addData($new_city_ids,$question_id);
            }
        }
    }

    public function updateStatusByQid($question_id,$status){
        $res = $this->updateAll(array('status' => $status),'question_id = :qid', array(':qid'=>$question_id) );
    }


    /**
     * update info
     * @param $id
     * @param $param
     * @return int
     * @author duke
     */
    public function updateInfo($city_id,$question_id, $param){
        $ids = implode(',', $city_id);
        $mod = new Question2city();
        $res = $mod->updateAll($param, 'city_id in ('.$ids.') and question_id = :qid',array(':qid'=>$question_id));
        // print_r($res);die;
        return $res;
    }


    public function deleteInfo($city_id, $question_id){
        $ids = implode(',', $city_id);
        $mod = new Question2city();
        $res = $mod->deleteAll( 'city_id in ('.$ids.') and question_id = :qid',array(':qid'=>$question_id));
        // print_r($res);die;
        return $res;
    }


    public function getCityIdByQid($question_id){
        $ret = array();
        $res = $this->findAll('question_id = :qid and status = :status',array(':qid'=>$question_id,':status'=>self::STATUS_NORMAL));
        if($res){
            foreach($res as $v){
                $ret[$v['city_id']] = $v['city_id'];
            }
        }
        return $ret;
    }


    public function getQidByCityId($city_id){
        $ret = array();
        $res = $this->findAll('city_id = :cid and status = :status',array(':cid'=>$city_id,':status'=>self::STATUS_NORMAL));
        if($res){
            foreach($res as $v){
                $ret[$v['question_id']] = $v['question_id'];
            }
        }
        return $ret;
    }
}