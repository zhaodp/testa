<?php

/**
 * This is the model class for table "{{driver_exam_test_success}}".
 *
 * The followings are the available columns in table '{{driver_exam_test_success}}':
 * @property integer $id
 * @property string $id_card
 * @property integer $question_id
 * @property integer $created
 */
class DriverExamTestSuccess extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverExamTestSuccess the static model class
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
		return '{{driver_exam_test_success}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_card', 'required'),
			array('question_id, created', 'numerical', 'integerOnly'=>true),
			array('id_card', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, id_card, question_id, created', 'safe', 'on'=>'search'),
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
			'id_card' => 'Id Card',
			'question_id' => 'Question',
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
		$criteria->compare('id_card',$this->id_card,true);
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function addExamSuccess($qkey){
/*		$q_key = explode(',', $qkey);
		
		foreach ($q_key as $key){
			if (!empty($key)){
				$add_sql = 'insert into t_driver_exam_test_success(id_card,question_id,created) values(:id_card,:question_id,:created)';
				$params[':id_card'] = $_POST['id_cards'];
				$params[':question_id'] = $key;
				$params[':created'] = time();
				$command = Yii::app()->db->createCommand($add_sql);
				$command->execute($params);
			}
		}*/
		$params = array();
		$up_success = 'update t_driver_exam_test set num = 1 where id_card = :id_card and created = :created and question_id = :question_id';
		$params[':id_card'] = $_POST['id_cards'];
		$params[':created'] = date('Y-m-d',time());
		$params[':question_id'] = $qkey;
		$command = Yii::app()->db->createCommand($up_success);
		$command->execute($params);
		$command->reset();
		
		$insertArr = array();
		
		$criteria = new CDbCriteria();
		$criteria->condition = "id_card=:id_card";
		$criteria->params = array(':id_card'=>$_POST['id_cards']);
		$driver = new Driver();
		$driverModel = $driver->find($criteria);
		if(!empty($driverModel)){
			$insertArr['name'] = $driverModel->attributes['name'];
			$insertArr['id_card'] = $driverModel->attributes['id_card'];
			$insertArr['message'] = '老司机考试通过';
			$insertArr['time'] = time();
		}else {
			$recruitmet = new DriverRecruitment();
			$dataRecruitment= DriverRecruitment::model()->find('id_card=:id_card',array(':id_card'=>$_POST['id_cards']));
			if (!empty($dataRecruitment)){
				$insertArr['name'] = $dataRecruitment->attributes['name'];
				$insertArr['id_card'] = $dataRecruitment->attributes['id_card'];
				$insertArr['message'] = '新司机考试通过';
				$insertArr['time'] = time();
			}
		}
		
		Yii::app()->db->createCommand()->insert('t_recruitment_log', $insertArr);
		
		$paramsd = array();
		$update_sql = 'update t_driver_recruitment set status = 7 where id_card = :id_card';
		$paramsd[':id_card'] = $_POST['id_cards'];
		$commands = Yii::app()->db->createCommand($update_sql);
		$commands->execute($paramsd);
		$commands->reset();
		
	}

	/**
	*客户端司机考试通过
	**/
	public function addClientExamSuccess($qkey,$id_card){
                $params = array();
                $up_success = 'update t_driver_exam_test set num = 1 where id_card = :id_card and created = :created and question_id = :question_id';
                $params[':id_card'] = $id_card;
                $params[':created'] = date('Y-m-d',time());
                $params[':question_id'] = $qkey;
                $command = Yii::app()->db->createCommand($up_success);
                $command->execute($params);
                $command->reset();
                
                $insertArr = array();
                
                $criteria = new CDbCriteria();
                $criteria->condition = "id_card=:id_card";
                $criteria->params = array(':id_card'=>$id_card);
                $recruitmet = new DriverRecruitment();
                $dataRecruitment= DriverRecruitment::model()->find('id_card=:id_card',array(':id_card'=>$id_card));
                if (!empty($dataRecruitment)){
                     $insertArr['name'] = $dataRecruitment->attributes['name'];
                     $insertArr['id_card'] = $dataRecruitment->attributes['id_card'];
                     $insertArr['message'] = '新司机考试通过';
                     $insertArr['time'] = time();
                }else{
		    return false;
		}

                Yii::app()->db->createCommand()->insert('t_recruitment_log', $insertArr);

                $paramsd = array();
                $update_sql = 'update t_driver_recruitment set status = 7 where id_card = :id_card';
                $paramsd[':id_card'] = $id_card;
                $commands = Yii::app()->db->createCommand($update_sql);
                $commands->execute($paramsd);
                $commands->reset();
                //更新在线考核通过
                DriverRecruitment::model()->updateExamStatus($id_card, DriverRecruitment::STATUS_ONLINE_EXAM_PASS);

        return true;
        }

}
