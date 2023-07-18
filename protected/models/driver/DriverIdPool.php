<?php

/**
 * This is the model class for table "{{driver_id_address}}".
 *
 * The followings are the available columns in table '{{driver_id_address}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $city_id
 * @property string $created
 * @property integer $status
 */
class DriverIdPool extends CActiveRecord
{
    //已经使用
    const STATUS_USED = 1;
    //未使用
    const STATUS_USABLE = 0;
    //被删除
    const STATUS_DEL = 2;
    //被选中
    const STATUS_TMP_USE = 3;

    public static $status_dict = array(
        self::STATUS_USABLE => '未使用',
        self::STATUS_TMP_USE => '被选中',
        self::STATUS_USED => '已经使用',
        self::STATUS_DEL => '已经删除',
    );

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverIdPool the static model class
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
		return '{{driver_id_pool}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_id, city_id, created, status', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('driver_id, city_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_id, city_id, created, status', 'safe', 'on'=>'search'),
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
			'driver_id' => '司机工号',
			'city_id' => '城市',
			'created' => '创建时间',
			'status' => '状态',
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
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination'=>array (
                'pageSize'=>100,
            ),
		));
	}

    private function changeDriverIdStatus($id, $status) {
        $model = DriverIdPool::model()->findByPk($id);
        if (!$model) {
            return false;
        } else {
            $model->status = $status;
            return $model->save();
        }
    }



    /**
     * 获取一个工号用于签约，并将此号置为临时使用
     */
    public function getDriverIdToEntry($city_id){
        $driver_id = false;
        for($i=0; $i<1000; $i++) {
            $_driver_id = $this->getMinDriverId($city_id);
            if (!$_driver_id) {
                EdjLog::info('city='.$city_id.', generate driver_id...');
                //add aiguoxin
                $this->InsertDriverId($city_id,10); //临时生成10个
                continue;
                // break;
            }
            if(!$this->checkDriverIdStatusAndChange($_driver_id)) {
                if ($_driver_id && $this->SelectedDriverId($_driver_id)) {
                    $driver_id = $_driver_id;
                    break;
                }
            }
        }
        return $driver_id;
    }
    /**
     * 取出该城市最大工号
     * @param $city_id
     * @return mixed
     */
    public function getMaxDriverId($city_id) {
        $command = Yii::app()->db_readonly->createCommand();
        $command->select('max(driver_id)');
        $command->from('t_driver_id_pool');
        $command->where('city_id=:city_id', array(':city_id'=>$city_id));
        $max_user = $command->queryScalar(); //取出该城市最大工号
        return $max_user;
    }

    /**
     * 取出该城市最小可用工号
     * @param $city_id
     * @return mixed
     */
    public function getMinDriverId($city_id) {
        $command = Yii::app()->db->createCommand();
        $command->select('driver_id');
        //$command->select('min(driver_id)');
        //$command->select('min( SUBSTRING( driver_id FROM 3 ) +0 )');
        $command->from('t_driver_id_pool');
        $command->where('city_id=:city_id AND status=:status', array(':city_id'=>$city_id,':status'=>self::STATUS_USABLE));
        $command->order = 'id asc';
        $command->limit = 1;
        $min_user = $command->queryScalar(); //取出该城市最小工号
        return $min_user;
    }

    /**
     * 通过司机工号获得主键
     * @param $driver_id
     * @return array|bool|mixed|null
     */
    public function getPkByDriverId($driver_id) {
        $model = self::model()->find("driver_id='{$driver_id}'");
        if ($model) {
            return $model->id;
        } else {
            return false;
        }
    }

    /**
     * 检查工号是否被使用，如果已经使用则修改状态。未使用返回false
     * @param $driver_id
     * @return bool
     */
    public function checkDriverIdStatusAndChange($driver_id) {
        if (Driver::getProfile($driver_id)) {
            $status = self::STATUS_USED;
            // $id = $this->getPkByDriverId($driver_id);
            // return $this->changeDriverIdStatus($id, $status);
            //changed by aiguoxin 重复工号，需要把该工号全部更新状态
            return $this->updateStatusByDriverId($driver_id,$status);
        } else {
            return false;
        }
    }

    /**
     * 删除工号
     * @param $id
     * @return bool
     */
    public function delDriverId($id) {
        $status = self::STATUS_DEL;
        return $this->changeDriverIdStatus($id, $status);
    }

    /**
     * 将工号置为已经使用状态
     * @param $driver_id
     * @return bool
     */
    public function usedDriverId($driver_id) {
        $model = self::model()->find("driver_id='{$driver_id}'");
        if($model){
            $model->status = self::STATUS_USED;
            return $model->save();
        } else {
            return false;
        }
    }
    /**
     * 临时选中工号
     * @param $id
     * @return bool
     */
    public function SelectedDriverId($driver_id) {
        $model = self::model()->find("driver_id='{$driver_id}'");
        if (!$model || $model->status == self::STATUS_USED ||
            $model->status == self::STATUS_DEL ||
            $model->status == self::STATUS_TMP_USE
        ) {
            return false;
        } else {
            $id = $model->id;
            $status = self::STATUS_TMP_USE;
            return $this->changeDriverIdStatus($id, $status);
        }
    }

    /**
     * 恢复工号
     * @param $id
     * @return bool
     */
    public function recoverDriverId($id) {
        $model = self::model()->findByPk($id);
        if (!$model || $model->status == self::STATUS_USED) {
            return false;
        } else {
            $status = self::STATUS_USABLE;
            return $this->changeDriverIdStatus($id, $status);
        }
    }


    /**
     * 批量生成工号(仅生成)
     * @param     $city_id
     * @param int $num
     * @return array|bool
     */
    public function createBatchDriverId($city_id, $num=100) {
        $new_num = 0;
        //$driver_id = false;
        $city = Dict::items('city_prefix');
        $city_prefix = $city[$city_id];
        //$city_length = strlen($city_prefix);
        /**$max_user = $this->getMaxDriverId($city_id);
        $max_num = $max_user ? substr($max_user, $city_length) : '0';
        if (is_numeric($max_num) && $max_num>=0) {
            $new_num = intval($max_num)+1;
        }
        //任何分公司9000段不安排司机
        if ($new_num >= 9000 && $new_num<=9999) {
            $new_num = 10000;
        }
        if (is_numeric($new_num)) {
            //循环一百次，防止生成工号已经被使用的情况发生
            for ($i=$new_num; $i<$new_num+10000; $i++) {
                //工号中不能带4
                $i = str_replace(4,5,$i);
                //生成工号不足4位前面用0补齐
                $i = sprintf("%04d", $i);
                $tmp_driver_id = $city_prefix.$i;
                //检查该工号是否有人使用，如果没有人使用跳出循环
                if (!Driver::getProfile($tmp_driver_id)) {
                    $driver_id[] = $tmp_driver_id;
                }
                if (count($driver_id)>=$num) {
                    break;
                }
            }
        }
 	**/
			
        $driver_id = array();
	$i=0;
	$keep_id_list = array(11111,22222,33333,44444,55555,66666,77777,88888,99999);
	
	//最多运行10000次，当生成完ID之后就停止运行
	while(count($driver_id) <=$num && $i < 10000) {
		$i++;
		$new_num = rand(13000,99999);
		//工号中不能带4
		$new_num = str_replace(4,5,$new_num);
			
		if(in_array($new_num,$keep_id_list)) {
			continue;
		}
		$tmp_driver_id = $city_prefix.$new_num;
		//检查该工号是否有人使用，如果没有人使用跳出循环
        $model = self::model()->find("driver_id='{$tmp_driver_id}'"); //aiguoxin 2014-07-03排除重复的工号
        if (!Driver::getProfile($tmp_driver_id) && !$model) {
            $driver_id[] = $tmp_driver_id;
        }
	}	
	
        return $driver_id;
    }

    /**
     * 批量生成并插入司机工号
     * @param     $city_id
     * @param int $num
     * @return array 返回插入成功的司机工号
     */
    public function InsertDriverId($city_id, $num=100) {
        $driver_id_list = $this->createBatchDriverId($city_id, $num);
        $driver_list = array();
        foreach ($driver_id_list as $driver_id) {
            $model = new DriverIdPool();
            $model->driver_id = $driver_id;
            $model->status = self::STATUS_USABLE;
            $model->city_id = $city_id;
            $model->created = date('Y-m-d H:i:s', time());
            if($model->save()) {
                $driver_list[] = $driver_id;
            }
        }
        return $driver_list;
    }

    /**
     * 按城市和状态示工号总数
     * @param int $city_id 0为全国
     * @param int $status NULL 为全部
     * @return string
     */
    public function getCountDriverId($city_id=0, $status=self::STATUS_USABLE) {
        $condition = array();
        if ($city_id)
            $condition['city_id'] = $city_id;
        if (!is_null($status))
            $condition['status'] = $status;
        return DriverIdPool::model()->countByAttributes($condition);
    }


    /*
    *   add by aiguoxin
    */
    public function updateStatusByDriverId($driverId,$status){
        $sql = "UPDATE `t_driver_id_pool` SET `status` = :status WHERE driver_id = :driver_id";
        return Yii::app()->db->createCommand($sql)->execute(array(
            ':driver_id' => $driverId,
            ':status' => $status,
        ));
    }
}
