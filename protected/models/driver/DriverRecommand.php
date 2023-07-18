<?php

/**
 * This is the model class for table "{{driver_recommand}}".
 *
 * The followings are the available columns in table '{{driver_recommand}}':
 * @property integer $id
 * @property string $driver_id
 * @property integer $type
 * @property string $reason
 * @property string $begin_time
 * @property string $end_time
 * @property string $created
 */
class DriverRecommand extends CActiveRecord
{
	const CROWN_TYPE=1; //皇冠类型
	const E_TYPE=2; //e币类型

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverRecommand the static model class
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
		return '{{driver_recommand}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        return array(
            array('driver_id, type, reason, created, operator,begin_time,end_time', 'required'),
            array('type, wealth', 'numerical', 'integerOnly'=>true),
            array('driver_id', 'length', 'max'=>10),
            array('reason', 'length', 'max'=>100),
            array('operator', 'length', 'max'=>50),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, driver_id, type, wealth, reason, begin_time, end_time, created, operator', 'safe', 'on'=>'search'),
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
			'id' => '序号',
			'driver_id' => '司机工号',
			'type' => '类型e',
			'reason' => '理由',
			'wealth' => '奖励e币',
			'begin_time' => '开始时间',
			'end_time' => '结束时间',
			'created' => '创建时间',
            'operator' => '操作人',
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
        $criteria->compare('driver_id',$this->driver_id,true);
        $criteria->compare('type',$this->type);
        $criteria->compare('wealth',$this->wealth);
        $criteria->compare('reason',$this->reason,true);
        $criteria->compare('begin_time',$this->begin_time,true);
        $criteria->compare('end_time',$this->end_time,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('operator',$this->operator,true);
		$criteria->order = 'id desc';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    /**
     * 司机奖励次数
     * @param $driver_id
     * @return mixed
     */
    public function getRecommandCount($driver_id) {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('count(*)');
        $command->from('t_driver_recommand');
        $command->where('driver_id=:driver_id', array(':driver_id'=>$driver_id));
        return $command->queryScalar();
    }


    /**
     * 司机奖励记录
     * @param $driver_id
     * @param int $page_size
     * @return CActiveDataProvider
     */
    public function getProviderByDriver($driver_id, $page_size=20) {
        $criteria=new CDbCriteria;
        $criteria->compare('driver_id',$driver_id,true);
        $criteria->order = 'id DESC';
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>$page_size,
            ),
        ));
    }


	/**
	 * 验证工号是否为皇冠
	 * @param string $driver_id
	 * @return array $recomand
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-08-05
	 */
    public function validateRecommend($driver_id = '') {
        if (empty($driver_id)) {
            return array();
        }
        $today = date(Yii::app()->params['formatDateTime'], time());
        $tomorrow = date(Yii::app()->params['formatDateTime'], strtotime("+1 day"));
        $sql = 'SELECT type,reason,begin_time,end_time
                    FROM t_driver_recommand
                    WHERE driver_id=:driver_id
                    and begin_time < :tomorrow
                    and end_time > :today
                    order by id desc';
        $command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(":driver_id", $driver_id);
        $command->bindParam(":today", $today);
        $command->bindParam(":tomorrow", $tomorrow);

        $recomand = $command->queryRow();
        return $recomand;
    }

    /**
     * 获取司机皇冠总数
     * @return mixed
     * author mengtianxue
     */
    public function getDriverRecommand($city_id = 0)
    {
        $time = date("Y-m-d H:i:s");
        $where = "id > 0 and end_time > :end_time";
        $params = array(':end_time' => $time);

        if ($city_id != 0) {
            $city_code = Common::getCityCode($city_id);
            $where .= " and left(driver_id,2) = :city_code";
            $params[':city_code'] = $city_code;
        }

        $driverRecommand = Yii::app()->db_readonly->createCommand()
            ->select("count(1) as recommand_count")
            ->from("t_driver_recommand")
            ->where($where, $params)
            ->queryScalar();

        return $driverRecommand;
    }

}